@extends('layouts.app')

@section('title', 'Nueva Transferencia')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Nueva Transferencia</h3>
                <a href="{{ route('inventario.transferencias.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('inventario.transferencias.store') }}" method="POST" id="transferenciaForm">
                @csrf

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="bodega_origen_id" class="form-label">Bodega Origen</label>
                            <select class="form-select @error('bodega_origen_id') is-invalid @enderror" 
                                    id="bodega_origen_id" 
                                    name="bodega_origen_id" 
                                    required>
                                <option value="">Seleccione la bodega origen</option>
                                @foreach($bodegas as $bodega)
                                    <option value="{{ $bodega->id }}" 
                                            {{ old('bodega_origen_id') == $bodega->id ? 'selected' : '' }}>
                                        {{ $bodega->nombre }} ({{ $bodega->sucursal->nombre }})
                                    </option>
                                @endforeach
                            </select>
                            @error('bodega_origen_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="bodega_destino_id" class="form-label">Bodega Destino</label>
                            <select class="form-select @error('bodega_destino_id') is-invalid @enderror" 
                                    id="bodega_destino_id" 
                                    name="bodega_destino_id" 
                                    required>
                                <option value="">Seleccione la bodega destino</option>
                                @foreach($bodegas as $bodega)
                                    <option value="{{ $bodega->id }}" 
                                            {{ old('bodega_destino_id') == $bodega->id ? 'selected' : '' }}>
                                        {{ $bodega->nombre }} ({{ $bodega->sucursal->nombre }})
                                    </option>
                                @endforeach
                            </select>
                            @error('bodega_destino_id')
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
                </div>

                <div class="mb-3">
                    <label for="motivo" class="form-label">Motivo de la Transferencia</label>
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
                    <a href="{{ route('inventario.transferencias.index') }}" class="btn btn-secondary">
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

document.addEventListener('DOMContentLoaded', function() {
    // Validar que no se seleccione la misma bodega
    const bodegaOrigenSelect = document.getElementById('bodega_origen_id');
    const bodegaDestinoSelect = document.getElementById('bodega_destino_id');

    function validarBodegas() {
        if (bodegaOrigenSelect.value && bodegaOrigenSelect.value === bodegaDestinoSelect.value) {
            alert('La bodega de origen y destino no pueden ser la misma');
            bodegaDestinoSelect.value = '';
        }
    }

    bodegaOrigenSelect.addEventListener('change', validarBodegas);
    bodegaDestinoSelect.addEventListener('change', validarBodegas);

    // Agregar al menos un producto al cargar la página
    agregarProducto();
});
</script>
@endpush

