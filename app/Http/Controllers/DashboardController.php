<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\CierreDiario;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();

        // Obtener el último cierre diario para el saldo inicial
        $ultimoCierre = CierreDiario::orderBy('fecha', 'desc')->first();
        $saldoInicial = $ultimoCierre ? $ultimoCierre->saldo_final : 0;

        // Ventas del día
        $ventasContadoHoy = Venta::whereDate('fecha', $hoy)
            ->where('tipo', 'contado')
            ->sum('total');

        $ventasCreditoHoy = Venta::whereDate('fecha', $hoy)
            ->where('tipo', 'credito')
            ->sum('total');

        // Compras del día
        $comprasContadoHoy = Compra::whereDate('fecha', $hoy)
            ->where('tipo', 'contado')
            ->sum('total');

        $comprasCreditoHoy = Compra::whereDate('fecha', $hoy)
            ->where('tipo', 'credito')
            ->sum('total');

        // Calcular saldo actual
        $saldoActual = $saldoInicial
            + $ventasContadoHoy
            - $comprasContadoHoy;

        // Estadísticas generales
        $productosStock = Producto::sum('stock');
        $clientesActivos = Cliente::count();
        $productosStockBajo = Producto::where('stock', '<=', 10)->count();

        // Datos para el gráfico de ventas
        $ventasMensuales = Venta::selectRaw('MONTH(fecha) as mes, SUM(total) as total')
            ->whereYear('fecha', Carbon::now()->year)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Últimos 5 cierres
        $ultimosCierres = CierreDiario::with('usuario')
            ->latest('fecha')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'saldoInicial',
            'saldoActual',
            'ventasContadoHoy',
            'ventasCreditoHoy',
            'comprasContadoHoy',
            'comprasCreditoHoy',
            'productosStock',
            'productosStockBajo',
            'clientesActivos',
            'ventasMensuales',
            'ultimosCierres'
        ));
    }
}