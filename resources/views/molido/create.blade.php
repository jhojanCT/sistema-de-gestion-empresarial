@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registrar Proceso de Molido</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('molido.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="materia_prima_filtrada_id" class="form-label">Materia Prima Filtrada</label>
            <select name="materia_prima_filtrada_id" id="materia_prima_filtrada_id" class="form-control" required>
                <option value="">Seleccione...</option>
                @foreach($materiasPrimas as $mp)
                    <option value="{{ $mp->id }}">{{ $mp->nombre }} (Stock: {{ $mp->stock }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="cantidad_entrada" class="form-label">Cantidad a Moler (entrada)</label>
            <input type="number" step="0.01" name="cantidad_entrada" id="cantidad_entrada" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="cantidad_salida" class="form-label">Cantidad Molida (salida)</label>
            <input type="number" step="0.01" name="cantidad_salida" id="cantidad_salida" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" name="fecha" id="fecha" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea name="observaciones" id="observaciones" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Registrar</button>
        <a href="{{ route('molido.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection 