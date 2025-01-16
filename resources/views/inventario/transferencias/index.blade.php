@extends('layouts.app')

@section('title', 'Transferencias entre Bodegas')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Transferencias entre Bodegas</h3>
                <a href="{{ route('inventario.transferencias.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Transferencia
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
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Estado</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transferencias as $transferencia)
                        <tr>
                            <td>{{ $transferencia->fecha->format('d/m/Y') }}</td>
                            <td>{{ $transferencia->numero_documento }}</td>
                            <td>{{ $transferencia->bodegaOrigen->nombre }}</td>
                            <td>{{ $transferencia->bodegaDestino->nombre }}</td>
                            <td>
                                <span class="badge bg-{{ $transferencia->estado === 'completada' ? 'success' : ($transferencia->estado === 'pendiente' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($transferencia->estado) }}
                                </span>
                            </td>
                            <td>{{ $transferencia->usuario->name }}</td>
                            <td>
                                <a href="{{ route('inventario.transferencias.show', $transferencia) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $transferencias->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

