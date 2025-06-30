@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Generar Asiento Contable de Salarios</h1>
        <a href="{{ route('pagos.salarios.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($pagosPendientes->isEmpty())
                <div class="alert alert-info">
                    No hay pagos pendientes para generar el asiento contable.
                </div>
            @else
                @foreach($pagosPendientes as $fecha => $pagos)
                    <div class="mb-4">
                        <h4>Pagos del {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Monto Total</th>
                                        <th>Método de Pago</th>
                                        <th>Comprobante</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pagos as $pago)
                                        <tr>
                                            <td>{{ $pago->id }}</td>
                                            <td>{{ number_format($pago->monto_total, 2) }}</td>
                                            <td>{{ ucfirst($pago->metodo_pago) }}</td>
                                            <td>{{ $pago->comprobante ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="1" class="text-end">Total:</th>
                                        <td colspan="3">{{ number_format($pagos->sum('monto_total'), 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <form action="{{ route('pagos.salarios.generar-asiento') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="fecha_pago" value="{{ $fecha }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-invoice"></i> Generar Asiento Contable
                            </button>
                        </form>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection 