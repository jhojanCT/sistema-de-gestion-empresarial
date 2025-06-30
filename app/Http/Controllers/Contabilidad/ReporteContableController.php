<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\AsientoContable;
use App\Models\CuentaContable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BalanceComprobacionExport;
use Carbon\Carbon;

class ReporteContableController extends Controller
{
    public function balance(Request $request)
    {
        $fecha = $request->get('fecha', now()->format('Y-m-d'));
        
        $cuentas = CuentaContable::with(['detallesAsiento' => function($query) use ($fecha) {
            $query->whereHas('asiento', function($q) use ($fecha) {
                $q->where('fecha', '<=', $fecha)
                  ->where('estado', 'aprobado');
            });
        }])->get();

        $totales = [
            'activo' => 0,
            'pasivo' => 0,
            'patrimonio' => 0
        ];

        foreach ($cuentas as $cuenta) {
            $saldo = $cuenta->calcularSaldo($fecha);
            switch ($cuenta->tipo) {
                case 'ACTIVO':
                    $totales['activo'] += $saldo;
                    break;
                case 'PASIVO':
                    $totales['pasivo'] += $saldo;
                    break;
                case 'PATRIMONIO':
                    $totales['patrimonio'] += $saldo;
                    break;
            }
        }

        return view('contabilidad.reportes.balance', compact('cuentas', 'totales', 'fecha'));
    }

    public function libroDiario(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $asientos = AsientoContable::with('detalles.cuenta')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', 'aprobado')
            ->orderBy('fecha')
            ->orderBy('numero_asiento')
            ->get();

        $datos = [
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin
            ],
            'asientos' => $asientos->map(function($asiento) {
                return [
                    'fecha' => $asiento->fecha,
                    'numero_asiento' => $asiento->numero_asiento,
                    'concepto' => $asiento->concepto,
                    'detalles' => $asiento->detalles->map(function($detalle) {
                        return [
                            'cuenta' => [
                                'codigo' => $detalle->cuenta->codigo,
                                'nombre' => $detalle->cuenta->nombre
                            ],
                            'descripcion' => $detalle->descripcion,
                            'debe' => $detalle->debe,
                            'haber' => $detalle->haber
                        ];
                    })
                ];
            })
        ];

        return view('contabilidad.reportes.libro-diario', compact('datos'));
    }

    public function libroMayor(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));
        $cuentaId = $request->get('cuenta_id');

        // Obtener todas las cuentas para el selector
        $cuentas = CuentaContable::orderBy('codigo')->get();

        if ($cuentaId) {
            try {
            $cuenta = CuentaContable::findOrFail($cuentaId);
            
            // Calcular saldo anterior (movimientos hasta la fecha de inicio)
            $saldoAnterior = $cuenta->detallesAsiento()
                    ->join('asientos_contables', 'detalles_asiento.asiento_id', '=', 'asientos_contables.id')
                    ->where('asientos_contables.fecha', '<', $fechaInicio)
                    ->where('asientos_contables.estado', 'aprobado')
                    ->whereNull('asientos_contables.deleted_at')
                    ->whereNull('detalles_asiento.deleted_at')
                    ->select(DB::raw('COALESCE(SUM(debe), 0) - COALESCE(SUM(haber), 0) as saldo'))
                    ->value('saldo');

            // Obtener movimientos del período
            $movimientos = $cuenta->detallesAsiento()
                    ->join('asientos_contables', 'detalles_asiento.asiento_id', '=', 'asientos_contables.id')
                    ->whereBetween('asientos_contables.fecha', [$fechaInicio, $fechaFin])
                    ->where('asientos_contables.estado', 'aprobado')
                    ->whereNull('asientos_contables.deleted_at')
                    ->whereNull('detalles_asiento.deleted_at')
                    ->select([
                        'detalles_asiento.*',
                        'asientos_contables.fecha',
                        'asientos_contables.numero_asiento',
                        'asientos_contables.concepto'
                    ])
                    ->orderBy('asientos_contables.fecha')
                    ->orderBy('asientos_contables.numero_asiento')
                ->get();

            // Calcular saldo actual
            $saldoActual = $saldoAnterior;
            foreach ($movimientos as $movimiento) {
                    $saldoActual += ($movimiento->debe - $movimiento->haber);
            }

            return view('contabilidad.reportes.libro-mayor', compact(
                'cuentas',
                'cuenta',
                'movimientos',
                'saldoAnterior',
                'saldoActual',
                'fechaInicio',
                'fechaFin'
            ));
            } catch (\Exception $e) {
                return back()->with('error', 'Error al procesar la cuenta: ' . $e->getMessage());
            }
        }

        return view('contabilidad.reportes.libro-mayor', compact('cuentas', 'fechaInicio', 'fechaFin'));
    }

    public function estadoResultados(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfYear()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $cuentas = CuentaContable::whereIn('tipo', ['INGRESO', 'EGRESO', 'COSTO'])
            ->orderBy('codigo')
            ->get();

        $datos = [
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin
            ],
            'ingresos' => [],
            'costos' => [],
            'gastos' => [],
            'total_ingresos' => 0,
            'total_costos' => 0,
            'total_gastos' => 0,
            'utilidad_bruta' => 0,
            'utilidad_neta' => 0
        ];

        foreach ($cuentas as $cuenta) {
            $saldo = $cuenta->calcularSaldo($fechaFin) - $cuenta->calcularSaldo($fechaInicio);
            
            if ($saldo != 0) {
                $item = [
                    'codigo' => $cuenta->codigo,
                    'nombre' => $cuenta->nombre,
                    'saldo' => $saldo
                ];

                switch ($cuenta->tipo) {
                    case 'INGRESO':
                        $datos['ingresos'][] = $item;
                        $datos['total_ingresos'] += $saldo;
                        break;
                    case 'COSTO':
                        $datos['costos'][] = $item;
                        $datos['total_costos'] += $saldo;
                        break;
                    case 'EGRESO':
                        $datos['gastos'][] = $item;
                        $datos['total_gastos'] += $saldo;
                        break;
                }
            }
        }

        $datos['utilidad_bruta'] = $datos['total_ingresos'] - $datos['total_costos'];
        $datos['utilidad_neta'] = $datos['utilidad_bruta'] - $datos['total_gastos'];

        return view('contabilidad.reportes.estado-resultados', compact('datos'));
    }

    public function balanceComprobacion(Request $request)
    {
        $fecha = $request->get('fecha', now()->format('Y-m-d'));
        $fechaInicio = Carbon::parse($fecha)->startOfMonth()->format('Y-m-d');
        $fechaFin = $fecha;

        $cuentas = CuentaContable::select('cuentas_contables.*')
            ->selectRaw('(
                SELECT COALESCE(SUM(debe), 0) - COALESCE(SUM(haber), 0)
                FROM detalles_asiento
                INNER JOIN asientos_contables ON detalles_asiento.asiento_id = asientos_contables.id
                WHERE detalles_asiento.cuenta_id = cuentas_contables.id
                AND asientos_contables.fecha < ? 
                AND asientos_contables.estado = "aprobado"
                AND asientos_contables.deleted_at IS NULL
                AND detalles_asiento.deleted_at IS NULL
            ) as saldo_anterior', [$fechaInicio])
            ->selectRaw('(
                SELECT COALESCE(SUM(debe), 0)
                FROM detalles_asiento
                INNER JOIN asientos_contables ON detalles_asiento.asiento_id = asientos_contables.id
                WHERE detalles_asiento.cuenta_id = cuentas_contables.id
                AND asientos_contables.fecha BETWEEN ? AND ?
                AND asientos_contables.estado = "aprobado"
                AND asientos_contables.deleted_at IS NULL
                AND detalles_asiento.deleted_at IS NULL
            ) as total_debe', [$fechaInicio, $fechaFin])
            ->selectRaw('(
                SELECT COALESCE(SUM(haber), 0)
                FROM detalles_asiento
                INNER JOIN asientos_contables ON detalles_asiento.asiento_id = asientos_contables.id
                WHERE detalles_asiento.cuenta_id = cuentas_contables.id
                AND asientos_contables.fecha BETWEEN ? AND ?
                AND asientos_contables.estado = "aprobado"
                AND asientos_contables.deleted_at IS NULL
                AND detalles_asiento.deleted_at IS NULL
            ) as total_haber', [$fechaInicio, $fechaFin])
            ->orderBy('codigo')
            ->get();

        $datos = [
            'fecha' => $fecha,
            'cuentas' => $cuentas,
            'total_debe' => $cuentas->sum('total_debe'),
            'total_haber' => $cuentas->sum('total_haber')
        ];

        return view('contabilidad.reportes.balance-comprobacion', compact('datos'));
    }
}
