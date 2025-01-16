@extends('layouts.app')

@section('title', 'Correlativos de Documentos')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Correlativos de Documentos</h3>
                <a href="{{ route('correlativos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Correlativo
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Sucursal</th>
                            <th>Tipo Documento</th>
                            <th>Serie</th>
                            <th>Correlativo Actual</th>
                            <th>Rango</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($correlativos as $correlativo)
                        <tr>
                            <td>{{ $correlativo->sucursal->nombre }}</td>
                            <td>{{ $correlativo->tipoDocumento->nombre }}</td>
                            <td>{{ $correlativo->serie ?? 'N/A' }}</td>
                            <td>{{ str_pad($correlativo->correlativo_actual, 8, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                {{ str_pad($correlativo->correlativo_inicial, 8, '0', STR_PAD_LEFT) }} - 
                                {{ str_pad($correlativo->correlativo_final, 8, '0', STR_PAD_LEFT) }}
                            </td>
                            <td>{{ $correlativo->fecha_vencimiento->format('d/m/Y') }}</td>
                            <td>
                                @if($correlativo->disponible())
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('correlativos.edit', $correlativo) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('correlativos.destroy', $correlativo) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('¿Está seguro de eliminar este correlativo? Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $correlativos->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

