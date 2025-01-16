@extends('layouts.app')

@push('css')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        border: 1px solid #dee2e6;
    }
    .producto-row:hover {
        background-color: #f8f9fa;
    }
    #codigoBarras:focus {
        background-color: #e8f0fe;
    }
    .total-section {
        font-size: 1.2em;
    }
    .payment-method-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .payment-method-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .payment-method-card.selected {
        border-color: #0d6efd;
        background-color: #e8f0fe;
    }
    .cantidad-producto::-webkit-inner-spin-button,
    .cantidad-producto::-webkit-outer-spin-button {
        opacity: 1;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.075);
    }
    .error-feedback {
        color: #dc3545;
        font-size: 80%;
        margin-top: 0.25rem;
    }
</style>
@endpush

@section('title', 'Nueva Venta')

@section('content')
<div class="container-fluid">
    @if($tiposDocumento->isEmpty())
        <div class="alert alert-warning">
            <h4><i class="fas fa-exclamation-triangle"></i> Atención</h4>
            <p>No hay tipos de documento disponibles para realizar ventas en esta sucursal.</p>
            <p>Por favor, contacte al administrador para configurar los correlativos necesarios.</p>
            <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    @else
        <form id="ventaForm" action="{{ route('ventas.store') }}" method="POST">
            @csrf
            <div class="row">
                <!-- Columna Principal -->
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Nueva Venta</h3>
                                <div>
                                    <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary ms-2" id="btnGuardar">
                                        <i class="fas fa-save"></i> Guardar Venta
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Sección de Código de Barras -->
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-barcode"></i>
                                    </span>
                                    <input type="text" 
                                           id="codigoBarras" 
                                           class="form-control form-control-lg" 
                                           placeholder="Escanee o ingrese el código de barras"
                                           autofocus>
                                    <button type="button" 
                                            class="btn btn-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#buscarProductoModal">
                                        <i class="fas fa-search"></i> Buscar Producto
                                    </button>
                                </div>
                            </div>

                            <!-- Tabla de Productos -->
                            <div class="table-responsive">
                                <table class="table table-hover" id="productosTable">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Descripción</th>
                                            <th width="120">Cantidad</th>
                                            <th width="150">Precio</th>
                                            <th width="150">Subtotal</th>
                                            <th width="50">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Los productos se agregarán dinámicamente aquí -->
                                    </tbody>
                                    <tfoot class="d-none">
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                No hay productos agregados
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Observaciones -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="notas" class="form-label">Observaciones</label>
                                <textarea name="notas" 
                                          id="notas" 
                                          class="form-control" 
                                          rows="2"
                                          placeholder="Ingrese observaciones adicionales">{{ old('notas') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Lateral -->
                <div class="col-md-4">
                    <!-- Cliente -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user"></i> Cliente
                            </h5>
                        </div>
                        <div class="card-body">
                            <select name="cliente_id" 
                                    id="cliente_id" 
                                    class="form-select select2 @error('cliente_id') is-invalid @enderror" 
                                    required
                                    data-placeholder="Seleccione el cliente">
                                <option value="">Seleccione el cliente</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" 
                                            {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}
                                            data-tipo="{{ $cliente->tipo_cliente }}"
                                            data-exento="{{ $cliente->exento }}">
                                        {{ $cliente->nombre }}
                                        @if($cliente->nombre_comercial)
                                            - {{ $cliente->nombre_comercial }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Documento -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-file-alt"></i> Documento
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="tipo_documento_id" class="form-label">Tipo de Documento</label>
                                <select name="tipo_documento_id" 
                                        id="tipo_documento_id" 
                                        class="form-select @error('tipo_documento_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Seleccione el tipo</option>
                                    @foreach($tiposDocumento as $tipo)
                                        <option value="{{ $tipo->id }}" 
                                                {{ old('tipo_documento_id') == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_documento_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="serie" class="form-label">Serie</label>
                                        <input type="text" 
                                               name="serie" 
                                               id="serie" 
                                               class="form-control @error('serie') is-invalid @enderror" 
                                               readonly>
                                        @error('serie')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="numero" class="form-label">Número</label>
                                        <input type="text" 
                                               name="numero" 
                                               id="numero" 
                                               class="form-control @error('numero') is-invalid @enderror" 
                                               readonly>
                                        @error('numero')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fecha" class="form-label">Fecha</label>
                                        <input type="date" 
                                               name="fecha" 
                                               id="fecha" 
                                               class="form-control @error('fecha') is-invalid @enderror" 
                                               value="{{ old('fecha', date('Y-m-d')) }}" 
                                               required>
                                        @error('fecha')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fecha_vencimiento" class="form-label">Vencimiento</label>
                                        <input type="date" 
                                               name="fecha_vencimiento" 
                                               id="fecha_vencimiento" 
                                               class="form-control @error('fecha_vencimiento') is-invalid @enderror" 
                                               value="{{ old('fecha_vencimiento') }}">
                                        @error('fecha_vencimiento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Totales -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-calculator"></i> Totales
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row total-section">
                                <div class="col-7">Subtotal:</div>
                                <div class="col-5 text-end" id="subtotal">$0.00</div>
                            </div>
                            <div class="row total-section">
                                <div class="col-7">Descuento:</div>
                                <div class="col-5 text-end" id="descuento">$0.00</div>
                            </div>
                            <div class="row total-section">
                                <div class="col-7">IVA:</div>
                                <div class="col-5 text-end" id="iva">$0.00</div>
                            </div>
                            <div class="row total-section fw-bold">
                                <div class="col-7">Total:</div>
                                <div class="col-5 text-end" id="total">$0.00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Forma de Pago -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-money-bill"></i> Forma de Pago
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                @foreach($formasPago as $formaPago)
                                <div class="col-6">
                                    <div class="card payment-method-card h-100" 
                                         data-forma-pago-id="{{ $formaPago->id }}"
                                         data-requiere-referencia="{{ $formaPago->requiere_referencia }}">
                                        <div class="card-body text-center">
                                            <i class="fas fa-{{ 
                                                $formaPago->tipo === 'efectivo' ? 'money-bill' : 
                                                ($formaPago->tipo === 'tarjeta' ? 'credit-card' : 
                                                ($formaPago->tipo === 'transferencia' ? 'university' : 
                                                ($formaPago->tipo === 'cheque' ? 'money-check' : 'circle'))) 
                                            }} fa-2x mb-2"></i>
                                            <h6 class="mb-0">{{ $formaPago->nombre }}</h6>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div id="referenciaPagoDiv" class="mt-3" style="display: none;">
                                <label for="referencia" class="form-label">Referencia</label>
                                <input type="text" 
                                       name="referencia" 
                                       id="referencia" 
                                       class="form-control"
                                       placeholder="Ingrese el número de referencia">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>

<!-- Modal de Búsqueda de Productos -->
<div class="modal fade" id="buscarProductoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buscar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               id="buscarProductoInput" 
                               class="form-control" 
                               placeholder="Buscar por nombre o código...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="productosModalTable">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                            <tr>
                                <td>{{ $producto->codigo }}</td>
                                <td>{{ $producto->nombre }}</td>
                                <td>${{ number_format($producto->precio_venta, 2) }}</td>
                                <td>{{ $producto->stock }}</td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-sm btn-primary btn-agregar-producto" 
                                            data-producto-id="{{ $producto->id }}"
                                            data-producto-codigo="{{ $producto->codigo }}"
                                            data-producto-nombre="{{ $producto->nombre }}"
                                            data-producto-precio="{{ $producto->precio_venta }}">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Inicializar Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        language: {
            noResults: function() {
                return "No se encontraron resultados";
            },
            searching: function() {
                return "Buscando...";
            }
        }
    });

    // Variables globales
    let productos = [];
    const IVA = 0.13; // 13%

    // Función para formatear números como moneda
    function formatMoney(amount) {
        return '$' + parseFloat(amount).toFixed(2);
    }

    // Función para calcular totales
    function calcularTotales() {
        let subtotal = 0;
        let descuento = 0;
        let iva = 0;
        let total = 0;

        productos.forEach(function(producto) {
            subtotal += producto.subtotal;
        });

        // Si el cliente es exento, no se calcula IVA
        const clienteExento = $('#cliente_id option:selected').data('exento');
        if (!clienteExento) {
            iva = subtotal * IVA;
        }

        total = subtotal - descuento + iva;

        $('#subtotal').text(formatMoney(subtotal));
        $('#descuento').text(formatMoney(descuento));
        $('#iva').text(formatMoney(iva));
        $('#total').text(formatMoney(total));

        // Mostrar u ocultar mensaje de tabla vacía
        if (productos.length === 0) {
            $('#productosTable tfoot').removeClass('d-none');
        } else {
            $('#productosTable tfoot').addClass('d-none');
        }
    }

    // Función para agregar producto a la tabla
    function agregarProducto(producto) {
        // Verificar si el producto ya existe
        const index = productos.findIndex(p => p.codigo === producto.codigo);
        
        if (index !== -1) {
            // Incrementar cantidad si ya existe
            productos[index].cantidad++;
            productos[index].subtotal = productos[index].cantidad * productos[index].precio;
            actualizarFilaProducto(index);
        } else {
            // Agregar nuevo producto
            const nuevoProducto = {
                id: producto.id,
                codigo: producto.codigo,
                nombre: producto.nombre,
                cantidad: 1,
                precio: parseFloat(producto.precio),
                subtotal: parseFloat(producto.precio)
            };
            
            productos.push(nuevoProducto);
            agregarFilaProducto(nuevoProducto, productos.length - 1);
        }

        calcularTotales();
    }

    // Función para crear fila de producto
    function agregarFilaProducto(producto, index) {
        const html = `
            <tr class="producto-row" data-index="${index}">
                <td>${producto.codigo}</td>
                <td>${producto.nombre}</td>
                <td>
                    <input type="number" 
                           class="form-control form-control-sm cantidad-producto" 
                           value="${producto.cantidad}" 
                           min="1" 
                           step="1">
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">$</span>
                        <input type="number" 
                               class="form-control precio-producto" 
                               value="${producto.precio.toFixed(2)}" 
                               step="0.01">
                    </div>
                </td>
                <td class="text-end subtotal-producto">${formatMoney(producto.subtotal)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger btn-eliminar-producto">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#productosTable tbody').append(html);
    }

    // Función para actualizar fila de producto
    function actualizarFilaProducto(index) {
        const producto = productos[index];
        const $fila = $(`.producto-row[data-index="${index}"]`);
        
        $fila.find('.cantidad-producto').val(producto.cantidad);
        $fila.find('.precio-producto').val(producto.precio.toFixed(2));
        $fila.find('.subtotal-producto').text(formatMoney(producto.subtotal));
    }

    // Evento para lector de código de barras
    $('#codigoBarras').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            const codigo = $(this).val();
            
            // Buscar producto por código de barras
            const producto = @json($productos->keyBy('codigo'));
            if (producto[codigo]) {
                agregarProducto({
                    id: producto[codigo].id,
                    codigo: producto[codigo].codigo,
                    nombre: producto[codigo].nombre,
                    precio: producto[codigo].precio_venta
                });
                $(this).val('').focus();
            } else {
                alert('Producto no encontrado');
                $(this).select();
            }
        }
    });

    // Evento para agregar producto desde el modal
    $(document).on('click', '.btn-agregar-producto', function() {
        const producto = {
            id: $(this).data('producto-id'),
            codigo: $(this).data('producto-codigo'),
            nombre: $(this).data('producto-nombre'),
            precio: $(this).data('producto-precio')
        };
        
        agregarProducto(producto);
        $('#buscarProductoModal').modal('hide');
        $('#codigoBarras').focus();
    });

    // Evento para eliminar producto
    $(document).on('click', '.btn-eliminar-producto', function() {
        const index = $(this).closest('tr').data('index');
        productos.splice(index, 1);
        $(this).closest('tr').remove();
        
        // Reindexar las filas restantes
        $('.producto-row').each(function(i) {
            $(this).attr('data-index', i);
        });
        
        calcularTotales();
    });

    // Eventos para actualizar cantidad y precio
    $(document).on('change', '.cantidad-producto, .precio-producto', function() {
        const $fila = $(this).closest('tr');
        const index = $fila.data('index');
        const cantidad = parseFloat($fila.find('.cantidad-producto').val());
        const precio = parseFloat($fila.find('.precio-producto').val());
        
        productos[index].cantidad = cantidad;
        productos[index].precio = precio;
        productos[index].subtotal = cantidad * precio;
        
        actualizarFilaProducto(index);
        calcularTotales();
    });

    // Manejo de formas de pago
    $('.payment-method-card').click(function() {
        $('.payment-method-card').removeClass('selected');
        $(this).addClass('selected');
        
        const formaPagoId = $(this).data('forma-pago-id');
        const requiereReferencia = $(this).data('requiere-referencia');
        
        $('#referenciaPagoDiv').toggle(requiereReferencia);
        
        // Agregar campo oculto para la forma de pago seleccionada
        $('input[name="forma_pago_id"]').remove();
        $('<input>').attr({
            type: 'hidden',
            name: 'forma_pago_id',
            value: formaPagoId
        }).appendTo('#ventaForm');
    });

    // Manejo del tipo de documento
    $('#tipo_documento_id').change(function() {
        const tipoDocumentoId = $(this).val();
        if (tipoDocumentoId) {
            // Obtener correlativo
            $.get(`/api/ventas/correlativo/${tipoDocumentoId}`)
                .done(function(response) {
                    $('#serie').val(response.serie || '');
                    $('#numero').val(response.siguiente);
                })
                .fail(function(xhr) {
                    const error = xhr.responseJSON?.error || 'Error al obtener el correlativo';
                    alert(error);
                    $(this).val(''); // Limpiar selección
                    $('#serie, #numero').val('');
                });
        } else {
            $('#serie, #numero').val('');
        }
    });

    // Validación del formulario
    $('#ventaForm').submit(function(e) {
        e.preventDefault();
        
        // Validar cliente
        if (!$('#cliente_id').val()) {
            alert('Debe seleccionar un cliente');
            $('#cliente_id').focus();
            return;
        }

        // Validar tipo de documento
        if (!$('#tipo_documento_id').val()) {
            alert('Debe seleccionar un tipo de documento');
            $('#tipo_documento_id').focus();
            return;
        }

        // Validar productos
        if (productos.length === 0) {
            alert('Debe agregar al menos un producto');
            $('#codigoBarras').focus();
            return;
        }
        
        // Validar forma de pago
        if (!$('input[name="forma_pago_id"]').length) {
            alert('Debe seleccionar una forma de pago');
            return;
        }

        // Validar referencia si es requerida
        const formaPagoCard = $('.payment-method-card.selected');
        if (formaPagoCard.data('requiere-referencia') && !$('#referencia').val()) {
            alert('Debe ingresar el número de referencia para esta forma de pago');
            $('#referencia').focus();
            return;
        }

        // Agregar productos al formulario
        productos.forEach(function(producto, index) {
            $('<input>').attr({
                type: 'hidden',
                name: `detalles[${index}][producto_id]`,
                value: producto.id
            }).appendTo('#ventaForm');
            
            $('<input>').attr({
                type: 'hidden',
                name: `detalles[${index}][cantidad]`,
                value: producto.cantidad
            }).appendTo('#ventaForm');
            
            $('<input>').attr({
                type: 'hidden',
                name: `detalles[${index}][precio_unitario]`,
                value: producto.precio
            }).appendTo('#ventaForm');
        });

        // Enviar formulario
        this.submit();
    });

    // Búsqueda de productos en el modal
    $('#buscarProductoInput').on('keyup', function() {
        const busqueda = $(this).val().toLowerCase();
        $('#productosModalTable tbody tr').filter(function() {
            $(this).toggle(
                $(this).text().toLowerCase().indexOf(busqueda) > -1
            );
        });
    });

    // Inicialización
    calcularTotales();
});
</script>
@endpush

