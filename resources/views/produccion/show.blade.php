@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detalles de Producción</h1>
        <div class="btn-group">
            <a href="{{ route('produccion.edit', $produccion) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
            <form action="{{ route('produccion.destroy', $produccion) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar esta producción?')">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Información General
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Fecha:</strong> {{ $produccion->fecha->format('d/m/Y') }}</p>
                    <p><strong>Materia Prima:</strong> {{ $produccion->materiaPrimaFiltrada->nombre }}</p>
                    <p><strong>Cantidad Utilizada:</strong> {{ number_format($produccion->cantidad_utilizada, 2) }} {{ $produccion->materiaPrimaFiltrada->unidad_medida }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Producto:</strong> {{ $produccion->producto->nombre }}</p>
                    <p><strong>Cantidad Producida:</strong> {{ number_format($produccion->cantidad_producida, 2) }} unidades</p>
                </div>
            </div>
        </div>
    </div>

    @if($produccion->observaciones)
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Observaciones
        </div>
        <div class="card-body">
            <p>{{ $produccion->observaciones }}</p>
        </div>
    </div>
    @endif

    <a href="{{ route('produccion.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver al Listado
    </a>
</div>
@endsection