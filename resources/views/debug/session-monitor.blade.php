@extends('layouts.app')

@section('title', 'Monitor de Sesión')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Monitor de Sesión</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Estado Actual de Sesión</h5>
                        </div>
                        <div class="card-body" id="session-status">
                            Cargando...
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Logs Recientes</h5>
                        </div>
                        <div class="card-body">
                            <pre id="session-logs" style="max-height: 400px; overflow-y: auto;">
                                @php
                                    $logFile = storage_path('logs/session-debug.log');
                                    if (file_exists($logFile)) {
                                        $logs = array_slice(file($logFile), -50);
                                        echo implode('', $logs);
                                    } else {
                                        echo "No hay logs disponibles";
                                    }
                                @endphp
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateSessionStatus() {
    fetch('/debug/session')
        .then(response => response.json())
        .then(data => {
            const statusHtml = `
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>Session ID</th>
                            <td>${data.session_id}</td>
                        </tr>
                        <tr>
                            <th>Empresa Activa</th>
                            <td>${data.session_data.empresa_activa || 'No establecida'}</td>
                        </tr>
                        <tr>
                            <th>Sucursal Activa</th>
                            <td>${data.session_data.sucursal_activa || 'No establecida'}</td>
                        </tr>
                        <tr>
                            <th>Usuario ID</th>
                            <td>${data.user_data.id}</td>
                        </tr>
                        <tr>
                            <th>Empresa Default</th>
                            <td>${data.user_data.empresa_default}</td>
                        </tr>
                        <tr>
                            <th>Sucursal Default</th>
                            <td>${data.user_data.sucursal_default}</td>
                        </tr>
                    </table>
                </div>
                <div class="mt-3">
                    <button onclick="location.reload()" class="btn btn-primary">
                        <i class="fas fa-sync"></i> Actualizar
                    </button>
                </div>
            `;
            document.getElementById('session-status').innerHTML = statusHtml;
        })
        .catch(error => {
            document.getElementById('session-status').innerHTML = `
                <div class="alert alert-danger">
                    Error al cargar el estado de la sesión: ${error.message}
                </div>
            `;
        });
}

// Actualizar cada 30 segundos
updateSessionStatus();
setInterval(updateSessionStatus, 30000);

// Función para recargar los logs
function reloadLogs() {
    fetch('/debug/session-logs')
        .then(response => response.text())
        .then(logs => {
            document.getElementById('session-logs').textContent = logs;
        });
}

// Actualizar logs cada minuto
setInterval(reloadLogs, 60000);
</script>
@endpush
@endsection

