@extends('layouts.app')

@section('title', 'Impuestos')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Impuestos</h1>
        @can('crear impuestos')
        <a href="{{ route('impuestos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Impuesto
        </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Porcentaje</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($impuestos as $impuesto)
                            <tr>
                                <td>{{ $impuesto->nombre }}</td>
                                <td>{{ $impuesto->descripcion }}</td>
                                <td>{{ number_format($impuesto->porcentaje, 2) }}%</td>
                                <td>
                                    <span class="badge bg-{{ $impuesto->activo ? 'success' : 'danger' }}">
                                        {{ $impuesto->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        @can('editar impuestos')
                                        <a href="{{ route('impuestos.edit', $impuesto) }}" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('eliminar impuestos')
                                        <form action="{{ route('impuestos.destroy', $impuesto) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('¿Está seguro de eliminar este impuesto?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay impuestos registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $impuestos->links() }}
            </div>
        </div>
    </div>
</div>
@endsection