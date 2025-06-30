@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Asiento Contable #{{ $asiento->numero_asiento }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('contabilidad.asientos.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        @if($asiento->estado === 'borrador')
                            <a href="{{ route('contabilidad.asientos.edit', $asiento) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha</label>
                                <p class="form-control-static">{{ $asiento->fecha->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tipo</label>
                                <p class="form-control-static">
                                    @switch($asiento->tipo)
                                        @case('diario')
                                            <span class="badge badge-info">Diario</span>
                                            @break
                                        @case('ajuste')
                                            <span class="badge badge-warning">Ajuste</span>
                                            @break
                                        @case('cierre')
                                            <span class="badge badge-danger">Cierre</span>
                                            @break
                                    @endswitch
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Centro de Costo</label>
                                <p class="form-control-static">
                                    @if($asiento->centroCosto)
                                        {{ $asiento->centroCosto->codigo }} - {{ $asiento->centroCosto->nombre }}
                                    @else
                                        <span class="text-muted">No asignado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Estado</label>
                                <p class="form-control-static">
                                    @switch($asiento->estado)
                                        @case('borrador')
                                            <span class="badge badge-secondary">Borrador</span>
                                            @break
                                        @case('APROBADO')
                                            <span class="badge badge-success">Aprobado</span>
                                            @break
                                        @case('ANULADO')
                                            <span class="badge badge-danger">Anulado</span>
                                            @break
                                    @endswitch
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tipo de Operación</label>
                                <p class="form-control-static">
                                    @if($asiento->tipo_documento === 'VENTA' || $asiento->tipo_documento === 'COMPRA')
                                        <span class="badge {{ $asiento->tipo_operacion === 'venta_credito' || $asiento->tipo_operacion === 'compra_credito' ? 'bg-danger' : 'bg-primary' }}">
                                            {{ strtoupper($asiento->tipo_operacion === 'venta_credito' || $asiento->tipo_operacion === 'compra_credito' ? 'CRÉDITO' : 'CONTADO') }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Concepto</label>
                                <p class="form-control-static">{{ $asiento->concepto }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4>Detalles del Asiento</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Cuenta</th>
                                            <th>Tipo</th>
                                            <th class="text-right">Debe</th>
                                            <th class="text-right">Haber</th>
                                            <th>Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($asiento->detalles as $detalle)
                                            <tr>
                                                <td>
                                                    @if($detalle->cuenta)
                                                        {{ $detalle->cuenta->codigo }} - {{ $detalle->cuenta->nombre }}
                                                        @if(str_contains(strtolower($detalle->cuenta->nombre), 'banco'))
                                                            <span class="badge bg-info">Banco</span>
                                                        @elseif(str_contains(strtolower($detalle->cuenta->nombre), 'caja'))
                                                            <span class="badge bg-secondary">Caja</span>
                                                        @endif
                                                    @else
                                                        <span class="text-danger">Cuenta no encontrada</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($detalle->debe > 0)
                                                        <span class="badge bg-primary text-white" style="font-size: 0.9em; padding: 6px 10px;">Debe</span>
                                                    @else
                                                        <span class="badge bg-info text-white" style="font-size: 0.9em; padding: 6px 10px;">Haber</span>
                                                    @endif
                                                </td>
                                                <td class="text-right">{{ number_format($detalle->debe, 2) }}</td>
                                                <td class="text-right">{{ number_format($detalle->haber, 2) }}</td>
                                                <td>
                                                    {{ $detalle->descripcion }}
                                                    @if($asiento->numero_documento)
                                                        <br><small class="text-muted">Doc: #{{ $asiento->numero_documento }}</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="text-right"><strong>Totales:</strong></td>
                                            <td class="text-right"><strong>{{ number_format($totales['debe'], 2) }}</strong></td>
                                            <td class="text-right"><strong>{{ number_format($totales['haber'], 2) }}</strong></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($asiento->estado === 'borrador')
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <form action="{{ route('contabilidad.asientos.publicar', $asiento) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success" onclick="return confirm('¿Está seguro de aprobar este asiento?')">
                                        <i class="fas fa-check"></i> Aprobar Asiento
                                    </button>
                                </form>

                                <form action="{{ route('contabilidad.asientos.anular', $asiento) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de anular este asiento?')">
                                        <i class="fas fa-times"></i> Anular Asiento
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 