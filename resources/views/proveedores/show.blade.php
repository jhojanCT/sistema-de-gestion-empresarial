@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalle del Proveedor</h1>
    
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ $proveedor->nombre }}</h5>
            <p class="card-text"><strong>Teléfono:</strong> {{ $proveedor->telefono }}</p>
            <p class="card-text"><strong>Dirección:</strong> {{ $proveedor->direccion ?? 'N/A' }}</p>
            <p class="card-text"><strong>Email:</strong> {{ $proveedor->email ?? 'N/A' }}</p>
        </div>
    </div>
    
    <h3>Historial de Compras</h3>
    
    @if($proveedor->compras->isEmpty())
        <div class="alert alert-info">Este proveedor no tiene compras registradas.</div>
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
                @foreach($proveedor->compras as $compra)
                <tr>
                    <td>{{ $compra->fecha->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($compra->tipo) }}</td>
                    <td>{{ number_format($compra->total, 2) }}</td>
                    <td>
                        @if($compra->tipo == 'contado' || $compra->pagada)
                            <span class="badge bg-success">Pagada</span>
                        @else
                            <span class="badge bg-warning">Pendiente</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('compras.show', $compra->id) }}" class="btn btn-sm btn-info">Ver</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
    <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection