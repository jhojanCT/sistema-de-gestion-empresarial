@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-money-bill-wave me-2"></i>Detalles del Pago de Salarios
                </h1>
                <div>
                    @if(!$pago->asiento_generado)
                        <a href="{{ route('pagos.salarios.edit', $pago) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="{{ route('pagos.salarios.destroy', $pago) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este pago?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger me-2">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('pagos.salarios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información General -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Información General
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Fecha de Pago
                            </div>
                            <div class="h5 mb-3">{{ $pago->fecha_pago->format('d/m/Y') }}</div>

                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Monto Total
                            </div>
                            <div class="h5 mb-3">Bs. {{ number_format($pago->monto_total, 2) }}</div>

                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Método de Pago
                            </div>
                            <div class="h5 mb-3">
                                <span class="badge bg-{{ $pago->metodo_pago == 'efectivo' ? 'success' : ($pago->metodo_pago == 'transferencia' ? 'primary' : 'info') }}">
                                    {{ ucfirst($pago->metodo_pago) }}
                                </span>
                            </div>

                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Comprobante
                            </div>
                            <div class="h5 mb-3">
                                @if($pago->comprobante)
                                    <a href="{{ asset('storage/' . $pago->comprobante) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-alt"></i> Ver Comprobante
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>

                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Observaciones
                            </div>
                            <div class="h5 mb-0">
                                {{ $pago->observaciones ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Contable -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-file-invoice me-2"></i>Información Contable
                    </h6>
                </div>
                <div class="card-body">
                    @if($pago->asientoContable)
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Número de Asiento
                                </div>
                                <div class="h5 mb-3">{{ $pago->asientoContable->numero_asiento }}</div>

                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Estado
                                </div>
                                <div class="h5 mb-3">
                                    <span class="badge bg-{{ $pago->asientoContable->estado === 'APROBADO' ? 'success' : 'warning' }}">
                                        {{ $pago->asientoContable->estado }}
                                    </span>
                                </div>

                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Fecha
                                </div>
                                <div class="h5 mb-3">{{ $pago->asientoContable->fecha->format('d/m/Y') }}</div>

                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Descripción
                                </div>
                                <div class="h5 mb-0">{{ $pago->asientoContable->descripcion }}</div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>No se ha generado el asiento contable para este pago.
                            @if(!$pago->asiento_generado)
                                <div class="mt-3">
                                    <a href="{{ route('pagos.salarios.vista-generar-asiento') }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-file-invoice me-2"></i>Generar Asiento Contable
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Resumen -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-chart-pie me-2"></i>Resumen
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Empleados
                            </div>
                            <div class="h5 mb-3">{{ $pago->detalles->count() }}</div>

                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Promedio por Empleado
                            </div>
                            <div class="h5 mb-3">Bs. {{ number_format($pago->monto_total / $pago->detalles->count(), 2) }}</div>

                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Estado del Pago
                            </div>
                            <div class="h5 mb-0">
                                @if($pago->asiento_generado)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Completado
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock me-1"></i>Pendiente
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles de Pago por Empleado -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users me-2"></i>Detalles de Pago por Empleado
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Empleado</th>
                            <th>Cargo</th>
                            <th>Monto</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pago->detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->id }}</td>
                                <td>{{ $detalle->empleado->nombre }}</td>
                                <td>{{ $detalle->empleado->cargo ?? 'N/A' }}</td>
                                <td class="text-end">Bs. {{ number_format($detalle->monto, 2) }}</td>
                                <td>{{ $detalle->observaciones ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th class="text-end">Bs. {{ number_format($pago->monto_total, 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            },
            order: [[0, 'asc']],
            pageLength: 10,
            responsive: true
        });
    });
</script>
@endpush
@endsection 