@extends('layouts.app')

@section('title', 'Balance de Comprobación')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Balance de Comprobación</h3>
                    <div class="card-tools">
                        <form id="filtroForm" class="form-inline">
                            <div class="form-group mx-sm-3">
                                <label for="fecha" class="mr-2">Fecha:</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" value="{{ $datos['fecha'] }}">
                            </div>
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <button type="button" class="btn btn-success ml-2" onclick="exportarExcel()">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4>Fecha: {{ \Carbon\Carbon::parse($datos['fecha'])->format('d/m/Y') }}</h4>
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Cuenta</th>
                                <th class="text-right">Débito</th>
                                <th class="text-right">Crédito</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datos['cuentas'] as $cuenta)
                            <tr>
                                <td>{{ $cuenta->codigo }}</td>
                                <td>{{ $cuenta->nombre }}</td>
                                <td class="text-right">{{ number_format($cuenta->total_debe, 2) }}</td>
                                <td class="text-right">{{ number_format($cuenta->total_haber, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr class="font-weight-bold">
                                <td colspan="2" class="text-right">TOTALES:</td>
                                <td class="text-right">{{ number_format($datos['total_debe'], 2) }}</td>
                                <td class="text-right">{{ number_format($datos['total_haber'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="alert {{ $datos['total_debe'] == $datos['total_haber'] ? 'alert-success' : 'alert-danger' }}">
                                <h5>Estado del Balance:</h5>
                                <p class="mb-0">
                                    @if($datos['total_debe'] == $datos['total_haber'])
                                        <i class="fas fa-check-circle"></i> El balance está cuadrado. Los totales de débito y crédito son iguales.
                                    @else
                                        <i class="fas fa-exclamation-circle"></i> El balance no está cuadrado. Hay una diferencia de {{ number_format(abs($datos['total_debe'] - $datos['total_haber']), 2) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportarExcel() {
    window.location.href = "{{ route('contabilidad.reportes.balance-comprobacion') }}?exportar=excel&fecha=" + document.getElementById('fecha').value;
}
</script>
@endpush 