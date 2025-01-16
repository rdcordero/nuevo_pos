@extends('layouts.app')

@section('title', 'Empresas')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Empresas</h1>
        <a href="{{ route('empresas.create') }}" class="btn btn-primary">Nueva Empresa</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>NRC</th>
                        <th>NIT</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empresas as $empresa)
                        <tr>
                            <td>{{ $empresa->nombre }}</td>
                            <td>{{ $empresa->nrc }}</td>
                            <td>{{ $empresa->nit }}</td>
                            <td>{{ $empresa->email }}</td>
                            <td>{{ $empresa->telefono }}</td>
                            <td>
                                <span class="badge bg-{{ $empresa->activo ? 'success' : 'danger' }}">
                                    {{ $empresa->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('empresas.edit', $empresa) }}" 
                                       class="btn btn-sm btn-warning">
                                        Editar
                                    </a>
                                    <form action="{{ route('empresas.destroy', $empresa) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="return confirm('¿Está seguro?')">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $empresas->links() }}
        </div>
    </div>
@endsection