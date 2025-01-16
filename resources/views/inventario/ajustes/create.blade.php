@extends('layouts.app')

@section('title', 'Nuevo Ajuste de Inventario')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Nuevo Ajuste de Inventario</h3>
            <div class="card-tools">
                <a href="{{ route('inventario.ajustes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('inventario.ajustes.store') }}" method="POST" id="ajusteForm">
                @csrf

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo de Ajuste</label>
                            <select class="form-select @error('tipo') is-invalid @enderror" 
                                    id="tipo" 
                                    name="tipo" 
                                    required>
                                <option value="">Seleccione el tipo</option>
                                <option value="entrada" {{ old('tipo') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                                <option value="salida" {{ old('tipo') == 'salida' ? 'selected' : '' }}>Salida</option>
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" 
                                   class="form-control @error('fecha') is-invalid @enderror" 
                                   id="fecha" 
                                   name="fecha" 
                                   value="{{ old('fecha', date('Y-m-d')) }}" 
                                   required>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="sucursal_id" class="form-label">Sucursal</label>
                            <select class="form-select @error('sucursal_id') is-invalid @enderror" 
                                    id="sucursal_id" 
                                    name="sucursal_id" 
                                    required>
                                <option value="">Seleccione una sucursal</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}" 
                                            {{ old('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                                        {{ $sucursal->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sucursal_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="bodega_id" class="form-label">Bodega</label>
                    <select class="form-select @error('bodega_id') is-invalid @enderror" 
                            id="bodega_id" 
                            name="bodega_id" 
                            required>
                        <option value="">Seleccione una bodega</option>
                        @foreach($bodegas as $bodega)
                            <option value="{{ $bodega->id }}" 
                                    data-sucursal="{{ $bodega->sucursal_id }}"
                                    {{ old('bodega_id') == $bodega->id ? 'selected' : '' }}>
                                {{ $bodega->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('bodega_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="motivo" class="form-label">Motivo del Ajuste</label>
                    <textarea class="form-control @error('motivo') is-invalid @enderror" 
                              id="motivo" 
                              name="motivo" 
                              rows="2" 
                              required>{{ old('motivo') }}</textarea>
                    @error('motivo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="observacion" class="form-label">Observación</label>
                    <textarea class="form-control @error('observacion') is-invalid @enderror" 
                              id="observacion" 
                              name="observacion" 
                              rows="2">{{ old('observacion') }}</textarea>
                    @error('observacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Productos</h5>
                    </div>
                    <div class="card-body">
                        <div id="productos-container">
                            <!-- Los productos se agregarán aquí dinámicamente -->
                        </div>
                        <button type="button" class="btn btn-secondary mt-2" onclick="agregarProducto()">
                            <i class="fas fa-plus"></i> Agregar Producto
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('inventario.ajustes.index') }}" class="btn btn-secondary">
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
@endsection

@push('scripts')
<script>
let productoIndex = 0;

function agregarProducto() {
    const container = document.getElementById('productos-container');
    const div = document.createElement('div');
    div.className = 'row mb-3 producto-row';
    div.innerHTML = `
        <div class="col-md-4">
            <select name="productos[${productoIndex}][id]" class="form-select" required>
                <option value="">Seleccione un producto</option>
                @foreach($productos as $producto)
                    <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text">Cant.</span>
                <input type="number" 
                       name="productos[${productoIndex}][cantidad]" 
                       class="form-control" 
                       step="0.01" 
                       min="0.01" 
                       required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" 
                       name="productos[${productoIndex}][costo_unitario]" 
                       class="form-control" 
                       step="0.01" 
                       min="0" 
                       required>
            </div>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarProducto(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
    productoIndex++;
}

function eliminarProducto(button) {
    button.closest('.producto-row').remove();
}

// Filtrar bodegas según la sucursal seleccionada
document.addEventListener('DOMContentLoaded', function() {
    const sucursalSelect = document.getElementById('sucursal_id');
    const bodegaSelect = document.getElementById('bodega_id');
    const bodegaOptions = Array.from(bodegaSelect.options);

    function filtrarBodegas() {
        const sucursalId = sucursalSelect.value;
        
        // Restaurar todas las opciones
        bodegaSelect.innerHTML = '<option value="">Seleccione una bodega</option>';
        
        if (sucursalId) {
            // Filtrar y agregar solo las bodegas de la sucursal seleccionada
            bodegaOptions.forEach(option => {
                if (option.dataset.sucursal === sucursalId) {
                    bodegaSelect.add(option.cloneNode(true));
                }
            });
        }
    }

    sucursalSelect.addEventListener('change', filtrarBodegas);
    
    // Ejecutar al cargar la página si hay una sucursal seleccionada
    if (sucursalSelect.value) {
        filtrarBodegas();
    }

    // Restaurar la selección previa de bodega si existe
    const oldBodegaId = "{{ old('bodega_id') }}";
    if (oldBodegaId) {
        bodegaSelect.value = oldBodegaId;
    }
});

// Agregar al menos un producto al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    agregarProducto();
});
</script>
@endpush

