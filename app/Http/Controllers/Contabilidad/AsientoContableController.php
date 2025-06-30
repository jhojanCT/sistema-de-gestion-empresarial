<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\AsientoContable;
use App\Models\CuentaContable;
use App\Models\CentroCosto;
use App\Models\Venta;
use App\Models\Compra;
use App\Services\ContabilidadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PagoCliente;
use App\Models\PagoProveedor;
use App\Models\PagoSalario;

class AsientoContableController extends Controller
{
    protected $contabilidadService;

    public function __construct(ContabilidadService $contabilidadService)
    {
        $this->contabilidadService = $contabilidadService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AsientoContable::with(['detalles', 'centroCosto']);

        // Aplicar filtros de búsqueda
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('concepto', 'like', '%' . $searchTerm . '%')
                  ->orWhere('numero_asiento', 'like', '%' . $searchTerm . '%')
                  ->orWhere('tipo_operacion', 'like', '%' . $searchTerm . '%')
                  ->orWhere('tipo_documento', 'like', '%' . $searchTerm . '%')
                  ->orWhere('numero_documento', 'like', '%' . $searchTerm . '%');
            });
        }

        // Aplicar filtro por tipo de operación
        if ($request->filled('tipoFilter')) {
            $query->where('tipo_operacion', $request->get('tipoFilter'));
        }

        // Aplicar filtro por rango de fechas
        if ($request->filled('fechaInicio')) {
            $query->where('fecha', '>=', $request->get('fechaInicio'));
        }
        if ($request->filled('fechaFin')) {
            $query->where('fecha', '<=', $request->get('fechaFin'));
        }

        $asientos = $query->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        foreach ($asientos as $asiento) {
            $detalles_sum_debe = $asiento->detalles->sum('debe');
            $detalles_sum_haber = $asiento->detalles->sum('haber');
            
            // Forzar los valores como float
            $asiento->total_debe = (float)$detalles_sum_debe;
            $asiento->total_haber = (float)$detalles_sum_haber;
        }

        // Pasar los valores de los filtros a la vista para mantenerlos en los campos del formulario
        return view('contabilidad.asientos.index', compact('asientos'))->withRequest($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $tipo = $request->get('tipo');
        $id = $request->get('id');
        $documento = null;
        $tipoDocumento = null;
        $numeroDocumento = null;
        $tipoOperacion = 'diario'; // Valor por defecto
        
        // Si se proporciona tipo y id, es un asiento basado en documento
        if ($tipo && $id) {
            switch ($tipo) {
                case 'venta':
                    $documento = Venta::with(['cliente', 'items.producto', 'items.materiaPrimaFiltrada'])->findOrFail($id);
                    $tipoDocumento = 'VENTA';
                    $numeroDocumento = $id;
                    $tipoOperacion = 'venta';
                    break;
                case 'compra':
                    $documento = Compra::with(['proveedor', 'items.producto'])->findOrFail($id);
                    $tipoDocumento = 'COMPRA';
                    $numeroDocumento = $id;
                    $tipoOperacion = 'compra';
                    break;
                case 'pago_cliente':
                    $documento = PagoCliente::with(['venta.cliente', 'venta.items.producto'])->findOrFail($id);
                    $tipoDocumento = 'COBRO_CLIENTE';
                    $numeroDocumento = $id;
                    break;
                case 'pago_proveedor':
                    $documento = PagoProveedor::with(['compra.proveedor', 'compra.items.producto'])->findOrFail($id);
                    $tipoDocumento = 'PAGO_PROVEEDOR';
                    $numeroDocumento = $id;
                    break;
                case 'pago_salario':
                    $documento = PagoSalario::with(['detalles.empleado'])->findOrFail($id);
                    $tipoDocumento = 'PAGO_SALARIO';
                    $numeroDocumento = $id;
                    break;
                default:
                    return redirect()->route('contabilidad.asientos.pendientes')
                        ->with('error', 'Tipo de documento no válido');
            }
        }
        
        $cuentas = CuentaContable::where('activo', true)->orderBy('codigo')->get();
        $centrosCosto = CentroCosto::where('activo', true)->orderBy('codigo')->get();
        
        return view('contabilidad.asientos.create', [
            'tipo' => $tipo,
            'documento' => $documento,
            'cuentas' => $cuentas,
            'centrosCosto' => $centrosCosto,
            'tipoDocumento' => $tipoDocumento,
            'numeroDocumento' => $numeroDocumento,
            'tipoOperacion' => $tipoOperacion
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Debe iniciar sesión para crear asientos contables.');
        }

        \Log::info('Iniciando creación de asiento contable', [
            'request' => $request->all(),
            'user_id' => auth()->id()
        ]);
        
        try {
            $request->validate([
                'fecha' => 'required|date',
                'tipo_operacion' => 'required|in:diario,ajuste,cierre,venta,compra',
                'concepto' => 'required|string|max:255',
                'estado' => 'required|in:borrador,aprobado',
                'centro_costo_id' => 'nullable|exists:centros_costo,id',
                'tipo_documento' => 'nullable|string|max:50',
                'numero_documento' => 'nullable|string|max:50',
                'detalles' => 'required|array|min:2',
                'detalles.*.cuenta_id' => 'required|exists:cuentas_contables,id',
                'detalles.*.debe' => 'required_without:detalles.*.haber|numeric|min:0',
                'detalles.*.haber' => 'required_without:detalles.*.debe|numeric|min:0',
                'detalles.*.descripcion' => 'nullable|string|max:255',
            ]);

            \Log::info('Validación pasada correctamente');

            // Verificar que el asiento esté balanceado
            $totalDebe = 0;
            $totalHaber = 0;
            $cuentas = [];

            foreach ($request->detalles as $detalle) {
                $debe = floatval($detalle['debe'] ?? 0);
                $haber = floatval($detalle['haber'] ?? 0);
                $totalDebe += $debe;
                $totalHaber += $haber;

                // Verificar que la cuenta no se repita
                if (isset($cuentas[$detalle['cuenta_id']])) {
                    throw new \Exception('No se puede usar la misma cuenta más de una vez en el mismo asiento.');
                }
                $cuentas[$detalle['cuenta_id']] = true;
            }

            if (abs($totalDebe - $totalHaber) > 0.01) {
                throw new \Exception('El asiento no está balanceado. Debe: ' . $totalDebe . ', Haber: ' . $totalHaber);
            }

            try {
                // Generar número de asiento
                $ultimoAsiento = AsientoContable::orderBy('id', 'desc')->first();
                $numeroAsiento = $ultimoAsiento ? (int)$ultimoAsiento->numero_asiento + 1 : 1;

                \Log::info('Creando asiento principal', [
                    'numero_asiento' => $numeroAsiento,
                    'totalDebe' => $totalDebe
                ]);

                // Preparar los datos del asiento
                $asientoData = [
                    'fecha' => $request->fecha,
                    'tipo_operacion' => $request->tipo_operacion,
                    'concepto' => $request->concepto,
                    'estado' => $request->estado,
                    'centro_costo_id' => $request->centro_costo_id,
                    'monto_total' => (float)$totalDebe,
                    'saldo_pendiente' => 0,
                    'numero_asiento' => $numeroAsiento,
                    'tipo_documento' => $request->tipo_documento ?? 'manual',
                    'user_id' => auth()->id()
                ];

                // Solo agregar numero_documento si se proporciona
                if ($request->filled('numero_documento')) {
                    $asientoData['numero_documento'] = $request->numero_documento;
                }

                $asiento = AsientoContable::create($asientoData);

                \Log::info('Asiento principal creado', [
                    'asiento_id' => $asiento->id,
                    'user_id' => auth()->id()
                ]);

                // Crear los detalles del asiento
                foreach ($request->detalles as $detalle) {
                    $asiento->detalles()->create([
                        'cuenta_id' => $detalle['cuenta_id'],
                        'debe' => floatval($detalle['debe'] ?? 0),
                        'haber' => floatval($detalle['haber'] ?? 0),
                        'descripcion' => $detalle['descripcion'] ?? null
                    ]);
                }

                \Log::info('Detalles del asiento creados', [
                    'asiento_id' => $asiento->id,
                    'detalles_count' => count($request->detalles)
                ]);

                return redirect()->route('contabilidad.asientos.show', $asiento)
                    ->with('success', 'Asiento contable creado correctamente.');

            } catch (\Exception $e) {
                \Log::error('Error al crear el asiento', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('Error en la validación o creación del asiento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error al crear el asiento contable: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AsientoContable $asiento)
    {
        $asiento->load(['detalles.cuenta', 'centroCosto']);
        
        if (!$asiento->detalles) {
            return redirect()->route('contabilidad.asientos.index')
                ->with('error', 'El asiento no tiene detalles registrados.');
        }

        // Calcular totales
        $totales = [
            'debe' => $asiento->detalles->sum('debe'),
            'haber' => $asiento->detalles->sum('haber')
        ];

        // Verificar que cada detalle tenga su cuenta
        foreach ($asiento->detalles as $detalle) {
            if (!$detalle->cuenta) {
                return redirect()->route('contabilidad.asientos.index')
                    ->with('error', 'Error: Hay detalles sin cuenta contable asignada.');
            }
        }
        
        return view('contabilidad.asientos.show', compact('asiento', 'totales'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AsientoContable $asiento)
    {
        $cuentas = CuentaContable::where('activo', true)->orderBy('codigo')->get();
        $centrosCosto = CentroCosto::where('activo', true)->orderBy('codigo')->get();
        $asiento->load('detalles');
        return view('contabilidad.asientos.edit', compact('asiento', 'cuentas', 'centrosCosto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AsientoContable $asiento)
    {
        $request->validate([
            'fecha' => 'required|date',
            'tipo' => 'required|in:diario,ajuste,cierre',
            'concepto' => 'required|string',
            'centro_costo_id' => 'nullable|exists:centros_costo,id',
            'detalles' => 'required|array|min:2',
            'detalles.*.cuenta_id' => 'required|exists:cuentas_contables,id',
            'detalles.*.debe' => 'required|numeric|min:0',
            'detalles.*.haber' => 'required|numeric|min:0',
            'detalles.*.descripcion' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Verificar que la suma de debe sea igual a la suma de haber
            $sumaDebe = collect($request->detalles)->sum('debe');
            $sumaHaber = collect($request->detalles)->sum('haber');

            if (abs($sumaDebe - $sumaHaber) > 0.01) {
                throw new \Exception('El asiento no está balanceado. La suma de debe debe ser igual a la suma de haber.');
            }

            $asiento->update([
                'fecha' => $request->fecha,
                'tipo' => $request->tipo,
                'concepto' => $request->concepto,
                'centro_costo_id' => $request->centro_costo_id,
            ]);

            // Eliminar detalles existentes
            $asiento->detalles()->delete();

            // Crear nuevos detalles
            foreach ($request->detalles as $detalle) {
                $asiento->detalles()->create([
                    'cuenta_id' => $detalle['cuenta_id'],
                    'debe' => $detalle['debe'],
                    'haber' => $detalle['haber'],
                    'descripcion' => $detalle['descripcion'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('contabilidad.asientos.index')
                ->with('success', 'Asiento contable actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar el asiento contable: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AsientoContable $asiento)
    {
        try {
            DB::beginTransaction();

            // Eliminar detalles primero
            $asiento->detalles()->delete();
            
            // Eliminar el asiento
            $asiento->delete();

            DB::commit();

            return redirect()->route('contabilidad.asientos.index')
                ->with('success', 'Asiento contable eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar el asiento contable: ' . $e->getMessage());
        }
    }

    /**
     * Anular un asiento contable
     */
    public function anular(AsientoContable $asiento)
    {
        try {
            DB::beginTransaction();

            if ($asiento->estado === 'ANULADO') {
                return redirect()->back()
                    ->with('warning', 'El asiento ya está anulado.');
            }

            if ($asiento->estado === 'PAGADO') {
                return redirect()->back()
                    ->with('error', 'No se puede anular un asiento que ya ha sido pagado.');
            }

            $asiento->anular();
            DB::commit();

            return redirect()->route('contabilidad.asientos.show', $asiento)
                ->with('success', 'Asiento contable anulado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al anular el asiento contable: ' . $e->getMessage());
        }
    }

    /**
     * Publicar un asiento contable
     */
    public function publicar(AsientoContable $asiento)
    {
        try {
            DB::beginTransaction();

            if ($asiento->estado === 'APROBADO') {
                return redirect()->back()
                    ->with('warning', 'El asiento ya está aprobado.');
            }

            if ($asiento->estado === 'ANULADO') {
                return redirect()->back()
                    ->with('error', 'No se puede publicar un asiento anulado.');
            }

            if (!$asiento->estaBalanceado()) {
                return redirect()->back()
                    ->with('error', 'No se puede publicar un asiento que no está balanceado.');
            }

            $asiento->aprobar();
            DB::commit();

            return redirect()->route('contabilidad.asientos.show', $asiento)
                ->with('success', 'Asiento contable aprobado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al aprobar el asiento contable: ' . $e->getMessage());
        }
    }

    // Método para listar pagos pendientes de asiento contable
    public function pendientesPago()
    {
        // Obtener pagos de salarios sin asiento contable
        $pagosSalarios = PagoSalario::whereDoesntHave('asientoContable')
            ->where('auto_asiento', false)
            ->with(['detalles.empleado'])
            ->orderBy('fecha_pago', 'desc')
            ->paginate(10);

        // Obtener pagos de clientes sin asiento contable
        $pagosClientes = PagoCliente::whereDoesntHave('asientoContable')
            ->where('auto_asiento', false)
            ->whereHas('venta', function($query) {
                $query->where('tipo', 'credito'); // Solo ventas a crédito
            })
            ->with(['venta', 'venta.cliente'])
            ->orderBy('fecha_pago', 'desc')
            ->paginate(10);
        
        // Obtener pagos a proveedores sin asiento contable
        $pagosProveedores = PagoProveedor::whereDoesntHave('asientoContable')
            ->where('auto_asiento', false)
            ->with(['compra', 'compra.proveedor'])
            ->orderBy('fecha_pago', 'desc')
            ->paginate(10);

        return view('contabilidad.asientos.pendientes-pago', compact('pagosSalarios', 'pagosClientes', 'pagosProveedores'));
    }

    // Método para generar asiento contable de un pago
    public function generarAsientoPago(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:cliente,proveedor,salario',
            'id' => 'required|integer'
        ]);

        try {
            DB::beginTransaction();

            switch ($request->tipo) {
                case 'cliente':
                    $pago = PagoCliente::findOrFail($request->id);
                    $asiento = $this->contabilidadService->generarAsientoCobroCliente($pago);
                    break;
                case 'proveedor':
                    $pago = PagoProveedor::findOrFail($request->id);
                    $asiento = $this->contabilidadService->generarAsientoPagoProveedor($pago);
                    break;
                case 'salario':
                    $pago = PagoSalario::findOrFail($request->id);
                    $asiento = $this->contabilidadService->generarAsientoPagoSalario($pago);
                    break;
            }

            DB::commit();
            return redirect()->route('contabilidad.asientos.show', $asiento)
                ->with('success', 'Asiento contable generado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al generar el asiento contable: ' . $e->getMessage());
        }
    }

    public function pendientes()
    {
        // Obtener ventas sin asiento contable
        $ventasPendientes = Venta::whereDoesntHave('asientoContable')
            ->where('auto_asiento', false)
            ->with(['cliente', 'items'])
            ->get();

        // Obtener compras sin asiento contable
        $comprasPendientes = Compra::whereDoesntHave('asientoContable')
            ->where('auto_asiento', false)
            ->with(['proveedor', 'items'])
            ->get();

        return view('contabilidad.asientos.pendientes', compact('ventasPendientes', 'comprasPendientes'));
    }

    public function generarAsientoPendiente(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:venta,compra',
            'id' => 'required|integer'
        ]);

        if ($request->tipo === 'venta') {
            $venta = Venta::findOrFail($request->id);
            $asiento = $this->contabilidadService->generarAsientoVenta($venta);
        } else {
            $compra = Compra::findOrFail($request->id);
            $asiento = $this->contabilidadService->generarAsientoCompra($compra);
        }

        return redirect()->route('contabilidad.asientos.show', $asiento)
            ->with('success', 'Asiento contable generado correctamente.');
    }
}
