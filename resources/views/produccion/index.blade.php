@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Producción</h1>
        <a href="{{ route('produccion.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Producción
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Materia Prima</th>
                            <th>Producto</th>
                            <th class="text-end">Cantidad Utilizada</th>
                            <th class="text-end">Cantidad Producida</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($producciones as $produccion)
                            <tr>
                                <td>{{ $produccion->fecha->format('d/m/Y') }}</td>
                                <td>{{ $produccion->materiaPrimaFiltrada->nombre }}</td>
                                <td>{{ $produccion->producto->nombre }}</td>
                                <td class="text-end">
                                    {{ number_format($produccion->cantidad_utilizada, 2) }} {{ $produccion->materiaPrimaFiltrada->unidad_medida }}
                                </td>
                                <td class="text-end">
                                    {{ number_format($produccion->cantidad_producida, 2) }} unidades
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('produccion.show', $produccion) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('produccion.edit', $produccion) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('produccion.destroy', $produccion) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar esta producción?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-industry fa-3x mb-3"></i>
                                        <p>No hay producciones registradas</p>
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