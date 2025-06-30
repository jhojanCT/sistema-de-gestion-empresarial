@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0"><i class="fas fa-edit"></i> Editar Compra #{{ $compra->id }}</h1>
            <a href="{{ route('compras.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        
        <div class="card-body">
            <form id="compraForm" action="{{ route('compras.update', $compra->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Información General -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información General</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="proveedor_id" class="form-label">Proveedor:</label>
                                    <select class="form-select" id="proveedor_id" name="proveedor_id" required>
                                        <option value="">Seleccione un proveedor</option>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id }}" {{ $compra->proveedor_id == $proveedor->id ? 'selected' : '' }}>
                                                {{ $proveedor->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="tipo" class="form-label">Tipo de Compra:</label>
                                    <select class="form-select" id="tipo" name="tipo" required>
                                        <option value="contado" {{ $compra->tipo == 'contado' ? 'selected' : '' }}>Contado</option>
                                        <option value="credito" {{ $compra->tipo == 'credito' ? 'selected' : '' }}>Crédito</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="fecha" class="form-label">Fecha:</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" value="{{ $compra->fecha->format('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Campos de Pago -->
                <div id="pagoContadoFields" class="card mb-3" style="{{ $compra->tipo == 'credito' ? 'display: none;' : '' }}">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-money-bill"></i> Información de Pago</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="tipo_pago" class="form-label">Tipo de Pago:</label>
                                    <select class="form-select" id="tipo_pago" name="tipo_pago" {{ $compra->tipo == 'contado' ? 'required' : '' }}>
                                        <option value="">Seleccione tipo de pago</option>
                                        <option value="transferencia" {{ $compra->pagos->first()?->metodo_pago == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                        <option value="efectivo" {{ $compra->pagos->first()?->metodo_pago == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ítems de Compra -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Ítems de Compra</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Ítem</th>
                                        <th style="width: 200px;">Cantidad</th>
                                        <th style="width: 150px;">Precio Unitario</th>
                                        <th style="width: 150px;">Subtotal</th>
                                        <th style="width: 100px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($compra->items as $item)
                                    <tr>
                                        <td>
                                            <select class="form-select tipo-item" name="items[{{ $loop->index }}][tipo]" required>
                                                <option value="materia_prima" {{ $item->item_type == 'App\Models\MateriaPrima' ? 'selected' : '' }}>Materia Prima</option>
                                                <option value="producto" {{ $item->item_type == 'App\Models\Producto' ? 'selected' : '' }}>Producto</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select item-select" name="items[{{ $loop->index }}][item_id]" required>
                                                <option value="">Seleccione un ítem</option>
                                                @foreach($materiasPrimas as $mp)
                                                    <option value="materia_prima_{{ $mp->id }}" 
                                                            data-tipo="materia_prima"
                                                            data-unidad="{{ $mp->unidad_medida }}"
                                                            {{ $item->item_type == 'App\Models\MateriaPrima' && $item->item_id == $mp->id ? 'selected' : '' }}>
                                                        {{ $mp->nombre }}
                                                    </option>
                                                @endforeach
                                                @foreach($productos as $prod)
                                                    <option value="producto_{{ $prod->id }}"
                                                            data-tipo="producto"
                                                            data-unidad="{{ $prod->unidad_medida }}"
                                                            {{ $item->item_type == 'App\Models\Producto' && $item->item_id == $prod->id ? 'selected' : '' }}>
                                                        {{ $prod->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" class="form-control cantidad" 
                                                       name="items[{{ $loop->index }}][cantidad]" 
                                                       value="{{ $item->cantidad }}"
                                                       min="0.01" step="0.01" required
                                                       style="min-width: 100px;">
                                                <span class="input-group-text unidad-medida" style="min-width: 80px; text-align: center;"></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">Bs</span>
                                                <input type="number" class="form-control precio" 
                                                       name="items[{{ $loop->index }}][precio_unitario]" 
                                                       value="{{ $item->precio_unitario }}"
                                                       min="0.01" step="0.01" required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">Bs</span>
                                                <input type="text" class="form-control subtotal" readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm eliminar-item">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">Bs</span>
                                                <input type="text" class="form-control" id="total" readonly>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <button type="button" class="btn btn-success" id="agregarItem">
                            <i class="fas fa-plus"></i> Agregar Ítem
                        </button>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <a href="{{ route('compras.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsTable = document.getElementById('itemsTable');
    const agregarItemBtn = document.getElementById('agregarItem');
    const tipoSelect = document.getElementById('tipo');
    const pagoContadoFields = document.getElementById('pagoContadoFields');
    const tipoPagoSelect = document.getElementById('tipo_pago');
    let itemCount = {{ count($compra->items) }};

    const materiasPrimas = @json($materiasPrimas);
    const productos = @json($productos);

    // Mostrar/ocultar campos de pago según el tipo de compra
    tipoSelect.addEventListener('change', function() {
        const esContado = this.value === 'contado';
        pagoContadoFields.style.display = esContado ? 'block' : 'none';
        tipoPagoSelect.required = esContado;
    });

    // Función para calcular subtotal de una fila
    function calcularSubtotal(row) {
        const cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
        const precio = parseFloat(row.querySelector('.precio').value) || 0;
        const subtotal = cantidad * precio;
        row.querySelector('.subtotal').value = subtotal.toFixed(2);
        calcularTotal();
    }

    // Función para calcular el total general
    function calcularTotal() {
        const subtotales = document.querySelectorAll('.subtotal');
        let total = 0;
        subtotales.forEach(subtotal => {
            total += parseFloat(subtotal.value) || 0;
        });
        document.getElementById('total').value = total.toFixed(2);
    }

    // Función para actualizar la unidad de medida
    function actualizarUnidadMedida(row) {
        const itemSelect = row.querySelector('.item-select');
        const unidadSpan = row.querySelector('.unidad-medida');
        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
        
        if (selectedOption && selectedOption.dataset.unidad) {
            unidadSpan.textContent = selectedOption.dataset.unidad;
        } else {
            unidadSpan.textContent = 'unidades';
        }
    }

    // Función para agregar un nuevo ítem
    agregarItemBtn.addEventListener('click', function() {
        const tbody = itemsTable.querySelector('tbody');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select class="form-select tipo-item" name="items[${itemCount}][tipo_item]" required>
                    <option value="materia_prima">Materia Prima</option>
                    <option value="producto">Producto</option>
                </select>
            </td>
            <td>
                <select class="form-select item-select" name="items[${itemCount}][item_id]" required>
                    <option value="">Seleccione un ítem</option>
                    @foreach($materiasPrimas as $mp)
                        <option value="materia_prima_{{ $mp->id }}" 
                                data-tipo="materia_prima"
                                data-unidad="{{ $mp->unidad_medida }}">
                            {{ $mp->nombre }}
                        </option>
                    @endforeach
                    @foreach($productos as $prod)
                        <option value="producto_{{ $prod->id }}"
                                data-tipo="producto"
                                data-unidad="{{ $prod->unidad_medida }}">
                            {{ $prod->nombre }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" class="form-control cantidad" 
                           name="items[${itemCount}][cantidad]" 
                           min="0.01" step="0.01" required
                           style="min-width: 100px;">
                    <span class="input-group-text unidad-medida" style="min-width: 80px; text-align: center;">unidades</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-text">Bs</span>
                    <input type="number" class="form-control precio" 
                           name="items[${itemCount}][precio_unitario]" 
                           min="0.01" step="0.01" required>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-text">Bs</span>
                    <input type="text" class="form-control subtotal" readonly>
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm eliminar-item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(newRow);
        itemCount++;
        initItemEvents(newRow);
        calcularTotal();
    });

    // Función para inicializar eventos en una fila
    function initItemEvents(row) {
        const tipoSelect = row.querySelector('.tipo-item');
        const itemSelect = row.querySelector('.item-select');
        const cantidadInput = row.querySelector('.cantidad');
        const precioInput = row.querySelector('.precio');
        const eliminarBtn = row.querySelector('.eliminar-item');

        // Evento cambio de tipo (materia prima/producto)
        tipoSelect.addEventListener('change', function() {
            const tipo = this.value;
            itemSelect.querySelectorAll('option').forEach(option => {
                if (option.value === '') return; // Mantener la opción "Seleccione un ítem"
                const optionTipo = option.dataset.tipo;
                option.style.display = optionTipo === tipo ? '' : 'none';
            });
            itemSelect.value = ''; // Resetear selección
            actualizarUnidadMedida(row);
            calcularSubtotal(row);
        });

        // Evento cambio de ítem
        itemSelect.addEventListener('change', function() {
            actualizarUnidadMedida(row);
            calcularSubtotal(row);
        });

        // Eventos de cálculo
        cantidadInput.addEventListener('input', () => calcularSubtotal(row));
        precioInput.addEventListener('input', () => calcularSubtotal(row));

        // Evento eliminar fila
        eliminarBtn.addEventListener('click', function() {
            row.remove();
            calcularTotal();
        });

        // Actualizar unidad de medida al inicializar
        actualizarUnidadMedida(row);
    }

    // Inicializar eventos para las filas existentes y calcular totales iniciales
    document.querySelectorAll('tbody tr').forEach(row => {
        initItemEvents(row);
        calcularSubtotal(row);
    });
});
</script>
@endsection
