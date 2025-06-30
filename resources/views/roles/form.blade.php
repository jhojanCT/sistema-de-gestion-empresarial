@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">{{ isset($role) ? __('Editar Rol') : __('Nuevo Rol') }}</h3>
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

                    <form method="POST" action="{{ isset($role) ? route('roles.update', $role) : route('roles.store') }}">
                        @csrf
                        @if(isset($role))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Nombre') }}</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $role->name ?? '') }}" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Descripción') }}</label>
                            <input type="text" class="form-control" id="description" name="description" value="{{ old('description', $role->description ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Permisos') }}</label>
                            <div class="row">
                                @foreach($permissions as $group => $groupPermissions)
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0">{{ ucfirst($group) }}</h5>
                                            </div>
                                            <div class="card-body">
                                                @foreach($groupPermissions as $permission)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                            value="{{ $permission->id }}" id="permission_{{ $permission->id }}"
                                                            {{ (isset($role) && $role->permissions->contains($permission->id)) || in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                            {{ $permission->description }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('Cancelar') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ isset($role) ? __('Actualizar') : __('Crear') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 