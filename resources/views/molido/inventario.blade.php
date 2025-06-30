@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Inventario de Materia Prima Molida</h1>
    <a href="{{ route('molido.molida.create') }}" class="btn btn-success mb-3">Agregar Materia Prima Molida</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Materia Prima Filtrada</th>
                <th>Cantidad</th>
                <th>Fecha de Molido</th>
                <th>Observaciones</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventario as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->materiaPrimaFiltrada->nombre }}</td>
                    <td>{{ $item->cantidad }}</td>
                    <td>{{ $item->fecha_molido }}</td>
                    <td>{{ $item->observaciones }}</td>
                    <td>
                        <a href="{{ route('molido.molida.edit', $item) }}" class="btn btn-primary btn-sm">Editar</a>
                        <form action="{{ route('molido.molida.destroy', $item) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este registro?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 