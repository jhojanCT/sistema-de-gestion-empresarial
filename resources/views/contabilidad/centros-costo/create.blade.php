@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Nuevo Centro de Costo</h3>
                    <div class="card-tools">
                        <a href="{{ route('contabilidad.centros-costo.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <form action="{{ route('contabilidad.centros-costo.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="codigo" class="text-dark">Código <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('codigo') is-invalid @enderror text-dark" 
                                           id="codigo" name="codigo" value="{{ old('codigo') }}" required>
                                    @error('codigo')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre" class="text-dark">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror text-dark" 
                                           id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                    @error('nombre')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo" class="text-dark">Tipo <span class="text-danger">*</span></label>
                                    <select class="form-control @error('tipo') is-invalid @enderror text-dark" 
                                            id="tipo" name="tipo" required>
                                        <option value="">Seleccione un tipo</option>
                                        <option value="PRODUCCION" {{ old('tipo') == 'PRODUCCION' ? 'selected' : '' }}>Producción</option>
                                        <option value="SERVICIO" {{ old('tipo') == 'SERVICIO' ? 'selected' : '' }}>Servicio</option>
                                        <option value="ADMINISTRATIVO" {{ old('tipo') == 'ADMINISTRATIVO' ? 'selected' : '' }}>Administrativo</option>
                                    </select>
                                    @error('tipo')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="presupuesto_mensual" class="text-dark">Presupuesto Mensual <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('presupuesto_mensual') is-invalid @enderror text-dark" 
                                           id="presupuesto_mensual" name="presupuesto_mensual" 
                                           value="{{ old('presupuesto_mensual') }}" required>
                                    @error('presupuesto_mensual')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="descripcion" class="text-dark">Descripción</label>
                                    <textarea class="form-control @error('descripcion') is-invalid @enderror text-dark" 
                                              id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="es_auxiliar" 
                                               name="es_auxiliar" value="1" {{ old('es_auxiliar') ? 'checked' : '' }}>
                                        <label class="custom-control-label text-dark" for="es_auxiliar">Es Centro de Costo Auxiliar</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="padre_group" style="display: none;">
                                    <label for="centro_costo_padre_id" class="text-dark">Centro de Costo Padre</label>
                                    <select class="form-control @error('centro_costo_padre_id') is-invalid @enderror text-dark" 
                                            id="centro_costo_padre_id" name="centro_costo_padre_id">
                                        <option value="">Seleccione un centro de costo padre</option>
                                        @foreach($centrosCostoPadre as $centroCosto)
                                            <option value="{{ $centroCosto->id }}" 
                                                {{ old('centro_costo_padre_id') == $centroCosto->id ? 'selected' : '' }}>
                                                {{ $centroCosto->codigo }} - {{ $centroCosto->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('centro_costo_padre_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="activo" 
                                               name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label text-dark" for="activo">Activo</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const esAuxiliarCheckbox = document.getElementById('es_auxiliar');
        const padreGroup = document.getElementById('padre_group');

        function togglePadreGroup() {
            padreGroup.style.display = esAuxiliarCheckbox.checked ? 'block' : 'none';
            const padreSelect = document.getElementById('centro_costo_padre_id');
            padreSelect.required = esAuxiliarCheckbox.checked;
        }

        esAuxiliarCheckbox.addEventListener('change', togglePadreGroup);
        togglePadreGroup(); // Initial state
    });
</script>
@endpush

@endsection 