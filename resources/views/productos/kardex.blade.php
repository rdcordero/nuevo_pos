@extends('layouts.app')

@section('title', 'Kardex de Producto')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Kardex: {{ $producto->nombre }}</h3>
                <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Información del Producto -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Información del Producto</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="150">Código:</th>
                            <td>{{ $producto->id }}</td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $producto->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Stock Actual:</th>
                            <td>{{ number_format($producto->stock, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Unidad:</th>
                            <td>{{ $producto->unidad_medida }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <!-- Filtros -->
                    <form method="GET" action="{{ route('productos.kardex', $producto) }}" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fecha_inicio" 
                                       name="fecha_inicio"
                                       value="{{ request('fecha_inicio') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fecha_fin" 
                                       name="fecha_fin"
                                       value="{{ request('fecha_fin') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="tipo_movimiento" class="form-label">Tipo</label>
                                <select class="form-select" id="tipo_movimiento" name="tipo_movimiento">
                                    <option value="">Todos</option>
                                    <option value="entrada" {{ request('tipo_movimiento') === 'entrada' ? 'selected' : '' }}>
                                        Entradas
                                    </option>
                                    <option value="salida" {{ request('tipo_movimiento') === 'salida' ? 'selected' : '' }}>
                                        Salidas
                                    </option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('productos.kardex', $producto) }}" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Movimientos -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Documento</th>
                            <th>Cantidad</th>
                            <th>Costo Unit.</th>
                            <th>Total</th>
                            <th>Saldo</th>
                            <th>Usuario</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movimientosConSaldo as $movimiento)
                        <tr>
                            <td>{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $movimiento->tipo_movimiento === 'entrada' ? 'success' : 'danger' }}">
                                    {{ ucfirst($movimiento->tipo_movimiento) }}
                                </span>
                            </td>
                            <td>
                                {{ $movimiento->numero_documento }}
                                <br>
                                <small class="text-muted">
                                    {{ ucfirst(str_replace('_', ' ', $movimiento->origen_movimiento)) }}
                                </small>
                            </td>
                            <td class="text-end">{{ number_format($movimiento->cantidad, 2) }}</td>
                            <td class="text-end">${{ number_format($movimiento->costo_unitario, 2) }}</td>
                            <td class="text-end">${{ number_format($movimiento->cantidad * $movimiento->costo_unitario, 2) }}</td>
                            <td class="text-end">{{ number_format($movimiento->saldo, 2) }}</td>
                            <td>{{ $movimiento->usuario->name }}</td>
                            <td>{{ $movimiento->observacion }}</td>
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

