@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-industry me-2"></i>Reporte de Producción</h4>
        </div>
        
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.produccion') }}" class="mb-4">
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
                        <label for="producto_id" class="form-label">
                            <i class="fas fa-box me-1"></i>Producto:
                        </label>
                        <select name="producto_id" id="producto_id" class="form-control">
                            <option value="">Todos los productos</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id }}" {{ request('producto_id') == $producto->id ? 'selected' : '' }}>
                                    {{ $producto->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Filtrar
                        </button>
                        <a href="{{ route('reportes.produccion') }}" class="btn btn-secondary">
                            <i class="fas fa-undo me-1"></i>Limpiar
                        </a>
                    </div>
                </div>
            </form>
            
            @if($producciones->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-chart-pie me-2"></i>Resumen
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h5 class="text-primary mb-2">Materia Prima Usada</h5>
                                <p class="mb-0 fs-4">
                                    {{ number_format($totalMateriaPrima, 2, ',', '.') }}
                                    <small class="text-muted">{{ $unidadMateriaPrima }}</small>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h5 class="text-success mb-2">Productos Producidos</h5>
                                <p class="mb-0 fs-4">
                                    {{ number_format($totalProductos, 2, ',', '.') }}
                                    <small class="text-muted">{{ $unidadProducto }}</small>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h5 class="text-warning mb-2">Costo Promedio</h5>
                                <p class="mb-0 fs-4">
                                    <span class="text-muted">Bs</span> 
                                    {{ number_format($costoPromedio, 2, ',', '.') }}
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
                            <th>Materia Prima</th>
                            <th>Producto</th>
                            <th>Cantidad Usada</th>
                            <th>Cantidad Producida</th>
                            <th>Costo Unitario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($producciones as $produccion)
                        <tr>
                            <td>
                                <i class="fas fa-calendar-day text-primary me-1"></i>
                                {{ $produccion->fecha->format('d/m/Y') }}
                            </td>
                            <td>{{ $produccion->materiaPrima->nombre }}</td>
                            <td>{{ $produccion->producto->nombre }}</td>
                            <td class="text-end">
                                {{ number_format($produccion->cantidad_usada, 2, ',', '.') }}
                                <small class="text-muted">{{ $produccion->materiaPrima->unidad }}</small>
                            </td>
                            <td class="text-end">
                                {{ number_format($produccion->cantidad_producida, 2, ',', '.') }}
                                <small class="text-muted">{{ $produccion->producto->unidad }}</small>
                            </td>
                            <td class="text-end">
                                <span class="text-muted">Bs</span> 
                                {{ number_format($produccion->costo_unitario, 2, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('produccion.show', $produccion->id) }}" 
                                   class="btn btn-sm btn-info" 
                                   data-bs-toggle="tooltip" 
                                   title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
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