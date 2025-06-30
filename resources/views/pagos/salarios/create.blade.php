@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-money-bill-wave me-2"></i>Nuevo Pago de Salarios
                </h1>
                <a href="{{ route('pagos.salarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('pagos.salarios.store') }}" method="POST" id="pagoForm">
        @csrf
        
        <div class="row">
            <!-- Información General -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle me-2"></i>Información General
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="fecha_pago" class="form-label">Fecha de Pago</label>
                            <input type="date" class="form-control @error('fecha_pago') is-invalid @enderror" 
                                   id="fecha_pago" name="fecha_pago" value="{{ old('fecha_pago', date('Y-m-d')) }}" required>
                            @error('fecha_pago')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="metodo_pago" class="form-label">Método de Pago</label>
                            <select class="form-control @error('metodo_pago') is-invalid @enderror" 
                                    id="metodo_pago" name="metodo_pago" required>
                                <option value="">Seleccione método de pago</option>
                                <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                <option value="cheque" {{ old('metodo_pago') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            </select>
                            @error('metodo_pago')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="comprobante" class="form-label">Número de Comprobante</label>
                            <input type="text" class="form-control @error('comprobante') is-invalid @enderror" 
                                   id="comprobante" name="comprobante" value="{{ old('comprobante') }}">
                            @error('comprobante')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control @error('observaciones') is-invalid @enderror" 
                                      id="observaciones" name="observaciones" rows="2">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="auto_asiento" name="auto_asiento" value="1" 
                                   {{ old('auto_asiento') ? 'checked' : '' }}>
                            <label class="form-check-label" for="auto_asiento">Generar asiento contable automáticamente</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles de Pago -->
            <div class="col-xl-8 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="fas fa-users me-2"></i>Detalles de Pago
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Empleados Pendientes -->
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-user-plus me-2"></i>Empleados Pendientes
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="detallesTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                                </div>
                                            </th>
                                            <th>Empleado</th>
                                            <th>Cargo</th>
                                            <th>Monto</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($empleadosPendientes as $empleado)
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input empleado-check" 
                                                               name="detalles[{{ $loop->index }}][seleccionado]" value="1">
                                                        <input type="hidden" name="detalles[{{ $loop->index }}][empleado_id]" value="{{ $empleado->id }}">
                                                    </div>
                                                </td>
                                                <td>{{ $empleado->nombre }}</td>
                                                <td>{{ $empleado->cargo ?? 'N/A' }}</td>
                                                <td>
                                                    <input type="number" step="0.01" min="0.01" class="form-control monto-input" 
                                                           name="detalles[{{ $loop->index }}][monto]" disabled>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" 
                                                           name="detalles[{{ $loop->index }}][observaciones]" disabled>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Empleados con Pagos Pendientes -->
                        @if($empleadosConPagosPendientes->isNotEmpty())
                            <div class="mb-4">
                                <h6 class="font-weight-bold text-warning mb-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Empleados con Pagos Pendientes
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Empleado</th>
                                                <th>Cargo</th>
                                                <th>Fecha Pago Pendiente</th>
                                                <th>Monto Pendiente</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($empleadosConPagosPendientes as $empleado)
                                                @foreach($empleado->pagosSalarios as $pago)
                                                    <tr>
                                                        <td>{{ $empleado->nombre }}</td>
                                                        <td>{{ $empleado->cargo ?? 'N/A' }}</td>
                                                        <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                                        <td class="text-end">Bs. {{ number_format($pago->monto_total, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <h5 class="mb-0">Total: <span id="totalMonto" class="text-success">Bs. 0.00</span></h5>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Pago
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('pagoForm');
    const detallesTable = document.getElementById('detallesTable');
    const totalMontoSpan = document.getElementById('totalMonto');
    const selectAllCheckbox = document.getElementById('selectAll');

    // Función para calcular el total
    function calcularTotal() {
        let total = 0;
        document.querySelectorAll('.monto-input:not([disabled])').forEach(input => {
            total += parseFloat(input.value || 0);
        });
        totalMontoSpan.textContent = 'Bs. ' + total.toFixed(2);
    }

    // Función para habilitar/deshabilitar campos
    function toggleCampos(row, enabled) {
        const inputs = row.querySelectorAll('input:not([type="checkbox"]):not([type="hidden"])');
        inputs.forEach(input => {
            input.disabled = !enabled;
            if (!enabled) {
                input.value = '';
            }
        });
    }

    // Evento para seleccionar/deseleccionar todos
    selectAllCheckbox.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.empleado-check');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
            const row = checkbox.closest('tr');
            toggleCampos(row, this.checked);
        });
        calcularTotal();
    });

    // Evento para checkbox individual
    detallesTable.addEventListener('change', function(e) {
        if (e.target.classList.contains('empleado-check')) {
            const row = e.target.closest('tr');
            toggleCampos(row, e.target.checked);
            calcularTotal();
        }
    });

    // Evento para calcular total cuando cambia un monto
    detallesTable.addEventListener('input', function(e) {
        if (e.target.classList.contains('monto-input')) {
            calcularTotal();
        }
    });

    // Validar formulario antes de enviar
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let total = 0;
        let hayMontos = false;
        let empleadosSeleccionados = 0;
        
        document.querySelectorAll('.empleado-check').forEach(checkbox => {
            if (checkbox.checked) {
                empleadosSeleccionados++;
                const row = checkbox.closest('tr');
                const montoInput = row.querySelector('.monto-input');
                if (montoInput.value && parseFloat(montoInput.value) > 0) {
                    total += parseFloat(montoInput.value);
                    hayMontos = true;
                }
            }
        });

        if (empleadosSeleccionados === 0) {
            alert('Debe seleccionar al menos un empleado');
            return;
        }

        if (!hayMontos) {
            alert('Debe ingresar al menos un monto mayor a 0');
            return;
        }

        if (total <= 0) {
            alert('El monto total debe ser mayor a 0');
            return;
        }

        this.submit();
    });
});
</script>
@endpush
@endsection 