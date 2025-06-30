@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Detalles del Empleado</h3>
                        <div>
                            @can('editar-empleados')
                            <a href="{{ route('empleados.edit', $empleado) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            @endcan
                            <a href="{{ route('empleados.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">ID</th>
                                    <td>{{ $empleado->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nombre</th>
                                    <td>{{ $empleado->nombre }}</td>
                                </tr>
                                <tr>
                                    <th>Apellido</th>
                                    <td>{{ $empleado->apellido ?? 'No especificado' }}</td>
                                </tr>
                                <tr>
                                    <th>CI</th>
                                    <td>{{ $empleado->ci ?? 'No especificado' }}</td>
                                </tr>
                                <tr>
                                    <th>Cargo</th>
                                    <td>{{ $empleado->cargo ?? 'No especificado' }}</td>
                                </tr>
                                <tr>
                                    <th>Estado</th>
                                    <td>
                                        @if($empleado->activo)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Fecha de Registro</th>
                                    <td>{{ $empleado->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Última Actualización</th>
                                    <td>{{ $empleado->updated_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Historial de Pagos</h4>
                                </div>
                                <div class="card-body">
                                    @if($empleado->pagosSalario->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Monto</th>
                                                        <th>Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($empleado->pagosSalario as $pago)
                                                        <tr>
                                                            <td>{{ $pago->pagoSalario->fecha_pago->format('d/m/Y') }}</td>
                                                            <td>{{ number_format($pago->monto, 2) }}</td>
                                                            <td>
                                                                @if($pago->pagoSalario->asiento_generado)
                                                                    <span class="badge bg-success">Asiento Generado</span>
                                                                @else
                                                                    <span class="badge bg-warning">Pendiente</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            No hay registros de pagos para este empleado.
                                        </div>
                                    @endif
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