@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Materias Primas Filtradas</h1>
        <a href="{{ route('materia-prima-filtrada.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Materia Prima Filtrada
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Unidad de Medida</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materiasPrimas as $materia)
                <tr>
                    <td>{{ $materia->nombre }}</td>
                    <td>{{ $materia->unidad_medida }}</td>
                    <td>{{ $materia->stock }}</td>
                    <td>
                        <a href="{{ route('materia-prima-filtrada.edit', $materia) }}" 
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection