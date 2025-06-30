@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detalles de Filtrado</h1>
        <div class="btn-group">
            <a href="{{ route('filtrado.edit', $filtrado) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
            <form action="{{ route('filtrado.destroy', $filtrado) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este filtrado?')">
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
                    <p><strong>Fecha:</strong> {{ $filtrado->fecha->format('d/m/Y') }}</p>
                    <p><strong>Materia Prima:</strong> {{ $filtrado->materiaPrimaSinFiltrar->nombre }}</p>
                    <p><strong>Cantidad Entrada:</strong> {{ number_format($filtrado->cantidad_entrada, 2) }} {{ $filtrado->materiaPrimaSinFiltrar->unidad_medida }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Cantidad Salida:</strong> {{ number_format($filtrado->cantidad_salida, 2) }} {{ $filtrado->materiaPrimaSinFiltrar->unidad_medida }}</p>
                    <p><strong>Desperdicio:</strong> {{ number_format($filtrado->desperdicio, 2) }} {{ $filtrado->materiaPrimaSinFiltrar->unidad_medida }}</p>
                    <p><strong>Eficiencia:</strong> {{ number_format(($filtrado->cantidad_salida / $filtrado->cantidad_entrada) * 100, 2) }}%</p>
                </div>
            </div>
        </div>
    </div>

    @if($filtrado->observaciones)
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Observaciones
        </div>
        <div class="card-body">
            <p>{{ $filtrado->observaciones }}</p>
        </div>
    </div>
    @endif

    <a href="{{ route('filtrado.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver al Listado
    </a>
</div>
@endsection