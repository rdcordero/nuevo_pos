@extends('layouts.app')

@section('title', 'Editar Turno')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Editar Turno</h3>
                <a href="{{ route('turnos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('turnos.update', $turno) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sucursal_id" class="form-label">Sucursal *</label>
                            <select class="form-select @error('sucursal_id') is-invalid @enderror" 
                                    id="sucursal_id" 
                                    name="sucursal_id" 
                                    required>
                                <option value="">Seleccione la sucursal</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}" 
                                            {{ old('sucursal_id', $turno->sucursal_id) == $sucursal->id ? 'selected' : '' }}>
                                        {{ $sucursal->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sucursal_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="caja_id" class="form-label">Caja *</label>
                            <select class="form-select @error('caja_id') is-invalid @enderror" 
                                    id="caja_id" 
                                    name="caja_id" 
                                    required>
                                <option value="">Seleccione la caja</option>
                                @foreach($cajas as $caja)
                                    <option value="{{ $caja->id }}" 
                                            {{ old('caja_id', $turno->caja_id) == $caja->id ? 'selected' : '' }}>
                                        {{ $caja->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('caja_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="monto_apertura" class="form-label">Monto de Apertura *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                       class="form-control @error('monto_apertura') is-invalid @enderror" 
                                       id="monto_apertura" 
                                       name="monto_apertura" 
                                       value="{{ old('monto_apertura', $turno->monto_apertura) }}" 
                                       step="0.01" 
                                       min="0" 
                                       required>
                                @error('monto_apertura')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="observaciones_apertura" class="form-label">Observaciones</label>
                            <textarea class="form-control @error('observaciones_apertura') is-invalid @enderror" 
                                      id="observaciones_apertura" 
                                      name="observaciones_apertura" 
                                      rows="3">{{ old('observaciones_apertura', $turno->observaciones_apertura) }}</textarea>
                            @error('observaciones_apertura')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('turnos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('sucursal_id').addEventListener('change', function() {
    const sucursalId = this.value;
    const cajaSelect = document.getElementById('caja_id');
    
    // Limpiar select de cajas
    cajaSelect.innerHTML = '<option value="">Seleccione la caja</option>';
    
    if (sucursalId) {
        // Cargar cajas de la sucursal seleccionada
        fetch(`/api/sucursales/${sucursalId}/cajas`)
            .then(response => response.json())
            .then(cajas => {
                cajas.forEach(caja => {
                    const option = new Option(caja.nombre, caja.id);
                    cajaSelect.add(option);
                });
                // Seleccionar la caja actual si existe
                if ({{ $turno->caja_id }}) {
                    cajaSelect.value = {{ $turno->caja_id }};
                }
            })
            .catch(error => console.error('Error:', error));
    }
});
</script>
@endpush

