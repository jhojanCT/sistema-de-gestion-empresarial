@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-file-invoice"></i> Crear Asiento Contable
                    </h3>
                </div>

                <div class="card-body">
                    <form action="{{ route('contabilidad.asientos.store') }}" method="POST" id="asientoForm">
                        @csrf
                        @if(isset($tipoDocumento))
                            <input type="hidden" name="tipo_documento" value="{{ $tipoDocumento }}">
                            <input type="hidden" name="numero_documento" value="{{ $numeroDocumento }}">
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecha" class="form-label">Fecha</label>
                                    <input type="date" class="form-control @error('fecha') is-invalid @enderror" 
                                           id="fecha" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
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
                                        <option value="diario" {{ (old('tipo_operacion', $tipoOperacion) == 'diario') ? 'selected' : '' }}>Asiento Diario</option>
                                        <option value="ajuste" {{ (old('tipo_operacion', $tipoOperacion) == 'ajuste') ? 'selected' : '' }}>Ajuste</option>
                                        <option value="cierre" {{ (old('tipo_operacion', $tipoOperacion) == 'cierre') ? 'selected' : '' }}>Cierre</option>
                                        <option value="venta" {{ (old('tipo_operacion', $tipoOperacion) == 'venta') ? 'selected' : '' }}>Venta</option>
                                        <option value="compra" {{ (old('tipo_operacion', $tipoOperacion) == 'compra') ? 'selected' : '' }}>Compra</option>
                                    </select>
                                    @error('tipo_operacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select @error('estado') is-invalid @enderror" 
                                            id="estado" name="estado" required>
                                        <option value="borrador" {{ old('estado') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                                        <option value="aprobado" {{ old('estado') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="centro_costo_id" class="form-label">Centro de Costo</label>
                                    <select class="form-select @error('centro_costo_id') is-invalid @enderror" 
                                            id="centro_costo_id" name="centro_costo_id">
                                        <option value="">Seleccione...</option>
                                        @foreach($centrosCosto as $centro)
                                            <option value="{{ $centro->id }}" {{ old('centro_costo_id') == $centro->id ? 'selected' : '' }}>
                                                {{ $centro->codigo }} - {{ $centro->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('centro_costo_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="concepto" class="form-label">Concepto</label>
                                    <input type="text" class="form-control @error('concepto') is-invalid @enderror" 
                                           id="concepto" name="concepto" value="{{ old('concepto') }}" required>
                                    @error('concepto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if(isset($documento))
                        <!-- Información del Documento -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Información del Documento</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="card bg-light mb-3">
                                                    <div class="card-header bg-primary text-white">
                                                        <h6 class="mb-0">Datos Generales</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @php
                                                            $fechaDocumento = null;
                                                            if (isset($documento)) {
                                                                if ($tipo == 'venta' || $tipo == 'compra') {
                                                                    $fechaDocumento = $documento->fecha;
                                                                } elseif ($tipo == 'pago_cliente' || $tipo == 'pago_proveedor' || $tipo == 'pago_salario') {
                                                                    $fechaDocumento = $documento->fecha_pago;
                                                                }
                                                            }
                                                        @endphp
                                                        @if($fechaDocumento)
                                                            <p><strong>Fecha:</strong> {{ $fechaDocumento->format('d/m/Y') }}</p>
                                                        @endif

                                                        @if($tipo == 'venta')
                                                            <p><strong>Cliente:</strong> {{ $documento->cliente->nombre ?? 'Consumidor Final' }}</p>
                                                            <p><strong>NIT/DUI:</strong> {{ $documento->cliente->nit_dui ?? 'N/A' }}</p>
                                                            <p><strong>Tipo de Venta:</strong> {{ ucfirst($documento->tipo) }}</p>
                                                            <p><strong>Método de Pago:</strong> {{ ucfirst($documento->metodo_pago) ?? 'N/A' }}</p>
                                                            <p><strong>Factura:</strong> {{ $documento->has_invoice ? 'Sí' : 'No' }}</p>
                                                            @if($documento->has_invoice)
                                                                <p><strong>N° Factura:</strong> {{ $documento->invoice_number }}</p>
                                                            @endif
                                                            <p><strong>Estado:</strong> {{ ucfirst($documento->estado) ?? 'N/A' }}</p>
                                                        @elseif($tipo == 'compra')
                                                            <p><strong>Proveedor:</strong> {{ $documento->proveedor->nombre ?? 'N/A' }}</p>
                                                            <p><strong>NIT/DUI:</strong> {{ $documento->proveedor->nit ?? 'N/A' }}</p>
                                                            <p><strong>Tipo de Compra:</strong> {{ ucfirst($documento->tipo) }}</p>
                                                            <p><strong>Método de Pago:</strong> {{ ucfirst($documento->metodo_pago) ?? 'N/A' }}</p>
                                                            <p><strong>Factura:</strong> {{ $documento->has_invoice ? 'Sí' : 'No' }}</p>
                                                            @if($documento->has_invoice)
                                                                <p><strong>N° Factura:</strong> {{ $documento->invoice_number }}</p>
                                                            @endif
                                                            <p><strong>Estado:</strong> {{ ucfirst($documento->estado) ?? 'N/A' }}</p>
                                                        @elseif($tipo == 'pago_cliente')
                                                            <p><strong>Cliente:</strong> {{ $documento->venta->cliente->nombre ?? 'Consumidor Final' }}</p>
                                                            <p><strong>Venta Relacionada ID:</strong> {{ $documento->venta_id ?? 'N/A' }}</p>
                                                            <p><strong>Monto del Pago:</strong> Bs. {{ number_format($documento->monto ?? 0, 2) }}</p>
                                                            <p><strong>Método de Pago:</strong> {{ ucfirst($documento->metodo_pago) ?? 'N/A' }}</p>
                                                            <p><strong>Comprobante:</strong> {{ $documento->comprobante ?? 'N/A' }}</p>
                                                        @elseif($tipo == 'pago_proveedor')
                                                             <p><strong>Proveedor:</strong> {{ $documento->compra->proveedor->nombre ?? 'N/A' }}</p>
                                                            <p><strong>Compra Relacionada ID:</strong> {{ $documento->compra_id ?? 'N/A' }}</p>
                                                            <p><strong>Monto del Pago:</strong> Bs. {{ number_format($documento->monto ?? 0, 2) }}</p>
                                                            <p><strong>Método de Pago:</strong> {{ ucfirst($documento->metodo_pago) ?? 'N/A' }}</p>
                                                            <p><strong>Comprobante:</strong> {{ $documento->comprobante ?? 'N/A' }}</p>
                                                        @elseif($tipo == 'pago_salario')
                                                            <p><strong>Total Pagado:</strong> Bs. {{ number_format($documento->monto_total ?? 0, 2) }}</p>
                                                            <p><strong>Método de Pago:</strong> {{ ucfirst($documento->metodo_pago) ?? 'N/A' }}</p>
                                                            <p><strong>Comprobante:</strong> {{ $documento->comprobante ?? 'N/A' }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card bg-light mb-3">
                                                    <div class="card-header bg-primary text-white">
                                                        <h6 class="mb-0">Montos</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        {{-- Mostrar montos relevantes según el tipo de documento --}}
                                                        @if($tipo == 'venta' || $tipo == 'compra')
                                                            <p><strong>Subtotal:</strong> Bs. {{ number_format($documento->subtotal ?? 0, 2) }}</p>
                                                            @if(($documento->descuento ?? 0) > 0)
                                                                <p><strong>Descuento:</strong> Bs. {{ number_format($documento->descuento, 2) }}</p>
                                                            @endif
                                                            @if(($documento->iva_amount ?? 0) > 0)
                                                                <p><strong>IVA:</strong> Bs. {{ number_format($documento->iva_amount, 2) }}</p>
                                                            @endif
                                                            <p><strong>Total:</strong> Bs. {{ number_format($documento->total ?? 0, 2) }}</p>
                                                            @if($tipo == 'venta' && ($documento->tipo ?? '') == 'credito')
                                                                <p><strong>Saldo Pendiente:</strong> Bs. {{ number_format($documento->saldo_pendiente ?? 0, 2) }}</p>
                                                            @endif
                                                        @elseif($tipo == 'pago_cliente' || $tipo == 'pago_proveedor' || $tipo == 'pago_salario')
                                                            <p><strong>Monto del Pago:</strong> Bs. {{ number_format($documento->monto ?? $documento->monto_total ?? 0, 2) }}</p>
                                                            {{-- Puedes añadir más detalles de montos si son relevantes para pagos --}}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card bg-light mb-3">
                                                    <div class="card-header bg-primary text-white">
                                                        <h6 class="mb-0">Información Adicional</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @if($tipo == 'venta' || $tipo == 'compra')
                                                             <p><strong>Vendedor/Comprador:</strong> {{ $documento->vendedor->nombre ?? $documento->proveedor->nombre ?? 'N/A' }}</p>
                                                        @endif
                                                        <p><strong>Centro de Costo:</strong> {{ $documento->centroCosto->nombre ?? 'N/A' }}</p>
                                                        <p><strong>Observaciones:</strong> {{ $documento->observaciones ?? 'Ninguna' }}</p> {{-- Asumiendo que otros documentos también pueden tener observaciones --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if(isset($documento) && $tipo == 'pago_salario')
                                        <!-- Detalles del Pago de Salario -->
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="card bg-light">
                                                    <div class="card-header bg-primary text-white">
                                                        <h6 class="mb-0">Detalles del Pago de Salario</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        @if(($documento->detalles ?? false) && count($documento->detalles) > 0)
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Empleado</th>
                                                                        <th>Monto</th>
                                                                        <th>Descuentos</th>
                                                                        <th>Bonos</th>
                                                                        <th>Total Neto</th>
                                                                        <th>Concepto</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($documento->detalles as $detalle)
                                                                        <tr>
                                                                            <td>{{ $detalle->empleado->nombre ?? 'N/A' }}</td>
                                                                            <td class="text-end">Bs. {{ number_format($detalle->monto ?? 0, 2) }}</td>
                                                                            <td class="text-end">Bs. {{ number_format($detalle->descuentos ?? 0, 2) }}</td>
                                                                            <td class="text-end">Bs. {{ number_format($detalle->bonos ?? 0, 2) }}</td>
                                                                            <td class="text-end">Bs. {{ number_format($detalle->total_neto ?? 0, 2) }}</td>
                                                                            <td>{{ $detalle->concepto ?? 'N/A' }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        @else
                                                            <p class="text-info">Este pago de salario no tiene detalles registrados.</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Detalles de Items (Ventas/Compras) -->
                                        @if(isset($documento) && ($tipo == 'venta' || $tipo == 'compra') && ($documento->items ?? false) && count($documento->items) > 0)
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="card bg-light">
                                                    <div class="card-header bg-primary text-white">
                                                        <h6 class="mb-0">Detalles de Items (Venta/Compra)</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Item</th>
                                                                        <th>Cantidad</th>
                                                                        <th>Precio Unit.</th>
                                                                        <th>Subtotal</th>
                                                                        {{-- Las columnas IVA y Total se eliminaron en una edición anterior --}}
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($documento->items as $item)
                                                                        <tr>
                                                                            <td>
                                                                                @if($item->tipo_item === 'producto')
                                                                                    {{ $item->producto->nombre ?? 'N/A' }}
                                                                                @else
                                                                                    {{ $item->materiaPrimaFiltrada->nombre ?? 'N/A' }}
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-end">{{ number_format($item->cantidad ?? 0, 2) }}</td>
                                                                            <td class="text-end">Bs. {{ number_format($item->precio_unitario ?? 0, 2) }}</td>
                                                                            <td class="text-end">Bs. {{ number_format($item->subtotal ?? 0, 2) }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                             @if(isset($documento) && ($tipo == 'venta' || $tipo == 'compra'))
                                                <p class="text-info">Este documento ({{ ucfirst($tipo) }}) no tiene items registrados.</p>
                                             @endif
                                        @endif {{-- Cierra el condicional de Detalles de Items --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Fin Información del Documento --}}

                        <!-- Pre-cargar los detalles del asiento para ventas -->
                        @if($tipo == 'venta')
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Obtener las cuentas contables necesarias
                            const cuentas = {
                                caja: '{{ $cuentas->where("codigo", "1.1.1.1")->first()->id }}', // Caja General
                                banco: '{{ $cuentas->where("codigo", "1.1.1.3")->first()->id }}', // Bancos
                                cuentasCobrar: '{{ $cuentas->where("codigo", "1.1.3.1")->first()->id }}', // Cuentas por Cobrar
                                ventas: '{{ $cuentas->where("codigo", "4.1.1.1")->first()->id }}', // Ventas
                                iva: '{{ $cuentas->where("codigo", "2.1.3.1")->first()->id }}', // IVA por Cobrar
                                costoVentas: '{{ $cuentas->where("codigo", "5.1.1.1")->first()->id }}', // Costo de Ventas
                                inventario: '{{ $cuentas->where("codigo", "1.1.4.3")->first()->id }}' // Inventario
                            };

                            // Limpiar la tabla de detalles
                            const tablaDetalles = document.getElementById('tablaDetalles').getElementsByTagName('tbody')[0];
                            tablaDetalles.innerHTML = '';

                            // Agregar los detalles según el tipo de venta
                            if ('{{ $documento->tipo }}' === 'contado') {
                                // Venta al contado
                                const cuentaDebe = '{{ $documento->metodo_pago }}' === 'transferencia' ? cuentas.banco : cuentas.caja;
                                agregarDetalle(cuentaDebe, '{{ $documento->total }}', 0, 'Ingreso por venta al contado');
                                agregarDetalle(cuentas.ventas, 0, '{{ $documento->subtotal }}', 'Venta de productos');
                                if ('{{ $documento->iva_amount }}' > 0) {
                                    agregarDetalle(cuentas.iva, 0, '{{ $documento->iva_amount }}', 'IVA por cobrar');
                                }
                            } else {
                                // Venta a crédito
                                agregarDetalle(cuentas.cuentasCobrar, '{{ $documento->total }}', 0, 'Cuenta por cobrar a {{ $documento->cliente->nombre ?? "Cliente Final" }}');
                                agregarDetalle(cuentas.ventas, 0, '{{ $documento->subtotal }}', 'Venta de productos');
                                if ('{{ $documento->iva_amount }}' > 0) {
                                    agregarDetalle(cuentas.iva, 0, '{{ $documento->iva_amount }}', 'IVA por cobrar');
                                }
                            }

                            // Calcular y agregar el costo de ventas
                            let costoTotal = 0;
                            @foreach($documento->items as $item)
                                @if($item->tipo_item === 'producto')
                                    costoTotal += {{ $item->cantidad }} * {{ $item->producto->costo_promedio }};
                                @else
                                    costoTotal += {{ $item->cantidad }} * {{ $item->materiaPrimaFiltrada->costo_promedio }};
                                @endif
                            @endforeach

                            if (costoTotal > 0) {
                                agregarDetalle(cuentas.costoVentas, costoTotal, 0, 'Costo de productos vendidos');
                                agregarDetalle(cuentas.inventario, 0, costoTotal, 'Salida de inventario por venta');
                            }

                            // Actualizar los totales
                            actualizarTotales();
                        });

                        function agregarDetalle(cuentaId, debe, haber, descripcion) {
                            const tablaDetalles = document.getElementById('tablaDetalles').getElementsByTagName('tbody')[0];
                            const row = tablaDetalles.insertRow();
                            
                            // Cuenta
                            const cellCuenta = row.insertCell();
                            const selectCuenta = document.createElement('select');
                            selectCuenta.className = 'form-select cuenta-select';
                            selectCuenta.name = `detalles[${tablaDetalles.rows.length - 1}][cuenta_id]`;
                            selectCuenta.required = true;
                            
                            // Agregar opciones de cuentas
                            @foreach($cuentas as $cuenta)
                                const option = document.createElement('option');
                                option.value = '{{ $cuenta->id }}';
                                option.text = '{{ $cuenta->codigo }} - {{ $cuenta->nombre }}';
                                if ('{{ $cuenta->id }}' === cuentaId) {
                                    option.selected = true;
                                }
                                selectCuenta.appendChild(option);
                            @endforeach
                            
                            cellCuenta.appendChild(selectCuenta);
                            
                            // Descripción
                            const cellDescripcion = row.insertCell();
                            const inputDescripcion = document.createElement('input');
                            inputDescripcion.type = 'text';
                            inputDescripcion.className = 'form-control';
                            inputDescripcion.name = `detalles[${tablaDetalles.rows.length - 1}][descripcion]`;
                            inputDescripcion.value = descripcion;
                            cellDescripcion.appendChild(inputDescripcion);
                            
                            // Debe
                            const cellDebe = row.insertCell();
                            const inputDebe = document.createElement('input');
                            inputDebe.type = 'number';
                            inputDebe.className = 'form-control debe';
                            inputDebe.name = `detalles[${tablaDetalles.rows.length - 1}][debe]`;
                            inputDebe.value = debe;
                            inputDebe.step = '0.01';
                            inputDebe.min = '0';
                            cellDebe.appendChild(inputDebe);
                            
                            // Haber
                            const cellHaber = row.insertCell();
                            const inputHaber = document.createElement('input');
                            inputHaber.type = 'number';
                            inputHaber.className = 'form-control haber';
                            inputHaber.name = `detalles[${tablaDetalles.rows.length - 1}][haber]`;
                            inputHaber.value = haber;
                            inputHaber.step = '0.01';
                            inputHaber.min = '0';
                            cellHaber.appendChild(inputHaber);
                            
                            // Acciones
                            const cellAcciones = row.insertCell();
                            const btnEliminar = document.createElement('button');
                            btnEliminar.type = 'button';
                            btnEliminar.className = 'btn btn-danger btn-sm';
                            btnEliminar.innerHTML = '<i class="fas fa-trash"></i>';
                            btnEliminar.onclick = function() {
                                row.remove();
                                actualizarTotales();
                            };
                            cellAcciones.appendChild(btnEliminar);
                        }
                        </script>
                        @endif
                        @endif

                        <!-- Detalles del Asiento -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Detalles del Asiento</h5>
                                    <button type="button" class="btn btn-primary btn-sm" id="agregarDetalle">
                                        <i class="fas fa-plus"></i> Agregar Línea
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tablaDetalles">
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
                                            <tr class="detalle-row">
                                                <td>
                                                    <select class="form-select cuenta-select" name="detalles[0][cuenta_id]" required>
                                                        <option value="">Seleccione una cuenta...</option>
                                                        @foreach($cuentas as $cuenta)
                                                            <option value="{{ $cuenta->id }}">
                                                                {{ $cuenta->codigo }} - {{ $cuenta->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="detalles[0][descripcion]">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control debe" 
                                                           name="detalles[0][debe]" value="0.00">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control haber" 
                                                           name="detalles[0][haber]" value="0.00">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm eliminar-detalle">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-light">
                                                <th colspan="2" class="text-end">Totales:</th>
                                                <th><span id="totalDebe">0.00</span></th>
                                                <th><span id="totalHaber">0.00</span></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <a href="{{ route('contabilidad.asientos.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnGuardar">
                                    <i class="fas fa-save"></i> Guardar Asiento
                                </button>
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
$(document).ready(function() {
    // Inicializar Select2 en los selectores de cuenta con búsqueda mejorada
    $('.cuenta-select').select2({
        theme: 'bootstrap-5',
        placeholder: 'Buscar cuenta por código o nombre...',
        allowClear: true,
        language: {
            noResults: function() {
                return "No se encontraron resultados";
            },
            searching: function() {
                return "Buscando...";
            }
        },
        matcher: function(params, data) {
            // Si no hay término de búsqueda, mostrar todas las opciones
            if ($.trim(params.term) === '') {
                return data;
            }

            // Buscar en el texto de la opción (código y nombre)
            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                return data;
            }

            // Si no hay coincidencia, no mostrar la opción
            return null;
        }
    });

    // Inicializar Select2 en el selector de centro de costo
    $('#centro_costo_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Buscar centro de costo por código o nombre...',
        allowClear: true,
        language: {
            noResults: function() {
                return "No se encontraron resultados";
            },
            searching: function() {
                return "Buscando...";
            }
        },
        matcher: function(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }

            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                return data;
            }

            return null;
        }
    });

    let detalleCount = 1;

    // Función para actualizar totales
    function actualizarTotales() {
        let totalDebe = 0;
        let totalHaber = 0;
        
        $('.debe').each(function() {
            totalDebe += parseFloat($(this).val()) || 0;
        });
        
        $('.haber').each(function() {
            totalHaber += parseFloat($(this).val()) || 0;
        });
        
        $('#totalDebe').text(totalDebe.toFixed(2));
        $('#totalHaber').text(totalHaber.toFixed(2));
        
        // Verificar si el asiento está balanceado
        if (Math.abs(totalDebe - totalHaber) < 0.01) {
            $('#btnGuardar').prop('disabled', false);
            $('#totalDebe, #totalHaber').removeClass('text-danger').addClass('text-success');
        } else {
            $('#btnGuardar').prop('disabled', true);
            $('#totalDebe, #totalHaber').removeClass('text-success').addClass('text-danger');
        }
    }

    // Agregar nuevo detalle con Select2 inicializado
    $('#agregarDetalle').click(function() {
        let newRow = `
            <tr class="detalle-row">
                <td>
                    <select class="form-select cuenta-select" name="detalles[${detalleCount}][cuenta_id]" required>
                        <option value="">Seleccione una cuenta...</option>
                        @foreach($cuentas as $cuenta)
                            <option value="{{ $cuenta->id }}">
                                {{ $cuenta->codigo }} - {{ $cuenta->nombre }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control" name="detalles[${detalleCount}][descripcion]">
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control debe" 
                           name="detalles[${detalleCount}][debe]" value="0.00">
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control haber" 
                           name="detalles[${detalleCount}][haber]" value="0.00">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm eliminar-detalle">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#tablaDetalles tbody').append(newRow);
        
        // Inicializar Select2 en el nuevo select
        $('#tablaDetalles tbody tr:last-child .cuenta-select').select2({
            theme: 'bootstrap-5',
            placeholder: 'Buscar cuenta por código o nombre...',
            allowClear: true,
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                },
                searching: function() {
                    return "Buscando...";
                }
            },
            matcher: function(params, data) {
                if ($.trim(params.term) === '') {
                    return data;
                }

                if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                    return data;
                }

                return null;
            }
        });
        
        detalleCount++;
    });

    // Eliminar detalle
    $(document).on('click', '.eliminar-detalle', function() {
        if ($('.detalle-row').length > 1) {
            $(this).closest('tr').remove();
            actualizarTotales();
        }
    });

    // Actualizar totales cuando cambian los valores
    $(document).on('input', '.debe, .haber', function() {
        actualizarTotales();
    });

    // Inicializar
    actualizarTotales();
});
</script>
@endpush
@endsection