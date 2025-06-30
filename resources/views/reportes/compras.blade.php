@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Reporte de Compras</h4>
        </div>
        
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.compras') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="fecha_inicio" class="form-label">
                            <i class="fas fa-calendar me-1"></i>Fecha Inicio:
                        </label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_fin" class="form-label">
                            <i class="fas fa-calendar-alt me-1"></i>Fecha Fin:
                        </label>
                        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="tipo" class="form-label">
                            <i class="fas fa-tag me-1"></i>Tipo:
                        </label>
                        <select name="tipo" id="tipo" class="form-control">
                            <option value="">Todos</option>
                            <option value="contado" {{ request('tipo') == 'contado' ? 'selected' : '' }}>Contado</option>
                            <option value="credito" {{ request('tipo') == 'credito' ? 'selected' : '' }}>Crédito</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Filtrar
                        </button>
                        <a href="{{ route('reportes.compras') }}" class="btn btn-secondary">
                            <i class="fas fa-undo me-1"></i>Limpiar
                        </a>
                    </div>
                </div>
            </form>
            
            @if($compras->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-chart-pie me-2"></i>Resumen
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h5 class="text-primary mb-2">Total Compras</h5>
                                <p class="mb-0 fs-4">
                                    <span class="text-muted">Bs</span> 
                                    {{ number_format($totalCompras, 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h5 class="text-success mb-2">Compras al Contado</h5>
                                <p class="mb-0 fs-4">
                                    <span class="text-muted">Bs</span> 
                                    {{ number_format($comprasContado, 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h5 class="text-warning mb-2">Compras a Crédito</h5>
                                <p class="mb-0 fs-4">
                                    <span class="text-muted">Bs</span> 
                                    {{ number_format($comprasCredito, 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Tipo</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($compras as $compra)
                        <tr>
                            <td>
                                <i class="fas fa-calendar-day text-primary me-1"></i>
                                {{ $compra->fecha->format('d/m/Y') }}
                            </td>
                            <td>{{ $compra->proveedor->nombre }}</td>
                            <td>
                                @if($compra->tipo == 'contado')
                                    <span class="badge bg-success">Contado</span>
                                @else
                                    <span class="badge bg-warning">Crédito</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="text-muted">Bs</span> 
                                {{ number_format($compra->total, 2, ',', '.') }}
                            </td>
                            <td>
                                @if($compra->tipo == 'contado' || $compra->pagada)
                                    <span class="badge bg-success">Pagada</span>
                                @else
                                    <span class="badge bg-warning">Pendiente</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('compras.show', $compra->id) }}" 
                                   class="btn btn-sm btn-info" 
                                   data-bs-toggle="tooltip" 
                                   title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No se encontraron registros para el rango de fechas seleccionado.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function(){
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush

@endsection