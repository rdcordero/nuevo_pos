@extends('layouts.app')

@section('title', 'Editar Correlativo')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Editar Correlativo</h3>
                <a href="{{ route('correlativos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('correlativos.update', $correlativo) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Sucursal</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $correlativo->sucursal->nombre }}" 
                                   readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tipo de Documento</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $correlativo->tipoDocumento->nombre }}" 
                                   readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="serie" class="form-label">Serie (Opcional)</label>
                            <input type="text" 
                                   class="form-control @error('serie') is-invalid @enderror" 
                                   id="serie" 
                                   name="serie" 
                                   value="{{ old('serie', $correlativo->serie) }}">
                            @error('serie')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Correlativo Actual</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ str_pad($correlativo->correlativo_actual, 8, '0', STR_PAD_LEFT) }}" 
                                   readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Correlativo Final</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ str_pad($correlativo->correlativo_final, 8, '0', STR_PAD_LEFT) }}" 
                                   readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Fecha de Inicio</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $correlativo->fecha_inicio->format('d/m/Y') }}" 
                                   readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                            <input type="date" 
                                   class="form-control @error('fecha_vencimiento') is-invalid @enderror" 
                                   id="fecha_vencimiento" 
                                   name="fecha_vencimiento" 
                                   value="{{ old('fecha_vencimiento', $correlativo->fecha_vencimiento->format('Y-m-d')) }}" 
                                   required>
                            @error('fecha_vencimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="activo" 
                               name="activo" 
                               value="1" 
                               {{ old('activo', $correlativo->activo) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activo">Correlativo Activo</label>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('correlativos.index') }}" class="btn btn-secondary">
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

