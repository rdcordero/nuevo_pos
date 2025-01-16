@php
$sucursalesUsuario = auth()->user()->sucursalesDeEmpresa(session('empresa_activa'));
$sucursalActiva = \App\Models\Sucursal::find(session('sucursal_activa'));
$sucursalDefault = auth()->user()->sucursalDefault;
@endphp

<div class="dropdown">
    <button class="btn btn-light dropdown-toggle" type="button" id="sucursalDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        @if($sucursalActiva)
            <i class="fas fa-store"></i> 
            {{ $sucursalActiva->nombre }}
            @if($sucursalActiva->id === optional($sucursalDefault)->id)
                <i class="fas fa-star text-warning" title="Sucursal predeterminada"></i>
            @endif
        @else
            Seleccionar Sucursal
        @endif
    </button>
    <ul class="dropdown-menu" aria-labelledby="sucursalDropdown">
        @forelse($sucursalesUsuario as $sucursal)
            <li>
                <div class="dropdown-item d-flex align-items-center justify-content-between">
                    <form action="{{ route('sucursal.cambiar') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="sucursal_id" value="{{ $sucursal->id }}">
                        <button type="submit" class="btn btn-link p-0 text-decoration-none {{ $sucursal->id === $sucursalActiva?->id ? 'fw-bold' : '' }}">
                            {{ $sucursal->nombre }}
                        </button>
                    </form>
                    
                    @if($sucursal->id === optional($sucursalDefault)->id)
                        <form action="{{ route('sucursal.quitar-default') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link p-0 text-warning" title="Quitar predeterminada">
                                <i class="fas fa-star"></i>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('sucursal.establecer-default') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="sucursal_id" value="{{ $sucursal->id }}">
                            <button type="submit" class="btn btn-link p-0 text-muted" title="Establecer como predeterminada">
                                <i class="far fa-star"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </li>
        @empty
            <li>
                <div class="dropdown-item text-muted">
                    No hay sucursales disponibles
                </div>
            </li>
        @endforelse
    </ul>
</div>

<style>
.dropdown-item {
    cursor: default;
}
.dropdown-item:hover {
    background-color: #f8f9fa;
}
.dropdown-item .btn-link {
    color: inherit;
}
.dropdown-item .btn-link:hover {
    text-decoration: none;
}
</style>