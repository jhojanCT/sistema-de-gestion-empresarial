<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\AsientoContable;
use App\Models\CierreDiario;
use App\Models\CuentaContable;
use App\Models\DetalleAsiento;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\PagoCliente;
use App\Models\PagoProveedor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CierreDiarioExport;

class CierreDiarioController extends Controller
{
    public function index()
    {
        $query = CierreDiario::with('usuario');
        
        if (request('fecha_inicio')) {
            $query->whereDate('fecha', '>=', request('fecha_inicio'));
        }
        
        if (request('fecha_fin')) {
            $query->whereDate('fecha', '<=', request('fecha_fin'));
        }
        
        $cierres = $query->orderBy('fecha', 'desc')->paginate(10);
        
        return view('contabilidad.cierres.diario.index', compact('cierres'));
    }

    public function create()
    {
        // Obtener el último cierre diario para determinar el saldo inicial
        $ultimoCierre = CierreDiario::orderBy('fecha', 'desc')->first();
        $fecha = $ultimoCierre ? Carbon::parse($ultimoCierre->fecha)->addDay() : Carbon::today();

        // Si ya existe un cierre para la fecha actual, redirigir
        if (CierreDiario::whereDate('fecha', $fecha)->exists()) {
            return redirect()->route('contabilidad.cierres.diario.index')
                ->with('error', 'Ya existe un cierre para la fecha seleccionada.');
        }

        // Calcular totales del día
        $totales = $this->calcularTotales($fecha);
        $saldoInicial = $ultimoCierre ? $ultimoCierre->saldo_final : 0;

        return view('contabilidad.cierres.diario.create', compact('fecha', 'totales', 'saldoInicial'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'saldo_inicial' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Verificar si ya existe un cierre para la fecha
            if (CierreDiario::whereDate('fecha', $request->fecha)->exists()) {
                throw new \Exception('Ya existe un cierre para la fecha seleccionada.');
            }

            // Calcular totales
            $totales = $this->calcularTotales($request->fecha);
            $saldoFinal = $request->saldo_inicial + 
                         $totales['ventas_contado'] + 
                         $totales['cobros_credito'] + 
                         $totales['otros_ingresos'] - 
                         $totales['compras_contado'] - 
                         $totales['pagos_credito'] - 
                         $totales['gastos'];

            // Crear el cierre diario
            $cierre = CierreDiario::create([
                'usuario_id' => Auth::id(),
                'fecha' => $request->fecha,
                'ventas_contado' => $totales['ventas_contado'],
                'ventas_credito' => $totales['ventas_credito'],
                'compras_contado' => $totales['compras_contado'],
                'compras_credito' => $totales['compras_credito'],
                'cobros_credito' => $totales['cobros_credito'],
                'pagos_credito' => $totales['pagos_credito'],
                'gastos' => $totales['gastos'],
                'otros_ingresos' => $totales['otros_ingresos'],
                'saldo_inicial' => $request->saldo_inicial,
                'saldo_final' => $saldoFinal,
                'diferencia' => 0, // Se actualizará al cerrar
                'observaciones' => $request->observaciones,
                'cerrado' => false,
                'iva_ventas_contado' => $totales['iva_ventas_contado'],
                'iva_ventas_credito' => $totales['iva_ventas_credito'],
                'iva_compras_contado' => $totales['iva_compras_contado'],
                'iva_compras_credito' => $totales['iva_compras_credito']
            ]);

            // Asociar ventas y compras del día al cierre
            Venta::whereDate('fecha', $request->fecha)->update(['cierre_diario_id' => $cierre->id]);
            Compra::whereDate('fecha', $request->fecha)->update(['cierre_diario_id' => $cierre->id]);
            PagoCliente::whereDate('fecha_pago', $request->fecha)->update(['cierre_diario_id' => $cierre->id]);
            PagoProveedor::whereDate('fecha_pago', $request->fecha)->update(['cierre_diario_id' => $cierre->id]);

            // Generar asiento contable del día
            $this->generarAsientoDiario($cierre);

            DB::commit();

            return redirect()->route('contabilidad.cierres.diario.index')
                ->with('success', 'Cierre diario registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el cierre diario: ' . $e->getMessage());
        }
    }

    public function show(CierreDiario $cierre)
    {
        try {
            $cierre->load(['usuario', 'asientos.detalles.cuenta', 'ventas', 'compras', 'pagosClientes', 'pagosProveedores']);
            return view('contabilidad.cierres.diario.show', compact('cierre'));
        } catch (\Exception $e) {
            Log::error('Error al cargar el cierre diario: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el cierre diario: ' . $e->getMessage());
        }
    }

    public function cerrar(CierreDiario $cierre, Request $request)
    {
        $request->validate([
            'saldo_real' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            if ($cierre->cerrado) {
                throw new \Exception('Este cierre ya está cerrado.');
            }

            // Calcular diferencia
            $diferencia = $request->saldo_real - $cierre->saldo_final;

            $cierre->update([
                'diferencia' => $diferencia,
                'observaciones' => $request->observaciones ? 
                    $cierre->observaciones . "\n" . $request->observaciones : 
                    $cierre->observaciones,
                'cerrado' => true,
                'fecha_cierre' => now()
            ]);

            // Si hay diferencia, generar asiento de ajuste
            if (abs($diferencia) > 0.01) {
                $this->generarAsientoAjuste($cierre, $diferencia);
            }

            DB::commit();

            return redirect()->route('contabilidad.cierres.diario.index')
                ->with('success', 'Cierre diario cerrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cerrar el día: ' . $e->getMessage());
        }
    }

    private function calcularTotales($fecha)
    {
        $totales = [
            'ventas_contado' => 0,
            'ventas_credito' => 0,
            'compras_contado' => 0,
            'compras_credito' => 0,
            'cobros_credito' => 0,
            'pagos_credito' => 0,
            'gastos' => 0,
            'otros_ingresos' => 0,
            'iva_ventas_contado' => 0,
            'iva_ventas_credito' => 0,
            'iva_compras_contado' => 0,
            'iva_compras_credito' => 0,
            'cantidad_ventas_contado' => 0,
            'cantidad_ventas_credito' => 0,
            'cantidad_compras_contado' => 0,
            'cantidad_compras_credito' => 0,
            'cantidad_cobros' => 0,
            'cantidad_pagos' => 0,
            'cantidad_gastos' => 0,
            'cantidad_otros_ingresos' => 0
        ];

        // Calcular ventas del día
        $ventas = Venta::whereDate('fecha', $fecha)
            ->where(function($query) {
                $query->where('tipo', 'contado')
                      ->orWhere(function($q) {
                          $q->where('tipo', 'credito')
                            ->where('pagada', false);
                      });
            })
            ->with('cliente')
            ->get();
        
        foreach ($ventas as $venta) {
            if ($venta->tipo === 'contado') {
                $totales['ventas_contado'] += $venta->total;
                $totales['iva_ventas_contado'] += $venta->iva_amount;
                $totales['cantidad_ventas_contado']++;
            } else {
                $totales['ventas_credito'] += $venta->total;
                $totales['iva_ventas_credito'] += $venta->iva_amount;
                $totales['cantidad_ventas_credito']++;
            }
        }

        // Calcular compras del día
        $compras = Compra::whereDate('fecha', $fecha)
            ->with('proveedor')
            ->get();
        
        foreach ($compras as $compra) {
            if ($compra->tipo === 'contado') {
                $totales['compras_contado'] += $compra->total;
                $totales['iva_compras_contado'] += $compra->iva_amount;
                $totales['cantidad_compras_contado']++;
            } else {
                $totales['compras_credito'] += $compra->total;
                $totales['iva_compras_credito'] += $compra->iva_amount;
                $totales['cantidad_compras_credito']++;
            }
        }

        // Calcular pagos y cobros (solo de créditos)
        $pagosClientes = PagoCliente::whereDate('fecha_pago', $fecha)
            ->whereHas('venta', function($query) {
                $query->where('tipo', 'credito');
            })
            ->with('cliente')
            ->get();
        
        foreach ($pagosClientes as $pago) {
            $totales['cobros_credito'] += $pago->monto;
            $totales['cantidad_cobros']++;
        }

        $pagosProveedores = PagoProveedor::whereDate('fecha_pago', $fecha)
            ->whereHas('compra', function($query) {
                $query->where('tipo', 'credito');
            })
            ->with('proveedor')
            ->get();
        
        foreach ($pagosProveedores as $pago) {
            $totales['pagos_credito'] += $pago->monto;
            $totales['cantidad_pagos']++;
        }

        // Calcular gastos y otros ingresos
        $asientos = AsientoContable::whereDate('fecha', $fecha)
            ->where('estado', 'aprobado')
            ->with(['detalles.cuenta', 'usuario'])
            ->get();

        foreach ($asientos as $asiento) {
            foreach ($asiento->detalles as $detalle) {
                $cuenta = $detalle->cuenta;
                if ($cuenta->tipo === 'GASTO') {
                    $totales['gastos'] += $detalle->debe - $detalle->haber;
                    $totales['cantidad_gastos']++;
                } elseif ($cuenta->tipo === 'OTRO_INGRESO') {
                    $totales['otros_ingresos'] += $detalle->haber - $detalle->debe;
                    $totales['cantidad_otros_ingresos']++;
                }
            }
        }

        return $totales;
    }

    private function generarAsientoDiario(CierreDiario $cierre)
    {
        // 1. Obtener todas las transacciones del día
        $ventas = Venta::whereDate('fecha', $cierre->fecha)->get();
        $compras = Compra::whereDate('fecha', $cierre->fecha)->get();
        $pagosClientes = PagoCliente::whereDate('fecha_pago', $cierre->fecha)->get();
        $pagosProveedores = PagoProveedor::whereDate('fecha_pago', $cierre->fecha)->get();
        $asientos = AsientoContable::whereDate('fecha', $cierre->fecha)
            ->where('estado', 'aprobado')
            ->get();

        // 2. Inicializar array para agrupar movimientos por cuenta
        $movimientos = [];

        // 3. Procesar ventas
        foreach ($ventas as $venta) {
            // Caja/Bancos (ventas al contado)
            if ($venta->tipo_pago === 'contado') {
                $cuentaCodigo = $venta->pagos->first()?->metodo_pago === 'transferencia' ? '1.1.1.02' : '1.1.1.01';
                $this->agregarMovimiento($movimientos, $cuentaCodigo, $venta->total, 0, "Ingreso por venta al contado #{$venta->id}");
            }
            // Cuentas por cobrar (ventas a crédito)
            if ($venta->tipo_pago === 'credito') {
                $this->agregarMovimiento($movimientos, '1.1.2.01', $venta->total, 0, "Venta a crédito #{$venta->id}");
            }
            // Ventas (siempre en haber)
            $this->agregarMovimiento($movimientos, '4.1.1.01', 0, $venta->subtotal, "Venta de productos #{$venta->id}");
            // IVA Débito Fiscal (siempre en haber)
            if ($venta->has_invoice && $venta->iva_amount > 0) {
                $this->agregarMovimiento($movimientos, '2.1.2.02', 0, $venta->iva_amount, "IVA Débito Fiscal venta #{$venta->id}");
            }
            // Costo de Ventas (debe)
            $costoTotal = 0;
            foreach ($venta->items as $item) {
                if ($item->tipo_item === 'producto') {
                    $costoTotal += $item->cantidad * $item->producto->costo_promedio;
                } else {
                    $costoTotal += $item->cantidad * $item->materiaPrimaFiltrada->costo_promedio;
                }
            }
            if ($costoTotal > 0) {
                $this->agregarMovimiento($movimientos, '5.1.1.01', $costoTotal, 0, "Costo de venta #{$venta->id}");
                $this->agregarMovimiento($movimientos, '1.1.3.03', 0, $costoTotal, "Salida de inventario por venta #{$venta->id}");
            }
        }

        // 4. Procesar compras
        foreach ($compras as $compra) {
            // Inventario de Materia Prima (debe)
            $this->agregarMovimiento($movimientos, '1.1.3.01', $compra->subtotal, 0, "Compra de materia prima #{$compra->id}");
            // IVA Crédito Fiscal (debe)
            if ($compra->has_invoice && $compra->iva_amount > 0) {
                $this->agregarMovimiento($movimientos, '2.1.2.01', $compra->iva_amount, 0, "IVA Crédito Fiscal compra #{$compra->id}");
            }
            // Proveedores (haber)
            $this->agregarMovimiento($movimientos, '2.1.1.01', 0, $compra->total, "Compra a proveedor #{$compra->id}");
        }

        // 5. Procesar pagos de clientes
        foreach ($pagosClientes as $pago) {
            // Caja/Bancos (debe)
            $cuentaCodigo = $pago->metodo_pago === 'transferencia' ? '1.1.1.02' : '1.1.1.01';
            $this->agregarMovimiento($movimientos, $cuentaCodigo, $pago->monto, 0, "Cobro de cliente #{$pago->id}");
            // Cuentas por cobrar (haber)
            $this->agregarMovimiento($movimientos, '1.1.2.01', 0, $pago->monto, "Cobro de venta #{$pago->venta_id}");
        }

        // 6. Procesar pagos a proveedores
        foreach ($pagosProveedores as $pago) {
            // Caja/Bancos (haber)
            $cuentaCodigo = $pago->metodo_pago === 'transferencia' ? '1.1.1.02' : '1.1.1.01';
            $this->agregarMovimiento($movimientos, $cuentaCodigo, 0, $pago->monto, "Pago a proveedor #{$pago->id}");
            // Cuentas por pagar (debe)
            $this->agregarMovimiento($movimientos, '2.1.1.01', $pago->monto, 0, "Pago de compra #{$pago->compra_id}");
        }

        // 7. Procesar otros asientos (gastos, ingresos, etc.)
        foreach ($asientos as $asiento) {
            foreach ($asiento->detalles as $detalle) {
                $cuenta = $detalle->cuenta;
                if ($cuenta->tipo === 'GASTO') {
                    $this->agregarMovimiento($movimientos, $cuenta->codigo, $detalle->debe, $detalle->haber, "Gasto: {$asiento->concepto}");
                } elseif ($cuenta->tipo === 'OTRO_INGRESO') {
                    $this->agregarMovimiento($movimientos, $cuenta->codigo, $detalle->debe, $detalle->haber, "Ingreso: {$asiento->concepto}");
                }
            }
        }

        // 8. Crear el asiento contable de cierre diario
        $asiento = AsientoContable::create([
            'fecha' => $cierre->fecha,
            'numero_asiento' => 'CD-' . str_pad($cierre->id, 6, '0', STR_PAD_LEFT),
            'tipo_documento' => 'CIERRE_DIARIO',
            'numero_documento' => $cierre->id,
            'concepto' => 'Cierre diario de operaciones',
            'estado' => 'aprobado',
            'user_id' => Auth::id(),
            'cierre_diario_id' => $cierre->id
        ]);

        // 9. Crear los detalles del asiento agrupados
        foreach ($movimientos as $codigo => $mov) {
            $cuenta = CuentaContable::where('codigo', $codigo)->first();
            if ($cuenta && ($mov['debe'] > 0 || $mov['haber'] > 0)) {
                DetalleAsiento::create([
                    'asiento_id' => $asiento->id,
                    'cuenta_id' => $cuenta->id,
                    'debe' => $mov['debe'],
                    'haber' => $mov['haber'],
                    'descripcion' => $mov['descripcion']
                ]);
            }
        }
    }

    // Método auxiliar para agrupar movimientos
    private function agregarMovimiento(&$movimientos, $codigo, $debe, $haber, $descripcion)
    {
        if (!isset($movimientos[$codigo])) {
            $movimientos[$codigo] = ['debe' => 0, 'haber' => 0, 'descripcion' => $descripcion];
        }
        $movimientos[$codigo]['debe'] += $debe;
        $movimientos[$codigo]['haber'] += $haber;
    }

    private function generarAsientoAjuste(CierreDiario $cierre, $diferencia)
    {
        // Crear asiento de ajuste
        $asiento = AsientoContable::create([
            'fecha' => $cierre->fecha,
            'numero_asiento' => 'CD-' . str_pad($cierre->id, 6, '0', STR_PAD_LEFT) . '-A',
            'tipo_documento' => 'AJUSTE_DIARIO',
            'numero_documento' => $cierre->id,
            'concepto' => 'Ajuste por diferencia en cierre diario',
            'estado' => 'aprobado',
            'user_id' => Auth::id(),
            'cierre_diario_id' => $cierre->id
        ]);

        // Obtener cuentas necesarias
        $cuentaCaja = CuentaContable::where('codigo', '1.1.1.01')->first(); // Caja
        $cuentaAjuste = CuentaContable::where('codigo', '5.2.1.01')->first(); // Cuenta de Ajustes

        // Registrar el ajuste
        if ($diferencia > 0) {
            // Sobrante
            $asiento->detalles()->create([
                'cuenta_id' => $cuentaCaja->id,
                'debe' => $diferencia,
                'haber' => 0,
                'descripcion' => 'Sobrante en caja'
            ]);
            $asiento->detalles()->create([
                'cuenta_id' => $cuentaAjuste->id,
                'debe' => 0,
                'haber' => $diferencia,
                'descripcion' => 'Sobrante en caja'
            ]);
        } else {
            // Faltante
            $asiento->detalles()->create([
                'cuenta_id' => $cuentaAjuste->id,
                'debe' => abs($diferencia),
                'haber' => 0,
                'descripcion' => 'Faltante en caja'
            ]);
            $asiento->detalles()->create([
                'cuenta_id' => $cuentaCaja->id,
                'debe' => 0,
                'haber' => abs($diferencia),
                'descripcion' => 'Faltante en caja'
            ]);
        }
    }

    public function exportExcel(CierreDiario $cierre)
    {
        return Excel::download(new CierreDiarioExport($cierre->id), 'cierre_diario_'.$cierre->fecha->format('Ymd').'.xlsx');
    }
} 