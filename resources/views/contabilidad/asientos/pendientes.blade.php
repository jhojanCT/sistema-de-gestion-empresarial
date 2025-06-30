@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-file-invoice"></i> Documentos Pendientes
                    </h3>
                    <div class="btn-group">
                        <a href="{{ route('contabilidad.asientos.pendientes') }}" class="btn btn-light {{ request()->routeIs('contabilidad.asientos.pendientes') ? 'active' : '' }}">
                            <i class="fas fa-file-invoice"></i> Pendientes de Asiento
                        </a>
                        <a href="{{ route('contabilidad.asientos.pendientes-pago') }}" class="btn btn-light {{ request()->routeIs('contabilidad.asientos.pendientes-pago') ? 'active' : '' }}">
                            <i class="fas fa-money-bill"></i> Pendientes de Pago
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(request()->routeIs('contabilidad.asientos.pendientes'))
                        <!-- Filtros para documentos pendientes de asiento -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchInput" placeholder="Buscar...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="tipoFilter">
                                    <option value="">Todos los tipos</option>
                                    <option value="venta">Ventas</option>
                                    <option value="compra">Compras</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="fechaFilter" placeholder="Filtrar por fecha">
                            </div>
                        </div>

                        <!-- Ventas Pendientes -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">
                                    <i class="fas fa-shopping-cart"></i> Ventas Pendientes
                                    <span class="badge bg-primary">{{ $ventasPendientes->count() }}</span>
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Fecha</th>
                                                <th>Cliente</th>
                                                <th>Tipo</th>
                                                <th>Subtotal</th>
                                                <th>IVA</th>
                                                <th>Total</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($ventasPendientes as $venta)
                                            <tr>
                                                <td>{{ $venta->id }}</td>
                                                <td>{{ $venta->fecha->format('d/m/Y') }}</td>
                                                <td>{{ $venta->cliente ? $venta->cliente->nombre : 'Cliente Final' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $venta->tipo === 'contado' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($venta->tipo) }}
                                                    </span>
                                                </td>
                                                <td class="text-end">{{ number_format($venta->subtotal, 2) }}</td>
                                                <td class="text-end">{{ number_format($venta->iva_amount, 2) }}</td>
                                                <td class="text-end">{{ number_format($venta->total, 2) }}</td>
                                                <td>
                                                    <form action="{{ route('contabilidad.asientos.generar-pendiente') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="tipo" value="venta">
                                                        <input type="hidden" name="id" value="{{ $venta->id }}">
                                                        <input type="hidden" name="auto_asiento" value="1">
                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-magic"></i> Asiento Automático
                                                        </button>
                                                    </form>
                                                    <a href="{{ route('contabilidad.asientos.create', ['tipo' => 'venta', 'id' => $venta->id]) }}" class="btn btn-secondary btn-sm">
                                                        <i class="fas fa-edit"></i> Asiento Manual
                                                    </a>
                                                    <a href="{{ route('ventas.show', $venta) }}" class="btn btn-info btn-sm" title="Ver Detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No hay ventas pendientes de asiento contable</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Compras Pendientes -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">
                                    <i class="fas fa-truck"></i> Compras Pendientes
                                    <span class="badge bg-primary">{{ $comprasPendientes->count() }}</span>
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Fecha</th>
                                                <th>Proveedor</th>
                                                <th>Tipo</th>
                                                <th>Subtotal</th>
                                                <th>IVA</th>
                                                <th>Total</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($comprasPendientes as $compra)
                                            @if($compra->tipo === 'contado') // Solo mostrar compras al contado
                                            <tr>
                                                <td>{{ $compra->id }}</td>
                                                <td>{{ $compra->fecha->format('d/m/Y') }}</td>
                                                <td>{{ $compra->proveedor->nombre }}</td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        {{ ucfirst($compra->tipo) }}
                                                    </span>
                                                </td>
                                                <td class="text-end">{{ number_format($compra->subtotal, 2) }}</td>
                                                <td class="text-end">{{ number_format($compra->iva_amount, 2) }}</td>
                                                <td class="text-end">{{ number_format($compra->total, 2) }}</td>
                                                <td>
                                                    <form action="{{ route('contabilidad.asientos.generar-pendiente') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="tipo" value="compra">
                                                        <input type="hidden" name="id" value="{{ $compra->id }}">
                                                        <input type="hidden" name="auto_asiento" value="1">
                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-magic"></i> Asiento Automático
                                                        </button>
                                                    </form>
                                                    <a href="{{ route('contabilidad.asientos.create', ['tipo' => 'compra', 'id' => $compra->id]) }}" class="btn btn-secondary btn-sm">
                                                        <i class="fas fa-edit"></i> Asiento Manual
                                                    </a>
                                                    <a href="{{ route('compras.show', $compra) }}" class="btn btn-info btn-sm" title="Ver Detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            @endif
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No hay compras pendientes de asiento contable</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Vista para documentos pendientes de pago -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Concepto</th>
                                        <th>Monto Total</th>
                                        <th>Saldo Pendiente</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($asientos as $asiento)
                                    <tr>
                                        <td>{{ $asiento->id }}</td>
                                        <td>{{ $asiento->fecha->format('d/m/Y') }}</td>
                                        <td>{{ ucfirst($asiento->tipo_operacion) }}</td>
                                        <td>{{ $asiento->concepto }}</td>
                                        <td class="text-end">{{ number_format($asiento->monto_total, 2) }}</td>
                                        <td class="text-end">{{ number_format($asiento->saldo_pendiente, 2) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('contabilidad.asientos.show', $asiento) }}" class="btn btn-sm btn-info" title="Ver Detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-success" title="Registrar Pago" data-bs-toggle="modal" data-bs-target="#pagoModal{{ $asiento->id }}">
                                                    <i class="fas fa-money-bill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No hay documentos pendientes de pago</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(request()->routeIs('contabilidad.asientos.pendientes-pago'))
    @foreach($asientos as $asiento)
    <!-- Modal para registrar pago -->
    <div class="modal fade" id="pagoModal{{ $asiento->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('contabilidad.asientos.registrar-pago', $asiento) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Fecha de Pago</label>
                            <input type="date" class="form-control" name="fecha" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Monto</label>
                            <input type="number" class="form-control" name="monto" max="{{ $asiento->saldo_pendiente }}" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Concepto</label>
                            <input type="text" class="form-control" name="concepto" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar Pago</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad de búsqueda
    const searchInput = document.getElementById('searchInput');
    const tipoFilter = document.getElementById('tipoFilter');
    const fechaFilter = document.getElementById('fechaFilter');
    const tables = document.querySelectorAll('table');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const tipoValue = tipoFilter.value.toLowerCase();
        const fechaValue = fechaFilter.value;

        tables.forEach(table => {
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const tipo = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                const fecha = row.querySelector('td:nth-child(2)').getAttribute('data-date');

                const matchesSearch = text.includes(searchTerm);
                const matchesTipo = !tipoValue || tipo.includes(tipoValue);
                const matchesFecha = !fechaValue || fecha === fechaValue;

                row.style.display = matchesSearch && matchesTipo && matchesFecha ? '' : 'none';
            });
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (tipoFilter) tipoFilter.addEventListener('change', filterTable);
    if (fechaFilter) fechaFilter.addEventListener('change', filterTable);
});
</script>
@endpush

@push('styles')
<style>
    .dropdown-menu {
        z-index: 9999 !important;
    }
    .table-responsive {
        overflow: visible !important;
    }
</style>
@endpush
@endsection