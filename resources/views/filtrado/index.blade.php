@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-filter text-primary"></i> Procesos de Filtrado
                        </h5>
                        <a href="{{ route('filtrado.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Proceso
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Materia Prima</th>
                                    <th class="text-end">Cantidad Entrada</th>
                                    <th class="text-end">Cantidad Salida</th>
                                    <th class="text-end">Desperdicio</th>
                                    <th class="text-end">% Desperdicio</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($filtrados as $filtrado)
                                    <tr>
                                        <td>{{ $filtrado->fecha->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="fw-medium">{{ $filtrado->materiaPrimaSinFiltrar->nombre }}</span>
                                            <br>
                                            <small class="text-muted">
                                                Stock actual: {{ number_format($filtrado->materiaPrimaSinFiltrar->stock, 2) }} 
                                                {{ $filtrado->materiaPrimaSinFiltrar->unidad_medida }}
                                            </small>
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($filtrado->cantidad_entrada, 2) }}
                                            <small class="text-muted">{{ $filtrado->materiaPrimaSinFiltrar->unidad_medida }}</small>
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($filtrado->cantidad_salida, 2) }}
                                            <small class="text-muted">{{ $filtrado->materiaPrimaSinFiltrar->unidad_medida }}</small>
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($filtrado->desperdicio, 2) }}
                                            <small class="text-muted">{{ $filtrado->materiaPrimaSinFiltrar->unidad_medida }}</small>
                                        </td>
                                        <td class="text-end">
                                            @php
                                                $porcentajeDesperdicio = $filtrado->cantidad_entrada > 0 
                                                    ? ($filtrado->desperdicio / $filtrado->cantidad_entrada) * 100 
                                                    : 0;
                                            @endphp
                                            <span class="badge {{ $porcentajeDesperdicio > 20 ? 'bg-danger' : 'bg-success' }}">
                                                {{ number_format($porcentajeDesperdicio, 1) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('filtrado.edit', $filtrado) }}" 
                                                    class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-filter fa-3x mb-3"></i>
                                                <p class="mb-0">No hay procesos de filtrado registrados</p>
                                                <small>Haga clic en "Nuevo Proceso" para comenzar</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($filtrados->isNotEmpty())
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="2"><strong>Totales</strong></td>
                                        <td class="text-end">
                                            <strong>
                                                {{ number_format($filtrados->sum('cantidad_entrada'), 2) }}
                                            </strong>
                                        </td>
                                        <td class="text-end">
                                            <strong>
                                                {{ number_format($filtrados->sum('cantidad_salida'), 2) }}
                                            </strong>
                                        </td>
                                        <td class="text-end">
                                            <strong>
                                                {{ number_format($filtrados->sum('desperdicio'), 2) }}
                                            </strong>
                                        </td>
                                        <td class="text-end">
                                            <strong>
                                                {{ number_format(($filtrados->sum('desperdicio') / $filtrados->sum('cantidad_entrada')) * 100, 1) }}%
                                            </strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection