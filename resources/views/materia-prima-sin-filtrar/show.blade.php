@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detalle de Materia Prima Sin Filtrar</h5>
                        <a href="{{ route('materia-prima-sin-filtrar.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Nombre</h6>
                            <p class="mb-0">{{ $materiaPrima->nombre }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Unidad de Medida</h6>
                            <p class="mb-0">{{ $materiaPrima->unidad_medida }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted">Descripción</h6>
                            <p class="mb-0">{{ $materiaPrima->descripcion ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Stock Actual</h6>
                            <p class="mb-0">
                                <span class="badge bg-{{ $materiaPrima->stock > 0 ? 'success' : 'danger' }}">
                                    {{ $materiaPrima->stock }} {{ $materiaPrima->unidad_medida }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Última Actualización</h6>
                            <p class="mb-0">{{ $materiaPrima->updated_at ? $materiaPrima->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('materia-prima-sin-filtrar.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection