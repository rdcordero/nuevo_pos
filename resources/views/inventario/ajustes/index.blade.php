@extends('layouts.app')

@section('title', 'Ajustes de Inventario')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ajustes de Inventario</h3>
            <div class="card-tools">
                <a href="{{ route('inventario.ajustes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Ajuste
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
                            <th>Tipo</th>
                            <th>Motivo</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ajustes as $ajuste)
                        <tr>
                            <td>{{ $ajuste->fecha->format('d/m/Y') }}</td>
                            <td>{{ $ajuste->numero_documento }}</td>
                            <td>
                                <span class="badge bg-{{ $ajuste->tipo === 'entrada' ? 'success' : 'danger' }}">
                                    {{ ucfirst($ajuste->tipo) }}
                                </span>
                            </td>
                            <td>{{ Str::limit($ajuste->motivo, 50) }}</td>
                            <td>{{ $ajuste->usuario->name }}</td>
                            <td>
                                <a href="{{ route('inventario.ajustes.show', $ajuste) }}" 
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
                {{ $ajustes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

