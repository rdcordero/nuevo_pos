@extends('layouts.app')

@section('title', 'Nuevo Usuario')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Nuevo Usuario</h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('usuarios.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Roles</label>
                                <div class="row">
                                    @foreach ($roles as $id => $nombre)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input type="checkbox"
                                                    class="form-check-input @error('roles') is-invalid @enderror"
                                                    id="rol{{ $id }}" name="roles[]" value="{{ $id }}"
                                                    {{ in_array($id, old('roles', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="rol{{ $id }}">
                                                    {{ $nombre }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('roles')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Empresas</label>
                                <div class="row">
                                    @foreach ($empresas as $id => $nombre)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input type="checkbox"
                                                    class="form-check-input @error('empresas') is-invalid @enderror"
                                                    id="empresa{{ $id }}" name="empresas[]"
                                                    value="{{ $id }}"
                                                    {{ in_array($id, old('empresas', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="empresa{{ $id }}">
                                                    {{ $nombre }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('empresas')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sucursales</label>
                                @foreach ($empresas as $empresaId => $empresaNombre)
                                    <div class="card mb-2">
                                        <div class="card-header">
                                            {{ $empresaNombre }}
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach ($sucursales[$empresaId] ?? [] as $sucursalId => $sucursalNombre)
                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input type="checkbox"
                                                                class="form-check-input @error('sucursales') is-invalid @enderror"
                                                                id="sucursal{{ $sucursalId }}" name="sucursales[]"
                                                                value="{{ $sucursalId }}"
                                                                {{ in_array($sucursalId, old('sucursales', [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="sucursal{{ $sucursalId }}">
                                                                {{ $sucursalNombre }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @error('sucursales')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
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
