@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalle de Materia Prima Filtrada</h1>
    
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ $materiaPrima->nombre }}</h5>
            <p class="card-text"><strong>Unidad de Medida:</strong> {{ $materiaPrima->unidad_medida }}</p>
            <p class="card-text"><strong>Stock Disponible:</strong> {{ $materiaPrima->stock }}</p>
        </div>
    </div>
    
    <a href="{{ route('materia-prima-filtrada.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection