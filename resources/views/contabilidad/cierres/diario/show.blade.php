@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detalles del Cierre Diario - {{ optional($cierre->fecha)->format('d/m/Y') }}</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('contabilidad.cierres.diario.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                        <a href="{{ route('contabilidad.cierres.diario.export-excel', $cierre) }}" class="btn btn-outline-success">
                            <i class="fas fa-file-excel"></i> Exportar a Excel
                        </a>
                        @if(!$cierre->cerrado)
                            <button type="button" 
                                    class="btn btn-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalCerrarPeriodo">
                                <i class="fas fa-lock"></i> Cerrar Período
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Información General -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Información General</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td class="fw-bold" style="width: 150px;">Fecha:</td>
                                            <td>{{ optional($cierre->fecha)->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Estado:</td>
                                            <td>
                                                @if($cierre->cerrado)
                                                    <span class="badge bg-success">Cerrado</span>
                                                @else
                                                    <span class="badge bg-warning">Pendiente</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Usuario:</td>
                                            <td>{{ optional($cierre->usuario)->name }}</td>
                                        </tr>
                                        @if($cierre->cerrado)
                                        <tr>
                                            <td class="fw-bold">Fecha de Cierre:</td>
                                            <td>{{ optional($cierre->fecha_cierre)->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Cerrado por:</td>
                                            <td>{{ optional($cierre->usuario_cierre)->name }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen Financiero -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-chart-line"></i> Resumen Financiero</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td class="fw-bold" style="width: 150px;">Saldo Inicial:</td>
                                            <td class="text-end">{{ number_format($cierre->saldo_inicial, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Ingresos:</td>
                                            <td class="text-end text-success">
                                                {{ number_format($cierre->ventas_contado + $cierre->cobros_credito + $cierre->otros_ingresos, 2) }}
                                                <small class="text-muted d-block">
                                                    ({{ $cierre->ventas->where('tipo', 'contado')->count() }} transacciones)
                                                </small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Egresos:</td>
                                            <td class="text-end text-danger">
                                                {{ number_format($cierre->compras_contado + $cierre->pagos_credito + $cierre->gastos, 2) }}
                                                <small class="text-muted d-block">
                                                    ({{ $cierre->compras->where('tipo', 'contado')->count() + $cierre->pagosProveedores->count() }} transacciones)
                                                </small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Saldo Final:</td>
                                            <td class="text-end fw-bold">{{ number_format($cierre->saldo_final, 2) }}</td>
                                        </tr>
                                        @if($cierre->cerrado && $cierre->diferencia != 0)
                                        <tr>
                                            <td class="fw-bold">Diferencia:</td>
                                            <td class="text-end {{ $cierre->diferencia > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($cierre->diferencia, 2) }}
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Detalle de Ventas -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-shopping-cart"></i> Detalle de Ventas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th class="text-end">Cantidad</th>
                                                    <th class="text-end">Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Ventas al Contado</td>
                                                    <td class="text-end">{{ $cierre->ventas->where('tipo', 'contado')->count() }}</td>
                                                    <td class="text-end text-success">{{ number_format($cierre->ventas_contado, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Ventas a Crédito</td>
                                                    <td class="text-end">{{ $cierre->ventas->where('tipo', 'credito')->count() }}</td>
                                                    <td class="text-end">{{ number_format($cierre->ventas_credito, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Cobros de Crédito</td>
                                                    <td class="text-end">{{ $cierre->pagosClientes->count() }}</td>
                                                    <td class="text-end text-success">{{ number_format($cierre->cobros_credito, 2) }}</td>
                                                </tr>
                                                <tr class="table-light">
                                                    <td class="fw-bold">Total Ventas</td>
                                                    <td class="text-end fw-bold">{{ $cierre->ventas->count() }}</td>
                                                    <td class="text-end fw-bold">{{ number_format($cierre->ventas_contado + $cierre->ventas_credito, 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Desglose de Ventas -->
                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-list"></i> Desglose de Ventas</h6>
                                </div>
                                <div class="card-body">
                                    <h6 class="border-bottom pb-2 text-success">Ventas al Contado (Afectan caja)</h6>
                                    <div class="table-responsive mb-4">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Cliente</th>
                                                    <th class="text-end">Subtotal</th>
                                                    <th class="text-end">IVA</th>
                                                    <th class="text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($cierre->ventas->where('tipo', 'contado') as $i => $venta)
                                                <tr><td colspan="5"><hr></td></tr>
                                                <tr>
                                                    <td colspan="5">
                                                        <strong>Detalle de la Venta #{{ $i + 1 }}</strong><br>
                                                        Cliente: {{ optional($venta->cliente)->nombre ?? '-' }}<br>
                                                        Tipo: <span class="text-success">Contado</span><br>
                                                        Total de la venta: {{ number_format($venta->total, 2) }} Bs
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td colspan="4">
                                                        <div class="ms-2">
                                                            <span class="fw-bold">Productos/Servicios vendidos en esta venta:</span>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered mb-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Tipo</th>
                                                                            <th>Nombre</th>
                                                                            <th class="text-end">Cantidad</th>
                                                                            <th class="text-end">Unidad</th>
                                                                            <th class="text-end">Precio Unitario (Bs)</th>
                                                                            <th class="text-end">Subtotal (Bs)</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($venta->items as $item)
                                                                        <tr>
                                                                            <td>
                                                                                @if($item->producto)
                                                                                    Producto
                                                                                @elseif($item->materiaPrimaFiltrada)
                                                                                    Materia Prima
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($item->producto)
                                                                                    {{ $item->producto->nombre }}
                                                                                @elseif($item->materiaPrimaFiltrada)
                                                                                    {{ $item->materiaPrimaFiltrada->nombre }}
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-end">{{ number_format($item->cantidad, 2) }}</td>
                                                                            <td class="text-end">
                                                                                @if($item->producto)
                                                                                    {{ $item->producto->unidad_medida ?? '-' }}
                                                                                @elseif(isset($item->materiaPrimaFiltrada) && $item->materiaPrimaFiltrada)
                                                                                    {{ $item->materiaPrimaFiltrada->unidad_medida ?? '-' }}
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-end">{{ number_format($item->precio_unitario, 2) }} Bs</td>
                                                                            <td class="text-end">{{ number_format($item->subtotal, 2) }} Bs</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr class="fw-bold">
                                                    <td colspan="2">Totales</td>
                                                    <td class="text-end">{{ number_format($cierre->ventas->where('tipo', 'contado')->sum('subtotal'), 2) }}</td>
                                                    <td class="text-end">{{ number_format($cierre->ventas->where('tipo', 'contado')->sum('iva_amount'), 2) }}</td>
                                                    <td class="text-end">{{ number_format($cierre->ventas->where('tipo', 'contado')->sum('total'), 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <h6 class="border-bottom pb-2 text-info">Ventas a Crédito (Solo informativo, no afecta caja)</h6>
                                    <div class="table-responsive mb-4">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Cliente</th>
                                                    <th class="text-end">Subtotal</th>
                                                    <th class="text-end">IVA</th>
                                                    <th class="text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($cierre->ventas->where('tipo', 'credito') as $i => $venta)
                                                <tr><td colspan="5"><hr></td></tr>
                                                <tr>
                                                    <td colspan="5">
                                                        <strong>Detalle de la Venta #{{ $i + 1 }}</strong><br>
                                                        Cliente: {{ optional($venta->cliente)->nombre ?? '-' }}<br>
                                                        Tipo: <span class="text-info">Crédito</span><br>
                                                        Total de la venta: {{ number_format($venta->total, 2) }} Bs
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td colspan="4">
                                                        <div class="ms-2">
                                                            <span class="fw-bold">Productos/Servicios vendidos en esta venta:</span>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered mb-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Tipo</th>
                                                                            <th>Nombre</th>
                                                                            <th class="text-end">Cantidad</th>
                                                                            <th class="text-end">Unidad</th>
                                                                            <th class="text-end">Precio Unitario (Bs)</th>
                                                                            <th class="text-end">Subtotal (Bs)</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($venta->items as $item)
                                                                        <tr>
                                                                            <td>
                                                                                @if($item->producto)
                                                                                    Producto
                                                                                @elseif($item->materiaPrimaFiltrada)
                                                                                    Materia Prima
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($item->producto)
                                                                                    {{ $item->producto->nombre }}
                                                                                @elseif($item->materiaPrimaFiltrada)
                                                                                    {{ $item->materiaPrimaFiltrada->nombre }}
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-end">{{ number_format($item->cantidad, 2) }}</td>
                                                                            <td class="text-end">
                                                                                @if($item->producto)
                                                                                    {{ $item->producto->unidad_medida ?? '-' }}
                                                                                @elseif(isset($item->materiaPrimaFiltrada) && $item->materiaPrimaFiltrada)
                                                                                    {{ $item->materiaPrimaFiltrada->unidad_medida ?? '-' }}
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-end">{{ number_format($item->precio_unitario, 2) }} Bs</td>
                                                                            <td class="text-end">{{ number_format($item->subtotal, 2) }} Bs</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr class="fw-bold">
                                                    <td colspan="2">Totales</td>
                                                    <td class="text-end">{{ number_format($cierre->ventas->where('tipo', 'credito')->sum('subtotal'), 2) }}</td>
                                                    <td class="text-end">{{ number_format($cierre->ventas->where('tipo', 'credito')->sum('iva_amount'), 2) }}</td>
                                                    <td class="text-end">{{ number_format($cierre->ventas->where('tipo', 'credito')->sum('total'), 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detalle de Compras -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-truck"></i> Detalle de Compras</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th class="text-end">Cantidad</th>
                                                    <th class="text-end">Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Compras al Contado</td>
                                                    <td class="text-end">{{ $cierre->compras->where('tipo', 'contado')->count() }}</td>
                                                    <td class="text-end text-danger">{{ number_format($cierre->compras_contado, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Compras a Crédito</td>
                                                    <td class="text-end">{{ $cierre->compras->where('tipo', 'credito')->count() }}</td>
                                                    <td class="text-end">{{ number_format($cierre->compras_credito, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Pagos de Crédito</td>
                                                    <td class="text-end">{{ $cierre->pagosProveedores->count() }}</td>
                                                    <td class="text-end text-danger">{{ number_format($cierre->pagos_credito, 2) }}</td>
                                                </tr>
                                                <tr class="table-light">
                                                    <td class="fw-bold">Total Compras</td>
                                                    <td class="text-end fw-bold">{{ $cierre->compras->count() }}</td>
                                                    <td class="text-end fw-bold">{{ number_format($cierre->compras_contado + $cierre->compras_credito, 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Desglose de Compras -->
                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-list"></i> Desglose de Compras</h6>
                                </div>
                                <div class="card-body">
                                    <h6 class="border-bottom pb-2 text-danger">Compras al Contado (Afectan caja)</h6>
                                    <div class="table-responsive mb-4">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Proveedor</th>
                                                    <th class="text-end">Subtotal</th>
                                                    <th class="text-end">IVA</th>
                                                    <th class="text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($cierre->compras->where('tipo', 'contado') as $i => $compra)
                                                <tr><td colspan="5"><hr></td></tr>
                                                <tr>
                                                    <td colspan="5">
                                                        <strong>Detalle de la Compra #{{ $i + 1 }}</strong><br>
                                                        Proveedor: {{ optional($compra->proveedor)->nombre ?? '-' }}<br>
                                                        Tipo: <span class="text-danger">Contado</span><br>
                                                        Total de la compra: {{ number_format($compra->total, 2) }} Bs
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td colspan="4">
                                                        <div class="ms-2">
                                                            <span class="fw-bold">Productos/Servicios comprados en esta compra:</span>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered mb-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Tipo</th>
                                                                            <th>Nombre</th>
                                                                            <th class="text-end">Cantidad</th>
                                                                            <th class="text-end">Unidad</th>
                                                                            <th class="text-end">Precio Unitario (Bs)</th>
                                                                            <th class="text-end">Subtotal (Bs)</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($compra->items as $item)
                                                                        <tr>
                                                                            <td>
                                                                                @if($item->producto)
                                                                                    Producto
                                                                                @elseif($item->materiaPrima)
                                                                                    Materia Prima
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($item->producto)
                                                                                    {{ $item->producto->nombre }}
                                                                                @elseif($item->materiaPrima)
                                                                                    {{ $item->materiaPrima->nombre }}
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-end">{{ number_format($item->cantidad, 2) }}</td>
                                                                            <td class="text-end">
                                                                                @if($item->producto)
                                                                                    {{ $item->producto->unidad_medida ?? '-' }}
                                                                                @elseif(isset($item->materiaPrima) && $item->materiaPrima)
                                                                                    {{ $item->materiaPrima->unidad_medida ?? '-' }}
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-end">{{ number_format($item->precio_unitario, 2) }} Bs</td>
                                                                            <td class="text-end">{{ number_format($item->subtotal, 2) }} Bs</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr class="fw-bold">
                                                    <td colspan="2">Totales</td>
                                                    <td class="text-end">{{ number_format($cierre->compras->where('tipo', 'contado')->sum('subtotal'), 2) }}</td>
                                                    <td class="text-end">{{ number_format($cierre->compras->where('tipo', 'contado')->sum('iva_amount'), 2) }}</td>
                                                    <td class="text-end">{{ number_format($cierre->compras->where('tipo', 'contado')->sum('total'), 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <h6 class="border-bottom pb-2 text-info">Compras a Crédito (Solo informativo, no afecta caja)</h6>
                                    <div class="table-responsive mb-4">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Proveedor</th>
                                                    <th class="text-end">Subtotal</th>
                                                    <th class="text-end">IVA</th>
                                                    <th class="text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($cierre->compras->where('tipo', 'credito') as $i => $compra)
                                                <tr><td colspan="5"><hr></td></tr>
                                                <tr>
                                                    <td colspan="5">
                                                        <strong>Detalle de la Compra #{{ $i + 1 }}</strong><br>
                                                        Proveedor: {{ optional($compra->proveedor)->nombre ?? '-' }}<br>
                                                        Tipo: <span class="text-info">Crédito</span><br>
                                                        Total de la compra: {{ number_format($compra->total, 2) }} Bs
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td colspan="4">
                                                        <div class="ms-2">
                                                            <span class="fw-bold">Productos/Servicios comprados en esta compra:</span>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered mb-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Tipo</th>
                                                                            <th>Nombre</th>
                                                                            <th class="text-end">Cantidad</th>
                                                                            <th class="text-end">Unidad</th>
                                                                            <th class="text-end">Precio Unitario (Bs)</th>
                                                                            <th class="text-end">Subtotal (Bs)</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($compra->items as $item)
                                                                        <tr>
                                                                            <td>
                                                                                @if($item->producto)
                                                                                    Producto
                                                                                @elseif($item->materiaPrima)
                                                                                    Materia Prima
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($item->producto)
                                                                                    {{ $item->producto->nombre }}
                                                                                @elseif($item->materiaPrima)
                                                                                    {{ $item->materiaPrima->nombre }}
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-end">{{ number_format($item->cantidad, 2) }}</td>
                                                                            <td class="text-end">
                                                                                @if($item->producto)
                                                                                    {{ $item->producto->unidad_medida ?? '-' }}
                                                                                @elseif(isset($item->materiaPrima) && $item->materiaPrima)
                                                                                    {{ $item->materiaPrima->unidad_medida ?? '-' }}
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-end">{{ number_format($item->precio_unitario, 2) }} Bs</td>
                                                                            <td class="text-end">{{ number_format($item->subtotal, 2) }} Bs</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr class="fw-bold">
                                                    <td colspan="2">Totales</td>
                                                    <td class="text-end">{{ number_format($cierre->compras->where('tipo', 'credito')->sum('subtotal'), 2) }}</td>
                                                    <td class="text-end">{{ number_format($cierre->compras->where('tipo', 'credito')->sum('iva_amount'), 2) }}</td>
                                                    <td class="text-end">{{ number_format($cierre->compras->where('tipo', 'credito')->sum('total'), 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen de IVA y Totales Generales -->
                        <div class="col-12 mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="card bg-light border-info">
                                        <div class="card-body py-2 px-3">
                                            <span class="fw-bold text-info">IVA Ventas</span>
                                            <div class="h5 mb-0">{{ number_format($cierre->iva_ventas_contado + $cierre->iva_ventas_credito, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light border-danger">
                                        <div class="card-body py-2 px-3">
                                            <span class="fw-bold text-danger">IVA Compras</span>
                                            <div class="h5 mb-0">{{ number_format($cierre->iva_compras_contado + $cierre->iva_compras_credito, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light border-success">
                                        <div class="card-body py-2 px-3">
                                            <span class="fw-bold text-success">IVA a Pagar</span>
                                            <div class="h5 mb-0">
                                                {{ number_format(
                                                    ($cierre->iva_ventas_contado + $cierre->iva_ventas_credito) -
                                                    ($cierre->iva_compras_contado + $cierre->iva_compras_credito),
                                                    2
                                                ) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light border-secondary">
                                        <div class="card-body py-2 px-3">
                                            <span class="fw-bold text-secondary">Total Ventas</span>
                                            <div class="h5 mb-0">{{ number_format($cierre->ventas_contado + $cierre->ventas_credito, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Métodos de Pago -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-money-bill-wave"></i> Métodos de Pago</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Método</th>
                                                    <th class="text-end">Ingresos</th>
                                                    <th class="text-end">Egresos</th>
                                                    <th class="text-end">Neto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($cierre->metodosPago && count($cierre->metodosPago) > 0)
                                                    @foreach($cierre->metodosPago as $metodo)
                                                    <tr>
                                                        <td>{{ $metodo->nombre }}</td>
                                                        <td class="text-end text-success">{{ number_format($metodo->ingresos, 2) }}</td>
                                                        <td class="text-end text-danger">{{ number_format($metodo->egresos, 2) }}</td>
                                                        <td class="text-end {{ $metodo->ingresos - $metodo->egresos >= 0 ? 'text-success' : 'text-danger' }}">
                                                            {{ number_format($metodo->ingresos - $metodo->egresos, 2) }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    <tr class="table-light">
                                                        <td class="fw-bold">Total</td>
                                                        <td class="text-end fw-bold text-success">
                                                            {{ number_format($cierre->metodosPago->sum('ingresos'), 2) }}
                                                        </td>
                                                        <td class="text-end fw-bold text-danger">
                                                            {{ number_format($cierre->metodosPago->sum('egresos'), 2) }}
                                                        </td>
                                                        <td class="text-end fw-bold">
                                                            {{ number_format($cierre->metodosPago->sum('ingresos') - $cierre->metodosPago->sum('egresos'), 2) }}
                                                        </td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="text-center">No hay datos de métodos de pago disponibles</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Otros Movimientos -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-exchange-alt"></i> Otros Movimientos</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tipo</th>
                                                <th class="text-end">Cantidad</th>
                                                <th class="text-end">Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Gastos</td>
                                                <td class="text-end">{{ $cierre->gastos_count }}</td>
                                                <td class="text-end text-danger">{{ number_format($cierre->gastos, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Otros Ingresos</td>
                                                <td class="text-end">{{ $cierre->otros_ingresos_count }}</td>
                                                <td class="text-end text-success">{{ number_format($cierre->otros_ingresos, 2) }}</td>
                                            </tr>
                                            <tr class="table-light">
                                                <td class="fw-bold">Neto</td>
                                                <td class="text-end fw-bold">{{ $cierre->gastos_count + $cierre->otros_ingresos_count }}</td>
                                                <td class="text-end fw-bold {{ ($cierre->otros_ingresos - $cierre->gastos) >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($cierre->otros_ingresos - $cierre->gastos, 2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        @if($cierre->observaciones)
                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-comment"></i> Observaciones</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info mb-0">
                                        {{ $cierre->observaciones }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cerrar Período -->
<div class="modal fade" id="modalCerrarPeriodo" tabindex="-1" aria-labelledby="modalCerrarPeriodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('contabilidad.cierres.diario.cerrar', $cierre) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCerrarPeriodoLabel">Cerrar Período</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Por favor, verifique el saldo real en caja antes de cerrar el período.
                    </div>
                    
                    <div class="mb-3">
                        <label for="saldo_real" class="form-label">Saldo Real en Caja</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                   class="form-control @error('saldo_real') is-invalid @enderror" 
                                   id="saldo_real" 
                                   name="saldo_real" 
                                   step="0.01" 
                                   min="0" 
                                   required>
                        </div>
                        @error('saldo_real')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones (opcional)</label>
                        <textarea class="form-control" 
                                  id="observaciones" 
                                  name="observaciones" 
                                  rows="3"></textarea>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Importante:</strong> Esta acción no se puede deshacer.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-lock"></i> Confirmar Cierre
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-2">Resumen General de Movimientos del Día</h5>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th class="text-end">Ingresos (Bs)</th>
                        <th class="text-end">Egresos (Bs)</th>
                        <th class="text-end">Saldo Total Acumulado (Bs)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $saldo = $cierre->saldo_inicial;
                    @endphp
                    <tr>
                        <td colspan="4" class="fw-bold text-primary">Movimientos que afectan caja</td>
                    </tr>
                    <tr>
                        <td><strong>Saldo Inicial</strong></td>
                        <td></td>
                        <td></td>
                        <td class="text-end"><strong>{{ number_format($saldo, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td>Ventas al Contado</td>
                        <td class="text-end">{{ number_format($cierre->ventas_contado, 2) }}</td>
                        <td></td>
                        <td class="text-end">{{ number_format($saldo += $cierre->ventas_contado, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Cobros de Créditos</td>
                        <td class="text-end">{{ number_format($cierre->cobros_credito, 2) }}</td>
                        <td></td>
                        <td class="text-end">{{ number_format($saldo += $cierre->cobros_credito, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Otros Ingresos</td>
                        <td class="text-end">{{ number_format($cierre->otros_ingresos, 2) }}</td>
                        <td></td>
                        <td class="text-end">{{ number_format($saldo += $cierre->otros_ingresos, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Compras al Contado</td>
                        <td></td>
                        <td class="text-end">{{ number_format($cierre->compras_contado, 2) }}</td>
                        <td class="text-end">{{ number_format($saldo -= $cierre->compras_contado, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Pagos de Créditos</td>
                        <td></td>
                        <td class="text-end">{{ number_format($cierre->pagos_credito, 2) }}</td>
                        <td class="text-end">{{ number_format($saldo -= $cierre->pagos_credito, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Gastos</td>
                        <td></td>
                        <td class="text-end">{{ number_format($cierre->gastos, 2) }}</td>
                        <td class="text-end">{{ number_format($saldo -= $cierre->gastos, 2) }}</td>
                    </tr>
                    <tr class="table-light">
                        <td class="fw-bold">Totales</td>
                        <td class="text-end fw-bold">{{ number_format($cierre->ventas_contado + $cierre->cobros_credito + $cierre->otros_ingresos, 2) }}</td>
                        <td class="text-end fw-bold">{{ number_format($cierre->compras_contado + $cierre->pagos_credito + $cierre->gastos, 2) }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><strong>Saldo Final</strong></td>
                        <td></td>
                        <td></td>
                        <td class="text-end"><strong>{{ number_format($cierre->saldo_final, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="fw-bold text-secondary">Movimientos informativos (no afectan caja)</td>
                    </tr>
                    <tr>
                        <td>Ventas a Crédito</td>
                        <td class="text-end">{{ number_format($cierre->ventas_credito, 2) }}</td>
                        <td></td>
                        <td class="text-end">{{ number_format($cierre->ventas_credito, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Compras a Crédito</td>
                        <td></td>
                        <td class="text-end">{{ number_format($cierre->compras_credito, 2) }}</td>
                        <td class="text-end">{{ number_format($cierre->compras_credito, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    body * { visibility: hidden; }
    .card, .card * { visibility: visible; }
    .card { position: absolute; left: 0; top: 0; width: 100%; }
    .btn, .modal, .modal-backdrop { display: none !important; }
}
</style>
@endpush 