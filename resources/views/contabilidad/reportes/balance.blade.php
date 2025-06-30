@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Balance General</h3>
                    <div class="card-tools">
                        <form action="{{ route('reportes.balance') }}" method="GET" class="form-inline">
                            <div class="form-group mx-sm-3 mb-2">
                                <label for="fecha" class="mr-2">Fecha:</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" 
                                       value="{{ $fecha }}" required>
                            </div>
                            <button type="submit" class="btn btn-primary mb-2">Generar</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Activo</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Cuenta</th>
                                        <th class="text-right">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cuentas->where('tipo', 'ACTIVO') as $cuenta)
                                        <tr>
                                            <td>{{ $cuenta->codigo }} - {{ $cuenta->nombre }}</td>
                                            <td class="text-right">{{ number_format($cuenta->calcularSaldo($fecha), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total Activo</th>
                                        <th class="text-right">{{ number_format($totales['activo'], 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Pasivo y Patrimonio</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Cuenta</th>
                                        <th class="text-right">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cuentas->where('tipo', 'PASIVO') as $cuenta)
                                        <tr>
                                            <td>{{ $cuenta->codigo }} - {{ $cuenta->nombre }}</td>
                                            <td class="text-right">{{ number_format($cuenta->calcularSaldo($fecha), 2) }}</td>
                                        </tr>
                                    @endforeach
                                    @foreach($cuentas->where('tipo', 'PATRIMONIO') as $cuenta)
                                        <tr>
                                            <td>{{ $cuenta->codigo }} - {{ $cuenta->nombre }}</td>
                                            <td class="text-right">{{ number_format($cuenta->calcularSaldo($fecha), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total Pasivo</th>
                                        <th class="text-right">{{ number_format($totales['pasivo'], 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th>Total Patrimonio</th>
                                        <th class="text-right">{{ number_format($totales['patrimonio'], 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th>Total Pasivo y Patrimonio</th>
                                        <th class="text-right">{{ number_format($totales['pasivo'] + $totales['patrimonio'], 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 