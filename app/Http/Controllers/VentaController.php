<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaItem;
use App\Models\Cliente;
use App\Models\MateriaPrimaFiltrada;
use App\Models\Producto;
use App\Models\PagoCliente;
use App\Services\ContabilidadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\AsientoContable;
use App\Models\DetalleAsiento;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    protected $contabilidadService;

    public function __construct(ContabilidadService $contabilidadService)
    {
        $this->contabilidadService = $contabilidadService;
        $this->middleware('can:ver-ventas')->only('index', 'show');
        $this->middleware('can:crear-ventas')->only(['create', 'store']);
        $this->middleware('can:editar-ventas')->only(['edit', 'update']);
        $this->middleware('can:eliminar-ventas')->only('destroy');
    }

    public function index()
    {
        $ventas = Venta::with(['cliente', 'items'])->orderBy('fecha', 'desc')->get();
        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        
        // Cargar materias primas con stock
        $materiasPrimas = MateriaPrimaFiltrada::where('stock', '>', 0)
            ->select('id', 'nombre', 'stock', 'unidad_medida')
            ->orderBy('nombre')
            ->get();

        // Cargar productos con stock
        $productos = Producto::where('stock', '>', 0)
            ->select('id', 'nombre', 'stock', 'unidad_medida', 'precio_venta')
            ->orderBy('nombre')
            ->get();

        // Debug information
        \Log::info('Datos para la vista de ventas:', [
            'cantidad_materias_primas' => $materiasPrimas->count(),
            'cantidad_productos' => $productos->count(),
            'materias_primas' => $materiasPrimas->toArray(),
            'productos' => $productos->toArray()
        ]);

        return view('ventas.create', compact('clientes', 'materiasPrimas', 'productos'));
    }

    public function store(Request $request)
    {
        \Log::info('Iniciando proceso de venta', [
            'request_data' => $request->all()
        ]);

        try {
            $request->validate([
                'cliente_id' => 'nullable|exists:clientes,id',
                'tipo' => 'required|in:contado,credito',
                'fecha' => 'required|date',
                'has_invoice' => 'required|in:0,1',
                'invoice_number' => 'nullable|string|required_if:has_invoice,1',
                'items' => 'required|array|min:1',
                'items.*.tipo_item' => 'required|in:materia_prima_filtrada,producto',
                'items.*.item_id' => 'required|integer',
                'items.*.cantidad' => 'required|numeric|min:0.01',
                'items.*.precio_unitario' => 'required|numeric|min:0',
                'tipo_pago' => 'required_if:tipo,contado|nullable|in:efectivo,transferencia',
                'auto_asiento' => 'nullable|in:0,1'
            ]);

            \Log::info('Validación exitosa');

            DB::transaction(function () use ($request) {
                $subtotal = 0;
                $ivaAmount = 0;

                // Calcular subtotal y IVA
                foreach ($request->items as $item) {
                    $itemSubtotal = $item['cantidad'] * $item['precio_unitario'];
                    $subtotal += $itemSubtotal;
                }

                // Calcular IVA solo si hay factura
                if ($request->boolean('has_invoice')) {
                    $ivaAmount = $subtotal * 0.13; // 13% IVA
                }
                $total = $subtotal + $ivaAmount;

                \Log::info('Creando venta', [
                    'subtotal' => $subtotal,
                    'iva' => $ivaAmount,
                    'total' => $total
                ]);

                $venta = Venta::create([
                    'cliente_id' => $request->cliente_id,
                    'tipo' => $request->tipo,
                    'fecha' => $request->fecha,
                    'has_invoice' => $request->boolean('has_invoice'),
                    'invoice_number' => $request->invoice_number,
                    'subtotal' => $subtotal,
                    'iva_amount' => $ivaAmount,
                    'total' => $total,
                    'pagada' => $request->tipo === 'contado',
                    'auto_asiento' => filter_var($request->boolean('auto_asiento') ?? true, FILTER_VALIDATE_BOOLEAN),
                    'metodo_pago' => $request->tipo === 'contado' ? $request->tipo_pago : null
                ]);

                \Log::info('Venta creada', ['venta_id' => $venta->id]);

                // Crear items y actualizar stock
                foreach ($request->items as $item) {
                    $itemSubtotal = $item['cantidad'] * $item['precio_unitario'];

                    VentaItem::create([
                        'venta_id' => $venta->id,
                        'tipo_item' => $item['tipo_item'],
                        'materia_prima_filtrada_id' => $item['tipo_item'] == 'materia_prima_filtrada' ? $item['item_id'] : null,
                        'producto_id' => $item['tipo_item'] == 'producto' ? $item['item_id'] : null,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                        'subtotal' => $itemSubtotal
                    ]);

                    // Reducir stock
                    if ($item['tipo_item'] == 'materia_prima_filtrada') {
                        $materia = MateriaPrimaFiltrada::findOrFail($item['item_id']);
                        if ($materia->stock < $item['cantidad']) {
                            throw new \Exception("No hay suficiente stock de {$materia->nombre}");
                        }
                        $materia->decrement('stock', $item['cantidad']);
                    } else {
                        $producto = Producto::findOrFail($item['item_id']);
                        if ($producto->stock < $item['cantidad']) {
                            throw new \Exception("No hay suficiente stock de {$producto->nombre}");
                        }
                        $producto->decrement('stock', $item['cantidad']);
                    }
                }

                \Log::info('Items de venta creados');

                // Si es pago al contado, registrar el pago
                if ($request->tipo === 'contado') {
                    PagoCliente::create([
                        'venta_id' => $venta->id,
                        'metodo_pago' => $request->tipo_pago,
                        'monto' => $total,
                        'fecha_pago' => $request->fecha
                    ]);
                    \Log::info('Pago registrado');
                }

                // Generar el asiento contable solo si auto_asiento es true
                if ($venta->auto_asiento) {
                $this->contabilidadService->generarAsientoVenta($venta);
                }
            });

            // Limpiar todo el caché después de la operación
            Cache::flush();

            \Log::info('Venta completada exitosamente');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Venta registrada correctamente',
                    'redirect' => route('ventas.index')
                ]);
            }

            return redirect()->route('ventas.index')->with('success', 'Venta registrada correctamente');
        } catch (\Exception $e) {
            \Log::error('Error al procesar la venta', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al procesar la venta: ' . $e->getMessage()
                ], 422);
            }

            return back()->with('error', 'Error al procesar la venta: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'items.materiaPrimaFiltrada', 'items.producto', 'pagos']);
        return view('ventas.show', compact('venta'));
    }

    public function edit(Venta $venta)
    {
        if ($venta->fecha < now()->subDays(30)) {
            return redirect()->route('ventas.index')->with('error', 'No se pueden editar ventas con más de 30 días');
        }

        $clientes = Cliente::all();
        $materiasPrimas = MateriaPrimaFiltrada::all();
        $productos = Producto::all();
        
        $venta->load('items');
        
        return view('ventas.edit', compact('venta', 'clientes', 'materiasPrimas', 'productos'));
    }

    public function update(Request $request, Venta $venta)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'tipo' => 'required|in:contado,credito',
            'fecha' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.tipo_item' => 'required|in:materia_prima_filtrada,producto',
            'items.*.item_id' => 'required|integer',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0'
        ]);

        DB::transaction(function () use ($request, $venta) {
            // Revertir stock de los items anteriores
            foreach ($venta->items as $oldItem) {
                if ($oldItem->tipo_item == 'materia_prima_filtrada') {
                    MateriaPrimaFiltrada::find($oldItem->materia_prima_filtrada_id)
                        ->increment('stock', $oldItem->cantidad);
                } else {
                    Producto::find($oldItem->producto_id)
                        ->increment('stock', $oldItem->cantidad);
                }
            }

            // Eliminar items anteriores
            $venta->items()->delete();

            // Actualizar datos de la venta
            $venta->update([
                'cliente_id' => $request->cliente_id,
                'tipo' => $request->tipo,
                'fecha' => $request->fecha,
                'total' => 0,
                'pagada' => $request->tipo == 'contado' ? true : false
            ]);

            $total = 0;

            // Procesar nuevos items
            foreach ($request->items as $item) {
                $subtotal = $item['cantidad'] * $item['precio_unitario'];
                $total += $subtotal;

                VentaItem::create([
                    'venta_id' => $venta->id,
                    'tipo_item' => $item['tipo_item'],
                    'materia_prima_filtrada_id' => $item['tipo_item'] == 'materia_prima_filtrada' ? $item['item_id'] : null,
                    'producto_id' => $item['tipo_item'] == 'producto' ? $item['item_id'] : null,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $subtotal
                ]);

                // Reducir stock
                if ($item['tipo_item'] == 'materia_prima_filtrada') {
                    $materia = MateriaPrimaFiltrada::findOrFail($item['item_id']);
                    if ($materia->stock < $item['cantidad']) {
                        throw new \Exception("No hay suficiente stock de {$materia->nombre}");
                    }
                    $materia->decrement('stock', $item['cantidad']);
                } else {
                    $producto = Producto::findOrFail($item['item_id']);
                    if ($producto->stock < $item['cantidad']) {
                        throw new \Exception("No hay suficiente stock de {$producto->nombre}");
                    }
                    $producto->decrement('stock', $item['cantidad']);
                }
            }

            $venta->update(['total' => $total]);

            // Generar nuevo asiento contable
            $this->contabilidadService->generarAsientoVenta($venta);
        });

        return redirect()->route('ventas.show', $venta)->with('success', 'Venta actualizada correctamente');
    }

    public function destroy(Venta $venta)
    {
        DB::transaction(function () use ($venta) {
            // Verificar si la venta está asociada a un cierre diario
            if ($venta->cierre_diario_id) {
                throw new \Exception('No se puede eliminar una venta que ya está incluida en un cierre diario.');
            }

            // Revertir stock
            foreach ($venta->items as $item) {
                if ($item->tipo_item == 'materia_prima_filtrada') {
                    MateriaPrimaFiltrada::find($item->materia_prima_filtrada_id)
                        ->increment('stock', $item->cantidad);
                } else {
                    Producto::find($item->producto_id)
                        ->increment('stock', $item->cantidad);
                }
            }

            // Eliminar movimientos bancarios asociados
            $venta->movimientosBancarios()->delete();

            // Eliminar asientos contables asociados y revertir saldos
            $asientos = AsientoContable::where('tipo_documento', 'VENTA')
                ->where('numero_documento', $venta->id)
                ->get();
            
            foreach ($asientos as $asiento) {
                // Generar número de asiento único para la reversión
                $ultimoAsiento = AsientoContable::where('numero_asiento', 'LIKE', 'REV-VENT-%')
                    ->orderBy('numero_asiento', 'desc')
                    ->first();
                
                $numeroReversion = $ultimoAsiento ? 
                    'REV-VENT-' . str_pad((int)substr($ultimoAsiento->numero_asiento, 9) + 1, 6, '0', STR_PAD_LEFT) : 
                    'REV-VENT-000001';
                
                // Crear un solo asiento de reversión
                $asientoReversion = AsientoContable::create([
                    'fecha' => now(),
                    'numero_asiento' => $numeroReversion,
                    'tipo_documento' => 'REVERSION',
                    'numero_documento' => $asiento->id,
                    'concepto' => "Reversión de venta #{$venta->id}",
                    'estado' => 'aprobado',
                    'user_id' => Auth::id()
                ]);

                // Crear todos los detalles de reversión en el mismo asiento
                foreach ($asiento->detalles as $detalle) {
                    $cuenta = $detalle->cuenta;
                    
                    // Para cuentas deudoras, invertimos debe y haber
                    // Para cuentas acreedoras, mantenemos los valores originales
                    $debe = $cuenta->naturaleza === 'DEUDORA' ? $detalle->haber : $detalle->debe;
                    $haber = $cuenta->naturaleza === 'DEUDORA' ? $detalle->debe : $detalle->haber;
                    
                    DetalleAsiento::create([
                        'asiento_id' => $asientoReversion->id,
                        'cuenta_id' => $detalle->cuenta_id,
                        'debe' => $debe,
                        'haber' => $haber,
                        'descripcion' => "Reversión de {$detalle->descripcion}"
                    ]);
                }

                $asiento->detalles()->delete();
                $asiento->delete();
            }

            // Eliminar items y pagos asociados
            $venta->items()->delete();
            $venta->pagos()->delete();
            $venta->delete();
        });

        return redirect()->route('ventas.index')->with('success', 'Venta eliminada correctamente');
    }


    protected function generarAsientoContable(Venta $venta)
    {
        // Agregar validación de cierre diario
        if ($venta->cierre_diario_id) {
            throw new \Exception('No se puede modificar una venta que ya está en un cierre diario.');
        }
        
        // Agregar registro de centro de costos
        $centroCosto = CentroCosto::where('nombre', 'Ventas')->first();
        
        if ($venta->tipo == 'credito') {
            if ($venta->has_invoice) {
                $this->contabilidadService->crearAsiento([
                    ['cuenta' => '1.1.2.01', 'debe' => $venta->total, 'centro_costo_id' => $centroCosto->id], 
                    ['cuenta' => '2.1.4.01', 'haber' => $venta->iva_amount],
                    ['cuenta' => '4.1.1.01', 'haber' => $venta->subtotal, 'centro_costo_id' => $centroCosto->id]
                ]);
            } else {
                $this->contabilidadService->crearAsiento([
                    ['cuenta' => '1.1.2.02', 'debe' => $venta->total, 'centro_costo_id' => $centroCosto->id],
                    ['cuenta' => '4.1.1.02', 'haber' => $venta->total, 'centro_costo_id' => $centroCosto->id]
                ]);
            }
        }
    }
}