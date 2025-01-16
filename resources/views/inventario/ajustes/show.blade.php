@extends('layouts.app')

@section('title', 'Detalle de Ajuste de Inventario')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detalle de Ajuste de Inventario</h3>
            <div class="card-tools">
                <a href="{{ route('inventario.ajustes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Información del Ajuste</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Número:</th>
                            <td>{{ $ajuste->numero_documento }}</td>
                        </tr>
                        <tr>
                            <th>Fecha:</th>
                            <td>{{ $ajuste->fecha->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Tipo:</th>
                            <td>
                                <span class="badge bg-{{ $ajuste->tipo === 'entrada' ? 'success' : 'danger' }}">
                                    {{ ucfirst($ajuste->tipo) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Motivo:</th>
                            <td>{{ $ajuste->motivo }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Información Adicional</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Empresa:</th>
                            <td>{{ $ajuste->empresa->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Sucursal:</th>
                            <td>{{ $ajuste->sucursal->nombre }}</t```php file="resources/views/inventario/ajustes/show.blade.php"
th>
                            <td>{{ $ajuste->sucursal->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Usuario:</th>
                            <td>{{ $ajuste->usuario->name }}</td>
                        </tr>
                        <tr>
                            <th>Observación:</th>
                            <td>{{ $ajuste->observacion ?: 'N/A' }}</td>
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
                                @foreach($ajuste->detalles as $detalle)
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
                                    <th>${{ number_format($ajuste->detalles->sum(function($detalle) {
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

