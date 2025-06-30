@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalle de Producto</h1>
    
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ $producto->nombre }}</h5>
            <p class="card-text"><strong>Tipo:</strong> {{ ucfirst($producto->tipo) }}</p>
            <p class="card-text"><strong>Precio de Venta:</strong> {{ number_format($producto->precio_venta, 2) }}</p>
            <p class="card-text"><strong>Stock Disponible:</strong> {{ $producto->stock }}</p>
            
            @if($producto->tipo == 'producido' && $producto->costo_promedio)
                <p class="card-text"><strong>Costo Promedio:</strong> {{ number_format($producto->costo_promedio, 2) }}</p>
                <p class="card-text"><strong>Margen de Ganancia:</strong> {{ number_format((($producto->precio_venta - $producto->costo_promedio) / $producto->costo_promedio) * 100, 2) }}%</p>
            @endif
        </div>
    </div>
    
    @if($producto->tipo == 'producido')
    <div class="card mb-3">
        <div class="card-header bg-info text-white">Historial de Producción</div>
        <div class="card-body">
            @if($producto->producciones->isEmpty())
                <div class="alert alert-info">No hay registros de producción para este producto.</div>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Materia Prima</th>
                            <th>Cantidad Producida</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($producto->producciones as $produccion)
                        <tr>
                            <td>{{ $produccion->fecha->format('d/m/Y') }}</td>
                            <td>{{ $produccion->materiaPrimaFiltrada->nombre }}</td>
                            <td>{{ $produccion->cantidad_producida }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    @endif
    
    <a href="{{ route('productos.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection