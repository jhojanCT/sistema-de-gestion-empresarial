@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-box text-primary"></i> Productos
                        </h5>
                        <a href="{{ route('productos.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Producto
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Filtros -->
                    <form action="{{ route('productos.index') }}" method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" name="search" 
                                        value="{{ request('search') }}" placeholder="Buscar por nombre...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="tipo">
                                    <option value="">Todos los tipos</option>
                                    <option value="producido" {{ request('tipo') == 'producido' ? 'selected' : '' }}>
                                        Producidos
                                    </option>
                                    <option value="comprado" {{ request('tipo') == 'comprado' ? 'selected' : '' }}>
                                        Comprados
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th class="text-end">Precio Venta</th>
                                    <th class="text-end">Stock</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productos as $producto)
                                    <tr>
                                        <td>
                                            <span class="fw-medium">{{ $producto->nombre }}</span>
                                            <br>
                                            <small class="text-muted">{{ $producto->unidad_medida }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $producto->tipo == 'producido' ? 'bg-success' : 'bg-info' }}">
                                                {{ ucfirst($producto->tipo) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            Bs. {{ number_format($producto->precio_venta, 2) }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($producto->stock, 2) }}
                                            <small class="text-muted">{{ $producto->unidad_medida }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('productos.show', $producto) }}" 
                                                    class="btn btn-sm btn-info" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('productos.edit', $producto) }}" 
                                                    class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('productos.destroy', $producto) }}" 
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('¿Está seguro de eliminar este producto? Esta acción no se puede deshacer.')" 
                                                        title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-box fa-3x mb-3"></i>
                                                <p class="mb-0">No hay productos registrados</p>
                                                <small>Haga clic en "Nuevo Producto" para comenzar</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $productos->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection