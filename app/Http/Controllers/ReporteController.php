<?php

namespace App\Http\Controllers;

use App\Models\MateriaPrimaSinFiltrar;
use App\Models\MateriaPrimaFiltrada;
use App\Models\Producto;
use App\Models\Filtrado;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Produccion;
use App\Models\CierreDiario;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function inventario()
    {
        $materiasPrimasSinFiltrar = MateriaPrimaSinFiltrar::with('compras')->get();
        $materiasPrimasFiltradas = MateriaPrimaFiltrada::all(); // Eliminado with('filtrados')
        $productos = Producto::with(['producciones', 'compras'])->get();
        
        return view('reportes.inventario', compact(
            'materiasPrimasSinFiltrar',
            'materiasPrimasFiltradas',
            'productos'
        ));
    }

    public function desperdicio(Request $request)
    {
        $query = Filtrado::with('materiaPrimaSinFiltrar');
        
        if ($request->fecha_inicio) {
            $query->where('fecha', '>=', $request->fecha_inicio);
        }
        
        if ($request->fecha_fin) {
            $query->where('fecha', '<=', $request->fecha_fin);
        }
        
        $filtrados = $query->orderBy('fecha', 'desc')->get();
        
        $totalEntrada = $filtrados->sum('cantidad_entrada');
        $totalSalida = $filtrados->sum('cantidad_salida');
        $totalDesperdicio = $totalEntrada - $totalSalida;
        $porcentajeDesperdicio = $totalEntrada > 0 ? ($totalDesperdicio / $totalEntrada) * 100 : 0;
        
        return view('reportes.desperdicio', compact(
            'filtrados',
            'totalEntrada',
            'totalSalida',
            'totalDesperdicio',
            'porcentajeDesperdicio'
        ));
    }

    public function produccion(Request $request)
    {
        $query = Produccion::with(['materiaPrimaFiltrada', 'producto']);
        
        if ($request->fecha_inicio) {
            $query->where('fecha', '>=', $request->fecha_inicio);
        }
        
        if ($request->fecha_fin) {
            $query->where('fecha', '<=', $request->fecha_fin);
        }
        
        if ($request->producto_id) {
            $query->where('producto_id', $request->producto_id);
        }
        
        $producciones = $query->orderBy('fecha', 'desc')->get();
        
        $totalMPUtilizada = $producciones->sum('cantidad_utilizada');
        $totalProductos = $producciones->sum('cantidad_producida');
        $totalCosto = $producciones->sum('costo_produccion');
        $costoPromedio = $totalProductos > 0 ? $totalCosto / $totalProductos : 0;
        
        $productos = Producto::where('tipo', 'producido')->get();
        
        return view('reportes.produccion', compact(
            'producciones',
            'totalMPUtilizada',
            'totalProductos',
            'totalCosto',
            'costoPromedio',
            'productos'
        ));
    }

    public function ventas(Request $request)
    {
        $query = Venta::with(['cliente', 'items.producto']);
        
        if ($request->fecha_inicio) {
            $query->where('fecha', '>=', $request->fecha_inicio);
        }
        
        if ($request->fecha_fin) {
            $query->where('fecha', '<=', $request->fecha_fin);
        }
        
        if ($request->tipo) {
            $query->where('tipo', $request->tipo);
        }
        
        $ventas = $query->orderBy('fecha', 'desc')->get();
        
        $totalVentas = $ventas->sum('total');
        $ventasContado = $ventas->where('tipo', 'contado')->sum('total');
        $ventasCredito = $ventas->where('tipo', 'credito')->sum('total');
        
        return view('reportes.ventas', compact(
            'ventas',
            'totalVentas',
            'ventasContado',
            'ventasCredito'
        ));
    }

    public function compras(Request $request)
    {
        $query = Compra::with(['proveedor', 'items.materiaPrima']);
        
        if ($request->fecha_inicio) {
            $query->where('fecha', '>=', $request->fecha_inicio);
        }
        
        if ($request->fecha_fin) {
            $query->where('fecha', '<=', $request->fecha_fin);
        }
        
        if ($request->tipo) {
            $query->where('tipo', $request->tipo);
        }
        
        $compras = $query->orderBy('fecha', 'desc')->get();
        
        $totalCompras = $compras->sum('total');
        $comprasContado = $compras->where('tipo', 'contado')->sum('total');
        $comprasCredito = $compras->where('tipo', 'credito')->sum('total');
        
        return view('reportes.compras', compact(
            'compras',
            'totalCompras',
            'comprasContado',
            'comprasCredito'
        ));
    }

    public function flujoCaja(Request $request)
    {
        // Validar fechas
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio'
        ]);

        // Obtener fechas del request o usar valores por defecto
        $fechaInicio = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio) : Carbon::now()->startOfMonth();
        $fechaFin = $request->fecha_fin ? Carbon::parse($request->fecha_fin) : Carbon::now();

        // Determinar el tipo de vista (día, semana, mes)
        $vista = $request->vista ?? 'dia';

        // Consulta base
        $query = CierreDiario::whereBetween('fecha', [$fechaInicio, $fechaFin]);

        // Agrupar según la vista seleccionada
        switch ($vista) {
            case 'semana':
                $cierres = $query->select(
                    DB::raw('DATE_FORMAT(fecha, "%Y-%v") as periodo'),
                    DB::raw('MIN(fecha) as fecha'),
                    DB::raw('SUM(ventas_contado) as ventas_contado'),
                    DB::raw('SUM(pagos_clientes) as pagos_clientes'),
                    DB::raw('SUM(ingresos_bancarios) as ingresos_bancarios'),
                    DB::raw('SUM(compras_contado) as compras_contado'),
                    DB::raw('SUM(pagos_proveedores) as pagos_proveedores'),
                    DB::raw('SUM(egresos_bancarios) as egresos_bancarios'),
                    DB::raw('MAX(saldo_final) as saldo_final')
                )
                ->groupBy('periodo')
                ->get();
                break;

            case 'mes':
                $cierres = $query->select(
                    DB::raw('DATE_FORMAT(fecha, "%Y-%m") as periodo'),
                    DB::raw('MIN(fecha) as fecha'),
                    DB::raw('SUM(ventas_contado) as ventas_contado'),
                    DB::raw('SUM(pagos_clientes) as pagos_clientes'),
                    DB::raw('SUM(ingresos_bancarios) as ingresos_bancarios'),
                    DB::raw('SUM(compras_contado) as compras_contado'),
                    DB::raw('SUM(pagos_proveedores) as pagos_proveedores'),
                    DB::raw('SUM(egresos_bancarios) as egresos_bancarios'),
                    DB::raw('MAX(saldo_final) as saldo_final')
                )
                ->groupBy('periodo')
                ->get();
                break;

            default: // día
                $cierres = $query->orderBy('fecha')->get();
                break;
        }

        // Calcular estadísticas adicionales
        $stats = [
            'total_ingresos' => $cierres->sum(function($cierre) {
                return $cierre->ventas_contado + $cierre->pagos_clientes + $cierre->ingresos_bancarios;
            }),
            'total_egresos' => $cierres->sum(function($cierre) {
                return $cierre->compras_contado + $cierre->pagos_proveedores + $cierre->egresos_bancarios;
            }),
            'flujo_neto' => $cierres->sum(function($cierre) {
                return ($cierre->ventas_contado + $cierre->pagos_clientes + $cierre->ingresos_bancarios) -
                       ($cierre->compras_contado + $cierre->pagos_proveedores + $cierre->egresos_bancarios);
            }),
            'promedio_diario_ingresos' => $cierres->avg(function($cierre) {
                return $cierre->ventas_contado + $cierre->pagos_clientes + $cierre->ingresos_bancarios;
            }),
            'promedio_diario_egresos' => $cierres->avg(function($cierre) {
                return $cierre->compras_contado + $cierre->pagos_proveedores + $cierre->egresos_bancarios;
            }),
            'dia_mayor_ingreso' => $cierres->sortByDesc(function($cierre) {
                return $cierre->ventas_contado + $cierre->pagos_clientes + $cierre->ingresos_bancarios;
            })->first(),
            'dia_mayor_egreso' => $cierres->sortByDesc(function($cierre) {
                return $cierre->compras_contado + $cierre->pagos_proveedores + $cierre->egresos_bancarios;
            })->first()
        ];

        return view('cierre.reporte-flujo-caja', compact('cierres', 'stats', 'vista'));
    }
}