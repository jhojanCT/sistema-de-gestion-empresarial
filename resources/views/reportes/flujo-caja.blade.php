@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-chart-line me-2"></i>Reporte de Flujo de Caja</h4>
        </div>
        
        <div class="card-body">
            <form method="GET" action="{{ route('reporte.flujo-caja') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="fecha_inicio" class="form-label">
                            <i class="fas fa-calendar me-1"></i>Fecha Inicio:
                        </label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_fin" class="form-label">
                            <i class="fas fa-calendar-alt me-1"></i>Fecha Fin:
                        </label>
                        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Filtrar
                        </button>
                        <a href="{{ route('reporte.flujo-caja') }}" class="btn btn-secondary">
                            <i class="fas fa-undo me-1"></i>Limpiar
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-dark">
                        <tr class="text-center">
                            <th>Fecha</th>
                            <th>Saldo Inicial</th>
                            <th>Ingresos</th>
                            <th>Egresos</th>
                            <th>Saldo Final</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cierres as $cierre)
                        <tr>
                            <td class="text-center">
                                <i class="fas fa-calendar-day text-primary me-1"></i>
                                {{ $cierre->fecha->format('d/m/Y') }}
                            </td>
                            <td class="text-end">
                                <span class="text-muted">Bs</span> 
                                {{ number_format($cierre->saldo_inicial, 2, ',', '.') }}
                            </td>
                            <td class="text-end">
                                <span class="text-success">
                                    <span class="text-muted">Bs</span> 
                                    {{ number_format($cierre->ventas_contado + $cierre->pagos_clientes + $cierre->ingresos_bancarios, 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="text-danger">
                                    <span class="text-muted">Bs</span> 
                                    {{ number_format($cierre->compras_contado + $cierre->pagos_proveedores + $cierre->egresos_bancarios, 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <strong>
                                    <span class="text-muted">Bs</span> 
                                    {{ number_format($cierre->saldo_final, 2, ',', '.') }}
                                </strong>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('cierre.show', $cierre->id) }}" 
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