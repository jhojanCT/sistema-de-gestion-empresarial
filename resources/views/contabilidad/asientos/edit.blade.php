@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-edit"></i> Editar Asiento Contable
                    </h3>
                </div>

                <div class="card-body">
                    <form action="{{ route('contabilidad.asientos.update', $asiento) }}" method="POST" id="asientoForm">
                    @csrf
                    @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecha" class="form-label">Fecha</label>
                                    <input type="date" class="form-control @error('fecha') is-invalid @enderror" 
                                           id="fecha" name="fecha" value="{{ old('fecha', $asiento->fecha->format('Y-m-d')) }}" required>
                                    @error('fecha')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo_operacion" class="form-label">Tipo de Operación</label>
                                    <select class="form-select @error('tipo_operacion') is-invalid @enderror" 
                                            id="tipo_operacion" name="tipo_operacion" required>
                                        <option value="">Seleccione...</option>
                                        <option value="venta" {{ old('tipo_operacion', $asiento->tipo_operacion) == 'venta' ? 'selected' : '' }}>Venta</option>
                                        <option value="compra" {{ old('tipo_operacion', $asiento->tipo_operacion) == 'compra' ? 'selected' : '' }}>Compra</option>
                                        <option value="gasto" {{ old('tipo_operacion', $asiento->tipo_operacion) == 'gasto' ? 'selected' : '' }}>Gasto</option>
                                        <option value="ingreso" {{ old('tipo_operacion', $asiento->tipo_operacion) == 'ingreso' ? 'selected' : '' }}>Ingreso</option>
                                    </select>
                                    @error('tipo_operacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="concepto" class="form-label">Concepto</label>
                                    <input type="text" class="form-control @error('concepto') is-invalid @enderror" 
                                           id="concepto" name="concepto" value="{{ old('concepto', $asiento->concepto) }}" required>
                                    @error('concepto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="documento_id" class="form-label">Documento Relacionado</label>
                                    <select class="form-select @error('documento_id') is-invalid @enderror" 
                                            id="documento_id" name="documento_id">
                                        <option value="">Seleccione...</option>
                                        @if($asiento->documento_id)
                                            <option value="{{ $asiento->documento_id }}" selected>
                                                {{ $asiento->tipo_documento }} {{ $asiento->numero_documento }}
                                            </option>
                                        @endif
                                    </select>
                                    @error('documento_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo_documento" class="form-label">Tipo de Documento</label>
                                    <select class="form-select @error('tipo_documento') is-invalid @enderror" 
                                            id="tipo_documento" name="tipo_documento">
                                        <option value="">Seleccione...</option>
                                        <option value="factura" {{ old('tipo_documento', $asiento->tipo_documento) == 'factura' ? 'selected' : '' }}>Factura</option>
                                        <option value="nota_credito" {{ old('tipo_documento', $asiento->tipo_documento) == 'nota_credito' ? 'selected' : '' }}>Nota de Crédito</option>
                                        <option value="nota_debito" {{ old('tipo_documento', $asiento->tipo_documento) == 'nota_debito' ? 'selected' : '' }}>Nota de Débito</option>
                                    </select>
                                    @error('tipo_documento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="numero_documento" class="form-label">Número de Documento</label>
                                    <input type="text" class="form-control @error('numero_documento') is-invalid @enderror" 
                                           id="numero_documento" name="numero_documento" value="{{ old('numero_documento', $asiento->numero_documento) }}">
                                    @error('numero_documento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                            </div>
                        </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecha_documento" class="form-label">Fecha del Documento</label>
                                    <input type="date" class="form-control @error('fecha_documento') is-invalid @enderror" 
                                           id="fecha_documento" name="fecha_documento" value="{{ old('fecha_documento', $asiento->fecha_documento ? $asiento->fecha_documento->format('Y-m-d') : '') }}">
                                    @error('fecha_documento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-list"></i> Detalles del Asiento
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="detallesTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Cuenta</th>
                                                <th>Descripción</th>
                                                <th>Debe</th>
                                                <th>Haber</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($asiento->detalles as $index => $detalle)
                                                <tr>
                                                    <td>
                                                    <select class="form-select cuenta-select" name="detalles[{{ $index }}][cuenta_id]" required>
                                                            <option value="">Seleccione...</option>
                                                            @foreach($cuentas as $cuenta)
                                                            <option value="{{ $cuenta->id }}" {{ $detalle->cuenta_id == $cuenta->id ? 'selected' : '' }}>
                                                                    {{ $cuenta->codigo }} - {{ $cuenta->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                    <input type="text" class="form-control" name="detalles[{{ $index }}][descripcion]" 
                                                           value="{{ $detalle->descripcion }}" required>
                                                    </td>
                                                    <td>
                                                    <input type="number" class="form-control debe-input" name="detalles[{{ $index }}][debe]" 
                                                           value="{{ $detalle->debe }}" step="0.01" min="0">
                                                    </td>
                                                    <td>
                                                    <input type="number" class="form-control haber-input" name="detalles[{{ $index }}][haber]" 
                                                           value="{{ $detalle->haber }}" step="0.01" min="0">
                                                    </td>
                                                    <td>
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" class="text-end"><strong>Totales:</strong></td>
                                                <td class="text-end"><span id="totalDebe">{{ number_format($asiento->total_debe, 2) }}</span></td>
                                                <td class="text-end"><span id="totalHaber">{{ number_format($asiento->total_haber, 2) }}</span></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-success" onclick="agregarFila()">
                                    <i class="fas fa-plus"></i> Agregar Línea
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                                <a href="{{ route('contabilidad.asientos.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Select2 en los selectores de cuenta y centro de costo
    $('.cuenta-select').select2({
        theme: 'bootstrap-5',
        placeholder: 'Seleccione una cuenta...'
    });
    $('#centro_costo_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Seleccione un centro de costo...',
        allowClear: true
    });

    let contadorFilas = {{ count($asiento->detalles) }};

    function agregarFila() {
        const tbody = document.querySelector('#detallesTable tbody');
        const nuevaFila = document.createElement('tr');
        nuevaFila.innerHTML = `
                <td>
                <select class="form-select cuenta-select" name="detalles[${contadorFilas}][cuenta_id]" required>
                        <option value="">Seleccione...</option>
                        @foreach($cuentas as $cuenta)
                        <option value="{{ $cuenta->id }}">{{ $cuenta->codigo }} - {{ $cuenta->nombre }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                <input type="text" class="form-control" name="detalles[${contadorFilas}][descripcion]" required>
                </td>
                <td>
                <input type="number" class="form-control debe-input" name="detalles[${contadorFilas}][debe]" step="0.01" min="0">
                </td>
                <td>
                <input type="number" class="form-control haber-input" name="detalles[${contadorFilas}][haber]" step="0.01" min="0">
                </td>
                <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
        `;
        tbody.appendChild(nuevaFila);
        contadorFilas++;
    }

    window.eliminarFila = function(button) {
        const tbody = document.querySelector('#detallesTable tbody');
        if (tbody.children.length > 1) {
            button.closest('tr').remove();
            actualizarTotales();
        } else {
            alert('Debe haber al menos una línea en el asiento');
        }
    }

    function actualizarTotales() {
        let totalDebe = 0;
        let totalHaber = 0;

        document.querySelectorAll('.debe-input').forEach(input => {
            totalDebe += parseFloat(input.value || 0);
        });

        document.querySelectorAll('.haber-input').forEach(input => {
            totalHaber += parseFloat(input.value || 0);
        });

        document.getElementById('totalDebe').textContent = totalDebe.toFixed(2);
        document.getElementById('totalHaber').textContent = totalHaber.toFixed(2);
    }

    // Actualizar totales cuando cambian los valores
    document.querySelector('#detallesTable').addEventListener('input', function(e) {
        if (e.target.classList.contains('debe-input') || e.target.classList.contains('haber-input')) {
            actualizarTotales();
        }
    });

    // Validar que los totales sean iguales antes de enviar el formulario
    document.getElementById('asientoForm').addEventListener('submit', function(e) {
        const totalDebe = parseFloat(document.getElementById('totalDebe').textContent);
        const totalHaber = parseFloat(document.getElementById('totalHaber').textContent);

        if (totalDebe !== totalHaber) {
            e.preventDefault();
            alert('Los totales de Debe y Haber deben ser iguales');
        }
    });

    // Cargar documentos relacionados según el tipo de operación
    document.getElementById('tipo_operacion').addEventListener('change', function() {
        const tipo = this.value;
        const documentoSelect = document.getElementById('documento_id');
        documentoSelect.innerHTML = '<option value="">Seleccione...</option>';

        if (tipo) {
            fetch(`/api/documentos/${tipo}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(doc => {
                        const option = document.createElement('option');
                        option.value = doc.id;
                        option.textContent = `${doc.numero} - ${doc.concepto}`;
                        documentoSelect.appendChild(option);
                    });
            });
        }
    });
});
</script>
@endpush 
@endsection 