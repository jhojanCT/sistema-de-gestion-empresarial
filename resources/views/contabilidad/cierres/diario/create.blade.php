@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Nuevo Cierre Diario</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('contabilidad.cierres.diario.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" 
                                       class="form-control @error('fecha') is-invalid @enderror" 
                                       id="fecha" 
                                       name="fecha" 
                                       value="{{ old('fecha', $fecha->format('Y-m-d')) }}"
                                       required>
                                @error('fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="saldo_inicial" class="form-label">Saldo Inicial</label>
                                <input type="number" 
                                       step="0.01"
                                       class="form-control @error('saldo_inicial') is-invalid @enderror" 
                                       id="saldo_inicial" 
                                       name="saldo_inicial" 
                                       value="{{ old('saldo_inicial', $saldoInicial) }}"
                                       required>
                                @error('saldo_inicial')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

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

                        <!-- Resumen de Totales -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Resumen del Día</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Ventas -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Ventas</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Concepto</th>
                                                                <th class="text-end">Cantidad</th>
                                                                <th class="text-end">Subtotal</th>
                                                                <th class="text-end">IVA</th>
                                                                <th class="text-end">Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Ventas al Contado</td>
                                                                <td class="text-end">{{ $totales['cantidad_ventas_contado'] }}</td>
                                                                <td class="text-end">{{ number_format($totales['ventas_contado'] - $totales['iva_ventas_contado'], 2) }}</td>
                                                                <td class="text-end">{{ number_format($totales['iva_ventas_contado'], 2) }}</td>
                                                                <td class="text-end text-success">{{ number_format($totales['ventas_contado'], 2) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Ventas a Crédito</td>
                                                                <td class="text-end">{{ $totales['cantidad_ventas_credito'] }}</td>
                                                                <td class="text-end">{{ number_format($totales['ventas_credito'] - $totales['iva_ventas_credito'], 2) }}</td>
                                                                <td class="text-end">{{ number_format($totales['iva_ventas_credito'], 2) }}</td>
                                                                <td class="text-end">{{ number_format($totales['ventas_credito'], 2) }}</td>
                                                            </tr>
                                                            <tr class="table-light fw-bold">
                                                                <td>Total Ventas</td>
                                                                <td class="text-end">{{ $totales['cantidad_ventas_contado'] + $totales['cantidad_ventas_credito'] }}</td>
                                                                <td class="text-end">{{ number_format(($totales['ventas_contado'] + $totales['ventas_credito']) - ($totales['iva_ventas_contado'] + $totales['iva_ventas_credito']), 2) }}</td>
                                                                <td class="text-end">{{ number_format($totales['iva_ventas_contado'] + $totales['iva_ventas_credito'], 2) }}</td>
                                                                <td class="text-end">{{ number_format($totales['ventas_contado'] + $totales['ventas_credito'], 2) }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Compras -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Compras</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Concepto</th>
                                                                <th class="text-end">Cantidad</th>
                                                                <th class="text-end">Subtotal</th>
                                                                <th class="text-end">IVA</th>
                                                                <th class="text-end">Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Compras al Contado</td>
                                                                <td class="text-end">{{ $totales['cantidad_compras_contado'] }}</td>
                                                                <td class="text-end">{{ number_format($totales['compras_contado'] - $totales['iva_compras_contado'], 2) }}</td>
                                                                <td class="text-end">{{ number_format($totales['iva_compras_contado'], 2) }}</td>
                                                                <td class="text-end text-danger">{{ number_format($totales['compras_contado'], 2) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Compras a Crédito</td>
                                                                <td class="text-end">{{ $totales['cantidad_compras_credito'] }}</td>
                                                                <td class="text-end">{{ number_format($totales['compras_credito'] - $totales['iva_compras_credito'], 2) }}</td>
                                                                <td class="text-end">{{ number_format($totales['iva_compras_credito'], 2) }}</td>
                                                                <td class="text-end">{{ number_format($totales['compras_credito'], 2) }}</td>
                                                            </tr>
                                                            <tr class="table-light fw-bold">
                                                                <td>Total Compras</td>
                                                                <td class="text-end">{{ $totales['cantidad_compras_contado'] + $totales['cantidad_compras_credito'] }}</td>
                                                                <td class="text-end">{{ number_format(($totales['compras_contado'] + $totales['compras_credito']) - ($totales['iva_compras_contado'] + $totales['iva_compras_credito']), 2) }}</td>
                                                                <td class="text-end">{{ number_format($totales['iva_compras_contado'] + $totales['iva_compras_credito'], 2) }}</td>
                                                                <td class="text-end">{{ number_format($totales['compras_contado'] + $totales['compras_credito'], 2) }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cobros y Pagos -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Cobros y Pagos</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Concepto</th>
                                                                <th class="text-end">Cantidad</th>
                                                                <th class="text-end">Monto</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Cobros de Créditos</td>
                                                                <td class="text-end">{{ $totales['cantidad_cobros'] }}</td>
                                                                <td class="text-end text-success">{{ number_format($totales['cobros_credito'], 2) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Pagos de Créditos</td>
                                                                <td class="text-end">{{ $totales['cantidad_pagos'] }}</td>
                                                                <td class="text-end text-danger">{{ number_format($totales['pagos_credito'], 2) }}</td>
                                                            </tr>
                                                            <tr class="table-light fw-bold">
                                                                <td>Neto Cobros/Pagos</td>
                                                                <td class="text-end">{{ $totales['cantidad_cobros'] + $totales['cantidad_pagos'] }}</td>
                                                                <td class="text-end {{ ($totales['cobros_credito'] - $totales['pagos_credito']) >= 0 ? 'text-success' : 'text-danger' }}">
                                                                    {{ number_format($totales['cobros_credito'] - $totales['pagos_credito'], 2) }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Otros Movimientos -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Otros Movimientos</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Concepto</th>
                                                                <th class="text-end">Cantidad</th>
                                                                <th class="text-end">Monto</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Gastos</td>
                                                                <td class="text-end">{{ $totales['cantidad_gastos'] }}</td>
                                                                <td class="text-end text-danger">{{ number_format($totales['gastos'], 2) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Otros Ingresos</td>
                                                                <td class="text-end">{{ $totales['cantidad_otros_ingresos'] }}</td>
                                                                <td class="text-end text-success">{{ number_format($totales['otros_ingresos'], 2) }}</td>
                                                            </tr>
                                                            <tr class="table-light fw-bold">
                                                                <td>Neto Otros</td>
                                                                <td class="text-end">{{ $totales['cantidad_gastos'] + $totales['cantidad_otros_ingresos'] }}</td>
                                                                <td class="text-end {{ ($totales['otros_ingresos'] - $totales['gastos']) >= 0 ? 'text-success' : 'text-danger' }}">
                                                                    {{ number_format($totales['otros_ingresos'] - $totales['gastos'], 2) }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Resumen de IVA -->
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header bg-info text-white">
                                                <h6 class="mb-0">Resumen de IVA</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="card bg-light">
                                                            <div class="card-body">
                                                                <h6 class="card-title">IVA Ventas Contado</h6>
                                                                <p class="card-text h4 text-success mb-0">{{ number_format($totales['iva_ventas_contado'], 2) }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="card bg-light">
                                                            <div class="card-body">
                                                                <h6 class="card-title">IVA Ventas Crédito</h6>
                                                                <p class="card-text h4 text-success mb-0">{{ number_format($totales['iva_ventas_credito'], 2) }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="card bg-light">
                                                            <div class="card-body">
                                                                <h6 class="card-title">IVA Compras Contado</h6>
                                                                <p class="card-text h4 text-danger mb-0">{{ number_format($totales['iva_compras_contado'], 2) }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="card bg-light">
                                                            <div class="card-body">
                                                                <h6 class="card-title">IVA Compras Crédito</h6>
                                                                <p class="card-text h4 text-danger mb-0">{{ number_format($totales['iva_compras_credito'], 2) }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card bg-light">
                                                            <div class="card-body">
                                                                <h6 class="card-title">Total IVA Ventas</h6>
                                                                <p class="card-text h4 text-success mb-0">{{ number_format($totales['iva_ventas_contado'] + $totales['iva_ventas_credito'], 2) }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card bg-light">
                                                            <div class="card-body">
                                                                <h6 class="card-title">Total IVA Compras</h6>
                                                                <p class="card-text h4 text-danger mb-0">{{ number_format($totales['iva_compras_contado'] + $totales['iva_compras_credito'], 2) }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="card bg-light">
                                                            <div class="card-body">
                                                                <h6 class="card-title">IVA a Pagar</h6>
                                                                <p class="card-text h4 {{ ($totales['iva_ventas_contado'] + $totales['iva_ventas_credito'] - $totales['iva_compras_contado'] - $totales['iva_compras_credito']) >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                                                                    {{ number_format($totales['iva_ventas_contado'] + $totales['iva_ventas_credito'] - $totales['iva_compras_contado'] - $totales['iva_compras_credito'], 2) }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('contabilidad.cierres.diario.index') }}" class="btn btn-secondary">
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