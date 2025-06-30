<?php

namespace App\Http\Controllers;

use App\Models\PagoCliente;
use App\Models\PagoProveedor;
use App\Models\Venta;
use App\Models\Compra;
use App\Services\ContabilidadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CierreDiario;

class PagoController extends Controller
{
    protected $contabilidadService;

    public function __construct(ContabilidadService $contabilidadService)
    {
        $this->contabilidadService = $contabilidadService;
    }

    // Pagos a clientes (para ventas a crédito)
    public function indexClientes()
    {
        $pagos = PagoCliente::with(['venta', 'venta.cliente'])->get();
        return view('pagos.clientes.index', compact('pagos'));
    }

    public function createCliente(Venta $venta)
    {
        return view('pagos.clientes.create', compact('venta'));
    }

    public function storeCliente(Request $request, Venta $venta)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01|max:' . $venta->saldoPendiente(),
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|string',
            'comprobante' => 'nullable|string',
            'auto_asiento' => 'boolean'
        ]);

        DB::transaction(function () use ($request, $venta) {
            // Buscar el cierre diario correspondiente a la fecha de pago
            $cierreDiario = CierreDiario::whereDate('fecha', $request->fecha_pago)->first();

            $pago = PagoCliente::create([
                'venta_id' => $venta->id,
                'monto' => $request->monto,
                'fecha_pago' => $request->fecha_pago,
                'metodo_pago' => $request->metodo_pago,
                'comprobante' => $request->comprobante,
                'cierre_diario_id' => $cierreDiario ? $cierreDiario->id : null,
                'auto_asiento' => $request->boolean('auto_asiento')
            ]);

            if ($venta->saldoPendiente() <= 0) {
                $venta->update(['pagada' => true]);
            }

            // Generar asiento contable para el cobro solo si auto_asiento es true
            if ($pago->auto_asiento) {
                $this->contabilidadService->generarAsientoCobroCliente($pago);
            }
        });

        return redirect()->route('ventas.show', $venta)->with('success', 'Pago registrado correctamente');
    }

    // Pagos a proveedores (para compras a crédito)
    public function indexProveedores()
    {
        $pagos = PagoProveedor::with(['compra', 'compra.proveedor'])->get();
        return view('pagos.proveedores.index', compact('pagos'));
    }

    public function createProveedor(Compra $compra)
    {
        return view('pagos.proveedores.create', compact('compra'));
    }

    public function storeProveedor(Request $request, Compra $compra)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01|max:' . $compra->saldoPendiente(),
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|string',
            'comprobante' => 'nullable|string',
            'auto_asiento' => 'boolean'
        ]);

        DB::transaction(function () use ($request, $compra) {
            // Buscar el cierre diario correspondiente a la fecha de pago
            $cierreDiario = CierreDiario::whereDate('fecha', $request->fecha_pago)->first();

            $pago = PagoProveedor::create([
                'compra_id' => $compra->id,
                'monto' => $request->monto,
                'fecha_pago' => $request->fecha_pago,
                'metodo_pago' => $request->metodo_pago,
                'comprobante' => $request->comprobante,
                'cierre_diario_id' => $cierreDiario ? $cierreDiario->id : null,
                'auto_asiento' => $request->boolean('auto_asiento')
            ]);

            if ($compra->saldoPendiente() <= 0) {
                $compra->update(['pagada' => true]);
            }

            // Generar asiento contable para el pago solo si auto_asiento es true
            if ($pago->auto_asiento) {
                $this->contabilidadService->generarAsientoPagoProveedor($pago);
            }
        });

        return redirect()->route('compras.show', $compra)->with('success', 'Pago registrado correctamente');
    }
}