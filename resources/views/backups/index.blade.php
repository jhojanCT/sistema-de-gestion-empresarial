@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Backups del Sistema</h2>
                    <div>
                        <form action="{{ route('backups.upload') }}" method="POST" enctype="multipart/form-data" class="d-inline me-2">
                            @csrf
                            <div class="input-group">
                                <input type="file" class="form-control" name="backup_file" accept=".zip" required>
                                <button type="submit" class="btn btn-success">
                                    Subir Backup
                                </button>
                            </div>
                        </form>
                        <form action="{{ route('backups.create') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Crear Nuevo Backup
                            </button>
                        </form>
                    </div>
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

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Tamaño</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($backups as $backup)
                                    <tr>
                                        <td>{{ $backup['name'] }}</td>
                                        <td>{{ $backup['type'] }}</td>
                                        <td>{{ $backup['size'] }}</td>
                                        <td>{{ $backup['date'] }}</td>
                                        <td>
                                            <a href="{{ route('backups.download', $backup['name']) }}" 
                                               class="btn btn-sm btn-info">
                                                Descargar
                                            </a>
                                            <form action="{{ route('backups.restore', $backup['name']) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Está seguro de restaurar este backup? Esta acción no se puede deshacer.');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    Restaurar
                                                </button>
                                            </form>
                                            <form action="{{ route('backups.destroy', $backup['name']) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Está seguro de eliminar este backup?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            No hay backups disponibles
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 