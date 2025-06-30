@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Producción Especial</h1>
    <form action="{{ route('produccion-especial.update', $produccionEspecial) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="materia_prima_sin_filtrar_id" class="form-label">Materia Prima Sin Filtrar</label>
            <select name="materia_prima_sin_filtrar_id" id="materia_prima_sin_filtrar_id" class="form-control" required>
                <option value="">Seleccione...</option>
                @foreach($materiasPrimas as $mp)
                    <option value="{{ $mp->id }}" {{ $produccionEspecial->materia_prima_sin_filtrar_id == $mp->id ? 'selected' : '' }}>{{ $mp->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="producto_id" class="form-label">Producto a Producir</label>
            <select name="producto_id" id="producto_id" class="form-control" required>
                <option value="">Seleccione...</option>
                @foreach($productos as $prod)
                    <option value="{{ $prod->id }}" {{ $produccionEspecial->producto_id == $prod->id ? 'selected' : '' }}>{{ $prod->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="cantidad_utilizada" class="form-label">Cantidad Utilizada</label>
            <input type="number" step="0.01" name="cantidad_utilizada" id="cantidad_utilizada" class="form-control" value="{{ $produccionEspecial->cantidad_utilizada }}" required>
        </div>
        <div class="mb-3">
            <label for="cantidad_producida" class="form-label">Cantidad Producida</label>
            <input type="number" step="0.01" name="cantidad_producida" id="cantidad_producida" class="form-control" value="{{ $produccionEspecial->cantidad_producida }}" required>
        </div>
        <div class="mb-3">
            <label for="costo_produccion" class="form-label">Costo Producción</label>
            <input type="number" step="0.01" name="costo_produccion" id="costo_produccion" class="form-control" value="{{ $produccionEspecial->costo_produccion }}" required>
        </div>
        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" name="fecha" id="fecha" class="form-control" value="{{ $produccionEspecial->fecha }}" required>
        </div>
        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea name="observaciones" id="observaciones" class="form-control">{{ $produccionEspecial->observaciones }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('produccion-especial.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection 