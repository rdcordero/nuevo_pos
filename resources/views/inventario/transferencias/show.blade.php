@extends('layouts.app')

@section('title', 'Detalle de Transferencia')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Detalle de Transferencia</h3>
                <a href="{{ route('inventario.transferencias.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Información de la Transferencia</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Número:</th>
                            <td>{{ $transferencia->numero_documento }}</td>
                        </tr>
                        <tr>
                            <th>Fecha:</th>
                            <td>{{ $transferencia->fecha->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                <span class="badge bg-{{ $transferencia->estado === 'completada' ? 'success' : ($transferencia->estado === 'pendiente' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($transferencia->estado) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Motivo:</th>
                            <td>{{ $transferencia->motivo }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Información de Bodegas</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Bodega Origen:</th>
                            <td>{{ $transferencia->bodegaOrigen->nombre }} ({{ $transferencia->bodegaOrigen->sucursal->nombre }})</td>
                        </tr>
                        <tr>
                            <th>Bodega Destino:</th>
                            <td>{{ $transferencia->bodegaDestino->nombre }} ({{ $transferencia->bodegaDestino->sucursal->nombre }})</td>
                        </tr>
                        <tr>
                            <th>Usuario:</th>
                            <td>{{ $transferencia->usuario->name }}</td>
                        </tr>
                        <tr>
                            <th>Observación:</th>
                            <td>{{ $transferencia->observacion ?: 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h5>Detalle de Productos</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Costo Unit.</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transferencia->detalles as $detalle)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $detalle->producto->nombre }}</td>
                                    <td>{{ number_format($detalle->cantidad, 2) }}</td>
                                    <td>${{ number_format($detalle->costo_unitario, 2) }}</td>
                                    <td>${{ number_format($detalle->cantidad * $detalle->costo_unitario, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th>${{ number_format($transferencia->detalles->sum(function($detalle) {
                                        return $detalle->cantidad * $detalle->costo_unitario;
                                    }), 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

