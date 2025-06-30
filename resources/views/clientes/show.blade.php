@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalle del Cliente</h1>
    
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ $cliente->nombre }}</h5>
            <p class="card-text"><strong>Teléfono:</strong> {{ $cliente->telefono }}</p>
            <p class="card-text"><strong>Dirección:</strong> {{ $cliente->direccion ?? 'N/A' }}</p>
        </div>
    </div>
    
    <h3>Historial de Ventas</h3>
    
    @if($ventas->isEmpty())
        <div class="alert alert-info">Este cliente no tiene ventas registradas.</div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventas as $venta)
                <tr>
                    <td>{{ $venta->fecha->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($venta->tipo) }}</td>
                    <td>{{ number_format($venta->total, 2) }}</td>
                    <td>
                        @if($venta->tipo == 'contado' || $venta->pagada)
                            <span class="badge bg-success">Pagada</span>
                        @else
                            <span class="badge bg-warning">Pendiente</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('ventas.show', $venta->id) }}" class="btn btn-sm btn-info">Ver</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
    <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection