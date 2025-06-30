@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Producción</h1>

    <form action="{{ route('produccion.update', $produccion) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fecha">Fecha:</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" value="{{ $produccion->fecha->format('Y-m-d') }}" required>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-primary text-white">Detalles de la Producción</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="materia_prima_molida_id" class="form-label">Materia Prima Molida</label>
                            <select name="materia_prima_molida_id" id="materia_prima_molida_id" class="form-control" required>
                                <option value="">Seleccione...</option>
                                @foreach($materiasPrimas as $mp)
                                    <option value="{{ $mp->id }}" {{ $produccion->materia_prima_molida_id == $mp->id ? 'selected' : '' }}>{{ $mp->materiaPrimaFiltrada->nombre }} (Stock: {{ $mp->cantidad }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cantidad_utilizada">Cantidad Utilizada:</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0.01" class="form-control" id="cantidad_utilizada" 
                                       name="cantidad_utilizada" value="{{ $produccion->cantidad_utilizada }}" required>
                                <span class="input-group-text unidad-medida">{{ $produccion->materiaPrimaFiltrada->unidad_medida }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="producto_id">Producto a Producir:</label>
                            <select class="form-control" id="producto_id" name="producto_id" required>
                                <option value="">Seleccione producto</option>
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id }}" {{ $produccion->producto_id == $producto->id ? 'selected' : '' }}>
                                        {{ $producto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cantidad_producida">Cantidad Producida:</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0.01" class="form-control" id="cantidad_producida" 
                                       name="cantidad_producida" value="{{ $produccion->cantidad_producida }}" required>
                                <span class="input-group-text">unidades</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="observaciones">Observaciones:</label>
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3">{{ $produccion->observaciones }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Producción</button>
        <a href="{{ route('produccion.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const materiaPrimaSelect = document.getElementById('materia_prima_molida_id');
    const cantidadUtilizadaInput = document.getElementById('cantidad_utilizada');
    const unidadMedidaSpan = document.querySelector('.unidad-medida');

    materiaPrimaSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const stock = parseFloat(selectedOption.getAttribute('data-stock')) || 0;
        const unidad = selectedOption.getAttribute('data-unidad') || 'unidades';
        
        cantidadUtilizadaInput.max = stock;
        cantidadUtilizadaInput.setAttribute('placeholder', `Máximo: ${stock}`);
        unidadMedidaSpan.textContent = unidad;
    });
});
</script>
@endsection