@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Materia Prima Molida</h1>
    <form action="{{ route('molido.molida.update', $materiaPrimaMolida) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="materia_prima_filtrada_id" class="form-label">Materia Prima Filtrada</label>
            <select name="materia_prima_filtrada_id" id="materia_prima_filtrada_id" class="form-control" required>
                <option value="">Seleccione...</option>
                @foreach($materiasFiltradas as $mp)
                    <option value="{{ $mp->id }}" {{ $materiaPrimaMolida->materia_prima_filtrada_id == $mp->id ? 'selected' : '' }}>{{ $mp->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad</label>
            <input type="number" step="0.01" name="cantidad" id="cantidad" class="form-control" value="{{ $materiaPrimaMolida->cantidad }}" required>
        </div>
        <div class="mb-3">
            <label for="fecha_molido" class="form-label">Fecha de Molido</label>
            <input type="date" name="fecha_molido" id="fecha_molido" class="form-control" value="{{ $materiaPrimaMolida->fecha_molido }}" required>
        </div>
        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea name="observaciones" id="observaciones" class="form-control">{{ $materiaPrimaMolida->observaciones }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('molido.inventario') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection 