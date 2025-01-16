@extends('layouts.app')

@section('title', 'Detalle de Caja')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Detalle de Caja</h3>
                <div>
                    <a href="{{ route('cajas.edit', $caja) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('cajas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Código</th>
                            <td>{{ $caja->codigo }}</td>
                        </tr>
                        <tr>
                            <th>Nombre</th>
                            <td>{{ $caja->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Sucursal</th>
                            <td>{{ $caja->sucursal->nombre }}</td>
                        </tr>
                        <tr>
                            <th>Estado</th>
                            <td>
                                <span class="badge bg-{{ $caja->activo ? 'success' : 'danger' }}">
                                    {{ $caja->activo ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                        </tr>
                        @if($caja->descripcion)
                        <tr>
                            <th>Descripción</th>
                            <td>{{ $caja->descripcion }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

