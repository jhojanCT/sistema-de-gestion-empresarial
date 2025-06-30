@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cuentas Contables</h3>
                    <div class="card-tools">
                        <a href="{{ route('contabilidad.cuentas.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nueva Cuenta
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Formulario de búsqueda -->
                    <form action="{{ route('contabilidad.cuentas.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">Buscar</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="Código o nombre...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tipo">Tipo</label>
                                    <select class="form-control" id="tipo" name="tipo">
                                        <option value="">Todos</option>
                                        @foreach($tipos as $tipo)
                                            <option value="{{ $tipo }}" {{ request('tipo') == $tipo ? 'selected' : '' }}>
                                                {{ $tipo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="naturaleza">Naturaleza</label>
                                    <select class="form-control" id="naturaleza" name="naturaleza">
                                        <option value="">Todas</option>
                                        @foreach($naturalezas as $naturaleza)
                                            <option value="{{ $naturaleza }}" {{ request('naturaleza') == $naturaleza ? 'selected' : '' }}>
                                                {{ $naturaleza }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="nivel">Nivel</label>
                                    <select class="form-control" id="nivel" name="nivel">
                                        <option value="">Todos</option>
                                        @foreach($niveles as $nivel)
                                            <option value="{{ $nivel }}" {{ request('nivel') == $nivel ? 'selected' : '' }}>
                                                {{ $nivel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="estado">Estado</label>
                                    <select class="form-control" id="estado" name="estado">
                                        <option value="">Todos</option>
                                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="cuentas-table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Naturaleza</th>
                                    <th>Nivel</th>
                                    <th>Saldo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cuentas as $cuenta)
                                <tr>
                                    <td>{{ $cuenta->codigo }}</td>
                                    <td>{{ $cuenta->nombre }}</td>
                                    <td>
                                        <span class="badge bg-info text-white" style="font-size: 0.9em; padding: 6px 10px;">{{ $cuenta->tipo }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary text-white" style="font-size: 0.9em; padding: 6px 10px;">{{ $cuenta->naturaleza }}</span>
                                    </td>
                                    <td>{{ $cuenta->nivel }}</td>
                                    <td class="text-right">{{ number_format($cuenta->saldo_actual, 2) }}</td>
                                    <td>
                                        @if($cuenta->activo)
                                            <span class="badge bg-success text-white" style="font-size: 0.9em; padding: 6px 10px;">Activo</span>
                                        @else
                                            <span class="badge bg-danger text-white" style="font-size: 0.9em; padding: 6px 10px;">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('contabilidad.cuentas.show', $cuenta) }}" class="btn btn-info btn-sm" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('contabilidad.cuentas.edit', $cuenta) }}" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('contabilidad.cuentas.destroy', $cuenta) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta cuenta?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
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
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#cuentas-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "order": [[0, "asc"]],
            "searching": false,
            "paging": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]]
        });

        $('#tipo, #naturaleza, #nivel, #estado').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    });
</script>
@endpush 