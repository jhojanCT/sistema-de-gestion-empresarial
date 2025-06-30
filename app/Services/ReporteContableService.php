<?php

namespace App\Services;

use App\Models\AsientoContable;
use App\Models\DetalleAsiento;
use App\Models\CuentaContable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteContableService
{
    /**
     * Genera el balance general
     */
    public function generarBalanceGeneral($fecha = null)
    {
        $fecha = $fecha ? Carbon::parse($fecha) : Carbon::now();
        
        $cuentas = CuentaContable::whereIn('tipo', ['ACTIVO', 'PASIVO', 'PATRIMONIO'])
            ->orderBy('codigo')
            ->get();

        $resultado = [
            'activos' => [],
            'pasivos' => [],
            'patrimonio' => [],
            'total_activos' => 0,
            'total_pasivos' => 0,
            'total_patrimonio' => 0
        ];

        foreach ($cuentas as $cuenta) {
            $saldo = $this->calcularSaldoCuenta($cuenta->id, $fecha);
            
            if ($saldo != 0) {
                $item = [
                    'codigo' => $cuenta->codigo,
                    'nombre' => $cuenta->nombre,
                    'saldo' => $saldo
                ];

                switch ($cuenta->tipo) {
                    case 'ACTIVO':
                        $resultado['activos'][] = $item;
                        $resultado['total_activos'] += $saldo;
                        break;
                    case 'PASIVO':
                        $resultado['pasivos'][] = $item;
                        $resultado['total_pasivos'] += $saldo;
                        break;
                    case 'PATRIMONIO':
                        $resultado['patrimonio'][] = $item;
                        $resultado['total_patrimonio'] += $saldo;
                        break;
                }
            }
        }

        return $resultado;
    }

    /**
     * Genera el estado de resultados
     */
    public function generarEstadoResultados($fechaInicio, $fechaFin)
    {
        $fechaInicio = Carbon::parse($fechaInicio);
        $fechaFin = Carbon::parse($fechaFin);

        $cuentas = CuentaContable::whereIn('tipo', ['INGRESO', 'EGRESO'])
            ->orderBy('codigo')
            ->get();

        $resultado = [
            'ingresos' => [],
            'egresos' => [],
            'total_ingresos' => 0,
            'total_egresos' => 0,
            'utilidad' => 0
        ];

        foreach ($cuentas as $cuenta) {
            $saldo = $this->calcularSaldoCuenta($cuenta->id, $fechaFin, $fechaInicio);
            
            if ($saldo != 0) {
                $item = [
                    'codigo' => $cuenta->codigo,
                    'nombre' => $cuenta->nombre,
                    'saldo' => $saldo
                ];

                if ($cuenta->tipo === 'INGRESO') {
                    $resultado['ingresos'][] = $item;
                    $resultado['total_ingresos'] += $saldo;
                } else {
                    $resultado['egresos'][] = $item;
                    $resultado['total_egresos'] += $saldo;
                }
            }
        }

        $resultado['utilidad'] = $resultado['total_ingresos'] - $resultado['total_egresos'];

        return $resultado;
    }

    /**
     * Genera el libro mayor
     */
    public function generarLibroMayor($cuentaId, $fechaInicio = null, $fechaFin = null)
    {
        $fechaInicio = $fechaInicio ? Carbon::parse($fechaInicio) : Carbon::now()->startOfMonth();
        $fechaFin = $fechaFin ? Carbon::parse($fechaFin) : Carbon::now();

        $cuenta = CuentaContable::findOrFail($cuentaId);
        
        $movimientos = DetalleAsiento::where('cuenta_id', $cuentaId)
            ->whereHas('asiento', function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
            })
            ->with(['asiento'])
            ->orderBy('created_at')
            ->get();

        $resultado = [
            'cuenta' => [
                'codigo' => $cuenta->codigo,
                'nombre' => $cuenta->nombre,
                'tipo' => $cuenta->tipo,
                'naturaleza' => $cuenta->naturaleza
            ],
            'movimientos' => [],
            'saldo_inicial' => $this->calcularSaldoCuenta($cuentaId, $fechaInicio->copy()->subDay()),
            'saldo_final' => $this->calcularSaldoCuenta($cuentaId, $fechaFin),
            'total_debe' => 0,
            'total_haber' => 0
        ];

        $saldo = $resultado['saldo_inicial'];

        foreach ($movimientos as $movimiento) {
            $saldo += ($movimiento->debe - $movimiento->haber);
            
            $resultado['movimientos'][] = [
                'fecha' => $movimiento->asiento->fecha,
                'numero_asiento' => $movimiento->asiento->numero_asiento,
                'concepto' => $movimiento->descripcion,
                'debe' => $movimiento->debe,
                'haber' => $movimiento->haber,
                'saldo' => $saldo
            ];

            $resultado['total_debe'] += $movimiento->debe;
            $resultado['total_haber'] += $movimiento->haber;
        }

        return $resultado;
    }

    /**
     * Genera el libro diario
     */
    public function generarLibroDiario($fechaInicio = null, $fechaFin = null)
    {
        $fechaInicio = $fechaInicio ? Carbon::parse($fechaInicio) : Carbon::now()->startOfMonth();
        $fechaFin = $fechaFin ? Carbon::parse($fechaFin) : Carbon::now();

        $asientos = AsientoContable::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->with(['detalles.cuenta'])
            ->orderBy('fecha')
            ->orderBy('numero_asiento')
            ->get();

        $resultado = [
            'periodo' => [
                'inicio' => $fechaInicio->format('Y-m-d'),
                'fin' => $fechaFin->format('Y-m-d')
            ],
            'asientos' => []
        ];

        foreach ($asientos as $asiento) {
            $asientoData = [
                'fecha' => $asiento->fecha,
                'numero_asiento' => $asiento->numero_asiento,
                'concepto' => $asiento->concepto,
                'detalles' => []
            ];

            foreach ($asiento->detalles as $detalle) {
                $asientoData['detalles'][] = [
                    'cuenta' => [
                        'codigo' => $detalle->cuenta->codigo,
                        'nombre' => $detalle->cuenta->nombre
                    ],
                    'debe' => $detalle->debe,
                    'haber' => $detalle->haber,
                    'descripcion' => $detalle->descripcion
                ];
            }

            $resultado['asientos'][] = $asientoData;
        }

        return $resultado;
    }

    /**
     * Genera el balance de comprobación
     */
    public function generarBalanceComprobacion($fecha = null)
    {
        $fecha = $fecha ? Carbon::parse($fecha) : Carbon::now();

        $cuentas = CuentaContable::orderBy('codigo')->get();

        $resultado = [
            'fecha' => $fecha->format('Y-m-d'),
            'cuentas' => [],
            'total_debe' => 0,
            'total_haber' => 0
        ];

        foreach ($cuentas as $cuenta) {
            $saldo = $this->calcularSaldoCuenta($cuenta->id, $fecha);
            
            if ($saldo != 0) {
                $item = [
                    'codigo' => $cuenta->codigo,
                    'nombre' => $cuenta->nombre,
                    'debe' => $saldo > 0 ? $saldo : 0,
                    'haber' => $saldo < 0 ? abs($saldo) : 0
                ];

                $resultado['cuentas'][] = $item;
                $resultado['total_debe'] += $item['debe'];
                $resultado['total_haber'] += $item['haber'];
            }
        }

        return $resultado;
    }

    /**
     * Calcula el saldo de una cuenta hasta una fecha específica
     */
    private function calcularSaldoCuenta($cuentaId, $fecha, $fechaInicio = null)
    {
        $query = DetalleAsiento::where('cuenta_id', $cuentaId)
            ->whereHas('asiento', function ($query) use ($fecha) {
                $query->where('fecha', '<=', $fecha);
            });

        if ($fechaInicio) {
            $query->whereHas('asiento', function ($query) use ($fechaInicio) {
                $query->where('fecha', '>=', $fechaInicio);
            });
        }

        $cuenta = CuentaContable::find($cuentaId);
        $debe = $query->sum('debe');
        $haber = $query->sum('haber');

        return $cuenta->naturaleza === 'DEUDORA' ? ($debe - $haber) : ($haber - $debe);
    }
} 