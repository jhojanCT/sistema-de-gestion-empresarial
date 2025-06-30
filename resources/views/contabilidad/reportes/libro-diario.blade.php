@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Libro Diario</h3>
                    <div class="card-tools">
                        <form id="filtroForm" class="form-inline">
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
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4>Período: {{ \Carbon\Carbon::parse($datos['periodo']['inicio'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($datos['periodo']['fin'])->format('d/m/Y') }}</h4>
                        </div>
                    </div>

                    @foreach($datos['asientos'] as $asiento)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($asiento['fecha'])->format('d/m/Y') }}
                                </div>
                                <div class="col-md-3">
                                    <strong>N° Asiento:</strong> {{ $asiento['numero_asiento'] }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Concepto:</strong> {{ $asiento['concepto'] }}
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Cuenta</th>
                                        <th>Descripción</th>
                                        <th class="text-right">Débito</th>
                                        <th class="text-right">Crédito</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($asiento['detalles'] as $detalle)
                                    <tr>
                                        <td>{{ $detalle['cuenta']['codigo'] }}</td>
                                        <td>{{ $detalle['cuenta']['nombre'] }}</td>
                                        <td>{{ $detalle['descripcion'] }}</td>
                                        <td class="text-right">{{ number_format($detalle['debe'], 2) }}</td>
                                        <td class="text-right">{{ number_format($detalle['haber'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td colspan="3" class="text-right">Totales:</td>
                                        <td class="text-right">{{ number_format(collect($asiento['detalles'])->sum('debe'), 2) }}</td>
                                        <td class="text-right">{{ number_format(collect($asiento['detalles'])->sum('haber'), 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportarExcel() {
    window.location.href = "{{ route('contabilidad.reportes.libro-diario') }}?exportar=excel&fecha_inicio=" + 
        document.getElementById('fecha_inicio').value + "&fecha_fin=" + document.getElementById('fecha_fin').value;
}
</script>
@endpush 