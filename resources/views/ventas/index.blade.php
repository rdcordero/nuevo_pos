@extends('layouts.app')

@section('title', 'Ventas')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Ventas</h3>
                <a href="{{ route('ventas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Venta
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Documento</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                        <tr>
                            <td>{{ $venta->fecha->format('d/m/Y') }}</td>
                            <td>
                                {{ $venta->tipo_documento }}<br>
                                <small class="text-muted">{{ $venta->numero_completo }}</small>
                            </td>
                            <td>
                                {{ $venta->cliente->nombre }}
                                @if($venta->cliente->nombre_comercial)
                                    <br>
                                    <small class="text-muted">{{ $venta->cliente->nombre_comercial }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $venta->moneda }} {{ number_format($venta->total, 2) }}
                                @if(!$venta->esta_pagada)
                                    <br>
                                    <small class="text-{{ $venta->esta_vencida ? 'danger' : 'warning' }}">
                                        Pendiente: {{ $venta->moneda }} {{ number_format($venta->saldo, 2) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ 
                                    $venta->estado === 'completada' ? 'success' : 
                                    ($venta->estado === 'pendiente' ? 'warning' : 'danger') 
                                }}">
                                    {{ ucfirst($venta->estado) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('ventas.show', $venta) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($venta->estado === 'pendiente')
                                    <a href="{{ route('ventas.edit', $venta) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    @if($venta->estado !== 'anulada')
                                    <form action="{{ route('ventas.destroy', $venta) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('¿Está seguro de anular esta venta?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                title="Anular">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No hay ventas registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $ventas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

