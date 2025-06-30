@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Editar Permiso</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Nombre del Permiso</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $permission->name) }}" 
                                   required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $permission->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="group">Grupo</label>
                            <select class="form-control @error('group') is-invalid @enderror" 
                                    id="group" 
                                    name="group" 
                                    required>
                                <option value="">Seleccione un grupo</option>
                                <option value="usuarios" {{ old('group', $permission->group) == 'usuarios' ? 'selected' : '' }}>Usuarios</option>
                                <option value="roles" {{ old('group', $permission->group) == 'roles' ? 'selected' : '' }}>Roles</option>
                                <option value="permisos" {{ old('group', $permission->group) == 'permisos' ? 'selected' : '' }}>Permisos</option>
                                <option value="cuentas" {{ old('group', $permission->group) == 'cuentas' ? 'selected' : '' }}>Cuentas Bancarias</option>
                                <option value="movimientos" {{ old('group', $permission->group) == 'movimientos' ? 'selected' : '' }}>Movimientos Bancarios</option>
                            </select>
                            @error('group')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 