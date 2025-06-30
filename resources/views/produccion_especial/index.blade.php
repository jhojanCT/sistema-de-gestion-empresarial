@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Producciones Especiales</h1>
    <a href="{{ route('produccion-especial.create') }}" class="btn btn-primary mb-3">Registrar Producción Especial</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Materia Prima Sin Filtrar</th>
                <th>Producto</th>
                <th>Cant. Utilizada</th>
                <th>Cant. Producida</th>
                <th>Costo Producción</th>
                <th>Fecha</th>
                <th>Observaciones</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($producciones as $prod)
                <tr>
                    <td>{{ $prod->id }}</td>
                    <td>{{ $prod->materiaPrimaSinFiltrar->nombre }}</td>
                    <td>{{ $prod->producto->nombre }}</td>
                    <td>{{ $prod->cantidad_utilizada }}</td>
                    <td>{{ $prod->cantidad_producida }}</td>
                    <td>{{ $prod->costo_produccion }}</td>
                    <td>{{ $prod->fecha }}</td>
                    <td>{{ $prod->observaciones }}</td>
                    <td>
                        <a href="{{ route('produccion-especial.edit', $prod) }}" class="btn btn-primary btn-sm">Editar</a>
                        <form action="{{ route('produccion-especial.destroy', $prod) }}" method="POST" style="display:inline-block">
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