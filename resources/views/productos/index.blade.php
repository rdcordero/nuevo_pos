@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Productos</h1>
            @can('crear productos')
                <a href="{{ route('productos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Producto
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
                                <th>Tipo</th>
                                <th>Categoría</th>
                                <th>Subcategoría</th>
                                <th>Precio Compra</th>
                                <th>Precio Venta</th>
                                <th>Stock Mínimo</th>
                                <th>Stock Máximo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productos as $producto)
                                <tr>
                                    <td>{{ $producto->nombre }}</td>
                                    <td>
                                        @if ($producto->esCompuesto())
                                            <span class="badge bg-info">Compuesto</span>
                                        @elseif($producto->esComponente())
                                            <span class="badge bg-secondary">Usado en otros</span>
                                        @else
                                            <span class="badge bg-primary">Simple</span>
                                        @endif
                                    </td>
                                    <td>{{ $producto->categoria->nombre }}</td>
                                    <td>{{ $producto->subcategoria->nombre }}</td>
                                    <td>${{ number_format($producto->precio_compra, 2) }}</td>
                                    <td>${{ number_format($producto->precio_venta, 2) }}</td>
                                    <td>{{ $producto->stock_minimo }}</td>
                                    <td>{{ $producto->stock_maximo }}</td>
                                    <td>
                                        <span class="badge bg-{{ $producto->activo ? 'success' : 'danger' }}">
                                            {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @can('editar productos')
                                                <a href="{{ route('productos.edit', $producto) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            <a href="{{ route('productos.kardex', $producto) }}"
                                                class="btn btn-sm btn-secondary" title="Kardex">
                                                <i class="fas fa-list-alt"></i>
                                            </a>

                                            @can('eliminar productos')
                                                <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No hay productos registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $productos->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
