@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pagos de Clientes</h1>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Venta</th>
                <th>Monto</th>
                <th>Método</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagos as $pago)
            <tr>
                <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                <td>{{ $pago->venta->cliente->nombre }}</td>
                <td>Venta #{{ $pago->venta->id }} ({{ number_format($pago->venta->total, 2) }})</td>
                <td>{{ number_format($pago->monto, 2) }}</td>
                <td>{{ $pago->metodo_pago }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection