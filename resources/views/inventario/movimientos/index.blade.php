@extends('layouts.app')

@section('title', 'Movimientos de Inventario')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Movimientos de Inventario</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Origen</th>
                            <th>Documento</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Costo Unit.</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movimientos as $movimiento)
                        <tr>
                            <td>{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $movimiento->tipo_movimiento === 'entrada' ? 'success' : 'danger' }}">
                                    {{ ucfirst($movimiento->tipo_movimiento) }}
                                </span>
                            </td>
                            <td>{{ ucfirst(str_replace('_', ' ', $movimiento->origen_movimiento)) }}</td>
                            <td>{{ $movimiento->numero_documento }}</td>
                            <td>{{ $movimiento->producto->nombre }}</td>
                            <td>{{ number_format($movimiento->cantidad, 2) }}</td>
                            <td>${{ number_format($movimiento->costo_unitario, 2) }}</td>
                            <td>{{ $movimiento->usuario->name }}</td>
                            <td>
                                <a href="{{ route('inventario.movimientos.show', $movimiento) }}" 
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
                {{ $movimientos->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

