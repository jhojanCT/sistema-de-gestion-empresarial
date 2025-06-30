<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraItem;
use App\Models\Proveedor;
use App\Models\MateriaPrimaSinFiltrar;
use App\Models\Producto;
use App\Models\PagoProveedor;
use App\Services\ContabilidadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\CentroCosto;
use App\Models\AsientoContable;
use App\Models\MateriaPrimaFiltrada;
use App\Models\DetalleAsiento;
use Illuminate\Support\Facades\Auth;

class CompraController extends Controller
{
    protected $contabilidadService;

    public function __construct(ContabilidadService $contabilidadService)
    {
        $this->contabilidadService = $contabilidadService;
    }

    public function index()
    {
        $compras = Compra::with(['proveedor', 'items'])->get();
        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        return view('compras.create', [
            'proveedores' => Proveedor::all(),
            'materiasPrimas' => MateriaPrimaSinFiltrar::all(),
            'productos' => Producto::all(),
            'centrosCosto' => CentroCosto::where('activo', true)->get()
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('Iniciando proceso de compra', [
            'request_data' => $request->all()
        ]);

        try {
            $validated = $this->validateCompra($request);

            \Log::info('Validación exitosa');

            DB::transaction(function () use ($validated) {
                // Determinar el centro de costo automáticamente si no se proporciona
                $centroCostoId = $validated['centro_costo_id'] ?? $this->determinarCentroCosto($validated['items']);
                
                $total = 0;
                $subtotal = 0;
                $ivaAmount = 0;

                // Calcular subtotal y IVA
                foreach ($validated['items'] as $item) {
                    $itemSubtotal = $item['cantidad'] * $item['precio_unitario'];
                    $subtotal += $itemSubtotal;
                }

                // Calcular IVA solo si hay factura
                if (filter_var($validated['has_invoice'], FILTER_VALIDATE_BOOLEAN)) {
                    $ivaAmount = $subtotal * 0.13; // 13% IVA
                }
                $total = $subtotal + $ivaAmount;

                \Log::info('Creando compra', [
                    'subtotal' => $subtotal,
                    'iva' => $ivaAmount,
                    'total' => $total
                ]);
                
                $compra = Compra::create([
                    'proveedor_id' => $validated['proveedor_id'],
                    'centro_costo_id' => $centroCostoId,
                    'tipo' => $validated['tipo'],
                    'fecha' => $validated['fecha'],
                    'has_invoice' => filter_var($validated['has_invoice'], FILTER_VALIDATE_BOOLEAN),
                    'invoice_number' => $validated['invoice_number'],
                    'subtotal' => $subtotal,
                    'iva_amount' => $ivaAmount,
                    'total' => $total,
                    'pagada' => $validated['tipo'] === 'contado',
                    'auto_asiento' => filter_var($validated['auto_asiento'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'metodo_pago' => $validated['tipo'] === 'contado' ? $validated['tipo_pago'] : null
                ]);

                \Log::info('Compra creada', ['compra_id' => $compra->id]);

                foreach ($validated['items'] as $item) {
                    $itemSubtotal = $item['cantidad'] * $item['precio_unitario'];

                    CompraItem::create([
                        'compra_id' => $compra->id,
                        'tipo_item' => $item['tipo_item'],
                        'materia_prima_id' => $item['tipo_item'] === 'materia_prima' ? $item['item_id'] : null,
                        'producto_id' => $item['tipo_item'] === 'producto' ? $item['item_id'] : null,
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                        'subtotal' => $itemSubtotal,
                    ]);

                    $this->actualizarStock($item, 'sumar');
                }

                \Log::info('Items de compra creados');

                // Si es pago al contado, registrar el pago
                if ($validated['tipo'] === 'contado') {
                    PagoProveedor::create([
                        'compra_id' => $compra->id,
                        'metodo_pago' => $validated['tipo_pago'],
                        'monto' => $total,
                        'fecha_pago' => $validated['fecha']
                    ]);
                    \Log::info('Pago registrado');
                }

                // Generar el asiento contable solo si auto_asiento es true
                if ($compra->auto_asiento) {
                $this->contabilidadService->generarAsientoCompra($compra);
                }
            });

            // Limpiar todo el caché después de la operación
            Cache::flush();

            \Log::info('Compra completada exitosamente');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Compra registrada correctamente',
                    'redirect' => route('compras.index')
                ]);
            }

            return redirect()->route('compras.index')->with('success', 'Compra registrada correctamente');
        } catch (\Exception $e) {
            \Log::error('Error al procesar la compra', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al procesar la compra: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar la compra: ' . $e->getMessage());
        }
    }

    public function show(Compra $compra)
    {
        $compra->load('items', 'proveedor');
        return view('compras.show', compact('compra'));
    }

    public function edit(Compra $compra)
    {
        $compra->load(['items', 'pagos']);
        return view('compras.edit', [
            'compra' => $compra,
            'proveedores' => Proveedor::all(),
            'materiasPrimas' => MateriaPrimaSinFiltrar::all(),
            'productos' => Producto::all()
        ]);
    }

    public function update(Request $request, Compra $compra)
    {
        $validated = $this->validateCompra($request);

        DB::transaction(function () use ($validated, $compra) {
            // Revertir stock anterior
            foreach ($compra->items as $oldItem) {
                $this->actualizarStock([
                    'tipo_item' => $oldItem->tipo_item,
                    'item_id' => $oldItem->tipo_item === 'materia_prima' ? $oldItem->materia_prima_id : $oldItem->producto_id,
                    'cantidad' => $oldItem->cantidad
                ], 'restar');
            }

            // Eliminar ítems anteriores
            $compra->items()->delete();

            // Actualizar datos de la compra
            $compra->update([
                'proveedor_id' => $validated['proveedor_id'],
                'tipo' => $validated['tipo'],
                'fecha' => $validated['fecha'],
                'total' => 0 // recalculado
            ]);

            $total = 0;

            foreach ($validated['items'] as $item) {
                $subtotal = $item['cantidad'] * $item['precio_unitario'];
                $total += $subtotal;

                CompraItem::create([
                    'compra_id' => $compra->id,
                    'tipo_item' => $item['tipo_item'],
                    'materia_prima_id' => $item['tipo_item'] === 'materia_prima' ? $item['item_id'] : null,
                    'producto_id' => $item['tipo_item'] === 'producto' ? $item['item_id'] : null,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $subtotal,
                ]);

                $this->actualizarStock($item, 'sumar');
            }

            $compra->update(['total' => $total]);

            // Generar nuevo asiento contable
            $this->contabilidadService->generarAsientoCompra($compra);
        });

        return redirect()->route('compras.index')->with('success', 'Compra actualizada correctamente');
    }

    public function destroy(Compra $compra)
    {
        DB::transaction(function () use ($compra) {
            // Verificar si la compra está asociada a un cierre diario
            if ($compra->cierre_diario_id) {
                throw new \Exception('No se puede eliminar una compra que ya está incluida en un cierre diario.');
            }

            // Revertir stock
            foreach ($compra->items as $item) {
                if ($item->tipo_item == 'materia_prima_filtrada') {
                    MateriaPrimaFiltrada::find($item->materia_prima_filtrada_id)
                        ->decrement('stock', $item->cantidad);
                } else {
                    Producto::find($item->producto_id)
                        ->decrement('stock', $item->cantidad);
                }
            }

            // Eliminar movimientos bancarios asociados
            $compra->movimientosBancarios()->delete();

            // Eliminar asientos contables asociados y revertir saldos
            $asientos = AsientoContable::where('tipo_documento', 'COMPRA')
                ->where('numero_documento', $compra->id)
                ->get();
            
            foreach ($asientos as $asiento) {
                // Generar número de asiento único para la reversión
                $ultimoAsiento = AsientoContable::where('numero_asiento', 'LIKE', 'REV-COMP-%')
                    ->orderBy('numero_asiento', 'desc')
                    ->first();
                
                $numeroReversion = $ultimoAsiento ? 
                    'REV-COMP-' . str_pad((int)substr($ultimoAsiento->numero_asiento, 9) + 1, 6, '0', STR_PAD_LEFT) : 
                    'REV-COMP-000001';
                
                // Crear un solo asiento de reversión
                $asientoReversion = AsientoContable::create([
                    'fecha' => now(),
                    'numero_asiento' => $numeroReversion,
                    'tipo_documento' => 'REVERSION',
                    'numero_documento' => $asiento->id,
                    'concepto' => "Reversión de compra #{$compra->id}",
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
            $compra->items()->delete();
            $compra->pagos()->delete();
            $compra->delete();
        });

        return redirect()->route('compras.index')->with('success', 'Compra eliminada correctamente');
    }

    private function validateCompra(Request $request)
    {
        $rules = [
            'proveedor_id' => 'required|exists:proveedores,id',
            'centro_costo_id' => 'nullable|exists:centros_costo,id',
            'tipo' => 'required|in:contado,credito',
            'fecha' => 'required|date',
            'has_invoice' => 'required|in:0,1',
            'invoice_number' => 'nullable|string|required_if:has_invoice,1',
            'items' => 'required|array|min:1',
            'items.*.tipo_item' => 'required|in:materia_prima,producto',
            'items.*.item_id' => 'required|integer',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'tipo_pago' => 'required_if:tipo,contado|nullable|in:efectivo,transferencia',
            'auto_asiento' => 'nullable|in:0,1'
        ];

        return $request->validate($rules);
    }

    private function actualizarStock(array $item, string $accion = 'sumar')
    {
        if ($item['tipo_item'] === 'materia_prima') {
            $modelo = MateriaPrimaSinFiltrar::find($item['item_id']);
        } else {
            $modelo = Producto::find($item['item_id']);
        }

        if ($accion === 'sumar') {
            $modelo->increment('stock', $item['cantidad']);
        } else {
            $modelo->decrement('stock', $item['cantidad']);
        }
    }

    private function determinarCentroCosto($items)
    {
        // Lógica para determinar el centro de costo basado en los items
        $tiposItems = collect($items)->pluck('tipo_item')->unique();
        
        // Si todos los items son materia prima, asignar al centro de costo de Almacén
        if ($tiposItems->count() === 1 && $tiposItems->first() === 'materia_prima') {
            return CentroCosto::where('codigo', 'ALM-MP')
                             ->where('activo', true)
                             ->first()?->id;
        }
        
        // Si todos los items son productos, asignar al centro de costo de Productos
        if ($tiposItems->count() === 1 && $tiposItems->first() === 'producto') {
            return CentroCosto::where('codigo', 'ALM-PT')
                             ->where('activo', true)
                             ->first()?->id;
        }
        
        // Si hay mezcla de items, asignar al centro de costo general de Almacén
        return CentroCosto::where('codigo', 'ALM')
                         ->where('activo', true)
                         ->first()?->id;
    }


    protected function generarAsientoContable(Compra $compra)
    {
        if ($compra->tipo == 'credito') {
            if ($compra->has_invoice) {
                // Asiento para compra a crédito con factura
                $this->contabilidadService->crearAsiento([
                    ['cuenta' => '5.1.1.01', 'debe' => $compra->subtotal], // Compras
                    ['cuenta' => '1.1.4.01', 'debe' => $compra->iva_amount], // IVA por Cobrar
                    ['cuenta' => '2.1.1.01', 'haber' => $compra->total] // Cuentas por Pagar
                ]);
            } else {
                // Asiento para compra a crédito sin factura
                $this->contabilidadService->crearAsiento([
                    ['cuenta' => '5.1.1.02', 'debe' => $compra->total], // Compras sin Factura
                    ['cuenta' => '2.1.1.02', 'haber' => $compra->total] // Documentos por Pagar
                ]);
            }
        }
    }
}
