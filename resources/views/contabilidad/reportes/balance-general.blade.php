@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Balance General</h3>
                    <div class="card-tools">
                        <form id="filtroForm" class="form-inline">
                            <div class="form-group mx-sm-3">
                                <input type="date" class="form-control" id="fecha" name="fecha" value="{{ request('fecha', now()->format('Y-m-d')) }}">
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
                        <div class="col-md-6">
                            <h4>Activos</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Cuenta</th>
                                        <th class="text-right">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($datos['activos'] as $activo)
                                    <tr>
                                        <td>{{ $activo['codigo'] }}</td>
                                        <td>{{ $activo['nombre'] }}</td>
                                        <td class="text-right">{{ number_format($activo['saldo'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="font-weight-bold">
                                        <td colspan="2" class="text-right">Total Activos:</td>
                                        <td class="text-right">{{ number_format($datos['total_activos'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Pasivos y Patrimonio</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Cuenta</th>
                                        <th class="text-right">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($datos['pasivos'] as $pasivo)
                                    <tr>
                                        <td>{{ $pasivo['codigo'] }}</td>
                                        <td>{{ $pasivo['nombre'] }}</td>
                                        <td class="text-right">{{ number_format($pasivo['saldo'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="font-weight-bold">
                                        <td colspan="2" class="text-right">Total Pasivos:</td>
                                        <td class="text-right">{{ number_format($datos['total_pasivos'], 2) }}</td>
                                    </tr>
                                    @foreach($datos['patrimonio'] as $patrimonio)
                                    <tr>
                                        <td>{{ $patrimonio['codigo'] }}</td>
                                        <td>{{ $patrimonio['nombre'] }}</td>
                                        <td class="text-right">{{ number_format($patrimonio['saldo'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="font-weight-bold">
                                        <td colspan="2" class="text-right">Total Patrimonio:</td>
                                        <td class="text-right">{{ number_format($datos['total_patrimonio'], 2) }}</td>
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
    window.location.href = "{{ route('contabilidad.reportes.balance-general') }}?exportar=excel&fecha=" + document.getElementById('fecha').value;
}
</script>
@endpush 