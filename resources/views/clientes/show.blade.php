@extends('layouts.app')

@section('title', 'Detalle de Cliente')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Detalle de Cliente</h3>
                <div>
                    <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Información General</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Nombre</th>
                            <td>{{ $cliente->nombre }}</td>
                        </tr>
                        @if($cliente->nombre_comercial)
                        <tr>
                            <th>Nombre Comercial</th>
                            <td>{{ $cliente->nombre_comercial }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Tipo de Cliente</th>
                            <td>
                                <span class="badge bg-{{ $cliente->tipo_cliente === 'contribuyente' ? 'info' : 'secondary' }}">
                                    {{ $cliente->tipo_cliente === 'contribuyente' ? 'Contribuyente' : 'No Contribuyente' }}
                                </span>
                                @if($cliente->gran_contribuyente)
                                    <span class="badge bg-primary ms-2">Gran Contribuyente</span>
                                @endif
                                @if($cliente->exento)
                                    <span class="badge bg-success ms-2">Exento</span>
                                @endif
                            </td>
                        </tr>
                        @if($cliente->nrc)
                        <tr>
                            <th>NRC</th>
                            <td>{{ $cliente->nrc }}</td>
                        </tr>
                        @endif
                        @if($cliente->nit)
                        <tr>
                            <th>NIT</th>
                            <td>{{ $cliente->nit }}</td>
                        </tr>
                        @endif
                        @if($cliente->dui)
                        <tr>
                            <th>DUI</th>
                            <td>{{ $cliente->dui }}</td>
                        </tr>
                        @endif
                        @if($cliente->giro)
                        <tr>
                            <th>Giro</th>
                            <td>{{ $cliente->giro }}</td>
                        </tr>
                        @endif
                        @if($cliente->actividadEconomica)
                        <tr>
                            <th>Actividad Económica</th>
                            <td>
                                {{ $cliente->actividadEconomica->codigo }} - 
                                {{ $cliente->actividadEconomica->descripcion }}
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Información de Contacto</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Dirección</th>
                            <td>{{ $cliente->direccion_completa }}</td>
                        </tr>
                        @if($cliente->telefono)
                        <tr>
                            <th>Teléfono</th>
                            <td>{{ $cliente->telefono }}</td>
                        </tr>
                        @endif
                        @if($cliente->celular)
                        <tr>
                            <th>Celular</th>
                            <td>{{ $cliente->celular }}</td>
                        </tr>
                        @endif
                        @if($cliente->email)
                        <tr>
                            <th>Email</th>
                            <td>{{ $cliente->email }}</td>
                        </tr>
                        @endif
                        @if($cliente->web)
                        <tr>
                            <th>Sitio Web</th>
                            <td>
                                <a href="{{ $cliente->web }}" target="_blank">{{ $cliente->web }}</a>
                            </td>
                        </tr>
                        @endif
                    </table>

                    <h5 class="mt-4">Información Comercial</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Categoría</th>
                            <td>
                                <span class="badge bg-{{ 
                                    $cliente->categoria === 'vip' ? 'success' : 
                                    ($cliente->categoria === 'frecuente' ? 'info' : 'secondary') 
                                }}">
                                    {{ ucfirst($cliente->categoria) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Límite de Crédito</th>
                            <td>${{ number_format($cliente->limite_credito, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Días de Crédito</th>
                            <td>{{ $cliente->dias_credito }}</td>
                        </tr>
                        @if($cliente->vendedor)
                        <tr>
                            <th>Vendedor</th>
                            <td>{{ $cliente->vendedor }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($cliente->observaciones)
            <div class="row mt-4">
                <div class="col-12">
                    <h5>Observaciones</h5>
                    <p>{{ $cliente->observaciones }}</p>
                </div>
            </div>
            @endif

            <div class="row mt-4">
                <div class="col-12">
                    <h5>Últimas Ventas</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Documento</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cliente->ventas as $venta)
                                <tr>
                                    <td>{{ $venta->fecha->format('d/m/Y') }}</td>
                                    <td>
                                        {{ $venta->tipoDocumento->nombre }}
                                        <br>
                                        <small class="text-muted">{{ $venta->numero_documento }}</small>
                                    </td>
                                    <td>${{ number_format($venta->total, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $venta->estado === 'completada' ? 'success' : 
                                            ($venta->estado === 'pendiente' ? 'warning' : 'danger') 
                                        }}">
                                            {{ ucfirst($venta->estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('ventas.show', $venta) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No hay ventas registradas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

