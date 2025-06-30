<?php

namespace App\Services;

use App\Models\AsientoContable;
use App\Models\DetalleAsiento;
use App\Models\Compra;
use App\Models\Venta;
use App\Models\Produccion;
use App\Models\CentroCosto;
use App\Models\CuentaContable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ContabilidadService
{
    // Tasas de retención (deben configurarse según la normativa local)
    const TASA_RETENCION_IVA = 0.13; // 13%
    const TASA_RETENCION_IT = 0.03;  // 3%
    const TASA_RETENCION_IUE = 0.025; // 2.5%

    /**
     * Genera asiento contable para una compra
     */
    public function generarAsientoCompra(Compra $compra)
    {
        return DB::transaction(function () use ($compra) {
            $asiento = AsientoContable::create([
                'fecha' => $compra->fecha,
                'numero_asiento' => 'COMP-' . str_pad($compra->id, 6, '0', STR_PAD_LEFT),
                'tipo_documento' => 'COMPRA',
                'numero_documento' => $compra->id,
                'concepto' => "Compra a {$compra->proveedor->nombre}",
                'estado' => $compra->tipo === 'contado' ? 'APROBADO' : 'BORRADOR',
                'tipo_operacion' => $compra->tipo === 'credito' ? 'compra_credito' : 'compra_contado',
                'centro_costo_id' => $compra->centro_costo_id,
                'user_id' => Auth::id()
            ]);

            $totalCompra = $compra->total;
            $retenciones = 0;
            $ivaCompra = $compra->iva_amount;
            $subtotalCompra = $compra->subtotal;

            // Cuenta de Inventario (DEBE)
            foreach ($compra->items as $item) {
                if ($item->tipo_item === 'materia_prima') {
                    $cuenta = CuentaContable::where('codigo', '1.1.4.1')->first(); // Inventario Materia Prima
                    $cuentaCompra = CuentaContable::where('codigo', '5.6.2.1')->first(); // Compra de Materia Prima Nacional
                } else {
                    $cuenta = CuentaContable::where('codigo', '1.1.4.3')->first(); // Inventario Productos Terminados
                    $cuentaCompra = CuentaContable::where('codigo', '5.6.1.1')->first(); // Compra de Mercaderías Nacionales
                }

                // Registro en inventario
                DetalleAsiento::create([
                    'asiento_id' => $asiento->id,
                    'cuenta_id' => $cuenta->id,
                    'debe' => $item->subtotal,
                    'haber' => 0,
                    'descripcion' => "Compra de {$item->cantidad} {$item->unidad_medida} de " . 
                        ($item->tipo_item === 'materia_prima' ? $item->materiaPrima->nombre : $item->producto->nombre)
                ]);

                // Registro en compras (para efectos de costo)
                DetalleAsiento::create([
                    'asiento_id' => $asiento->id,
                    'cuenta_id' => $cuentaCompra->id,
                    'debe' => $item->subtotal,
                    'haber' => 0,
                    'descripcion' => "Compra de {$item->cantidad} {$item->unidad_medida} de " . 
                        ($item->tipo_item === 'materia_prima' ? $item->materiaPrima->nombre : $item->producto->nombre)
                ]);
            }

            // Manejo de retenciones cuando no hay factura
            if (!$compra->has_invoice) {
                // Retención de IVA (cuando no hay factura)
                $retencionIva = $subtotalCompra * self::TASA_RETENCION_IVA;
                $cuentaRetencionIva = CuentaContable::where('codigo', '1.1.7.1')->first(); // Retención IVA Sufrida
                
                DetalleAsiento::create([
                    'asiento_id' => $asiento->id,
                    'cuenta_id' => $cuentaRetencionIva->id,
                    'debe' => $retencionIva,
                    'haber' => 0,
                    'descripcion' => "Retención IVA por compra sin factura"
                ]);

                // Retención de IT (Impuesto a las Transacciones)
                $retencionIt = $subtotalCompra * self::TASA_RETENCION_IT;
                $cuentaRetencionIt = CuentaContable::where('codigo', '1.1.7.2')->first(); // Retención IUE/RENTA Sufrida
                
                DetalleAsiento::create([
                    'asiento_id' => $asiento->id,
                    'cuenta_id' => $cuentaRetencionIt->id,
                    'debe' => $retencionIt,
                    'haber' => 0,
                    'descripcion' => "Retención IT por compra sin factura"
                ]);

                $retenciones = $retencionIva + $retencionIt;
                $totalCompra -= $retenciones;

                // Registro en gastos sin factura
                $cuentaGastoSinFactura = CuentaContable::where('codigo', '5.8.1.1')->first(); // Compras sin factura
                DetalleAsiento::create([
                    'asiento_id' => $asiento->id,
                    'cuenta_id' => $cuentaGastoSinFactura->id,
                    'debe' => $subtotalCompra,
                    'haber' => 0,
                    'descripcion' => "Compra sin factura registrada como gasto no deducible"
                ]);
            } else {
                // IVA Crédito Fiscal (DEBE) - Solo si hay factura
                if ($ivaCompra > 0) {
                    $cuentaIva = CuentaContable::where('codigo', '1.1.6.1')->first(); // IVA Crédito Fiscal
                    DetalleAsiento::create([
                        'asiento_id' => $asiento->id,
                        'cuenta_id' => $cuentaIva->id,
                        'debe' => $ivaCompra,
                        'haber' => 0,
                        'descripcion' => "IVA Crédito Fiscal de compra"
                    ]);
                }
            }

            // Cuenta por Pagar, Efectivo o Banco (HABER)
            if ($compra->tipo === 'credito') {
                $cuenta = CuentaContable::where('codigo', '2.1.1.1')->first(); // Cuentas por Pagar Proveedores Nacionales
                $descripcion = "Cuenta por pagar a {$compra->proveedor->nombre}";
            } else {
                // Obtener el método de pago del primer pago (para compras al contado)
                $metodoPago = $compra->pagos->first()?->metodo_pago;
                
                if ($metodoPago === 'transferencia') {
                    $cuenta = CuentaContable::where('codigo', '1.1.1.3')->first(); // Bancos - Cuenta Corriente
                    $descripcion = "Pago por transferencia a {$compra->proveedor->nombre}";
                } else {
                    $cuenta = CuentaContable::where('codigo', '1.1.1.1')->first(); // Caja General
                    $descripcion = "Pago en efectivo a {$compra->proveedor->nombre}";
                }
            }

            DetalleAsiento::create([
                'asiento_id' => $asiento->id,
                'cuenta_id' => $cuenta->id,
                'debe' => 0,
                'haber' => $totalCompra,
                'descripcion' => $descripcion
            ]);

            return $asiento;
        });
    }

    /**
     * Genera asiento contable para una venta
     */
    public function generarAsientoVenta(Venta $venta)
    {
        // Validar si ya existe un asiento para esta venta
        $asientoExistente = AsientoContable::where('tipo_documento', 'VENTA')
            ->where('numero_documento', $venta->id)
            ->first();
        if ($asientoExistente) {
            return $asientoExistente;
        }
        
        return DB::transaction(function () use ($venta) {
            $asiento = AsientoContable::create([
                'fecha' => $venta->fecha,
                'numero_asiento' => 'VENT-' . str_pad($venta->id, 6, '0', STR_PAD_LEFT),
                'tipo_documento' => 'VENTA',
                'numero_documento' => $venta->id,
                'concepto' => $venta->cliente_id ? 
                    "Venta a {$venta->cliente->nombre}" : 
                    "Venta al contado",
                'estado' => $venta->tipo === 'contado' ? 'APROBADO' : 'BORRADOR',
                'tipo_operacion' => $venta->tipo === 'credito' ? 'venta_credito' : 'venta_contado',
                'user_id' => Auth::id()
            ]);

            $totalVenta = $venta->total;
            $subtotalVenta = $venta->subtotal;
            $ivaVenta = $venta->iva_amount;
            $retenciones = 0;

            // Cuenta por Cobrar o Efectivo (DEBE)
            if ($venta->tipo === 'credito') {
                $cuenta = CuentaContable::where('codigo', '1.1.3.1')->first(); // Cuentas por Cobrar - Clientes
                $descripcion = "Cuenta por cobrar a " . ($venta->cliente_id ? $venta->cliente->nombre : "Cliente Final");
            } else {
                $cuenta = CuentaContable::where('codigo', '1.1.1.1')->first(); // Caja General
                $descripcion = "Ingreso por venta al contado";
            }

            DetalleAsiento::create([
                'asiento_id' => $asiento->id,
                'cuenta_id' => $cuenta->id,
                'debe' => $totalVenta,
                'haber' => 0,
                'descripcion' => $descripcion
            ]);

            // Ventas (HABER)
            $cuentaVentas = CuentaContable::where('codigo', '4.1.1.1')->first(); // Ventas Nacionales
            DetalleAsiento::create([
                'asiento_id' => $asiento->id,
                'cuenta_id' => $cuentaVentas->id,
                'debe' => 0,
                'haber' => $subtotalVenta,
                'descripcion' => "Venta de productos"
            ]);

            // IVA por Cobrar (HABER)
            if ($ivaVenta > 0) {
                $cuentaIVA = CuentaContable::where('codigo', '2.1.3.1')->first(); // IVA por Cobrar
                DetalleAsiento::create([
                    'asiento_id' => $asiento->id,
                    'cuenta_id' => $cuentaIVA->id,
                    'debe' => 0,
                    'haber' => $ivaVenta,
                    'descripcion' => "IVA por cobrar"
                ]);
            }

            // Registrar el costo de ventas
            $this->registrarCostoVenta($asiento, $venta);

            return $asiento;
        });
    }

    /**
     * Registra el costo de venta en el mismo asiento
     */
    private function registrarCostoVenta($asiento, $venta)
    {
        $costoTotal = 0;
        foreach ($venta->items as $item) {
            if ($item->tipo_item === 'producto') {
                $costoTotal += $item->cantidad * $item->producto->costo_promedio;
            } else {
                $costoTotal += $item->cantidad * $item->materiaPrimaFiltrada->costo_promedio;
            }
        }

        if ($costoTotal > 0) {
            // Costo de Ventas (DEBE)
            $cuentaCosto = CuentaContable::where('codigo', '5.1.1.1')->first(); // Costo de Productos Vendidos - Materias Primas
            DetalleAsiento::create([
                'asiento_id' => $asiento->id,
                'cuenta_id' => $cuentaCosto->id,
                'debe' => $costoTotal,
                'haber' => 0,
                'descripcion' => "Costo de productos vendidos"
            ]);

            // Inventario (HABER)
            $cuentaInventario = CuentaContable::where('codigo', '1.1.4.3')->first(); // Inventario Productos Terminados
            DetalleAsiento::create([
                'asiento_id' => $asiento->id,
                'cuenta_id' => $cuentaInventario->id,
                'debe' => 0,
                'haber' => $costoTotal,
                'descripcion' => "Salida de inventario por venta"
            ]);
        }
    }

    /**
     * Genera asiento contable para una producción
     */
    public function generarAsientoProduccion(Produccion $produccion)
    {
        return DB::transaction(function () use ($produccion) {
            $asiento = AsientoContable::create([
                'fecha' => $produccion->fecha,
                'numero_asiento' => 'PROD-' . str_pad($produccion->id, 6, '0', STR_PAD_LEFT),
                'tipo_documento' => 'PRODUCCION',
                'numero_documento' => $produccion->id,
                'concepto' => "Producción de {$produccion->producto->nombre}",
                'estado' => 'BORRADOR',
                'user_id' => Auth::id()
            ]);

            // Productos en Proceso (DEBE)
            $cuentaProceso = CuentaContable::where('codigo', '1.1.4.2')->first(); // Productos en Proceso
            DetalleAsiento::create([
                'asiento_id' => $asiento->id,
                'cuenta_id' => $cuentaProceso->id,
                'debe' => $produccion->costo_produccion,
                'haber' => 0,
                'descripcion' => "Producción en proceso de {$produccion->producto->nombre}"
            ]);

            // Materia Prima (HABER)
            $cuentaMateriaPrima = CuentaContable::where('codigo', '1.1.4.1')->first(); // Inventario Materia Prima
            DetalleAsiento::create([
                'asiento_id' => $asiento->id,
                'cuenta_id' => $cuentaMateriaPrima->id,
                'debe' => 0,
                'haber' => $produccion->costo_materia_prima,
                'descripcion' => "Salida de materia prima para producción"
            ]);

            if ($produccion->costo_mano_obra > 0) {
                // Mano de obra directa (HABER)
                $cuentaManoObra = CuentaContable::where('codigo', '5.1.1.2')->first(); // Mano de Obra Directa
                DetalleAsiento::create([
                    'asiento_id' => $asiento->id,
                    'cuenta_id' => $cuentaManoObra->id,
                    'debe' => 0,
                    'haber' => $produccion->costo_mano_obra,
                    'descripcion' => "Costo de mano de obra directa"
                ]);
            }

            if ($produccion->costo_adicional > 0) {
                // Costos Indirectos (HABER)
                $cuentaCostosIndirectos = CuentaContable::where('codigo', '5.1.1.3')->first(); // Costos Indirectos de Fabricación
                DetalleAsiento::create([
                    'asiento_id' => $asiento->id,
                    'cuenta_id' => $cuentaCostosIndirectos->id,
                    'debe' => 0,
                    'haber' => $produccion->costo_adicional,
                    'descripcion' => "Costos adicionales de producción"
                ]);
            }

            // Asiento para producto terminado
            $this->generarAsientoProductoTerminado($produccion);

            return $asiento;
        });
    }

    /**
     * Genera asiento para producto terminado
     */
    private function generarAsientoProductoTerminado(Produccion $produccion)
    {
        $asiento = AsientoContable::create([
            'fecha' => $produccion->fecha,
            'numero_asiento' => 'PT-' . str_pad($produccion->id, 6, '0', STR_PAD_LEFT),
            'tipo_documento' => 'PRODUCTO_TERMINADO',
            'numero_documento' => $produccion->id,
            'concepto' => "Producto terminado: {$produccion->producto->nombre}",
            'estado' => 'BORRADOR',
            'user_id' => Auth::id()
        ]);

        // Productos Terminados (DEBE)
        $cuentaTerminados = CuentaContable::where('codigo', '1.1.4.3')->first(); // Inventario Productos Terminados
        DetalleAsiento::create([
            'asiento_id' => $asiento->id,
            'cuenta_id' => $cuentaTerminados->id,
            'debe' => $produccion->costo_produccion,
            'haber' => 0,
            'descripcion' => "Ingreso de producto terminado al inventario"
        ]);

        // Productos en Proceso (HABER)
        $cuentaProceso = CuentaContable::where('codigo', '1.1.4.2')->first(); // Productos en Proceso
        DetalleAsiento::create([
            'asiento_id' => $asiento->id,
            'cuenta_id' => $cuentaProceso->id,
            'debe' => 0,
            'haber' => $produccion->costo_produccion,
            'descripcion' => "Salida de producción en proceso"
        ]);
    }

    /**
     * Genera asiento contable para un pago a proveedor
     */
    public function generarAsientoPagoProveedor($pago)
    {
        return DB::transaction(function () use ($pago) {
            $asiento = AsientoContable::create([
                'fecha' => $pago->fecha_pago,
                'numero_asiento' => 'PAGO-' . str_pad($pago->id, 6, '0', STR_PAD_LEFT),
                'tipo_documento' => 'PAGO_PROVEEDOR',
                'numero_documento' => $pago->id,
                'concepto' => "Pago a proveedor por compra #{$pago->compra_id}",
                'estado' => 'BORRADOR',
                'user_id' => Auth::id()
            ]);

            // Cuentas por Pagar (DEBE)
            $cuentaPorPagar = CuentaContable::where('codigo', '2.1.1.1')->first(); // Cuentas por Pagar - Proveedores Nacionales
            DetalleAsiento::create([
                'asiento_id' => $asiento->id,
                'cuenta_id' => $cuentaPorPagar->id,
                'debe' => $pago->monto,
                'haber' => 0,
                'descripcion' => "Pago de cuenta por pagar a {$pago->compra->proveedor->nombre}"
            ]);

            // Banco/Caja (HABER)
            $cuenta = $pago->metodo_pago === 'transferencia' 
                ? CuentaContable::where('codigo', '1.1.1.3')->first() // Bancos - Cuenta Corriente
                : CuentaContable::where('codigo', '1.1.1.1')->first(); // Caja General
            
            DetalleAsiento::create([
                'asiento_id' => $asiento->id,
                'cuenta_id' => $cuenta->id,
                'debe' => 0,
                'haber' => $pago->monto,
                'descripcion' => "Salida de efectivo por pago a proveedor"
            ]);

            // Si hay descuentos por pronto pago
            if ($pago->descuento > 0) {
                $cuentaDescuento = CuentaContable::where('codigo', '4.2.1.1')->first(); // Ingresos Financieros - Intereses Ganados
                DetalleAsiento::create([
                    'asiento_id' => $asiento->id,
                    'cuenta_id' => $cuentaDescuento->id,
                    'debe' => 0,
                    'haber' => $pago->descuento,
                    'descripcion' => "Descuento por pronto pago"
                ]);
            }

            return $asiento;
        });
    }

    /**
     * Genera asiento contable para un cobro a cliente
     */
    public function generarAsientoCobroCliente($pago)
    {
        return DB::transaction(function () use ($pago) {
            $asiento = AsientoContable::create([
                'fecha' => $pago->fecha_pago,
                'numero_asiento' => $pago->venta->tipo === 'contado' ?
                    'VENT-' . str_pad($pago->venta_id, 6, '0', STR_PAD_LEFT) :
                    'COBRO-' . str_pad($pago->id, 6, '0', STR_PAD_LEFT),
                'tipo_documento' => $pago->venta->tipo === 'contado' ? 'VENTA' : 'COBRO_CLIENTE',
                'numero_documento' => $pago->venta->tipo === 'contado' ? $pago->venta_id : $pago->id,
                'concepto' => $pago->venta->tipo === 'contado' ? 
                    "Venta al contado #{$pago->venta_id}" : 
                    "Cobro a cliente por venta #{$pago->venta_id}",
                'estado' => 'BORRADOR',
                'user_id' => Auth::id()
            ]);

            // Banco/Caja (DEBE)
            $cuenta = $pago->metodo_pago === 'transferencia' 
                ? CuentaContable::where('codigo', '1.1.1.3')->first() // Bancos - Cuenta Corriente
                : CuentaContable::where('codigo', '1.1.1.1')->first(); // Caja General
                
            DetalleAsiento::create([
                'asiento_id' => $asiento->id,
                'cuenta_id' => $cuenta->id,
                'debe' => $pago->monto,
                'haber' => 0,
                'descripcion' => "Ingreso de efectivo por " . 
                    ($pago->venta->tipo === 'contado' ? "venta al contado" : "cobro a cliente")
            ]);

            if ($pago->venta->tipo === 'contado') {
                // Para ventas al contado, registramos directamente contra la cuenta de ventas
                $cuentaVentas = CuentaContable::where('codigo', '4.1.1.1')->first(); // Ventas Nacionales
                DetalleAsiento::create([
                    'asiento_id' => $asiento->id,
                    'cuenta_id' => $cuentaVentas->id,
                    'debe' => 0,
                    'haber' => $pago->monto,
                    'descripcion' => "Venta de productos al contado"
                ]);

                // Registrar el costo de ventas si hay items
                $this->registrarCostoVenta($asiento, $pago->venta);
            } else {
                // Para ventas a crédito, registramos contra cuentas por cobrar
                $cuentaPorCobrar = CuentaContable::where('codigo', '1.1.3.1')->first(); // Cuentas por Cobrar - Clientes
                DetalleAsiento::create([
                    'asiento_id' => $asiento->id,
                    'cuenta_id' => $cuentaPorCobrar->id,
                    'debe' => 0,
                    'haber' => $pago->monto,
                    'descripcion' => "Cobro de cuenta por cobrar a {$pago->venta->cliente->nombre}"
                ]);
            }

            return $asiento;
        });
    }

    /**
     * Genera asiento contable para el pago de impuestos
     */
    public function generarAsientoPagoImpuestos($pagoImpuesto)
    {
        return DB::transaction(function () use ($pagoImpuesto) {
            $asiento = AsientoContable::create([
                'fecha' => $pagoImpuesto->fecha_pago,
                'numero_asiento' => 'IMP-' . str_pad($pagoImpuesto->id, 6, '0', STR_PAD_LEFT),
                'tipo_documento' => 'PAGO_IMPUESTOS',
                'numero_documento' => $pagoImpuesto->id,
                'concepto' => "Pago de {$pagoImpuesto->tipo_impuesto}",
                'estado' => 'APROBADO',
                'user_id' => Auth::id()
            ]);

            // Determinar la cuenta del impuesto
            switch ($pagoImpuesto->tipo_impuesto) {
                case 'IVA':
                    $cuentaImpuesto = CuentaContable::where('codigo', '2.1.3.1')->first(); // IVA por Pagar
                    break;
                case 'IT':
                    $cuentaImpuesto = CuentaContable::where('codigo', '2.1.3.6')->first(); // IT por Pagar
                    break;
                case 'IUE':
                    $cuentaImpuesto = CuentaContable::where('codigo', '2.1.3.5')->first(); // IUE por Pagar
                    break;
                default:
                    $cuentaImpuesto = CuentaContable::where('codigo', '2.1.3.2')->first(); // Retenciones por Pagar
            }

            // Impuesto (DEBE)
            DetalleAsiento::create([
                'asiento_id' => $asiento->id,
                'cuenta_id' => $cuentaImpuesto->id,
                'debe' => $pagoImpuesto->monto,
                'haber' => 0,
                'descripcion' => "Pago de {$pagoImpuesto->tipo_impuesto}"
            ]);

            // Banco/Caja (HABER)
            $cuenta = $pagoImpuesto->metodo_pago === 'transferencia' 
                ? CuentaContable::where('codigo', '1.1.1.3')->first() // Bancos - Cuenta Corriente
                : CuentaContable::where('codigo', '1.1.1.1')->first(); // Caja General
            
            DetalleAsiento::create([
                'asiento_id' => $asiento->id,
                'cuenta_id' => $cuenta->id,
                'debe' => 0,
                'haber' => $pagoImpuesto->monto,
                'descripcion' => "Salida por pago de impuestos"
            ]);

            return $asiento;
        });
    }

    /**
     * Genera asiento contable para un pago de salarios
     */
    public function generarAsientoPagoSalario($pago)
    {
        return DB::transaction(function () use ($pago) {
            $asiento = AsientoContable::create([
                'fecha' => $pago->fecha_pago,
                'numero_asiento' => 'SAL-' . str_pad($pago->id, 6, '0', STR_PAD_LEFT),
                'tipo_documento' => 'PAGO_SALARIO',
                'numero_documento' => $pago->id,
                'concepto' => "Pago de salarios - {$pago->detalles->count()} empleados",
                'estado' => 'APROBADO',
                'tipo_operacion' => 'pago_salario',
                'user_id' => Auth::id()
            ]);

            // Cuenta de Gastos de Personal (DEBE)
            $cuentaGastos = CuentaContable::where('codigo', '5.1.1.1')->first(); // Gastos de Personal
            DetalleAsiento::create([
                'asiento_id' => $asiento->id,
                'cuenta_id' => $cuentaGastos->id,
                'debe' => $pago->monto_total,
                'haber' => 0,
                'descripcion' => "Pago de salarios a {$pago->detalles->count()} empleados"
            ]);

            // Cuenta de Efectivo o Banco (HABER)
            if ($pago->metodo_pago === 'transferencia') {
                $cuenta = CuentaContable::where('codigo', '1.1.1.3')->first(); // Bancos - Cuenta Corriente
                $descripcion = "Pago de salarios por transferencia";
            } else {
                $cuenta = CuentaContable::where('codigo', '1.1.1.1')->first(); // Caja General
                $descripcion = "Pago de salarios en efectivo";
            }

            DetalleAsiento::create([
                'asiento_id' => $asiento->id,
                'cuenta_id' => $cuenta->id,
                'debe' => 0,
                'haber' => $pago->monto_total,
                'descripcion' => $descripcion
            ]);

            // Marcar el pago como procesado
            $pago->update(['asiento_generado' => true]);

            return $asiento;
        });
    }

    /**
     * Genera asiento contable masivo para pagos de salarios
     */
    public function generarAsientoPagoSalarioMasivo($pagos, $montoTotal, $fecha)
    {
        return DB::transaction(function () use ($pagos, $montoTotal, $fecha) {
            $asiento = AsientoContable::create([
                'fecha' => $fecha,
                'numero_asiento' => 'SAL-MAS-' . str_pad($pagos->first()->id, 6, '0', STR_PAD_LEFT),
                'tipo_documento' => 'PAGO_SALARIO_MASIVO',
                'numero_documento' => $pagos->first()->id,
                'concepto' => "Pago masivo de salarios del " . $fecha,
                'estado' => 'BORRADOR',
                'user_id' => Auth::id()
            ]);

            // Gastos de Personal (DEBE)
            $cuentaGastos = CuentaContable::where('codigo', '5.1.1.1')->first(); // Gastos de Personal - Sueldos y Salarios
            DetalleAsiento::create([
                'asiento_id' => $asiento->id,
                'cuenta_id' => $cuentaGastos->id,
                'debe' => $montoTotal,
                'haber' => 0,
                'descripcion' => "Pago masivo de salarios del " . $fecha
            ]);

            // Banco/Caja (HABER)
            $cuenta = CuentaContable::where('codigo', '1.1.1.1')->first(); // Caja General
            DetalleAsiento::create([
                'asiento_id' => $asiento->id,
                'cuenta_id' => $cuenta->id,
                'debe' => 0,
                'haber' => $montoTotal,
                'descripcion' => "Salida por pago masivo de salarios"
            ]);

            return $asiento;
        });
    }
}