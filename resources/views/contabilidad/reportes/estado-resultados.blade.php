@extends('layouts.app')

@section('title', 'Estado de Resultados')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Estado de Resultados</h3>
                    <div class="card-tools">
                        <form id="filtroForm" class="form-inline">
                            <div class="form-group mx-sm-3">
                                <label for="fecha_inicio" class="mr-2">Desde:</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ $datos['periodo']['inicio'] }}">
                            </div>
                            <div class="form-group mx-sm-3">
                                <label for="fecha_fin" class="mr-2">Hasta:</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ $datos['periodo']['fin'] }}">
                            </div>
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <button type="button" class="btn btn-success ml-2" onclick="exportarExcel()">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Cuenta</th>
                                        <th class="text-right">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-primary">
                                        <td colspan="3"><strong>INGRESOS</strong></td>
                                    </tr>
                                    @foreach($datos['ingresos'] as $ingreso)
                                    <tr>
                                        <td>{{ $ingreso['codigo'] }}</td>
                                        <td>{{ $ingreso['nombre'] }}</td>
                                        <td class="text-right">{{ number_format($ingreso['saldo'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="font-weight-bold">
                                        <td colspan="2" class="text-right">Total Ingresos:</td>
                                        <td class="text-right">{{ number_format($datos['total_ingresos'], 2) }}</td>
                                    </tr>

                                    <tr class="table-primary">
                                        <td colspan="3"><strong>COSTOS</strong></td>
                                    </tr>
                                    @foreach($datos['costos'] as $costo)
                                    <tr>
                                        <td>{{ $costo['codigo'] }}</td>
                                        <td>{{ $costo['nombre'] }}</td>
                                        <td class="text-right">{{ number_format($costo['saldo'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="font-weight-bold">
                                        <td colspan="2" class="text-right">Total Costos:</td>
                                        <td class="text-right">{{ number_format($datos['total_costos'], 2) }}</td>
                                    </tr>

                                    <tr class="table-info">
                                        <td colspan="2" class="text-right"><strong>UTILIDAD BRUTA:</strong></td>
                                        <td class="text-right font-weight-bold">{{ number_format($datos['utilidad_bruta'], 2) }}</td>
                                    </tr>

                                    <tr class="table-primary">
                                        <td colspan="3"><strong>GASTOS</strong></td>
                                    </tr>
                                    @foreach($datos['gastos'] as $gasto)
                                    <tr>
                                        <td>{{ $gasto['codigo'] }}</td>
                                        <td>{{ $gasto['nombre'] }}</td>
                                        <td class="text-right">{{ number_format($gasto['saldo'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="font-weight-bold">
                                        <td colspan="2" class="text-right">Total Gastos:</td>
                                        <td class="text-right">{{ number_format($datos['total_gastos'], 2) }}</td>
                                    </tr>

                                    <tr class="table-info">
                                        <td colspan="2" class="text-right"><strong>UTILIDAD NETA:</strong></td>
                                        <td class="text-right font-weight-bold">{{ number_format($datos['utilidad_neta'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
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
    window.location.href = "{{ route('contabilidad.reportes.estado-resultados') }}?exportar=excel&fecha_inicio=" + 
        document.getElementById('fecha_inicio').value + "&fecha_fin=" + document.getElementById('fecha_fin').value;
}
</script>
@endpush 