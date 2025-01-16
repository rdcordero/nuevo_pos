@extends('layouts.app')

@section('title', 'Bodegas')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Bodegas</h3>
                <a href="{{ route('bodegas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Bodega
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Sucursal</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bodegas as $bodega)
                        <tr>
                            <td>{{ $bodega->id }}</td>
                            <td>{{ $bodega->nombre }}</td>
                            <td>{{ $bodega->sucursal->nombre }}</td>
                            <td>{{ $bodega->descripcion }}</td>
                            <td>
                                <span class="badge bg-{{ $bodega->activo ? 'success' : 'danger' }}">
                                    {{ $bodega->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('bodegas.edit', $bodega) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('bodegas.destroy', $bodega) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('¿Está seguro de eliminar esta bodega?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $bodegas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

