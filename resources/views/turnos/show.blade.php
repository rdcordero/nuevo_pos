@extends('layouts.app')

@section('title', 'Detalle de Turno')

@section('content')
    <div class="container-fluid">
        @if ($turno->estado === 'cerrado')
            <div class="alert alert-warning">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>
                        <strong>¡Atención!</strong> Este turno está cerrado.
                        <br>
                        Para realizar nuevas ventas debe abrir un nuevo turno.
                    </div>
                </div>
            </div>
        @endif
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Detalle de Turno</h3>
                    <div>
                        @if ($turno->estado === 'abierto')
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalCerrarTurno">
                                <i class="fas fa-lock"></i> Cerrar Turno
                            </button>
                            <a href="{{ route('turnos.edit', $turno) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @endif
                        @if ($turno->estado === 'cerrado')
                            <a href="{{ route('turnos.reporte', $turno) }}" class="btn btn-info">
                                <i class="fas fa-file-alt"></i> Ver Reporte
                            </a>
                        @endif

                        <a href="{{ route('turnos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Información General</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Sucursal</th>
                                <td>{{ $turno->sucursal->nombre }}</td>
                            </tr>
                            <tr>
                                <th>Caja</th>
                                <td>{{ $turno->caja->nombre }}</td>
                            </tr>
                            <tr>
                                <th>Usuario</th>
                                <td>{{ $turno->usuario->name }}</td>
                            </tr>
                            <tr>
                                <th>Estado</th>
                                <td>
                                    <span class="badge bg-{{ $turno->estado === 'abierto' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($turno->estado) }}
                                    </span>
                                </td>
                            </tr>
                        </table>

                        <h5 class="mt-4">Apertura</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Fecha y Hora</th>
                                <td>{{ $turno->fecha_apertura->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Monto</th>
                                <td>${{ number_format($turno->monto_apertura, 2) }}</td>
                            </tr>
                            @if ($turno->observaciones_apertura)
                                <tr>
                                    <th>Observaciones</th>
                                    <td>{{ $turno->observaciones_apertura }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>

                    @if ($turno->estado === 'cerrado')
                        <div class="col-md-6">
                            <h5>Cierre</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Fecha y Hora</th>
                                    <td>{{ $turno->fecha_cierre->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Monto en Sistema</th>
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
                                @if ($turno->observaciones_cierre)
                                    <tr>
                                        <th>Observaciones</th>
                                        <td>{{ $turno->observaciones_cierre }}</td>
                                    </tr>
                                @endif
                                @if ($turno->observaciones_cierre)
                                    <tr>
                                        <th>Observaciones</th>
                                        <td>{{ $turno->observaciones_cierre }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cerrar Turno -->
    <div class="modal fade" id="modalCerrarTurno" tabindex="-1" aria-labelledby="modalCerrarTurnoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('turnos.cerrar', $turno) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCerrarTurnoLabel">Cerrar Turno</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="monto_sistema" class="form-label">Monto en Sistema *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('monto_sistema') is-invalid @enderror"
                                    id="monto_sistema" name="monto_sistema" value="{{ old('monto_sistema') }}"
                                    step="0.01" min="0" required>
                                @error('monto_sistema')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="monto_cierre" class="form-label">Monto en Caja *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('monto_cierre') is-invalid @enderror"
                                    id="monto_cierre" name="monto_cierre" value="{{ old('monto_cierre') }}" step="0.01"
                                    min="0" required>
                                @error('monto_cierre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="observaciones_cierre" class="form-label">Observaciones</label>
                            <textarea class="form-control @error('observaciones_cierre') is-invalid @enderror" id="observaciones_cierre"
                                name="observaciones_cierre" rows="3">{{ old('observaciones_cierre') }}</textarea>
                            @error('observaciones_cierre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-lock"></i> Cerrar Turno
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const montoSistemaInput = document.getElementById('monto_sistema');
            const montoCierreInput = document.getElementById('monto_cierre');

            function calcularDiferencia() {
                const montoSistema = parseFloat(montoSistemaInput.value) || 0;
                const montoCierre = parseFloat(montoCierreInput.value) || 0;
                const diferencia = montoCierre - montoSistema;

                // Actualizar el color del input según la diferencia
                if (diferencia < 0) {
                    montoCierreInput.classList.add('is-invalid');
                    montoCierreInput.classList.remove('is-valid');
                } else {
                    montoCierreInput.classList.add('is-valid');
                    montoCierreInput.classList.remove('is-invalid');
                }
            }

            montoSistemaInput.addEventListener('input', calcularDiferencia);
            montoCierreInput.addEventListener('input', calcularDiferencia);
        });
    </script>
@endpush
