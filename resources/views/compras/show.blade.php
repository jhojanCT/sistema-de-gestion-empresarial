@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Detalle de Compra #{{ $compra->id }}</h1>
            <a href="{{ route('compras.index') }}" class="btn btn-light">
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
                                <div class="col-md-4 fw-bold">Proveedor:</div>
                                <div class="col-md-8">{{ $compra->proveedor->nombre }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Fecha:</div>
                                <div class="col-md-8">{{ $compra->fecha->format('d/m/Y') }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Tipo:</div>
                                <div class="col-md-8">
                                    @if($compra->tipo == 'contado')
                                        <span class="badge bg-success">Contado</span>
                                    @else
                                        <span class="badge bg-info">Crédito</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Factura:</div>
                                <div class="col-md-8">
                                    @if($compra->has_invoice)
                                        <span class="badge bg-success">Sí - N° {{ $compra->invoice_number }}</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Estado:</div>
                                <div class="col-md-8">
                                    @if($compra->tipo == 'contado' || $compra->pagada)
                                        <span class="badge bg-success">Pagada</span>
                                    @else
                                        <span class="badge bg-warning">Pendiente</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Centro de Costo:</div>
                                <div class="col-md-8">
                                    @if($compra->centro_costo_id)
                                        <span class="badge bg-info">{{ $compra->centroCosto->nombre }}</span>
                                    @else
                                        <span class="badge bg-secondary">No asignado</span>
                                    @endif
                                </div>
                            </div>
                            
                            @if($compra->tipo == 'credito' && !$compra->pagada)
                            <div class="mt-3">
                                <a href="{{ route('pagos.proveedores.create', $compra->id) }}" class="btn btn-success">
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
                                <div class="col-md-6">{{ $compra->items->count() }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 fw-bold">Materias Primas:</div>
                                <div class="col-md-6">{{ $compra->items->where('tipo_item', 'materia_prima')->count() }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 fw-bold">Productos:</div>
                                <div class="col-md-6">{{ $compra->items->where('tipo_item', 'producto')->count() }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 fw-bold">Subtotal:</div>
                                <div class="col-md-6">Bs {{ number_format($compra->subtotal, 2) }}</div>
                            </div>
                            @if($compra->has_invoice)
                            <div class="row mb-2">
                                <div class="col-md-6 fw-bold">IVA (13%):</div>
                                <div class="col-md-6">Bs {{ number_format($compra->iva_amount, 2) }}</div>
                            </div>
                            @endif
                            <div class="row mb-2">
                                <div class="col-md-6 fw-bold">Total:</div>
                                <div class="col-md-6 fw-bold text-primary">Bs {{ number_format($compra->total, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Ítems de Compra</h5>
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
                                @foreach($compra->items as $item)
                                <tr>
                                    <td>
                                        @if($item->tipo_item == 'materia_prima')
                                            <span class="badge bg-secondary">Materia Prima</span>
                                        @else
                                            <span class="badge bg-primary">Producto</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->tipo_item == 'materia_prima')
                                            {{ $item->materiaPrima->nombre }}
                                            <small class="text-muted d-block">Unidad: {{ $item->materiaPrima->unidad_medida }}</small>
                                        @else
                                            {{ $item->producto->nombre }}
                                            <small class="text-muted d-block">Unidad: {{ $item->producto->unidad_medida }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ number_format($item->cantidad, 2) }}
                                        @if($item->tipo_item == 'materia_prima')
                                            <small class="text-muted d-block">{{ $item->materiaPrima->unidad_medida }}</small>
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
                                    <th class="text-end">Bs {{ number_format($compra->subtotal, 2) }}</th>
                                </tr>
                                @if($compra->has_invoice)
                                <tr>
                                    <th colspan="4" class="text-end">IVA (13%):</th>
                                    <th class="text-end">Bs {{ number_format($compra->iva_amount, 2) }}</th>
                                </tr>
                                @endif
                                <tr>
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th class="text-end fw-bold">Bs {{ number_format($compra->total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            @if($compra->tipo == 'credito')
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Pagos Realizados</h5>
                </div>
                <div class="card-body">
                    @if($compra->pagos->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No se han registrado pagos para esta compra.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th class="text-end">Monto</th>
                                        <th>Método</th>
                                        <th>Comprobante</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($compra->pagos as $pago)
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
                                        <td>{{ $pago->comprobante ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="1" class="text-end">Total Pagado:</th>
                                        <th class="text-end">Bs {{ number_format($compra->pagos->sum('monto'), 2) }}</th>
                                        <th colspan="3"></th>
                                    </tr>
                                    <tr>
                                        <th colspan="1" class="text-end">Saldo Pendiente:</th>
                                        <th class="text-end fw-bold {{ $compra->total - $compra->pagos->sum('monto') > 0 ? 'text-danger' : 'text-success' }}">
                                            Bs {{ number_format($compra->total - $compra->pagos->sum('monto'), 2) }}
                                        </th>
                                        <th colspan="3"></th>
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