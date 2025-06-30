@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registrar Pago de Cliente</h1>
    <h4>Venta #{{ $venta->id }} - {{ $venta->cliente->nombre ?? 'Consumidor Final' }}</h4>
    <h5>Total: {{ number_format($venta->total, 2) }} | Saldo Pendiente: {{ number_format($venta->saldoPendiente(), 2) }}</h5>
    
    <form action="{{ route('pagos.clientes.store', $venta->id) }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="monto">Monto a Pagar:</label>
            <input type="number" step="0.01" min="0.01" max="{{ $venta->saldoPendiente() }}" class="form-control" id="monto" name="monto" required>
        </div>
        
        <div class="form-group">
            <label for="fecha_pago">Fecha de Pago:</label>
            <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" value="{{ date('Y-m-d') }}" required>
        </div>
        
        <div class="form-group">
            <label for="metodo_pago">Método de Pago:</label>
            <select class="form-control" id="metodo_pago" name="metodo_pago" required>
                <option value="">Seleccione tipo de pago</option>
                <option value="efectivo">Efectivo</option>
                <option value="transferencia">Transferencia</option>
                <option value="cheque">Cheque</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="comprobante">Número de Comprobante (opcional):</label>
            <input type="text" class="form-control" id="comprobante" name="comprobante">
        </div>

        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="auto_asiento" name="auto_asiento" value="1">
                <label class="form-check-label" for="auto_asiento">Generar asiento contable automáticamente</label>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Registrar Pago</button>
        <a href="{{ route('ventas.show', $venta->id) }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection