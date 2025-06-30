<?php

namespace App\Http\Controllers;

use App\Services\ReporteContableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ReporteContableController extends Controller
{
    protected $reporteService;

    public function __construct(ReporteContableService $reporteService)
    {
        $this->reporteService = $reporteService;
    }

    /**
     * Muestra el balance general
     */
    public function balanceGeneral(Request $request)
    {
        $fecha = $request->get('fecha');
        $datos = $this->reporteService->generarBalanceGeneral($fecha);

        if ($request->ajax()) {
            return response()->json($datos);
        }

        return view('contabilidad.reportes.balance-general', compact('datos'));
    }

    /**
     * Muestra el estado de resultados
     */
    public function estadoResultados(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');
        $datos = $this->reporteService->generarEstadoResultados($fechaInicio, $fechaFin);

        if ($request->ajax()) {
            return response()->json($datos);
        }

        return view('contabilidad.reportes.estado-resultados', compact('datos'));
    }

    /**
     * Muestra el libro mayor
     */
    public function libroMayor(Request $request)
    {
        $cuentaId = $request->get('cuenta_id');
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');
        $datos = $this->reporteService->generarLibroMayor($cuentaId, $fechaInicio, $fechaFin);

        if ($request->ajax()) {
            return response()->json($datos);
        }

        return view('contabilidad.reportes.libro-mayor', compact('datos'));
    }

    /**
     * Muestra el libro diario
     */
    public function libroDiario(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');
        $datos = $this->reporteService->generarLibroDiario($fechaInicio, $fechaFin);

        if ($request->ajax()) {
            return response()->json($datos);
        }

        return view('contabilidad.reportes.libro-diario', compact('datos'));
    }

    /**
     * Muestra el balance de comprobación
     */
    public function balanceComprobacion(Request $request)
    {
        $fecha = $request->get('fecha');
        $datos = $this->reporteService->generarBalanceComprobacion($fecha);

        if ($request->ajax()) {
            return response()->json($datos);
        }

        return view('contabilidad.reportes.balance-comprobacion', compact('datos'));
    }
} 