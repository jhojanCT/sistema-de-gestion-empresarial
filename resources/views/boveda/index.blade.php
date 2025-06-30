@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-vault"></i> Cuenta Bóveda
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Información de la Bóveda</h5>
                                </div>
                                <div class="card-body">
                                    @if($cuentaBoveda)
                                        <p><strong>Número de Cuenta:</strong> {{ $cuentaBoveda->numero_cuenta }}</p>
                                        <p><strong>Saldo Actual:</strong> Bs. {{ number_format($cuentaBoveda->saldo, 2) }}</p>
                                    @else
                                        <div class="alert alert-warning">
                                            No se ha encontrado la cuenta bóveda. Por favor, cree una cuenta con el nombre "Bóveda".
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Transferir a Bóveda</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('boveda.transferir') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="cuenta_origen_id">Cuenta Origen:</label>
                                            <select class="form-control" id="cuenta_origen_id" name="cuenta_origen_id" required>
                                                <option value="">Seleccione una cuenta</option>
                                                @foreach($cuentas as $cuenta)
                                                    <option value="{{ $cuenta->id }}">
                                                        {{ $cuenta->nombre_banco }} - {{ $cuenta->numero_cuenta }}
                                                        (Saldo: Bs. {{ number_format($cuenta->saldo, 2) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="monto">Monto a Transferir:</label>
                                            <input type="number" step="0.01" min="0.01" class="form-control" id="monto" name="monto" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="concepto">Concepto:</label>
                                            <input type="text" class="form-control" id="concepto" name="concepto" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-exchange-alt"></i> Transferir a Bóveda
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 