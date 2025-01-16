@extends('layouts.app')

@section('title', 'Reporte de Turno')

@push('css')
<style media="print">
    @page {
        size: auto;
        margin: 5mm;
    }
    .no-print {
        display: none !important;
    }
    .print-only {
        display: block !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header no-print">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Reporte de Turno</h3>
                <div>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                    <a href="{{ route('turnos.show', $turno) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Encabezado del Reporte -->
            <div class="text-center mb-4">
                <h4>Reporte de Cierre de Turno</h4>
                <h5>{{ $turno->sucursal->nombre }} - {{ $turno->caja->nombre }}</h5>
            </div>

            <!-- Información General -->
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Turno #</th>
                            <td>{{ $turno->id }}</td>
                        </tr>
                        <tr>
                            <th>Cajero</th>
                            <td>{{ $turno->usuario->name }}</td>
                        </tr>
                        <tr>
                            <th>Apertura</th>
                            <td>
                                {{ $turno->fecha_apertura->format('d/m/Y H:i:s') }}
                                <br>
                                <small>Monto: ${{ number_format($turno->monto_apertura, 2) }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Cierre</th>
                            <td>
                                {{ $turno->fecha_cierre->format('d/m/Y H:i:s') }}
                                <br>
                                <small>Monto: ${{ number_format($turno->monto_cierre, 2) }}</small>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Monto Sistema</th>
                            <td>${{ number_format($turno->monto_sistema, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Monto en Caja</th>
                            <td>${{ number_format($turno->monto_cierre, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Diferencia</th>
                            <td>
                                <span class="text-{{ $turno->diferencia >= 0 ? 'success' : 'danger' }}">
                                    ${{ number_format($turno->diferencia, 2) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Totales por Forma de Pago -->
            <h5 class="mt-4">Totales por Forma de Pago</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Forma de Pago</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($totalesPorFormaPago as $total)
                        <tr>
                            <td>{{ $total['forma_pago'] }}</td>
                            <td class="text-right">${{ number_format($total['total'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Detalle de Ventas -->
            <h5 class="mt-4">Detalle de Ventas</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Documento</th>
                            <th>Cliente</th>
                            <th class="text-right">Subtotal</th>
                            <th class="text-right">Descuento</th>
                            <th class="text-right">Impuesto</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($turno->ventas as $venta)
                        <tr>
                            <td>{{ $venta->fecha->format('H:i:s') }}</td>
                            <td>
                                {{ $venta->tipo_documento }}
                                <br>
                                <small>{{ $venta->numero_completo }}</small>
                            </td>
                            <td>{{ $venta->cliente->nombre }}</td>
                            <td class="text-right">${{ number_format($venta->subtotal, 2) }}</td>
                            <td class="text-right">${{ number_format($venta->descuento, 2) }}</td>
                            <td class="text-right">${{ number_format($venta->impuesto, 2) }}</td>
                            <td class="text-right">${{ number_format($venta->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay ventas registradas</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Totales:</th>
                            <th class="text-right">${{ number_format($turno->ventas->sum('subtotal'), 2) }}</th>
                            <th class="text-right">${{ number_format($turno->ventas->sum('descuento'), 2) }}</th>
                            <th class="text-right">${{ number_format($turno->ventas->sum('impuesto'), 2) }}</th>
                            <th class="text-right">${{ number_format($turno->ventas->sum('total'), 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Observaciones -->
            @if($turno->observaciones_apertura || $turno->observaciones_cierre)
            <div class="mt-4">
                <h5>Observaciones</h5>
                @if($turno->observaciones_apertura)
                <p>
                    <strong>Apertura:</strong>
                    {{ $turno->observaciones_apertura }}
                </p>
                @endif
                @if($turno->observaciones_cierre)
                <p>
                    <strong>Cierre:</strong>
                    {{ $turno->observaciones_cierre }}
                </p>
                @endif
            </div>
            @endif

            <!-- Firma -->
            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="border-top pt-2 text-center">
                        <p>_______________________</p>
                        <p>Firma del Cajero</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border-top pt-2 text-center">
                        <p>_______________________</p>
                        <p>Firma del Supervisor</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

