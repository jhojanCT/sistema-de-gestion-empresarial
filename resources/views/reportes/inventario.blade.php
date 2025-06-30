@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Reporte de Inventario</h1>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Materias Primas Sin Filtrar
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Stock</th>
                        <th>Unidad</th>
                        <th>Última Compra</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materiasPrimasSinFiltrar as $materia)
                    <tr>
                        <td>{{ $materia->nombre }}</td>
                        <td>{{ $materia->stock }}</td>
                        <td>{{ $materia->unidad_medida }}</td>
                        <td>
                            @if($materia->compras->count() > 0)
                                {{ $materia->compras->first()->fecha->format('d/m/Y') }}
                            @else
                                Sin compras
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            Materias Primas Filtradas
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Stock</th>
                        <th>Unidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materiasPrimasFiltradas as $materia)
                    <tr>
                        <td>{{ $materia->nombre }}</td>
                        <td>{{ $materia->stock }}</td>
                        <td>{{ $materia->unidad_medida }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-info text-white">
            Productos Terminados
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Stock</th>
                        <th>Unidad</th>
                        <th>Producciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $producto)
                    <tr>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->stock }}</td>
                        <td>{{ $producto->unidad_medida }}</td>
                        <td>{{ $producto->producciones->count() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection