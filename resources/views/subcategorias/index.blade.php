@extends('layouts.app')

@section('title', 'Subcategorías')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Subcategorías</h1>
        @can('crear subcategorias')
        <a href="{{ route('subcategorias.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Subcategoría
        </a>
        @endcan
    </div>

   

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subcategorias as $subcategoria)
                            <tr>
                                <td>{{ $subcategoria->nombre }}</td>
                                <td>{{ $subcategoria->categoria->nombre }}</td>
                                <td>{{ $subcategoria->descripcion }}</td>
                                <td>
                                    <span class="badge bg-{{ $subcategoria->activo ? 'success' : 'danger' }}">
                                        {{ $subcategoria->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        @can('editar subcategorias')
                                        <a href="{{ route('subcategorias.edit', $subcategoria) }}" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('eliminar subcategorias')
                                        <form action="{{ route('subcategorias.destroy', $subcategoria) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('¿Está seguro de eliminar esta subcategoría?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay subcategorías registradas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $subcategorias->links() }}
            </div>
        </div>
    </div>
</div>
@endsection