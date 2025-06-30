@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nuevo Cierre Anual</h5>
                    <a href="{{ route('contabilidad.cierres.anual.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <!-- Resumen de Totales -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light border-primary">
                                <div class="card-body py-2 px-3">
                                    <span class="fw-bold text-primary">Total Activos</span>
                                    <div class="h5 mb-0">{{ number_format($totales['activos'], 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-danger">
                                <div class="card-body py-2 px-3">
                                    <span class="fw-bold text-danger">Total Pasivos</span>
                                    <div class="h5 mb-0">{{ number_format($totales['pasivos'], 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-success">
                                <div class="card-body py-2 px-3">
                                    <span class="fw-bold text-success">Total Patrimonio</span>
                                    <div class="h5 mb-0">{{ number_format($totales['patrimonio'], 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-info">
                                <div class="card-body py-2 px-3">
                                    <span class="fw-bold text-info">Utilidad Neta</span>
                                    <div class="h5 mb-0">{{ number_format($totales['utilidad_neta'], 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('contabilidad.cierres.anual.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="anio" class="form-label">Año</label>
                                    <input type="number" 
                                           class="form-control @error('anio') is-invalid @enderror" 
                                           id="anio" 
                                           name="anio" 
                                           value="{{ old('anio', $anio) }}" 
                                           required>
                                    @error('anio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                              id="observaciones" 
                                              name="observaciones" 
                                              rows="3">{{ old('observaciones') }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Resumen del Período</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th colspan="2" class="text-center">Balance General</th>
                                                        <th colspan="2" class="text-center">Estado de Resultados</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Total Activos</td>
                                                        <td class="text-end">{{ number_format($totales['activos'], 2) }}</td>
                                                        <td>Total Ingresos</td>
                                                        <td class="text-end">{{ number_format($totales['ingresos'], 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Pasivos</td>
                                                        <td class="text-end">{{ number_format($totales['pasivos'], 2) }}</td>
                                                        <td>Total Egresos</td>
                                                        <td class="text-end">{{ number_format($totales['egresos'], 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Patrimonio</td>
                                                        <td class="text-end">{{ number_format($totales['patrimonio'], 2) }}</td>
                                                        <td>Total Ventas</td>
                                                        <td class="text-end">{{ number_format($totales['ventas'], 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td>Total Compras</td>
                                                        <td class="text-end">{{ number_format($totales['compras'], 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td>Depreciación</td>
                                                        <td class="text-end">{{ number_format($totales['depreciacion'], 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td>Provisiones</td>
                                                        <td class="text-end">{{ number_format($totales['provisiones'], 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td><strong>Utilidad Bruta</strong></td>
                                                        <td class="text-end"><strong>{{ number_format($totales['utilidad_bruta'], 2) }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td><strong>Utilidad Neta</strong></td>
                                                        <td class="text-end"><strong>{{ number_format($totales['utilidad_neta'], 2) }}</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('contabilidad.cierres.anual.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 