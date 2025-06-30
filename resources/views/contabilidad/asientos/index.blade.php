@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-file-invoice"></i> Asientos Contables
                    </h3>
                    <div class="btn-group">
                        <a href="{{ route('contabilidad.asientos.create') }}" class="btn btn-light">
                            <i class="fas fa-plus"></i> Nuevo Asiento
                        </a>
                        <a href="{{ route('contabilidad.asientos.pendientes') }}" class="btn btn-light">
                            <i class="fas fa-clock"></i> Pendientes
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form action="{{ route('contabilidad.asientos.index') }}" method="GET" id="filterForm">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchInput" name="search" placeholder="Buscar..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="tipoFilter" name="tipoFilter">
                                    <option value="">Todos los tipos</option>
                                    <option value="diario" {{ request('tipoFilter') == 'diario' ? 'selected' : '' }}>Asiento Diario</option>
                                    <option value="ajuste" {{ request('tipoFilter') == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                                    <option value="cierre" {{ request('tipoFilter') == 'cierre' ? 'selected' : '' }}>Cierre</option>
                                    <option value="venta" {{ request('tipoFilter') == 'venta' ? 'selected' : '' }}>Venta</option>
                                    <option value="compra" {{ request('tipoFilter') == 'compra' ? 'selected' : '' }}>Compra</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" placeholder="Fecha inicio" value="{{ request('fechaInicio') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" class="form-control" id="fechaFin" name="fechaFin" placeholder="Fecha fin" value="{{ request('fechaFin') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Aplicar Filtros
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">
                                    <i class="fas fa-times"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de Asientos -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Concepto</th>
                                    <th>Documento</th>
                                    <th>Debe</th>
                                    <th>Haber</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asientos as $asiento)
                                    <tr>
                                    <td>{{ $asiento->id }}</td>
                                        <td>{{ $asiento->fecha->format('d/m/Y') }}</td>
                                        <td>
                                        <span class="badge bg-{{ $asiento->tipo_operacion === 'venta' ? 'success' : ($asiento->tipo_operacion === 'compra' ? 'primary' : 'info') }}">
                                            {{ ucfirst($asiento->tipo_operacion) }}
                                            </span>
                                        </td>
                                    <td>{{ $asiento->concepto }}</td>
                                        <td>
                                        @if($asiento->tipo_documento)
                                            {{ strtoupper($asiento->tipo_documento) }} {{ $asiento->numero_documento }}
                                            @else
                                            -
                                            @endif
                                        </td>
                                    <td class="text-end">{{ number_format($asiento->total_debe, 2) }}</td>
                                    <td class="text-end">{{ number_format($asiento->total_haber, 2) }}</td>
                                        <td>
                                        <span class="badge bg-{{ $asiento->estado === 'aprobado' ? 'success' : 'warning' }}">
                                            {{ ucfirst($asiento->estado) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                            <a href="{{ route('contabilidad.asientos.show', $asiento) }}" 
                                               class="btn btn-sm btn-info" title="Ver Detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($asiento->estado === 'borrador')
                                                <a href="{{ route('contabilidad.asientos.edit', $asiento) }}" 
                                                   class="btn btn-sm btn-warning" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <form action="{{ route('contabilidad.asientos.destroy', $asiento) }}" 
                                                      method="POST" class="d-inline">
                                                        @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            title="Eliminar" onclick="return confirm('¿Está seguro?')">
                                                        <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No hay asientos contables registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-end mt-3">
                            {{ $asientos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tipoFilter = document.getElementById('tipoFilter');
        const fechaInicio = document.getElementById('fechaInicio');
        const fechaFin = document.getElementById('fechaFin');
        const filterForm = document.getElementById('filterForm');

        function aplicarFiltros() {
            // La lógica de filtrado ahora la maneja el servidor
            // Simplemente enviamos el formulario
            filterForm.submit();
        }

        function limpiarFiltros() {
            searchInput.value = '';
            tipoFilter.value = '';
            fechaInicio.value = '';
            fechaFin.value = '';
            // Enviar el formulario después de limpiar para recargar la página sin filtros
            filterForm.submit();
        }

        // Los eventos de cambio en los filtros ya no necesitan llamar a aplicarFiltros directamente
        // El botón "Aplicar Filtros" es de tipo submit por defecto, lo que envía el formulario
        // Se mantiene la función limpiarFiltros para el botón Limpiar.

    });
</script>
@endpush 
@endsection 