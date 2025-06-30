@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Editar Proceso de Filtrado</h5>
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

                    <form method="POST" action="{{ route('filtrado.update', $filtrado) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control @error('fecha') is-invalid @enderror" 
                                    id="fecha" name="fecha" value="{{ old('fecha', $filtrado->fecha->format('Y-m-d')) }}" required>
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
                                            {{ old('materia_prima_sin_filtrar_id', $filtrado->materia_prima_sin_filtrar_id) == $materia->id ? 'selected' : '' }}>
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
                                        value="{{ old('cantidad_entrada', $filtrado->cantidad_entrada) }}" required>
                                    <span class="input-group-text unidad-medida"></span>
                                </div>
                                @error('cantidad_entrada')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="cantidad_salida" class="form-label">Cantidad de Salida</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control @error('cantidad_salida') is-invalid @enderror" 
                                        id="cantidad_salida" name="cantidad_salida" 
                                        value="{{ old('cantidad_salida', $filtrado->cantidad_salida) }}" required>
                                    <span class="input-group-text unidad-medida"></span>
                                </div>
                                @error('cantidad_salida')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                    id="observaciones" name="observaciones" rows="3">{{ old('observaciones', $filtrado->observaciones) }}</textarea>
                                @error('observaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Proceso
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
    const unidadMedidaSpans = document.querySelectorAll('.unidad-medida');
    const materiasPrimas = @json($materiasPrimas);

    function actualizarUnidadMedida() {
        const materiaId = materiaPrimaSelect.value;
        const materia = materiasPrimas.find(m => m.id == materiaId);
        if (materia) {
            unidadMedidaSpans.forEach(span => {
                span.textContent = materia.unidad_medida;
            });
        }
    }

    materiaPrimaSelect.addEventListener('change', actualizarUnidadMedida);
    actualizarUnidadMedida(); // Actualizar al cargar la página
});
</script>
@endpush
@endsection 