@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Cierres Diarios</h5>
                    <a href="{{ route('contabilidad.cierres.diario.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Cierre Diario
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form action="{{ route('contabilidad.cierres.diario.index') }}" method="GET" class="d-flex gap-2">
                                <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}" placeholder="Fecha Inicio">
                                <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}" placeholder="Fecha Fin">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Resumen de IVA -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card bg-light border-info mb-2">
                                <div class="card-body py-2 px-3">
                                    <span class="fw-bold text-info">IVA Ventas</span>
                                    <div class="h5 mb-0">{{ number_format($cierres->sum('iva_ventas_contado') + $cierres->sum('iva_ventas_credito'), 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-danger mb-2">
                                <div class="card-body py-2 px-3">
                                    <span class="fw-bold text-danger">IVA Compras</span>
                                    <div class="h5 mb-0">{{ number_format($cierres->sum('iva_compras_contado') + $cierres->sum('iva_compras_credito'), 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-success mb-2">
                                <div class="card-body py-2 px-3">
                                    <span class="fw-bold text-success">IVA a Pagar</span>
                                    <div class="h5 mb-0">
                                        {{ number_format(
                                            ($cierres->sum('iva_ventas_contado') + $cierres->sum('iva_ventas_credito')) -
                                            ($cierres->sum('iva_compras_contado') + $cierres->sum('iva_compras_credito')),
                                            2
                                        ) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-secondary mb-2">
                                <div class="card-body py-2 px-3">
                                    <span class="fw-bold text-secondary">Total Cierres</span>
                                    <div class="h5 mb-0">{{ $cierres->total() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light align-middle">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th class="text-center">Ventas</th>
                                    <th class="text-center">Compras</th>
                                    <th class="text-center">Créditos</th>
                                    <th class="text-center">IVA</th>
                                    <th class="text-end">Saldo Final</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cierres as $cierre)
                                    <tr>
                                        <td>{{ $cierre->fecha->format('d/m/Y') }}</td>
                                        <td>{{ $cierre->usuario->name }}</td>
                                        <td>
                                            <div class="d-flex justify-content-between">
                                                <span>Contado:</span>
                                                <span class="text-success">{{ number_format($cierre->ventas_contado, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Crédito:</span>
                                                <span>{{ number_format($cierre->ventas_credito, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between border-top">
                                                <span>Total:</span>
                                                <span class="fw-bold">{{ number_format($cierre->ventas_contado + $cierre->ventas_credito, 2) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between">
                                                <span>Contado:</span>
                                                <span class="text-danger">{{ number_format($cierre->compras_contado, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Crédito:</span>
                                                <span>{{ number_format($cierre->compras_credito, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between border-top">
                                                <span>Total:</span>
                                                <span class="fw-bold">{{ number_format($cierre->compras_contado + $cierre->compras_credito, 2) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between">
                                                <span>Cobros:</span>
                                                <span class="text-success">{{ number_format($cierre->cobros_credito, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Pagos:</span>
                                                <span class="text-danger">{{ number_format($cierre->pagos_credito, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between border-top">
                                                <span>Neto:</span>
                                                <span class="fw-bold {{ ($cierre->cobros_credito - $cierre->pagos_credito) >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($cierre->cobros_credito - $cierre->pagos_credito, 2) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-info">IVA Ventas:</span>
                                                <span>{{ number_format($cierre->iva_ventas_contado + $cierre->iva_ventas_credito, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-danger">IVA Compras:</span>
                                                <span>{{ number_format($cierre->iva_compras_contado + $cierre->iva_compras_credito, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between border-top">
                                                <span class="fw-bold">IVA a Pagar:</span>
                                                <span class="fw-bold {{ ($cierre->iva_ventas_contado + $cierre->iva_ventas_credito - $cierre->iva_compras_contado - $cierre->iva_compras_credito) >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format(($cierre->iva_ventas_contado + $cierre->iva_ventas_credito) - ($cierre->iva_compras_contado + $cierre->iva_compras_credito), 2) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-end {{ $cierre->saldo_final < 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                            {{ number_format($cierre->saldo_final, 2) }}
                                        </td>
                                        <td class="text-center">
                                            @if($cierre->cerrado)
                                                <span class="badge bg-success"><i class="fas fa-lock"></i> Cerrado</span>
                                            @else
                                                <span class="badge bg-warning text-dark"><i class="fas fa-unlock"></i> Pendiente</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('contabilidad.cierres.diario.show', $cierre) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(!$cierre->cerrado)
                                                    <form action="{{ route('contabilidad.cierres.diario.cerrar', $cierre) }}" 
                                                          method="POST" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-success"
                                                                title="Cerrar período"
                                                                onclick="return confirm('¿Está seguro de cerrar este período?')">
                                                            <i class="fas fa-lock"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No hay cierres diarios registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($cierres->isNotEmpty())
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold">Totales:</td>
                                        <td>
                                            <div class="d-flex justify-content-between">
                                                <span>Contado:</span>
                                                <span class="text-success">{{ number_format($cierres->sum('ventas_contado'), 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Crédito:</span>
                                                <span>{{ number_format($cierres->sum('ventas_credito'), 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between border-top">
                                                <span>Total:</span>
                                                <span class="fw-bold">{{ number_format($cierres->sum('ventas_contado') + $cierres->sum('ventas_credito'), 2) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between">
                                                <span>Contado:</span>
                                                <span class="text-danger">{{ number_format($cierres->sum('compras_contado'), 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Crédito:</span>
                                                <span>{{ number_format($cierres->sum('compras_credito'), 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between border-top">
                                                <span>Total:</span>
                                                <span class="fw-bold">{{ number_format($cierres->sum('compras_contado') + $cierres->sum('compras_credito'), 2) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between">
                                                <span>Cobros:</span>
                                                <span class="text-success">{{ number_format($cierres->sum('cobros_credito'), 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Pagos:</span>
                                                <span class="text-danger">{{ number_format($cierres->sum('pagos_credito'), 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between border-top">
                                                <span>Neto:</span>
                                                <span class="fw-bold {{ ($cierres->sum('cobros_credito') - $cierres->sum('pagos_credito')) >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($cierres->sum('cobros_credito') - $cierres->sum('pagos_credito'), 2) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-info">IVA Ventas:</span>
                                                <span>{{ number_format($cierres->sum('iva_ventas_contado') + $cierres->sum('iva_ventas_credito'), 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-danger">IVA Compras:</span>
                                                <span>{{ number_format($cierres->sum('iva_compras_contado') + $cierres->sum('iva_compras_credito'), 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between border-top">
                                                <span class="fw-bold">IVA a Pagar:</span>
                                                <span class="fw-bold {{ ($cierres->sum('iva_ventas_contado') + $cierres->sum('iva_ventas_credito') - $cierres->sum('iva_compras_contado') - $cierres->sum('iva_compras_credito')) >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format(($cierres->sum('iva_ventas_contado') + $cierres->sum('iva_ventas_credito')) - ($cierres->sum('iva_compras_contado') + $cierres->sum('iva_compras_credito')), 2) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($cierres->sum('saldo_final'), 2) }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    {{ $cierres->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 