@extends('layouts.app')

@section('title', 'Nuevo Correlativo')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Nuevo Correlativo</h3>
                <a href="{{ route('correlativos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('correlativos.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
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
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tipo_documento_id" class="form-label">Tipo de Documento</label>
                            <select class="form-select @error('tipo_documento_id') is-invalid @enderror" 
                                    id="tipo_documento_id" 
                                    name="tipo_documento_id" 
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
                                   value="{{ old('serie') }}">
                            @error('serie')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="correlativo_inicial" class="form-label">Correlativo Inicial</label>
                            <input type="number" 
                                   class="form-control @error('correlativo_inicial') is-invalid @enderror" 
                                   id="correlativo_inicial" 
                                   name="correlativo_inicial" 
                                   value="{{ old('correlativo_inicial') }}" 
                                   min="1"
                                   required>
                            @error('correlativo_inicial')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="correlativo_final" class="form-label">Correlativo Final</label>
                            <input type="number" 
                                   class="form-control @error('correlativo_final') is-invalid @enderror" 
                                   id="correlativo_final" 
                                   name="correlativo_final" 
                                   value="{{ old('correlativo_final') }}" 
                                   required>
                            @error('correlativo_final')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" 
                                   class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                   id="fecha_inicio" 
                                   name="fecha_inicio" 
                                   value="{{ old('fecha_inicio') }}" 
                                   required>
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                            <input type="date" 
                                   class="form-control @error('fecha_vencimiento') is-invalid @enderror" 
                                   id="fecha_vencimiento" 
                                   name="fecha_vencimiento" 
                                   value="{{ old('fecha_vencimiento') }}" 
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
                               {{ old('activo', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activo">Correlativo Activo</label>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('correlativos.index') }}" class="btn btn-secondary">
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

