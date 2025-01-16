@extends('layouts.app')

@section('title', 'Cajas')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Cajas</h3>
                <a href="{{ route('cajas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Caja
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Sucursal</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cajas as $caja)
                        <tr>
                            <td>{{ $caja->codigo }}</td>
                            <td>{{ $caja->nombre }}</td>
                            <td>{{ $caja->sucursal->nombre }}</td>
                            <td>
                                <span class="badge bg-{{ $caja->activo ? 'success' : 'danger' }}">
                                    {{ $caja->activo ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('cajas.show', $caja) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('cajas.edit', $caja) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('cajas.destroy', $caja) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('¿Está seguro de eliminar esta caja?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay cajas registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $cajas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

