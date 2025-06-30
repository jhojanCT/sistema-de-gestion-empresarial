@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-plus text-primary"></i> Nuevo Producto
                        </h5>
                        <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('productos.store') }}" id="productoForm">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                    id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select class="form-select @error('tipo') is-invalid @enderror" 
                                    id="tipo" name="tipo" required>
                                    <option value="">Seleccione un tipo</option>
                                    <option value="producido" {{ old('tipo') == 'producido' ? 'selected' : '' }}>
                                        Producido
                                    </option>
                                    <option value="comprado" {{ old('tipo') == 'comprado' ? 'selected' : '' }}>
                                        Comprado
                                    </option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="precio_venta" class="form-label">Precio de Venta</label>
                                <div class="input-group">
                                    <span class="input-group-text">Bs.</span>
                                    <input type="number" step="0.01" min="0" 
                                        class="form-control @error('precio_venta') is-invalid @enderror" 
                                        id="precio_venta" name="precio_venta" value="{{ old('precio_venta') }}" required>
                                </div>
                                @error('precio_venta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                                <select class="form-select @error('unidad_medida') is-invalid @enderror" 
                                    id="unidad_medida" name="unidad_medida" required>
                                    <option value="">Seleccione una unidad</option>
                                    <option value="unidad" {{ old('unidad_medida') == 'unidad' ? 'selected' : '' }}>
                                        Unidad
                                    </option>
                                    <option value="kg" {{ old('unidad_medida') == 'kg' ? 'selected' : '' }}>
                                        Kilogramo (kg)
                                    </option>
                                    <option value="g" {{ old('unidad_medida') == 'g' ? 'selected' : '' }}>
                                        Gramo (g)
                                    </option>
                                    <option value="l" {{ old('unidad_medida') == 'l' ? 'selected' : '' }}>
                                        Litro (L)
                                    </option>
                                    <option value="ml" {{ old('unidad_medida') == 'ml' ? 'selected' : '' }}>
                                        Mililitro (mL)
                                    </option>
                                </select>
                                @error('unidad_medida')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock Inicial</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" 
                                    class="form-control @error('stock') is-invalid @enderror" 
                                    id="stock" name="stock" value="{{ old('stock', 0) }}" required>
                                <span class="input-group-text unidad-medida"></span>
                            </div>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Producto
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
    const unidadMedidaSelect = document.getElementById('unidad_medida');
    const unidadMedidaSpans = document.querySelectorAll('.unidad-medida');

    function actualizarUnidadMedida() {
        const unidad = unidadMedidaSelect.value;
        unidadMedidaSpans.forEach(span => {
            span.textContent = unidad;
        });
    }

    unidadMedidaSelect.addEventListener('change', actualizarUnidadMedida);
    actualizarUnidadMedida(); // Actualizar al cargar la página
});
</script>
@endpush
@endsection