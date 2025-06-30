@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-dark">
                        Centro de Costo: {{ $centroCosto->codigo }} - {{ $centroCosto->nombre }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('contabilidad.centros-costo.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="{{ route('contabilidad.centros-costo.edit', $centroCosto) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Información General -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">Información General</h5>
                                </div>
                                <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                            <th width="30%">Código</th>
                                    <td>{{ $centroCosto->codigo }}</td>
                                </tr>
                                <tr>
                                    <th>Nombre</th>
                                    <td>{{ $centroCosto->nombre }}</td>
                                </tr>
                                        <tr>
                                            <th>Tipo</th>
                                            <td>
                                                <span class="badge {{ $centroCosto->tipo == 'PRODUCCION' ? 'bg-primary' : ($centroCosto->tipo == 'SERVICIO' ? 'bg-success' : 'bg-info') }}">
                                                    {{ $centroCosto->tipo }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Descripción</th>
                                            <td>{{ $centroCosto->descripcion }}</td>
                                        </tr>
                                <tr>
                                    <th>Estado</th>
                                    <td>
                                                <span class="badge {{ $centroCosto->activo ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $centroCosto->activo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Centro Padre</th>
                                            <td>
                                                @if($centroCosto->padre)
                                                    <a href="{{ route('contabilidad.centros-costo.show', $centroCosto->padre) }}">
                                                        {{ $centroCosto->padre->codigo }} - {{ $centroCosto->padre->nombre }}
                                                    </a>
                                        @else
                                                    <span class="text-muted">Centro Principal</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                            </div>
                        </div>

                        <!-- Información Financiera -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0">Información Financiera</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Presupuesto Mensual</span>
                                                    <span class="info-box-number">Bs {{ number_format($centroCosto->presupuesto_mensual, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Gasto Actual</span>
                                                    <span class="info-box-number">Bs {{ number_format($centroCosto->getTotalGastadoMesActual(), 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress mt-3">
                                        @php
                                            $porcentaje = $centroCosto->getPorcentajeEjecucionMesActual();
                                            $colorClass = $porcentaje > 100 ? 'bg-danger' : ($porcentaje >= 80 ? 'bg-warning' : 'bg-success');
                                        @endphp
                                        <div class="progress-bar {{ $colorClass }}" role="progressbar" 
                                             style="width: {{ min($porcentaje, 100) }}%" 
                                             aria-valuenow="{{ $porcentaje }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ number_format($porcentaje, 1) }}%
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <p class="mb-1">Variación Presupuestal:</p>
                                        @php
                                            $variacion = $centroCosto->getVariacionPresupuestalMesActual();
                                            $colorText = $variacion < 0 ? 'text-danger' : 'text-success';
                                        @endphp
                                        <h4 class="{{ $colorText }}">
                                            Bs {{ number_format($variacion, 2) }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subcentros de Costo -->
                    @if($centroCosto->hijos->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="card-title mb-0">Subcentros de Costo</h5>
                                </div>
                                <div class="card-body">
                            <div class="table-responsive">
                                        <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                                    <th>Código</th>
                                                    <th>Nombre</th>
                                                    <th>Presupuesto</th>
                                                    <th>Gasto Actual</th>
                                                    <th>% Ejecución</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                                @foreach($centroCosto->hijos as $hijo)
                                            <tr>
                                                    <td>{{ $hijo->codigo }}</td>
                                                    <td>{{ $hijo->nombre }}</td>
                                                    <td class="text-right">Bs {{ number_format($hijo->presupuesto_mensual, 2) }}</td>
                                                    <td class="text-right">Bs {{ number_format($hijo->getTotalGastadoMesActual(), 2) }}</td>
                                                    <td class="text-right">
                                                        @php
                                                            $porcentaje = $hijo->getPorcentajeEjecucionMesActual();
                                                            $colorClass = $porcentaje > 100 ? 'text-danger' : ($porcentaje >= 80 ? 'text-warning' : 'text-success');
                                                        @endphp
                                                        <span class="{{ $colorClass }}">
                                                            {{ number_format($porcentaje, 1) }}%
                                                        </span>
                                                </td>
                                                    <td>
                                                        <span class="badge {{ $hijo->activo ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $hijo->activo ? 'Activo' : 'Inactivo' }}
                                                        </span>
                                                </td>
                                                <td>
                                                        <a href="{{ route('contabilidad.centros-costo.show', $hijo) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Historial de Movimientos -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="card-title mb-0">Últimos Movimientos</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Asiento</th>
                                                    <th>Concepto</th>
                                                    <th>Debe</th>
                                                    <th>Haber</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($centroCosto->detallesAsiento()->with('asiento')->latest()->take(10)->get() as $detalle)
                                                <tr>
                                                    <td>{{ $detalle->asiento->fecha->format('d/m/Y') }}</td>
                                                    <td>{{ $detalle->asiento->numero_asiento }}</td>
                                                    <td>{{ $detalle->asiento->concepto }}</td>
                                                    <td class="text-right">Bs {{ number_format($detalle->debe, 2) }}</td>
                                                    <td class="text-right">Bs {{ number_format($detalle->haber, 2) }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">No hay movimientos registrados</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 

@push('styles')
<style>
    .info-box {
        min-height: 80px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    .info-box-text {
        display: block;
        font-size: 14px;
        color: #666;
    }
    .info-box-number {
        display: block;
        font-size: 18px;
        font-weight: bold;
    }
    .progress {
        height: 20px;
    }
    .progress-bar {
        line-height: 20px;
    }
</style>
@endpush 