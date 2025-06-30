@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">{{ __('Permisos') }}</h3>
                    @can('crear-permisos')
                        <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('Nuevo Permiso') }}
                        </a>
                    @endcan
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @foreach($permissions as $group => $groupPermissions)
                        <div class="mb-4">
                            <h4 class="mb-3">{{ ucfirst($group) }}</h4>
                            <div class="row">
                                @foreach($groupPermissions as $permission)
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $permission->name }}</h5>
                                                <p class="card-text">{{ $permission->description }}</p>
                                                <div class="btn-group">
                                                    @can('editar-permisos')
                                                        <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('eliminar-permisos')
                                                        <form action="{{ route('permissions.destroy', $permission) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Está seguro de eliminar este permiso?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 