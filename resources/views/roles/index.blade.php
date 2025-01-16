@extends('layouts.app')

@section('title', 'Roles')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Roles</h1>
        @can('crear roles')
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Rol
        </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Permisos</th>
                            <th>Usuarios</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $rol)
                            <tr>
                                <td>{{ $rol->name }}</td>
                                <td>
                                    @foreach($rol->permissions as $permiso)
                                        <span class="badge bg-info">{{ $permiso->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $rol->users->count() }}</td>
                                <td>
                                    <div class="btn-group">
                                        @if($rol->name !== 'administrador')
                                            @can('editar roles')
                                            <a href="{{ route('roles.edit', $rol) }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            @endcan
                                            
                                            @can('eliminar roles')
                                            <form action="{{ route('roles.destroy', $rol) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('¿Está seguro de eliminar este rol?')">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                            @endcan
                                        @else
                                            <span class="badge bg-secondary">Rol del Sistema</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
</div>
@endsection