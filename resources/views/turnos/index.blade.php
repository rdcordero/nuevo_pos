@extends('layouts.app')

@section('title', 'Turnos')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Turnos</h3>
                <a href="{{ route('turnos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Turno
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Sucursal</th>
                            <th>Caja</th>
                            <th>Usuario</th>
                            <th>Estado</th>
                            <th>Montos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($turnos as $turno)
                        <tr>
                            <td>
                                {{ $turno->fecha_apertura->format('d/m/Y H:i') }}
                                @if($turno->fecha_cierre)
                                    <br>
                                    <small class="text-muted">
                                        Cierre: {{ $turno->fecha_cierre->format('d/m/Y H:i') }}
                                    </small>
                                @endif
                            </td>
                            <td>{{ $turno->sucursal->nombre }}</td>
                            <td>{{ $turno->caja->nombre }}</td>
                            <td>{{ $turno->usuario->name }}</td>
                            <td>
                                <span class="badge bg-{{ $turno->estado === 'abierto' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($turno->estado) }}
                                </span>
                            </td>
                            <td>
                                Apertura: ${{ number_format($turno->monto_apertura, 2) }}
                                @if($turno->monto_cierre !== null)
                                    <br>
                                    Cierre: ${{ number_format($turno->monto_cierre, 2) }}
                                    <br>
                                    <span class="text-{{ $turno->diferencia >= 0 ? 'success' : 'danger' }}">
                                        Diferencia: ${{ number_format($turno->diferencia, 2) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('turnos.show', $turno) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($turno->estado === 'abierto')
                                        <a href="{{ route('turnos.edit', $turno) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('turnos.destroy', $turno) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('¿Está seguro de eliminar este turno?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay turnos registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $turnos->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

