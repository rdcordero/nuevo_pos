@extends('layouts.app')

@section('title', 'Nueva Empresa')

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Nueva Empresa</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('empresas.store') }}" method="POST">
                @csrf

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
                    <label for="nrc" class="form-label">NRC</label>
                    <input type="text" 
                           class="form-control @error('nrc') is-invalid @enderror" 
                           id="nrc" 
                           name="nrc" 
                           value="{{ old('nrc') }}" 
                           required>
                    @error('nrc')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="nit" class="form-label">NIT</label>
                    <input type="text" 
                           class="form-control @error('nit') is-invalid @enderror" 
                           id="nit" 
                           name="nit" 
                           value="{{ old('nit') }}" 
                           required>
                    @error('nit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" 
                           class="form-control @error('direccion') is-invalid @enderror" 
                           id="direccion" 
                           name="direccion" 
                           value="{{ old('direccion') }}" 
                           required>
                    @error('direccion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" 
                           class="form-control @error('telefono') is-invalid @enderror" 
                           id="telefono" 
                           name="telefono" 
                           value="{{ old('telefono') }}" 
                           required>
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('empresas.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection