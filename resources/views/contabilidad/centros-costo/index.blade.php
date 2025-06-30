@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-dark">Centros de Costo</h3>
                    <div class="card-tools">
                        <a href="{{ route('contabilidad.centros-costo.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nuevo Centro de Costo
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-dark">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Presupuesto Mensual</th>
                                    <th>Gasto Actual</th>
                                    <th>% Ejecución</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($centrosCosto->whereNull('centro_costo_padre_id') as $centro)
                                    <tr class="bg-light">
                                        <td><strong>{{ $centro->codigo }}</strong></td>
                                        <td><strong>{{ $centro->nombre }}</strong></td>
                                        <td>
                                            <span class="badge {{ $centro->tipo == 'PRODUCCION' ? 'bg-primary' : ($centro->tipo == 'SERVICIO' ? 'bg-success' : 'bg-info') }}">
                                                {{ $centro->tipo }}
                                            </span>
                                        </td>
                                        <td class="text-right">Bs {{ number_format($centro->presupuesto_mensual, 2) }}</td>
                                        <td class="text-right">Bs {{ number_format($centro->getTotalGastadoMesActual(), 2) }}</td>
                                        <td class="text-right">
                                            @php
                                                $porcentaje = $centro->getPorcentajeEjecucionMesActual();
                                                $colorClass = $porcentaje > 100 ? 'text-danger' : ($porcentaje >= 80 ? 'text-warning' : 'text-success');
                                            @endphp
                                            <span class="{{ $colorClass }}">
                                                {{ number_format($porcentaje, 1) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $centro->activo ? 'bg-success' : 'bg-danger' }}">
                                                {{ $centro->activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('contabilidad.centros-costo.show', $centro) }}" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('contabilidad.centros-costo.edit', $centro) }}" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('contabilidad.centros-costo.destroy', $centro) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar" 
                                                            onclick="return confirm('¿Está seguro de eliminar este centro de costo?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @foreach($centro->hijos as $subcentro)
                                        <tr>
                                            <td class="pl-4">{{ $subcentro->codigo }}</td>
                                            <td class="pl-4">{{ $subcentro->nombre }}</td>
                                            <td>
                                                <span class="badge {{ $subcentro->tipo == 'PRODUCCION' ? 'bg-primary' : ($subcentro->tipo == 'SERVICIO' ? 'bg-success' : 'bg-info') }}">
                                                    {{ $subcentro->tipo }}
                                                </span>
                                            </td>
                                            <td class="text-right">Bs {{ number_format($subcentro->presupuesto_mensual, 2) }}</td>
                                            <td class="text-right">Bs {{ number_format($subcentro->getTotalGastadoMesActual(), 2) }}</td>
                                            <td class="text-right">
                                                @php
                                                    $porcentaje = $subcentro->getPorcentajeEjecucionMesActual();
                                                    $colorClass = $porcentaje > 100 ? 'text-danger' : ($porcentaje >= 80 ? 'text-warning' : 'text-success');
                                                @endphp
                                                <span class="{{ $colorClass }}">
                                                    {{ number_format($porcentaje, 1) }}%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $subcentro->activo ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $subcentro->activo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('contabilidad.centros-costo.show', $subcentro) }}" class="btn btn-info btn-sm" title="Ver">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('contabilidad.centros-costo.edit', $subcentro) }}" class="btn btn-warning btn-sm" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('contabilidad.centros-costo.destroy', $subcentro) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar" 
                                                                onclick="return confirm('¿Está seguro de eliminar este centro de costo?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .pl-4 {
        padding-left: 2rem !important;
    }
    .table td {
        vertical-align: middle;
    }
</style>
@endpush 