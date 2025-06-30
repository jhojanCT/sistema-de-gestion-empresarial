@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Reporte de Desperdicio</h1>
    
    <form method="GET" action="{{ route('reportes.desperdicio') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
            </div>
            <div class="col-md-4">
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('reportes.desperdicio') }}" class="btn btn-secondary ml-2">Limpiar</a>
            </div>
        </div>
    </form>
    
    @if($filtrados->isNotEmpty())
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Resumen</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Total Entrada:</strong> {{ $totalEntrada }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Total Salida:</strong> {{ $totalSalida }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Total Desperdicio:</strong> {{ $totalDesperdicio }} ({{ number_format($porcentajeDesperdicio, 2) }}%)</p>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Materia Prima</th>
                <th>Entrada</th>
                <th>Salida</th>
                <th>Desperdicio</th>
                <th>Rendimiento</th>
            </tr>
        </thead>
        <tbody>
            @foreach($filtrados as $filtrado)
            <tr>
                <td>{{ $filtrado->fecha->format('d/m/Y') }}</td>
                <td>{{ $filtrado->materiaPrimaSinFiltrar->nombre }}</td>
                <td>{{ $filtrado->cantidad_entrada }} {{ $filtrado->materiaPrimaSinFiltrar->unidad_medida }}</td>
                <td>{{ $filtrado->cantidad_salida }} {{ $filtrado->materiaPrimaSinFiltrar->unidad_medida }}</td>
                <td>{{ $filtrado->desperdicio }} {{ $filtrado->materiaPrimaSinFiltrar->unidad_medida }}</td>
                <td>{{ number_format(($filtrado->cantidad_salida / $filtrado->cantidad_entrada) * 100, 2) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    @if($filtrados->isEmpty())
    <div class="alert alert-info">
        No se encontraron registros para el rango de fechas seleccionado.
    </div>
    @endif
</div>
@endsection