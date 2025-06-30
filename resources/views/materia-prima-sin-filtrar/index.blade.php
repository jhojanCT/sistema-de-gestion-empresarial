@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Materias Primas Sin Filtrar</h1>
        <a href="{{ route('materia-prima-sin-filtrar.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Materia Prima
        </a>
    </div>
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Unidad de Medida</th>
                            <th>Stock</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materiasPrimas as $materia)
                        <tr>
                            <td>{{ $materia->nombre }}</td>
                            <td>{{ $materia->descripcion ?? 'N/A' }}</td>
                            <td>{{ $materia->unidad_medida }}</td>
                            <td>
                                <span class="badge bg-{{ $materia->stock > 0 ? 'success' : 'danger' }}">
                                    {{ $materia->stock }} {{ $materia->unidad_medida }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('materia-prima-sin-filtrar.edit', ['materia_prima_sin_filtrar' => $materia->id]) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('materia-prima-sin-filtrar.destroy', ['materia_prima_sin_filtrar' => $materia->id]) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('¿Está seguro de eliminar esta materia prima?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-box-open fa-2x mb-3"></i>
                                    <p>No hay materias primas registradas</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection