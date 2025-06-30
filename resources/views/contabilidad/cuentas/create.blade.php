@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Nueva Cuenta Contable</h3>
                    <div class="card-tools">
                        <a href="{{ route('contabilidad.cuentas.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <form action="{{ route('contabilidad.cuentas.store') }}" method="POST" id="formCuenta">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="codigo">Código</label>
                                    <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                                           id="codigo" name="codigo" value="{{ old('codigo') }}" required>
                                    @error('codigo')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Formato: números y puntos (ej: 1.1.1)</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                           id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                    @error('nombre')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">Tipo</label>
                                    <select class="form-control @error('tipo') is-invalid @enderror" 
                                            id="tipo" name="tipo" required>
                                        <option value="">Seleccione...</option>
                                        <option value="ACTIVO" {{ old('tipo') == 'ACTIVO' ? 'selected' : '' }}>Activo</option>
                                        <option value="PASIVO" {{ old('tipo') == 'PASIVO' ? 'selected' : '' }}>Pasivo</option>
                                        <option value="PATRIMONIO" {{ old('tipo') == 'PATRIMONIO' ? 'selected' : '' }}>Patrimonio</option>
                                        <option value="INGRESO" {{ old('tipo') == 'INGRESO' ? 'selected' : '' }}>Ingreso</option>
                                        <option value="EGRESO" {{ old('tipo') == 'EGRESO' ? 'selected' : '' }}>Egreso</option>
                                        <option value="COSTO" {{ old('tipo') == 'COSTO' ? 'selected' : '' }}>Costo</option>
                                    </select>
                                    @error('tipo')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="naturaleza">Naturaleza</label>
                                    <select class="form-control @error('naturaleza') is-invalid @enderror" 
                                            id="naturaleza" name="naturaleza" required>
                                        <option value="">Seleccione...</option>
                                        <option value="DEUDORA" {{ old('naturaleza') == 'DEUDORA' ? 'selected' : '' }}>Deudora</option>
                                        <option value="ACREEDORA" {{ old('naturaleza') == 'ACREEDORA' ? 'selected' : '' }}>Acreedora</option>
                                    </select>
                                    @error('naturaleza')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cuenta_padre_id">Cuenta Padre</label>
                                    <select class="form-control @error('cuenta_padre_id') is-invalid @enderror" 
                                            id="cuenta_padre_id" name="cuenta_padre_id">
                                        <option value="">Seleccione...</option>
                                        @foreach($cuentas as $cuenta)
                                            <option value="{{ $cuenta->id }}" 
                                                    data-codigo="{{ $cuenta->codigo }}"
                                                    {{ old('cuenta_padre_id') == $cuenta->id ? 'selected' : '' }}>
                                                {{ $cuenta->codigo }} - {{ $cuenta->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cuenta_padre_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nivel">Nivel</label>
                                    <select class="form-control @error('nivel') is-invalid @enderror" 
                                            id="nivel" name="nivel" required>
                                        <option value="">Seleccione...</option>
                                        <option value="1" {{ old('nivel') == '1' ? 'selected' : '' }}>1 - Grupo</option>
                                        <option value="2" {{ old('nivel') == '2' ? 'selected' : '' }}>2 - Subgrupo</option>
                                        <option value="3" {{ old('nivel') == '3' ? 'selected' : '' }}>3 - Cuenta</option>
                                        <option value="4" {{ old('nivel') == '4' ? 'selected' : '' }}>4 - Subcuenta</option>
                                        <option value="5" {{ old('nivel') == '5' ? 'selected' : '' }}>5 - Auxiliar</option>
                                    </select>
                                    @error('nivel')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="es_centro_costo">Es Centro de Costo</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" 
                                               id="es_centro_costo" name="es_centro_costo" 
                                               {{ old('es_centro_costo') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="es_centro_costo"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="activo">Activo</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" 
                                               id="activo" name="activo" 
                                               {{ old('activo', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="activo"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cuenta
                        </button>
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
    // Validación del código
    $('#codigo').on('input', function() {
        const codigo = $(this).val();
        if (!/^\d+(\.\d+)*$/.test(codigo)) {
            $(this).addClass('is-invalid');
            $(this).next('.invalid-feedback').text('El código debe contener solo números y puntos');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // Validación de consistencia con cuenta padre
    $('#cuenta_padre_id').change(function() {
        const cuentaPadre = $(this).find('option:selected');
        const codigoPadre = cuentaPadre.data('codigo');
        const codigo = $('#codigo').val();

        if (codigoPadre && codigo && !codigo.startsWith(codigoPadre + '.')) {
            $('#codigo').addClass('is-invalid');
            $('#codigo').next('.invalid-feedback').text('El código debe comenzar con el código de la cuenta padre');
        } else {
            $('#codigo').removeClass('is-invalid');
        }
    });

    // Validación del nivel
    $('#nivel').change(function() {
        const nivel = $(this).val();
        const cuentaPadre = $('#cuenta_padre_id').find('option:selected');
        
        if (cuentaPadre.val() && nivel <= cuentaPadre.data('nivel')) {
            $(this).addClass('is-invalid');
            $(this).next('.invalid-feedback').text('El nivel debe ser mayor al nivel de la cuenta padre');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // Validación del formulario
    $('#formCuenta').submit(function(e) {
        const codigo = $('#codigo').val();
        const cuentaPadre = $('#cuenta_padre_id').find('option:selected');
        const codigoPadre = cuentaPadre.data('codigo');

        if (!/^\d+(\.\d+)*$/.test(codigo)) {
            e.preventDefault();
            alert('El código debe contener solo números y puntos');
            return false;
        }

        if (cuentaPadre.val() && !codigo.startsWith(codigoPadre + '.')) {
            e.preventDefault();
            alert('El código debe comenzar con el código de la cuenta padre');
            return false;
        }
    });
});
</script>
@endpush 