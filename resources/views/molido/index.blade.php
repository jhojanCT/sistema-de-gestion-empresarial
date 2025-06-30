@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Procesos de Molido</h1>
    <a href="{{ route('molido.create') }}" class="btn btn-primary mb-3">Registrar Molido</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Materia Prima Filtrada</th>
                <th>Cant. Entrada</th>
                <th>Cant. Salida</th>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($molidos as $molido)
                <tr>
                    <td>{{ $molido->id }}</td>
                    <td>{{ $molido->materiaPrimaFiltrada->nombre }}</td>
                    <td>{{ $molido->cantidad_entrada }}</td>
                    <td>{{ $molido->cantidad_salida }}</td>
                    <td>{{ $molido->fecha }}</td>
                    <td>{{ $molido->usuario?->name }}</td>
                    <td>
                        <form action="{{ route('molido.destroy', $molido) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este proceso de molido?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 