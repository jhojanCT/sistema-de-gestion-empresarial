@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Cierres Anuales</h5>
                    <a href="{{ route('contabilidad.cierres.anual.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Cierre Anual
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filtro de Año -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <form action="{{ route('contabilidad.cierres.anual.index') }}" method="GET" class="form-inline">
                                <div class="input-group">
                                    <input type="number" name="anio" class="form-control" placeholder="Año" value="{{ request('anio') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Resumen de Totales -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light border-primary">
                                <div class="card-body py-2 px-3">
                                    <span class="fw-bold text-primary">Total Activos</span>
                                    <div class="h5 mb-0">{{ number_format($cierres->sum('total_activos'), 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-danger">
                                <div class="card-body py-2 px-3">
                                    <span class="fw-bold text-danger">Total Pasivos</span>
                                    <div class="h5 mb-0">{{ number_format($cierres->sum('total_pasivos'), 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-success">
                                <div class="card-body py-2 px-3">
                                    <span class="fw-bold text-success">Total Patrimonio</span>
                                    <div class="h5 mb-0">{{ number_format($cierres->sum('total_patrimonio'), 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-info">
                                <div class="card-body py-2 px-3">
                                    <span class="fw-bold text-info">Utilidad Neta</span>
                                    <div class="h5 mb-0">{{ number_format($cierres->sum('utilidad_neta'), 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Año</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Total Activos</th>
                                    <th>Total Pasivos</th>
                                    <th>Total Patrimonio</th>
                                    <th>Utilidad Neta</th>
                                    <th>Estado</th>
                                    <th>Usuario</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cierres as $cierre)
                                <tr>
                                    <td>{{ $cierre->anio }}</td>
                                    <td>{{ $cierre->fecha_inicio->format('d/m/Y') }}</td>
                                    <td>{{ $cierre->fecha_fin->format('d/m/Y') }}</td>
                                    <td class="text-end">{{ number_format($cierre->total_activos, 2) }}</td>
                                    <td class="text-end">{{ number_format($cierre->total_pasivos, 2) }}</td>
                                    <td class="text-end">{{ number_format($cierre->total_patrimonio, 2) }}</td>
                                    <td class="text-end">{{ number_format($cierre->utilidad_neta, 2) }}</td>
                                    <td>
                                        @if($cierre->cerrado)
                                            <span class="badge bg-success">Cerrado</span>
                                        @else
                                            <span class="badge bg-warning">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>{{ $cierre->usuario->name }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('contabilidad.cierres.anual.show', $cierre) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!$cierre->cerrado)
                                                <form action="{{ route('contabilidad.cierres.anual.cerrar', $cierre) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-success"
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
                                    <td colspan="10" class="text-center">No hay cierres anuales registrados</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $cierres->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 