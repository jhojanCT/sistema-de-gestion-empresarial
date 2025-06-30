@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Libro Mayor</h3>
                    <div class="card-tools">
                        <form id="filtroForm" class="form-inline">
                            <div class="form-group mx-sm-3">
                                <label for="cuenta_id" class="mr-2">Cuenta:</label>
                                <select class="form-control" id="cuenta_id" name="cuenta_id" required>
                                    <option value="">Seleccione una cuenta</option>
                                    @foreach($cuentas as $cuenta)
                                    <option value="{{ $cuenta->id }}" {{ request('cuenta_id') == $cuenta->id ? 'selected' : '' }}>
                                        {{ $cuenta->codigo }} - {{ $cuenta->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mx-sm-3">
                                <label for="fecha_inicio" class="mr-2">Desde:</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ request('fecha_inicio', now()->startOfMonth()->format('Y-m-d')) }}">
                            </div>
                            <div class="form-group mx-sm-3">
                                <label for="fecha_fin" class="mr-2">Hasta:</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ request('fecha_fin', now()->format('Y-m-d')) }}">
                            </div>
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <button type="button" class="btn btn-success ml-2" onclick="exportarExcel()">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($datos))
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4>Información de la Cuenta</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Código:</th>
                                    <td>{{ $datos['cuenta']['codigo'] }}</td>
                                </tr>
                                <tr>
                                    <th>Nombre:</th>
                                    <td>{{ $datos['cuenta']['nombre'] }}</td>
                                </tr>
                                <tr>
                                    <th>Tipo:</th>
                                    <td>{{ $datos['cuenta']['tipo'] }}</td>
                                </tr>
                                <tr>
                                    <th>Naturaleza:</th>
                                    <td>{{ $datos['cuenta']['naturaleza'] }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Resumen</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Saldo Inicial:</th>
                                    <td class="text-right">{{ number_format($datos['saldo_inicial'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Débitos:</th>
                                    <td class="text-right">{{ number_format($datos['total_debe'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Créditos:</th>
                                    <td class="text-right">{{ number_format($datos['total_haber'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Saldo Final:</th>
                                    <td class="text-right">{{ number_format($datos['saldo_final'], 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h4>Movimientos</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>N° Asiento</th>
                                <th>Concepto</th>
                                <th class="text-right">Débito</th>
                                <th class="text-right">Crédito</th>
                                <th class="text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datos['movimientos'] as $movimiento)
                            <tr>
                                <td>{{ $movimiento['fecha'] }}</td>
                                <td>{{ $movimiento['numero_asiento'] }}</td>
                                <td>{{ $movimiento['concepto'] }}</td>
                                <td class="text-right">{{ number_format($movimiento['debe'], 2) }}</td>
                                <td class="text-right">{{ number_format($movimiento['haber'], 2) }}</td>
                                <td class="text-right">{{ number_format($movimiento['saldo'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-info">
                        Seleccione una cuenta para ver sus movimientos.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportarExcel() {
    if (!document.getElementById('cuenta_id').value) {
        alert('Por favor seleccione una cuenta');
        return;
    }
    
    window.location.href = "{{ route('contabilidad.reportes.libro-mayor') }}?exportar=excel&cuenta_id=" + 
        document.getElementById('cuenta_id').value + "&fecha_inicio=" + document.getElementById('fecha_inicio').value + 
        "&fecha_fin=" + document.getElementById('fecha_fin').value;
}
</script>
@endpush 