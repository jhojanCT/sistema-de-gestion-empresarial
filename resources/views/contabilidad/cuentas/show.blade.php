@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detalles de Cuenta Contable</h3>
                    <div class="card-tools">
                        <a href="{{ route('contabilidad.cuentas.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="{{ route('contabilidad.cuentas.edit', $cuenta) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Código</th>
                                    <td>{{ $cuenta->codigo }}</td>
                                </tr>
                                <tr>
                                    <th>Nombre</th>
                                    <td>{{ $cuenta->nombre }}</td>
                                </tr>
                                <tr>
                                    <th>Tipo</th>
                                    <td>{{ $cuenta->tipo }}</td>
                                </tr>
                                <tr>
                                    <th>Naturaleza</th>
                                    <td>{{ $cuenta->naturaleza }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Cuenta Padre</th>
                                    <td>
                                        @if($cuenta->cuentaPadre)
                                            {{ $cuenta->cuentaPadre->codigo }} - {{ $cuenta->cuentaPadre->nombre }}
                                        @else
                                            No tiene cuenta padre
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Nivel</th>
                                    <td>{{ $cuenta->nivel }}</td>
                                </tr>
                                <tr>
                                    <th>Es Centro de Costo</th>
                                    <td>
                                        @if($cuenta->es_centro_costo)
                                            <span class="badge bg-success text-white" style="font-size: 0.9em; padding: 6px 10px;">Sí</span>
                                        @else
                                            <span class="badge bg-secondary text-white" style="font-size: 0.9em; padding: 6px 10px;">No</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Estado</th>
                                    <td>
                                        @if($cuenta->activo)
                                            <span class="badge bg-success text-white" style="font-size: 0.9em; padding: 6px 10px;">Activo</span>
                                        @else
                                            <span class="badge bg-danger text-white" style="font-size: 0.9em; padding: 6px 10px;">Inactivo</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4>Saldo Actual</h4>
                            <h2 class="text-primary">{{ number_format($cuenta->saldo_actual, 2) }}</h2>
                        </div>
                    </div>

                    @if($cuenta->cuentasHijas->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4>Cuentas Hijas</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Tipo</th>
                                            <th>Naturaleza</th>
                                            <th>Saldo</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cuenta->cuentasHijas as $cuentaHija)
                                        <tr>
                                            <td>{{ $cuentaHija->codigo }}</td>
                                            <td>{{ $cuentaHija->nombre }}</td>
                                            <td>{{ $cuentaHija->tipo }}</td>
                                            <td>{{ $cuentaHija->naturaleza }}</td>
                                            <td class="text-right">{{ number_format($cuentaHija->saldo_actual, 2) }}</td>
                                            <td>
                                                @if($cuentaHija->activo)
                                                    <span class="badge bg-success text-white" style="font-size: 0.9em; padding: 6px 10px;">Activo</span>
                                                @else
                                                    <span class="badge bg-danger text-white" style="font-size: 0.9em; padding: 6px 10px;">Inactivo</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($cuenta->detallesAsiento->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4>Últimos Movimientos</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Asiento</th>
                                            <th>Debe</th>
                                            <th>Haber</th>
                                            <th>Saldo</th>
                                            <th>Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cuenta->detallesAsiento->sortByDesc('created_at')->take(10) as $detalle)
                                        <tr>
                                            <td>{{ $detalle->asiento->fecha->format('d/m/Y') }}</td>
                                            <td>{{ $detalle->asiento->numero_asiento }}</td>
                                            <td class="text-right">{{ number_format($detalle->debe, 2) }}</td>
                                            <td class="text-right">{{ number_format($detalle->haber, 2) }}</td>
                                            <td class="text-right">{{ number_format($detalle->saldo, 2) }}</td>
                                            <td>{{ $detalle->descripcion }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 