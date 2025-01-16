@extends('layouts.app')

@section('title', 'Nuevo Producto')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Nuevo Producto</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" 
                                           class="form-control @error('nombre') is-invalid @enderror" 
                                           id="nombre" 
                                           name="nombre" 
                                           value="{{ old('nombre') }}" 
                                           required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                              id="descripcion" 
                                              name="descripcion" 
                                              rows="3">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="categoria_id" class="form-label">Categoría</label>
                                    <select class="form-select @error('categoria_id') is-invalid @enderror" 
                                            id="categoria_id" 
                                            name="categoria_id" 
                                            required>
                                        <option value="">Seleccione una categoría</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id }}" 
                                                    {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                                {{ $categoria->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('categoria_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="subcategoria_id" class="form-label">Subcategoría</label>
                                    <select class="form-select @error('subcategoria_id') is-invalid @enderror" 
                                            id="subcategoria_id" 
                                            name="subcategoria_id" 
                                            required>
                                        <option value="">Seleccione una subcategoría</option>
                                        @foreach($subcategorias as $subcategoria)
                                            <option value="{{ $subcategoria->id }}" 
                                                    {{ old('subcategoria_id') == $subcategoria->id ? 'selected' : '' }}>
                                                {{ $subcategoria->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subcategoria_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="tipo" class="form-label">Tipo de Producto</label>
                                    <select class="form-select @error('tipo') is-invalid @enderror" 
                                            id="tipo" 
                                            name="tipo" 
                                            required>
                                        <option value="simple" {{ old('tipo') == 'simple' ? 'selected' : '' }}>Simple</option>
                                        <option value="compuesto" {{ old('tipo') == 'compuesto' ? 'selected' : '' }}>Compuesto</option>
                                    </select>
                                    @error('tipo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                                    <select class="form-select @error('unidad_medida') is-invalid @enderror" 
                                            id="unidad_medida" 
                                            name="unidad_medida" 
                                            required>
                                        <option value="unidad" {{ old('unidad_medida') == 'unidad' ? 'selected' : '' }}>Unidad</option>
                                        <option value="kg" {{ old('unidad_medida') == 'kg' ? 'selected' : '' }}>Kilogramo</option>
                                        <option value="g" {{ old('unidad_medida') == 'g' ? 'selected' : '' }}>Gramo</option>
                                        <option value="l" {{ old('unidad_medida') == 'l' ? 'selected' : '' }}>Litro</option>
                                        <option value="ml" {{ old('unidad_medida') == 'ml' ? 'selected' : '' }}>Mililitro</option>
                                        <option value="m" {{ old('unidad_medida') == 'm' ? 'selected' : '' }}>Metro</option>
                                        <option value="cm" {{ old('unidad_medida') == 'cm' ? 'selected' : '' }}>Centímetro</option>
                                    </select>
                                    @error('unidad_medida')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="precio_compra" class="form-label">Precio de Compra</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control @error('precio_compra') is-invalid @enderror" 
                                               id="precio_compra" 
                                               name="precio_compra" 
                                               value="{{ old('precio_compra') }}" 
                                               step="0.01" 
                                               min="0" 
                                               required>
                                        @error('precio_compra')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="precio_venta" class="form-label">Precio de Venta</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control @error('precio_venta') is-invalid @enderror" 
                                               id="precio_venta" 
                                               name="precio_venta" 
                                               value="{{ old('precio_venta') }}" 
                                               step="0.01" 
                                               min="0" 
                                               required>
                                        @error('precio_venta')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                                    <input type="number" 
                                           class="form-control @error('stock_minimo') is-invalid @enderror" 
                                           id="stock_minimo" 
                                           name="stock_minimo" 
                                           value="{{ old('stock_minimo') }}" 
                                           min="0" 
                                           required>
                                    @error('stock_minimo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="stock_maximo" class="form-label">Stock Máximo</label>
                                    <input type="number" 
                                           class="form-control @error('stock_maximo') is-invalid @enderror" 
                                           id="stock_maximo" 
                                           name="stock_maximo" 
                                           value="{{ old('stock_maximo') }}" 
                                           min="0" 
                                           required>
                                    @error('stock_maximo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="punto_reorden" class="form-label">Punto de Reorden</label>
                                    <input type="number" 
                                           class="form-control @error('punto_reorden') is-invalid @enderror" 
                                           id="punto_reorden" 
                                           name="punto_reorden" 
                                           value="{{ old('punto_reorden') }}" 
                                           min="0" 
                                           required>
                                    @error('punto_reorden')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="ubicacion" class="form-label">Ubicación</label>
                                    <input type="text" 
                                           class="form-control @error('ubicacion') is-invalid @enderror" 
                                           id="ubicacion" 
                                           name="ubicacion" 
                                           value="{{ old('ubicacion') }}">
                                    @error('ubicacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="imagen" class="form-label">Imagen del Producto</label>
                            <input type="file" 
                                   class="form-control @error('imagen') is-invalid @enderror" 
                                   id="imagen" 
                                   name="imagen" 
                                   accept="image/*">
                            @error('imagen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Impuestos Aplicables</label>
                            <div class="row">
                                @foreach($impuestos as $impuesto)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   class="form-check-input" 
                                                   id="impuesto_{{ $impuesto->id }}" 
                                                   name="impuestos[]" 
                                                   value="{{ $impuesto->id }}"
                                                   {{ in_array($impuesto->id, old('impuestos', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="impuesto_{{ $impuesto->id }}">
                                                {{ $impuesto->nombre }} ({{ $impuesto->porcentaje }}%)
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('impuestos')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="componentes-section" class="card mb-3" style="{{ old('tipo') == 'compuesto' ? '' : 'display: none;' }}">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Componentes del Producto</h5>
                            </div>
                            <div class="card-body">
                                <div id="componentes-container">
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label">Producto</label>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">Cantidad</label>
                                        </div>
                                    </div>
                                    <div id="componentes-list">
                                        <!-- Los componentes se agregarán aquí dinámicamente -->
                                    </div>
                                    <button type="button" class="btn btn-secondary mt-2" onclick="agregarComponente()">
                                        <i class="fas fa-plus"></i> Agregar Componente
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Códigos de Barra</label>
                            <div id="codigos-barra-list">
                                <!-- Los códigos de barra se agregarán aquí dinámicamente -->
                            </div>
                            <button type="button" class="btn btn-secondary mt-2" onclick="agregarCodigoBarra()">
                                <i class="fas fa-plus"></i> Agregar Código de Barras
                            </button>
                            @error('codigos_barra.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="activo" 
                                       name="activo" 
                                       value="1" 
                                       {{ old('activo', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">Producto Activo</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('productos.index') }}" class="btn btn-secondary">
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

@push('scripts')
<script>
// Variables globales
let componenteIndex = 0;
let codigoBarraIndex = 0;

function agregarComponente() {
    const container = document.getElementById('componentes-list');
    const row = document.createElement('div');
    row.className = 'row mb-2 componente-row';
    row.innerHTML = `
        <div class="col-md-6">
            <select name="componentes[${componenteIndex}][id]" class="form-select" required>
                <option value="">Seleccione un producto</option>
                @foreach($productos as $prod)
                    <option value="{{ $prod->id }}">{{ $prod->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5">
            <input type="number" 
                   name="componentes[${componenteIndex}][cantidad]" 
                   class="form-control" 
                   step="0.01" 
                   min="0.01" 
                   required>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarComponente(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(row);
    componenteIndex++;
}

function eliminarComponente(button) {
    button.closest('.componente-row').remove();
}

function agregarCodigoBarra() {
    const container = document.getElementById('codigos-barra-list');
    const row = document.createElement('div');
    row.className = 'row mb-2 codigo-barra-row';
    row.innerHTML = `
        <div class="col-md-10">
            <input type="text" name="codigos_barra[${codigoBarraIndex}]" class="form-control" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarCodigoBarra(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(row);
    codigoBarraIndex++;
}

function eliminarCodigoBarra(button) {
    button.closest('.codigo-barra-row').remove();
}

document.addEventListener('DOMContentLoaded', function() {
    const categoriaSelect = document.getElementById('categoria_id');
    const subcategoriaSelect = document.getElementById('subcategoria_id');
    const tipoSelect = document.getElementById('tipo');
    const componentesSection = document.getElementById('componentes-section');
    
    // Función para mostrar/ocultar la sección de componentes
    function toggleComponentesSection() {
        componentesSection.style.display = tipoSelect.value === 'compuesto' ? 'block' : 'none';
    }

    // Evento change para el select de tipo de producto
    tipoSelect.addEventListener('change', toggleComponentesSection);
    
    // Función para cargar las subcategorías
    async function cargarSubcategorias(categoriaId) {
        if (!categoriaId) {
            subcategoriaSelect.innerHTML = '<option value="">Seleccione una subcategoría</option>';
            subcategoriaSelect.disabled = true;
            return;
        }

        try {
            const response = await fetch(`/productos/subcategorias/${categoriaId}`);
            if (!response.ok) throw new Error('Error al cargar subcategorías');
            
            const subcategorias = await response.json();
            
            // Guardar la subcategoría seleccionada actualmente (si existe)
            const subcategoriaSeleccionada = subcategoriaSelect.value;
            
            // Limpiar y actualizar el select de subcategorías
            subcategoriaSelect.innerHTML = '<option value="">Seleccione una subcategoría</option>';
            
            subcategorias.forEach(subcategoria => {
                const option = new Option(subcategoria.nombre, subcategoria.id);
                subcategoriaSelect.appendChild(option);
            });
            
            // Restaurar la selección si la subcategoría existe en la nueva lista
            if (subcategoriaSeleccionada) {
                subcategoriaSelect.value = subcategoriaSeleccionada;
            }
            
            subcategoriaSelect.disabled = false;
        } catch (error) {
            console.error('Error:', error);
            subcategoriaSelect.innerHTML = '<option value="">Error al cargar subcategorías</option>';
            subcategoriaSelect.disabled = true;
        }
    }

    // Evento change para el select de categoría
    categoriaSelect.addEventListener('change', function() {
        cargarSubcategorias(this.value);
    });

    // Cargar subcategorías si hay una categoría seleccionada al cargar la página
    if (categoriaSelect.value) {
        cargarSubcategorias(categoriaSelect.value);
    }
});
</script>
@endpush

