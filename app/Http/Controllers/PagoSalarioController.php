<?php

namespace App\Http\Controllers;

use App\Models\PagoSalario;
use App\Models\DetallePagoSalario;
use App\Models\Empleado;
use App\Models\CierreDiario;
use App\Services\ContabilidadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PagoSalarioController extends Controller
{
    protected $contabilidadService;

    public function __construct(ContabilidadService $contabilidadService)
    {
        $this->contabilidadService = $contabilidadService;
    }

    public function index()
    {
        $pagos = PagoSalario::with(['detalles.empleado'])
            ->orderBy('fecha_pago', 'desc')
            ->paginate(10);
        return view('pagos.salarios.index', compact('pagos'));
    }

    public function create()
    {
        // Obtener empleados activos que no tienen pagos pendientes
        $empleadosPendientes = Empleado::where('activo', true)
            ->whereDoesntHave('pagosSalarios', function($query) {
                $query->where('asiento_generado', false);
            })
            ->get();

        // Obtener empleados que ya tienen pagos pendientes
        $empleadosConPagosPendientes = Empleado::where('activo', true)
            ->whereHas('pagosSalarios', function($query) {
                $query->where('asiento_generado', false);
            })
            ->with(['pagosSalarios' => function($query) {
                $query->where('asiento_generado', false);
            }])
            ->get();

        return view('pagos.salarios.create', compact('empleadosPendientes', 'empleadosConPagosPendientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|string',
            'comprobante' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'auto_asiento' => 'boolean',
            'detalles' => 'required|array|min:1',
            'detalles.*.empleado_id' => 'required|exists:empleados,id',
            'detalles.*.monto' => 'required|numeric|min:0.01',
            'detalles.*.observaciones' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Buscar el cierre diario correspondiente a la fecha de pago
            $cierreDiario = CierreDiario::whereDate('fecha', $request->fecha_pago)->first();

            // Calcular el monto total
            $montoTotal = collect($request->detalles)->sum('monto');

            // Crear el pago de salarios
            $pago = PagoSalario::create([
                'fecha_pago' => $request->fecha_pago,
                'monto_total' => $montoTotal,
                'metodo_pago' => $request->metodo_pago,
                'comprobante' => $request->comprobante,
                'observaciones' => $request->observaciones,
                'auto_asiento' => $request->boolean('auto_asiento'),
                'asiento_generado' => false,
                'cierre_diario_id' => $cierreDiario ? $cierreDiario->id : null
            ]);

            // Crear los detalles del pago
            foreach ($request->detalles as $detalle) {
                DetallePagoSalario::create([
                    'pago_salario_id' => $pago->id,
                    'empleado_id' => $detalle['empleado_id'],
                    'monto' => $detalle['monto'],
                    'observaciones' => $detalle['observaciones'] ?? null
                ]);
            }

            DB::commit();
            return redirect()->route('pagos.salarios.index')
                ->with('success', 'Pago de salarios registrado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el pago de salarios: ' . $e->getMessage());
        }
    }

    public function show(PagoSalario $pago)
    {
        $pago->load(['detalles.empleado', 'asientoContable']);
        return view('pagos.salarios.show', compact('pago'));
    }

    /**
     * Genera el asiento contable para todos los pagos pendientes
     */
    public function generarAsientoContable(Request $request)
    {
        try {
            DB::beginTransaction();

            // Obtener todos los pagos pendientes de asiento
            $pagosPendientes = PagoSalario::where('asiento_generado', false)
                ->where('fecha_pago', $request->fecha_pago)
                ->get();

            if ($pagosPendientes->isEmpty()) {
                return back()->with('error', 'No hay pagos pendientes para generar el asiento contable');
            }

            // Calcular el monto total
            $montoTotal = $pagosPendientes->sum('monto_total');

            // Generar el asiento contable
            $asiento = $this->contabilidadService->generarAsientoPagoSalarioMasivo(
                $pagosPendientes,
                $montoTotal,
                $request->fecha_pago
            );

            // Marcar los pagos como procesados
            $pagosPendientes->each(function ($pago) {
                $pago->update(['asiento_generado' => true]);
            });

            DB::commit();
            return redirect()->route('pagos.salarios.index')
                ->with('success', 'Asiento contable generado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al generar el asiento contable: ' . $e->getMessage());
        }
    }

    /**
     * Muestra la vista para generar el asiento contable
     */
    public function vistaGenerarAsiento()
    {
        $pagosPendientes = PagoSalario::where('asiento_generado', false)
            ->orderBy('fecha_pago')
            ->get()
            ->groupBy('fecha_pago');

        return view('pagos.salarios.generar-asiento', compact('pagosPendientes'));
    }
} 