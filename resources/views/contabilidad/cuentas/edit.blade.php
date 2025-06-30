@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar Cuenta Contable</h3>
                    <div class="card-tools">
                        <a href="{{ route('contabilidad.cuentas.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <form action="{{ route('contabilidad.cuentas.update', $cuenta) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="codigo">Código</label>
                                    <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                                           id="codigo" name="codigo" value="{{ old('codigo', $cuenta->codigo) }}" required>
                                    @error('codigo')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                           id="nombre" name="nombre" value="{{ old('nombre', $cuenta->nombre) }}" required>
                                    @error('nombre')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo">Tipo</label>
                                    <select class="form-control @error('tipo') is-invalid @enderror" 
                                            id="tipo" name="tipo" required>
                                        <option value="">Seleccione un tipo</option>
                                        <option value="ACTIVO" {{ old('tipo', $cuenta->tipo) == 'ACTIVO' ? 'selected' : '' }}>Activo</option>
                                        <option value="PASIVO" {{ old('tipo', $cuenta->tipo) == 'PASIVO' ? 'selected' : '' }}>Pasivo</option>
                                        <option value="PATRIMONIO" {{ old('tipo', $cuenta->tipo) == 'PATRIMONIO' ? 'selected' : '' }}>Patrimonio</option>
                                        <option value="INGRESO" {{ old('tipo', $cuenta->tipo) == 'INGRESO' ? 'selected' : '' }}>Ingreso</option>
                                        <option value="EGRESO" {{ old('tipo', $cuenta->tipo) == 'EGRESO' ? 'selected' : '' }}>Egreso</option>
                                        <option value="COSTO" {{ old('tipo', $cuenta->tipo) == 'COSTO' ? 'selected' : '' }}>Costo</option>
                                    </select>
                                    @error('tipo')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="naturaleza">Naturaleza</label>
                                    <select class="form-control @error('naturaleza') is-invalid @enderror" 
                                            id="naturaleza" name="naturaleza" required>
                                        <option value="">Seleccione una naturaleza</option>
                                        <option value="DEUDORA" {{ old('naturaleza', $cuenta->naturaleza) == 'DEUDORA' ? 'selected' : '' }}>Deudora</option>
                                        <option value="ACREEDORA" {{ old('naturaleza', $cuenta->naturaleza) == 'ACREEDORA' ? 'selected' : '' }}>Acreedora</option>
                                    </select>
                                    @error('naturaleza')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cuenta_padre_id">Cuenta Padre</label>
                                    <select class="form-control @error('cuenta_padre_id') is-invalid @enderror" 
                                            id="cuenta_padre_id" name="cuenta_padre_id">
                                        <option value="">Seleccione una cuenta padre</option>
                                        @foreach($cuentasPadre as $cuentaPadre)
                                            @if($cuentaPadre->id != $cuenta->id)
                                                <option value="{{ $cuentaPadre->id }}" {{ old('cuenta_padre_id', $cuenta->cuenta_padre_id) == $cuentaPadre->id ? 'selected' : '' }}>
                                                    {{ $cuentaPadre->codigo }} - {{ $cuentaPadre->nombre }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('cuenta_padre_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nivel">Nivel</label>
                                    <input type="number" class="form-control @error('nivel') is-invalid @enderror" 
                                           id="nivel" name="nivel" value="{{ old('nivel', $cuenta->nivel) }}" min="1" max="4" required>
                                    @error('nivel')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="es_centro_costo" 
                                               name="es_centro_costo" value="1" {{ old('es_centro_costo', $cuenta->es_centro_costo) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="es_centro_costo">Es Centro de Costo</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="activo" 
                                               name="activo" value="1" {{ old('activo', $cuenta->activo) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="activo">Activo</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Actualizar nivel automáticamente cuando se selecciona una cuenta padre
        $('#cuenta_padre_id').change(function() {
            var cuentaPadreId = $(this).val();
            if (cuentaPadreId) {
                $.get('/api/cuentas/' + cuentaPadreId + '/nivel', function(data) {
                    $('#nivel').val(data.nivel + 1);
                });
            } else {
                $('#nivel').val(1);
            }
        });
    });
</script>
@endpush 