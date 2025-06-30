@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pagos a Proveedores</h1>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Compra</th>
                <th>Monto</th>
                <th>Método</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagos as $pago)
            <tr>
                <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                <td>{{ $pago->compra->proveedor->nombre }}</td>
                <td>Compra #{{ $pago->compra->id }} ({{ number_format($pago->compra->total, 2) }})</td>
                <td>{{ number_format($pago->monto, 2) }}</td>
                <td>{{ $pago->metodo_pago }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection