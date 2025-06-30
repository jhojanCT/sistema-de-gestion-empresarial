@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pagos Pendientes de Asiento Contable</h3>
                </div>
                <div class="card-body">
                    <!-- Pagos de Salarios -->
                    <h4>Pagos de Salarios</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha Pago</th>
                                    <th>Total Empleados</th>
                                    <th>Monto Total</th>
                                    <th>Método</th>
                                    <th>Comprobante</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pagosSalarios as $pago)
                                <tr>
                                    <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                    <td>{{ $pago->detalles->count() }}</td>
                                    <td>Bs. {{ number_format($pago->monto_total, 2) }}</td>
                                    <td>{{ ucfirst($pago->metodo_pago) }}</td>
                                    <td>{{ $pago->comprobante ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('contabilidad.asientos.generar-pago') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="tipo" value="salario">
                                            <input type="hidden" name="id" value="{{ $pago->id }}">
                                            <input type="hidden" name="auto_asiento" value="1">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-magic"></i> Asiento Automático
                                            </button>
                                        </form>
                                        <a href="{{ route('contabilidad.asientos.create', ['tipo' => 'pago_salario', 'id' => $pago->id]) }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-edit"></i> Asiento Manual
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hay pagos de salarios pendientes de asiento contable</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $pagosSalarios->links() }}

                    <!-- Pagos de Clientes -->
                    <h4 class="mt-4">Pagos de Clientes</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha Pago</th>
                                    <th>Venta #</th>
                                    <th>Cliente</th>
                                    <th>Monto</th>
                                    <th>Método</th>
                                    <th>Comprobante</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pagosClientes as $pago)
                                <tr>
                                    <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                    <td>{{ $pago->venta->id }}</td>
                                    <td>{{ $pago->venta->cliente->nombre ?? 'Consumidor Final' }}</td>
                                    <td>{{ number_format($pago->monto, 2) }}</td>
                                    <td>{{ ucfirst($pago->metodo_pago) }}</td>
                                    <td>{{ $pago->comprobante ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('contabilidad.asientos.generar-pago') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="tipo" value="cliente">
                                            <input type="hidden" name="id" value="{{ $pago->id }}">
                                            <input type="hidden" name="auto_asiento" value="1">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-magic"></i> Asiento Automático
                                            </button>
                                        </form>
                                        <a href="{{ route('contabilidad.asientos.create', ['tipo' => 'pago_cliente', 'id' => $pago->id]) }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-edit"></i> Asiento Manual
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No hay pagos de clientes pendientes de asiento contable</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $pagosClientes->links() }}

                    <!-- Pagos a Proveedores -->
                    <h4 class="mt-4">Pagos a Proveedores</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha Pago</th>
                                    <th>Compra #</th>
                                    <th>Proveedor</th>
                                    <th>Monto</th>
                                    <th>Método</th>
                                    <th>Comprobante</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pagosProveedores as $pago)
                                @if($pago->compra->tipo === 'credito') // Solo mostrar pagos de compras a crédito
                                <tr>
                                    <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                    <td>{{ $pago->compra->id }}</td>
                                    <td>{{ $pago->compra->proveedor->nombre }}</td>
                                    <td>{{ number_format($pago->monto, 2) }}</td>
                                    <td>{{ ucfirst($pago->metodo_pago) }}</td>
                                    <td>{{ $pago->comprobante ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('contabilidad.asientos.generar-pago') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="tipo" value="proveedor">
                                            <input type="hidden" name="id" value="{{ $pago->id }}">
                                            <input type="hidden" name="auto_asiento" value="1">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-magic"></i> Asiento Automático
                                            </button>
                                        </form>
                                        <a href="{{ route('contabilidad.asientos.create', ['tipo' => 'pago_proveedor', 'id' => $pago->id]) }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-edit"></i> Asiento Manual
                                        </a>
                                    </td>
                                </tr>
                                @endif
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No hay pagos a proveedores pendientes de asiento contable</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $pagosProveedores->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .dropdown-menu {
        z-index: 9999 !important;
    }
    .table-responsive {
        overflow: visible !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush