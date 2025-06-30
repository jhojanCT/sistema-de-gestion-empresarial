@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-money-bill-wave me-2"></i>Pagos de Salarios
                </h1>
                <div>
                    <a href="{{ route('pagos.salarios.vista-generar-asiento') }}" class="btn btn-success me-2">
                        <i class="fas fa-file-invoice"></i> Generar Asiento Contable
                    </a>
                    <a href="{{ route('pagos.salarios.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Pago de Salarios
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('pagos.salarios.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="fecha_desde" class="form-label">Fecha Desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-3">
                    <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-3">
                    <label for="metodo_pago" class="form-label">Método de Pago</label>
                    <select class="form-select" id="metodo_pago" name="metodo_pago">
                        <option value="">Todos</option>
                        <option value="efectivo" {{ request('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                        <option value="transferencia" {{ request('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                        <option value="cheque" {{ request('metodo_pago') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="estado_asiento" class="form-label">Estado Asiento</label>
                    <select class="form-select" id="estado_asiento" name="estado_asiento">
                        <option value="">Todos</option>
                        <option value="1" {{ request('estado_asiento') == '1' ? 'selected' : '' }}>Generado</option>
                        <option value="0" {{ request('estado_asiento') == '0' ? 'selected' : '' }}>Pendiente</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Buscar
                    </button>
                    <a href="{{ route('pagos.salarios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i>Limpiar Filtros
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pagos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pagos->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Monto Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Bs. {{ number_format($pagos->sum('monto_total'), 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Asientos Generados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pagos->where('asiento_generado', true)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Asientos Pendientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pagos->where('asiento_generado', false)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Pagos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Pagos de Salarios</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Monto Total</th>
                            <th>Método de Pago</th>
                            <th>Comprobante</th>
                            <th>Estado Asiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagos as $pago)
                            <tr>
                                <td>{{ $pago->id }}</td>
                                <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                <td class="text-end">Bs. {{ number_format($pago->monto_total, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $pago->metodo_pago == 'efectivo' ? 'success' : ($pago->metodo_pago == 'transferencia' ? 'primary' : 'info') }}">
                                        {{ ucfirst($pago->metodo_pago) }}
                                    </span>
                                </td>
                                <td>
                                    @if($pago->comprobante)
                                        <a href="{{ asset('storage/' . $pago->comprobante) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-alt"></i> Ver
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($pago->asiento_generado)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Generado
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('pagos.salarios.show', $pago) }}" class="btn btn-sm btn-info" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(!$pago->asiento_generado)
                                            <a href="{{ route('pagos.salarios.edit', $pago) }}" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('pagos.salarios.destroy', $pago) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este pago?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-info-circle me-2"></i>No hay pagos de salarios registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pagos->hasPages())
                <div class="d-flex justify-content-end mt-4">
                    {{ $pagos->links() }}
                </div>
            @endif
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
            order: [[1, 'desc']], // Ordenar por fecha descendente
            pageLength: 10,
            responsive: true
        });
    });
</script>
@endpush
@endsection 