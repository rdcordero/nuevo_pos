@extends('layouts.app')

@section('title', 'Detalle de Movimiento')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detalle de Movimiento</h3>
            <div class="card-tools">
                <a href="{{ route('inventario.movimientos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Información del Movimiento</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Fecha:</th>
                            <td>{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Tipo:</th>
                            <td>
                                <span class="badge bg-{{ $movimiento->tipo_movimiento === 'entrada' ? 'success' : 'danger' }}">
                                    {{ ucfirst($movimiento->tipo_movimiento) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Origen:</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $movimiento->origen_movimiento)) }}</td>
                        </tr>
                        <tr>
                            <th>Documento:</th>
                            <td>{{ $movimiento->numero_documento }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Información del Producto</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Producto:</th>
                            <td>{{ $movimiento->producto->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Cantidad:</th>
                            <td>{{ number_format($movimiento->cantidad, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Costo Unitario:</th>
                            <td>${{ number_format($movimiento->costo_unitario, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Total:</th>
                            <td>${{ number_format($movimiento->cantidad * $movimiento->costo_unitario, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h5>Información Adicional</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Empresa:</th>
                            <td>{{ $movimiento->empresa->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Sucursal:</th>
                            <td>{{ $movimiento->sucursal->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Usuario:</th>
                            <td>{{ $movimiento->usuario->name }}</td>
                        </tr>
                        <tr>
                            <th>Observación:</th>
                            <td>{{ $movimiento->observacion ?: 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

