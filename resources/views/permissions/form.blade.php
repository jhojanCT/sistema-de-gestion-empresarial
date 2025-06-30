@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">{{ isset($permission) ? __('Editar Permiso') : __('Nuevo Permiso') }}</h3>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ isset($permission) ? route('permissions.update', $permission) : route('permissions.store') }}">
                        @csrf
                        @if(isset($permission))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Nombre') }}</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $permission->name ?? '') }}" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Descripción') }}</label>
                            <input type="text" class="form-control" id="description" name="description" value="{{ old('description', $permission->description ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="group" class="form-label">{{ __('Grupo') }}</label>
                            <select class="form-select" id="group" name="group" required>
                                <option value="">Seleccione un grupo</option>
                                <option value="usuarios" {{ old('group', $permission->group ?? '') == 'usuarios' ? 'selected' : '' }}>Usuarios</option>
                                <option value="roles" {{ old('group', $permission->group ?? '') == 'roles' ? 'selected' : '' }}>Roles</option>
                                <option value="permisos" {{ old('group', $permission->group ?? '') == 'permisos' ? 'selected' : '' }}>Permisos</option>
                                <option value="inventario" {{ old('group', $permission->group ?? '') == 'inventario' ? 'selected' : '' }}>Inventario</option>
                                <option value="compras" {{ old('group', $permission->group ?? '') == 'compras' ? 'selected' : '' }}>Compras</option>
                                <option value="ventas" {{ old('group', $permission->group ?? '') == 'ventas' ? 'selected' : '' }}>Ventas</option>
                                <option value="reportes" {{ old('group', $permission->group ?? '') == 'reportes' ? 'selected' : '' }}>Reportes</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('Cancelar') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ isset($permission) ? __('Actualizar') : __('Crear') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 