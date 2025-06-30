@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nuevo Proceso de Filtrado</h5>
                    <a href="{{ route('filtrado.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('filtrado.store') }}" id="filtradoForm">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control @error('fecha') is-invalid @enderror" 
                                    id="fecha" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="materia_prima_sin_filtrar_id" class="form-label">Materia Prima</label>
                                <select class="form-select @error('materia_prima_sin_filtrar_id') is-invalid @enderror" 
                                    id="materia_prima_sin_filtrar_id" name="materia_prima_sin_filtrar_id" required>
                                    <option value="">Seleccione una materia prima</option>
                                    @foreach($materiasPrimas as $materia)
                                        <option value="{{ $materia->id }}" 
                                            data-stock="{{ $materia->stock }}"
                                            data-unidad="{{ $materia->unidad_medida }}"
                                            {{ old('materia_prima_sin_filtrar_id') == $materia->id ? 'selected' : '' }}>
                                            {{ $materia->nombre }} (Stock: {{ $materia->stock }} {{ $materia->unidad_medida }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('materia_prima_sin_filtrar_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cantidad_entrada" class="form-label">Cantidad de Entrada</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control @error('cantidad_entrada') is-invalid @enderror" 
                                        id="cantidad_entrada" name="cantidad_entrada" 
                                        value="{{ old('cantidad_entrada') }}" required>
                                    <span class="input-group-text unidad-medida"></span>
                                </div>
                                <div class="form-text text-muted stock-disponible"></div>
                                @error('cantidad_entrada')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="cantidad_salida" class="form-label">Cantidad de Salida</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control @error('cantidad_salida') is-invalid @enderror" 
                                        id="cantidad_salida" name="cantidad_salida" 
                                        value="{{ old('cantidad_salida') }}" required>
                                    <span class="input-group-text unidad-medida"></span>
                                </div>
                                <div class="form-text text-muted desperdicio-info"></div>
                                @error('cantidad_salida')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                    id="observaciones" name="observaciones" rows="3">{{ old('observaciones') }}</textarea>
                                @error('observaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Proceso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const materiaPrimaSelect = document.getElementById('materia_prima_sin_filtrar_id');
    const cantidadEntrada = document.getElementById('cantidad_entrada');
    const cantidadSalida = document.getElementById('cantidad_salida');
    const unidadMedidaSpans = document.querySelectorAll('.unidad-medida');
    const stockDisponible = document.querySelector('.stock-disponible');
    const desperdicioInfo = document.querySelector('.desperdicio-info');
    const form = document.getElementById('filtradoForm');

    function actualizarUnidadMedida() {
        const materiaId = materiaPrimaSelect.value;
        const materia = materiaPrimaSelect.options[materiaPrimaSelect.selectedIndex];
        if (materiaId) {
            const unidad = materia.dataset.unidad;
            const stock = parseFloat(materia.dataset.stock);
            
            unidadMedidaSpans.forEach(span => {
                span.textContent = unidad;
            });
            
            stockDisponible.textContent = `Stock disponible: ${stock} ${unidad}`;
            cantidadEntrada.max = stock;
        } else {
            unidadMedidaSpans.forEach(span => {
                span.textContent = '';
            });
            stockDisponible.textContent = '';
            cantidadEntrada.max = '';
        }
    }

    function actualizarDesperdicio() {
        const entrada = parseFloat(cantidadEntrada.value) || 0;
        const salida = parseFloat(cantidadSalida.value) || 0;
        const desperdicio = entrada - salida;
        
        if (entrada && salida) {
            desperdicioInfo.textContent = `Desperdicio: ${desperdicio.toFixed(2)} ${unidadMedidaSpans[0].textContent}`;
            if (desperdicio < 0) {
                desperdicioInfo.classList.add('text-danger');
            } else {
                desperdicioInfo.classList.remove('text-danger');
            }
        } else {
            desperdicioInfo.textContent = '';
        }
    }

    materiaPrimaSelect.addEventListener('change', actualizarUnidadMedida);
    cantidadEntrada.addEventListener('input', actualizarDesperdicio);
    cantidadSalida.addEventListener('input', actualizarDesperdicio);

    form.addEventListener('submit', function(e) {
        const entrada = parseFloat(cantidadEntrada.value);
        const salida = parseFloat(cantidadSalida.value);
        
        if (salida > entrada) {
            e.preventDefault();
            alert('La cantidad de salida no puede ser mayor que la cantidad de entrada');
        }
    });

    // Inicializar valores
    actualizarUnidadMedida();
    actualizarDesperdicio();
});
</script>
@endpush
@endsection