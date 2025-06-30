@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0"><i class="fas fa-cash-register"></i> Nueva Venta</h1>
            <a href="{{ route('ventas.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="card-body">
            <form id="ventaForm" action="{{ route('ventas.store') }}" method="POST">
                @csrf

                <!-- Información General -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información General</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="cliente_id">Cliente:</label>
                                    <select class="form-select" id="cliente_id" name="cliente_id">
                                        <option value="">Consumidor Final</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="tipo">Tipo de Venta:</label>
                                    <select class="form-select" id="tipo" name="tipo" required>
                                        <option value="contado">Contado</option>
                                        <option value="credito">Crédito</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="fecha">Fecha:</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Campos de pago al contado -->
                        <div class="row" id="pagoContadoFields" style="display: none;">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="tipo_pago">Método de Pago:</label>
                                    <select class="form-select" id="tipo_pago" name="tipo_pago">
                                        <option value="">Seleccione método de pago</option>
                                        <option value="efectivo">Efectivo</option>
                                        <option value="transferencia">Transferencia</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Campos de Facturación -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input type="hidden" name="has_invoice" value="0">
                                    <input class="form-check-input" type="checkbox" id="has_invoice" name="has_invoice" value="1" onchange="toggleInvoiceNumber()">
                                    <label class="form-check-label" for="has_invoice">
                                        Esta venta tiene factura
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3" id="invoice_number_group" style="display: none;">
                                    <label for="invoice_number">Número de Factura:</label>
                                    <input type="text" class="form-control" id="invoice_number" name="invoice_number">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="auto_asiento">¿Generar Asiento Contable Automáticamente?</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="auto_asiento" name="auto_asiento" value="1" checked>
                                <label class="custom-control-label" for="auto_asiento">Sí</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ítems de Venta -->
                <div class="card mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Ítems de Venta</h5>
                        <button type="button" id="addItemBtn" class="btn btn-sm btn-success">
                            <i class="fas fa-plus"></i> Agregar Ítem
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 20%">Tipo</th>
                                        <th style="width: 25%">Item</th>
                                        <th style="width: 20%">Cantidad</th>
                                        <th style="width: 15%">Precio Unitario</th>
                                        <th style="width: 15%">Subtotal</th>
                                        <th style="width: 5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsContainer">
                                    <tr class="item-row">
                                        <td>
                                            <select class="form-select tipo-item" name="items[0][tipo_item]" required>
                                                <option value="">Seleccione tipo</option>
                                                <option value="materia_prima_filtrada">Materia Prima Filtrada</option>
                                                <option value="producto">Producto</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select item-id" name="items[0][item_id]" required>
                                                <option value="">Seleccione item</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group" style="min-width: 300px;">
                                                <input type="number" 
                                                    step="0.01" 
                                                    min="0.01" 
                                                    class="form-control cantidad" 
                                                    name="items[0][cantidad]" 
                                                    placeholder="Cantidad" 
                                                    required
                                                    style="min-width: 200px; font-size: 1.1em;">
                                                <span class="input-group-text unidad-medida" style="min-width: 100px;">unidades</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">Bs</span>
                                                <input type="number" step="0.01" min="0" class="form-control precio" name="items[0][precio_unitario]" placeholder="Precio" required>
                                            </div>
                                        </td>
                                        <td class="text-end subtotal">Bs 0.00</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm remove-item">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                                        <td class="text-end fw-bold" id="subtotalVenta">Bs 0.00</td>
                                        <td></td>
                                    </tr>
                                    <tr id="ivaRow" style="display: none;">
                                        <td colspan="4" class="text-end fw-bold">IVA (13%):</td>
                                        <td class="text-end fw-bold" id="ivaVenta">Bs 0.00</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total:</td>
                                        <td class="text-end fw-bold" id="totalVenta">Bs 0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Venta
                    </button>
                    <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemBtn = document.getElementById('addItemBtn');
    let itemCount = 1;

    const materiasPrimas = @json($materiasPrimas);
    const productos = @json($productos);

    function calcularSubtotal(row) {
        const cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
        const precio = parseFloat(row.querySelector('.precio').value) || 0;
        const subtotal = cantidad * precio;
        row.querySelector('.subtotal').textContent = `Bs ${subtotal.toFixed(2)}`;
        calcularTotal();
    }

    function calcularTotal() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const subtotalText = row.querySelector('.subtotal').textContent;
            subtotal += parseFloat(subtotalText.replace('Bs ', '')) || 0;
        });

        const hasInvoice = document.getElementById('has_invoice').checked;
        const ivaRow = document.getElementById('ivaRow');
        const ivaVenta = document.getElementById('ivaVenta');
        const subtotalVenta = document.getElementById('subtotalVenta');
        const totalVenta = document.getElementById('totalVenta');

        subtotalVenta.textContent = `Bs ${subtotal.toFixed(2)}`;

        if (hasInvoice) {
            const iva = subtotal * 0.13;
            ivaRow.style.display = '';
            ivaVenta.textContent = `Bs ${iva.toFixed(2)}`;
            totalVenta.textContent = `Bs ${(subtotal + iva).toFixed(2)}`;
        } else {
            ivaRow.style.display = 'none';
            totalVenta.textContent = `Bs ${subtotal.toFixed(2)}`;
        }
    }

    function actualizarUnidadMedida(row) {
        const tipoSelect = row.querySelector('.tipo-item');
        const itemSelect = row.querySelector('.item-id');
        const unidadMedidaSpan = row.querySelector('.unidad-medida');
        
        if (tipoSelect.value && itemSelect.value) {
            const selectedOption = itemSelect.options[itemSelect.selectedIndex];
            unidadMedidaSpan.textContent = selectedOption.getAttribute('data-unidad') || 'unidades';
        }
    }

    function actualizarItemsDisponibles(row) {
        const tipoSelect = row.querySelector('.tipo-item');
        const itemSelect = row.querySelector('.item-id');
        const precioInput = row.querySelector('.precio');
        const cantidadInput = row.querySelector('.cantidad');
        const items = tipoSelect.value === 'materia_prima_filtrada' ? materiasPrimas : productos;
        
        itemSelect.innerHTML = '<option value="">Seleccione item</option>';
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = `${item.nombre} (Stock: ${item.stock} ${item.unidad_medida || 'unidades'})`;
            option.setAttribute('data-stock', item.stock);
            option.setAttribute('data-unidad', item.unidad_medida || 'unidades');
            option.setAttribute('data-precio', item.precio_venta || 0);
            itemSelect.appendChild(option);
        });

        // Limpiar campos al cambiar el tipo
        precioInput.value = '';
        cantidadInput.value = '';
        cantidadInput.max = '';
        actualizarUnidadMedida(row);
        calcularSubtotal(row);
    }

    function agregarNuevoItem() {
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td>
                <select class="form-select tipo-item" name="items[${itemCount}][tipo_item]" required>
                    <option value="">Seleccione tipo</option>
                    <option value="materia_prima_filtrada">Materia Prima Filtrada</option>
                    <option value="producto">Producto</option>
                </select>
            </td>
            <td>
                <select class="form-select item-id" name="items[${itemCount}][item_id]" required>
                    <option value="">Seleccione item</option>
                </select>
            </td>
            <td>
                <div class="input-group" style="min-width: 300px;">
                    <input type="number" 
                        step="0.01" 
                        min="0.01" 
                        class="form-control cantidad" 
                        name="items[${itemCount}][cantidad]" 
                        placeholder="Cantidad" 
                        required
                        style="min-width: 200px; font-size: 1.1em;">
                    <span class="input-group-text unidad-medida" style="min-width: 100px;">unidades</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-text">Bs</span>
                    <input type="number" step="0.01" min="0" class="form-control precio" name="items[${itemCount}][precio_unitario]" placeholder="Precio" required>
                </div>
            </td>
            <td class="text-end subtotal">Bs 0.00</td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-item">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        
        itemsContainer.appendChild(newRow);
        initItemEvents(newRow);
        itemCount++;
    }

    function initItemEvents(row) {
        const tipoSelect = row.querySelector('.tipo-item');
        const itemSelect = row.querySelector('.item-id');
        const cantidadInput = row.querySelector('.cantidad');
        const precioInput = row.querySelector('.precio');

        tipoSelect.addEventListener('change', () => {
            actualizarItemsDisponibles(row);
        });

        itemSelect.addEventListener('change', () => {
            const selectedOption = itemSelect.options[itemSelect.selectedIndex];
            if (selectedOption.value) {
                const stock = parseFloat(selectedOption.getAttribute('data-stock')) || 0;
                const precio = parseFloat(selectedOption.getAttribute('data-precio')) || 0;
                
                cantidadInput.max = stock;
                cantidadInput.placeholder = `Máximo: ${stock}`;
                precioInput.value = precio;
                
                actualizarUnidadMedida(row);
                calcularSubtotal(row);
            }
        });

        cantidadInput.addEventListener('input', () => {
            const max = parseFloat(cantidadInput.max);
            const valor = parseFloat(cantidadInput.value);
            if (max && valor > max) {
                cantidadInput.value = max;
            }
            calcularSubtotal(row);
        });

        precioInput.addEventListener('input', () => calcularSubtotal(row));

        const removeBtn = row.querySelector('.remove-item');
        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                row.remove();
                calcularTotal();
            });
        }
    }

    // Inicializar eventos para el primer item
    document.querySelectorAll('.item-row').forEach(row => {
        initItemEvents(row);
    });

    // Event listener para agregar nuevos items
    addItemBtn.addEventListener('click', agregarNuevoItem);

    // Manejar visibilidad del campo de método de pago
    const tipoVenta = document.getElementById('tipo');
    const pagoContadoFields = document.getElementById('pagoContadoFields');
    const tipoPago = document.getElementById('tipo_pago');

    function togglePagoFields() {
        if (tipoVenta.value === 'contado') {
            pagoContadoFields.style.display = 'block';
            tipoPago.required = true;
        } else {
            pagoContadoFields.style.display = 'none';
            tipoPago.required = false;
            tipoPago.value = '';
        }
    }

    tipoVenta.addEventListener('change', togglePagoFields);
    togglePagoFields(); // Ejecutar al cargar la página

    // Función para manejar el campo has_invoice
    window.toggleInvoiceNumber = function() {
        const hasInvoice = document.getElementById('has_invoice');
        const invoiceNumberGroup = document.getElementById('invoice_number_group');
        const invoiceNumber = document.getElementById('invoice_number');
        
        if (hasInvoice.checked) {
            invoiceNumberGroup.style.display = 'block';
            invoiceNumber.required = true;
        } else {
            invoiceNumberGroup.style.display = 'none';
            invoiceNumber.required = false;
            invoiceNumber.value = '';
        }
        calcularTotal();
    };

    // Inicializar el estado del campo de factura
    toggleInvoiceNumber();

    // Validación del formulario
    document.getElementById('ventaForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Verificar que haya al menos un item
        if (document.querySelectorAll('.item-row').length === 0) {
            alert('Debe agregar al menos un item a la venta');
            return;
        }

        // Verificar que se haya seleccionado un tipo de pago si es venta al contado
        if (tipoVenta.value === 'contado' && !tipoPago.value) {
            alert('Debe seleccionar un tipo de pago para ventas al contado');
            tipoPago.focus();
            return;
        }

        // Si es venta a crédito, asegurarse de que no se envíe el tipo de pago
        if (tipoVenta.value === 'credito') {
            tipoPago.value = '';
            tipoPago.required = false;
        }

        // Verificar stock disponible
        let stockValido = true;
        document.querySelectorAll('.item-row').forEach(row => {
            const cantidad = parseFloat(row.querySelector('.cantidad').value);
            const max = parseFloat(row.querySelector('.cantidad').max);
            if (cantidad > max) {
                stockValido = false;
                alert('La cantidad excede el stock disponible en uno o más items');
            }
        });

        if (stockValido) {
            // Mostrar indicador de carga
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

            // Enviar el formulario
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Error al procesar la venta');
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la venta');
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            });
        }
    });
});
</script>
@endsection
