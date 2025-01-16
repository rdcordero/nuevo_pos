@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Clientes</h3>
                <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Cliente
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Identificación</th>
                            <th>Contacto</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientes as $cliente)
                        <tr>
                            <td>
                                {{ $cliente->nombre }}
                                @if($cliente->nombre_comercial)
                                    <br>
                                    <small class="text-muted">{{ $cliente->nombre_comercial }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $cliente->tipo_cliente === 'contribuyente' ? 'info' : 'secondary' }}">
                                    {{ $cliente->tipo_cliente === 'contribuyente' ? 'Contribuyente' : 'No Contribuyente' }}
                                </span>
                                @if($cliente->gran_contribuyente)
                                    <br>
                                    <span class="badge bg-primary">Gran Contribuyente</span>
                                @endif
                            </td>
                            <td>
                                @if($cliente->nrc)
                                    NRC: {{ $cliente->nrc }}<br>
                                @endif
                                @if($cliente->nit)
                                    NIT: {{ $cliente->nit }}<br>
                                @endif
                                @if($cliente->dui)
                                    DUI: {{ $cliente->dui }}
                                @endif
                            </td>
                            <td>
                                @if($cliente->telefono)
                                    <i class="fas fa-phone"></i> {{ $cliente->telefono }}<br>
                                @endif
                                @if($cliente->celular)
                                    <i class="fas fa-mobile-alt"></i> {{ $cliente->celular }}<br>
                                @endif
                                @if($cliente->email)
                                    <i class="fas fa-envelope"></i> {{ $cliente->email }}
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ 
                                    $cliente->categoria === 'vip' ? 'success' : 
                                    ($cliente->categoria === 'frecuente' ? 'info' : 'secondary') 
                                }}">
                                    {{ ucfirst($cliente->categoria) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $cliente->activo ? 'success' : 'danger' }}">
                                    {{ $cliente->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('clientes.show', $cliente) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('clientes.edit', $cliente) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('clientes.destroy', $cliente) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('¿Está seguro de eliminar este cliente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                title="Eliminar"
                                                {{ $cliente->ventas()->exists() ? 'disabled' : '' }}>
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
                {{ $clientes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

