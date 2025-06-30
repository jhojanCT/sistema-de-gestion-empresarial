@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Venta</h1>
    
    <form id="ventaForm" action="{{ route('ventas.update', $venta) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="cliente_id">Cliente:</label>
                    <select class="form-control" id="cliente_id" name="cliente_id">
                        <option value="">Consumidor Final</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ $venta->cliente_id == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    <label for="tipo">Tipo:</label>
                    <select class="form-control" id="tipo" name="tipo" required>
                        <option value="contado" {{ $venta->tipo == 'contado' ? 'selected' : '' }}>Contado</option>
                        <option value="credito" {{ $venta->tipo == 'credito' ? 'selected' : '' }}>Crédito</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    <label for="fecha">Fecha:</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" value="{{ $venta->fecha->format('Y-m-d') }}" required>
                </div>
            </div>
        </div>
        
        <!-- Campos de pago al contado -->
        <div id="pagoContadoFields" class="row mb-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="tipo_pago">Tipo de Pago:</label>
                    <select class="form-control" id="tipo_pago" name="tipo_pago">
                        <option value="">Seleccione tipo de pago</option>
                        <option value="transferencia" {{ $venta->tipo_pago == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                        <option value="efectivo" {{ $venta->tipo_pago == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">Ítems de Venta</div>
            <div class="card-body">
                <div id="itemsContainer">
                    @foreach($venta->items as $index => $item)
                    <div class="item-row row mb-2">
                        <div class="col-md-3">
                            <select class="form-control tipo-item" name="items[{{ $index }}][tipo_item]" required>
                                <option value="">Seleccione tipo</option>
                                <option value="materia_prima_filtrada" {{ $item->tipo_item == 'materia_prima_filtrada' ? 'selected' : '' }}>Materia Prima Filtrada</option>
                                <option value="producto" {{ $item->tipo_item == 'producto' ? 'selected' : '' }}>Producto</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control item-id" name="items[{{ $index }}][item_id]" required>
                                <option value="">Seleccione item</option>
                                @if($item->tipo_item == 'materia_prima_filtrada')
                                    @foreach($materiasPrimas as $materia)
                                        <option value="{{ $materia->id }}" 
                                                {{ $item->materia_prima_filtrada_id == $materia->id ? 'selected' : '' }}
                                                data-stock="{{ $materia->stock }}"
                                                data-unidad="{{ $materia->unidad_medida }}">
                                            {{ $materia->nombre }} (Stock: {{ $materia->stock }} {{ $materia->unidad_medida }})
                                        </option>
                                    @endforeach
                                @else
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id }}"
                                                {{ $item->producto_id == $producto->id ? 'selected' : '' }}
                                                data-stock="{{ $producto->stock }}"
                                                data-unidad="unidades">
                                            {{ $producto->nombre }} (Stock: {{ $producto->stock }} unidades)
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" min="0.01" class="form-control cantidad" 
                                   name="items[{{ $index }}][cantidad]" 
                                   value="{{ $item->cantidad }}" 
                                   placeholder="Cantidad" required>
                            <small class="text-muted unidad-medida">
                                {{ $item->tipo_item == 'materia_prima_filtrada' ? $item->materiaPrimaFiltrada->unidad_medida : 'unidades' }}
                            </small>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" class="form-control precio" 
                                       name="items[{{ $index }}][precio_unitario]" 
                                       value="{{ $item->precio_unitario }}" 
                                       placeholder="Precio" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">Bs.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <input type="text" class="form-control subtotal" 
                                       value="{{ number_format($item->subtotal, 2) }}" 
                                       readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text">Bs.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-center">
                            <button type="button" class="btn btn-danger btn-sm remove-item">✕</button>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <button type="button" id="addItemBtn" class="btn btn-sm btn-success mt-2">Agregar Ítem</button>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Total de la Venta:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="totalVenta" value="{{ number_format($venta->total, 2) }}" readonly>
                        <div class="input-group-append">
                            <span class="input-group-text">Bs.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Actualizar Venta</button>
        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemBtn = document.getElementById('addItemBtn');
    let itemCount = {{ count($venta->items) }};
    
    const materiasPrimas = @json($materiasPrimas);
    const productos = @json($productos);
    
    function calcularSubtotal(row) {
        const cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
        const precio = parseFloat(row.querySelector('.precio').value) || 0;
        const subtotal = cantidad * precio;
        row.querySelector('.subtotal').value = subtotal.toFixed(2);
        calcularTotal();
    }

    function calcularTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const subtotal = parseFloat(row.querySelector('.subtotal').value) || 0;
            total += subtotal;
        });
        document.getElementById('totalVenta').value = total.toFixed(2);
    }

    addItemBtn.addEventListener('click', function() {
        const newItem = document.createElement('div');
        newItem.className = 'item-row row mb-2';
        newItem.innerHTML = `
            <div class="col-md-3">
                <select class="form-control tipo-item" name="items[${itemCount}][tipo_item]" required>
                    <option value="">Seleccione tipo</option>
                    <option value="materia_prima_filtrada">Materia Prima Filtrada</option>
                    <option value="producto">Producto</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control item-id" name="items[${itemCount}][item_id]" required>
                    <option value="">Seleccione item</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" min="0.01" class="form-control cantidad" name="items[${itemCount}][cantidad]" placeholder="Cantidad" required>
                <small class="text-muted unidad-medida">unidades</small>
            </div>
            <div class="col-md-2">
                <div class="input-group">
                    <input type="number" step="0.01" min="0" class="form-control precio" name="items[${itemCount}][precio_unitario]" placeholder="Precio" required>
                    <div class="input-group-append">
                        <span class="input-group-text">Bs.</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="input-group">
                    <input type="text" class="form-control subtotal" readonly>
                    <div class="input-group-append">
                        <span class="input-group-text">Bs.</span>
                    </div>
                </div>
            </div>
            <div class="col-md-1 d-flex align-items-center">
                <button type="button" class="btn btn-danger btn-sm remove-item">✕</button>
            </div>
        `;
        itemsContainer.appendChild(newItem);
        initItemEvents(newItem);
        itemCount++;
    });

    document.querySelectorAll('.item-row').forEach(row => {
        initItemEvents(row);
    });

    function initItemEvents(row) {
        const tipoSelect = row.querySelector('.tipo-item');
        const itemSelect = row.querySelector('.item-id');
        const cantidadInput = row.querySelector('.cantidad');
        const precioInput = row.querySelector('.precio');
        const unidadMedida = row.querySelector('.unidad-medida');

        tipoSelect.addEventListener('change', function() {
            itemSelect.innerHTML = '<option value="">Seleccione item</option>';
            itemSelect.disabled = !this.value;

            if (this.value === 'materia_prima_filtrada') {
                materiasPrimas.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nombre + ' (Stock: ' + item.stock + ' ' + item.unidad_medida + ')';
                    option.setAttribute('data-stock', item.stock);
                    option.setAttribute('data-unidad', item.unidad_medida);
                    itemSelect.appendChild(option);
                });
            } else if (this.value === 'producto') {
                productos.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nombre + ' (Stock: ' + item.stock + ' unidades)';
                    option.setAttribute('data-stock', item.stock);
                    option.setAttribute('data-unidad', 'unidades');
                    itemSelect.appendChild(option);
                });
            }
        });

        itemSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const stock = parseFloat(selectedOption.getAttribute('data-stock')) || 0;
            const unidad = selectedOption.getAttribute('data-unidad') || 'unidades';
            cantidadInput.max = stock;
            cantidadInput.setAttribute('placeholder', `Máximo: ${stock}`);
            unidadMedida.textContent = unidad;
        });

        cantidadInput.addEventListener('input', () => calcularSubtotal(row));
        precioInput.addEventListener('input', () => calcularSubtotal(row));

        const removeBtn = row.querySelector('.remove-item');
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                row.remove();
                calcularTotal();
            });
        }
    }

    // Mostrar/ocultar campos de pago al contado
    const tipoSelect = document.getElementById('tipo');
    const pagoContadoFields = document.getElementById('pagoContadoFields');
    const tipoPagoSelect = document.getElementById('tipo_pago');
    const ventaForm = document.getElementById('ventaForm');

    function togglePagoContadoFields() {
        if (tipoSelect.value === 'contado') {
            pagoContadoFields.style.display = 'flex';
            tipoPagoSelect.required = true;
        } else {
            pagoContadoFields.style.display = 'none';
            tipoPagoSelect.required = false;
            tipoPagoSelect.value = '';
        }
    }

    ventaForm.addEventListener('submit', function(e) {
        if (tipoSelect.value === 'credito') {
            tipoPagoSelect.value = '';
            tipoPagoSelect.required = false;
        }
    });

    tipoSelect.addEventListener('change', togglePagoContadoFields);
    togglePagoContadoFields(); // Ejecutar al cargar la página
});
</script>
@endsection
