@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Detalle de Venta #{{ $venta->id }}</h1>
            <a href="{{ route('ventas.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información General</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Cliente:</div>
                                <div class="col-md-8">{{ $venta->cliente ? $venta->cliente->nombre : 'Consumidor Final' }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Fecha:</div>
                                <div class="col-md-8">{{ $venta->fecha->format('d/m/Y') }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Tipo:</div>
                                <div class="col-md-8">
                                    @if($venta->tipo == 'contado')
                                        <span class="badge bg-success">Contado</span>
                                    @else
                                        <span class="badge bg-info">Crédito</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Factura:</div>
                                <div class="col-md-8">
                                    @if($venta->has_invoice)
                                        <span class="badge bg-success">Sí - N° {{ $venta->invoice_number }}</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Estado:</div>
                                <div class="col-md-8">
                                    @if($venta->tipo == 'contado' || $venta->pagada)
                                        <span class="badge bg-success">Pagada</span>
                                    @else
                                        <span class="badge bg-warning">Pendiente</span>
                                    @endif
                                </div>
                            </div>
                            
                            @if($venta->tipo == 'credito' && !$venta->pagada)
                            <div class="mt-3">
                                <a href="{{ route('pagos.clientes.create', $venta->id) }}" class="btn btn-success">
                                    <i class="fas fa-money-bill"></i> Registrar Pago
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-list"></i> Resumen</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-6 fw-bold">Total de Ítems:</div>
                                <div class="col-md-6">{{ $venta->items->count() }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 fw-bold">Materias Primas:</div>
                                <div class="col-md-6">{{ $venta->items->where('tipo_item', 'materia_prima_filtrada')->count() }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 fw-bold">Productos:</div>
                                <div class="col-md-6">{{ $venta->items->where('tipo_item', 'producto')->count() }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 fw-bold">Subtotal:</div>
                                <div class="col-md-6">Bs {{ number_format($venta->subtotal, 2) }}</div>
                            </div>
                            @if($venta->has_invoice)
                            <div class="row mb-2">
                                <div class="col-md-6 fw-bold">IVA (13%):</div>
                                <div class="col-md-6">Bs {{ number_format($venta->iva_amount, 2) }}</div>
                            </div>
                            @endif
                            <div class="row mb-2">
                                <div class="col-md-6 fw-bold">Total:</div>
                                <div class="col-md-6 fw-bold text-primary">Bs {{ number_format($venta->total, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Ítems de Venta</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unitario</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venta->items as $item)
                                <tr>
                                    <td>
                                        @if($item->tipo_item == 'materia_prima_filtrada')
                                            <span class="badge bg-secondary">Materia Prima</span>
                                        @else
                                            <span class="badge bg-primary">Producto</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->tipo_item == 'materia_prima_filtrada')
                                            {{ $item->materiaPrimaFiltrada->nombre }}
                                            <small class="text-muted d-block">Unidad: {{ $item->materiaPrimaFiltrada->unidad_medida }}</small>
                                        @else
                                            {{ $item->producto->nombre }}
                                            <small class="text-muted d-block">Unidad: {{ $item->producto->unidad_medida }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ number_format($item->cantidad, 2) }}
                                        @if($item->tipo_item == 'materia_prima_filtrada')
                                            <small class="text-muted d-block">{{ $item->materiaPrimaFiltrada->unidad_medida }}</small>
                                        @else
                                            <small class="text-muted d-block">{{ $item->producto->unidad_medida }}</small>
                                        @endif
                                    </td>
                                    <td class="text-end">Bs {{ number_format($item->precio_unitario, 2) }}</td>
                                    <td class="text-end">Bs {{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Subtotal:</th>
                                    <th class="text-end">Bs {{ number_format($venta->subtotal, 2) }}</th>
                                </tr>
                                @if($venta->has_invoice)
                                <tr>
                                    <th colspan="4" class="text-end">IVA (13%):</th>
                                    <th class="text-end">Bs {{ number_format($venta->iva_amount, 2) }}</th>
                                </tr>
                                @endif
                                <tr>
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th class="text-end fw-bold">Bs {{ number_format($venta->total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            @if($venta->tipo == 'credito')
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Pagos Recibidos</h5>
                </div>
                <div class="card-body">
                    @if($venta->pagos->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No se han registrado pagos para esta venta.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th class="text-end">Monto</th>
                                        <th>Método</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($venta->pagos as $pago)
                                    <tr>
                                        <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                        <td class="text-end">Bs {{ number_format($pago->monto, 2) }}</td>
                                        <td>
                                            @if($pago->metodo_pago == 'transferencia')
                                                <span class="badge bg-info">Transferencia</span>
                                            @else
                                                <span class="badge bg-success">Efectivo</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="1" class="text-end">Total Pagado:</th>
                                        <th class="text-end">Bs {{ number_format($venta->pagos->sum('monto'), 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="1" class="text-end">Saldo Pendiente:</th>
                                        <th class="text-end fw-bold {{ $venta->total - $venta->pagos->sum('monto') > 0 ? 'text-danger' : 'text-success' }}">
                                            Bs {{ number_format($venta->total - $venta->pagos->sum('monto'), 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection