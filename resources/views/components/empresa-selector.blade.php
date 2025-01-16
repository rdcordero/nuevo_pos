@php
$empresasUsuario = auth()->user()->empresasActivas;
$empresaActiva = \App\Models\Empresa::find(session('empresa_activa'));
$empresaDefault = auth()->user()->empresaDefault;
@endphp

<div class="dropdown">
    <button class="btn btn-light dropdown-toggle" type="button" id="empresaDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        @if($empresaActiva)
            <i class="fas fa-building"></i> 
            {{ $empresaActiva->nombre }}
            @if($empresaActiva->id === optional($empresaDefault)->id)
                <i class="fas fa-star text-warning" title="Empresa predeterminada"></i>
            @endif
        @else
            Seleccionar Empresa
        @endif
    </button>
    <ul class="dropdown-menu" aria-labelledby="empresaDropdown">
        @foreach($empresasUsuario as $empresa)
            <li>
                <div class="dropdown-item d-flex align-items-center justify-content-between">
                    <form action="{{ route('empresa.cambiar') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="empresa_id" value="{{ $empresa->id }}">
                        <button type="submit" class="btn btn-link p-0 text-decoration-none {{ $empresa->id === $empresaActiva?->id ? 'fw-bold' : '' }}">
                            {{ $empresa->nombre }}
                        </button>
                    </form>
                    
                    @if($empresa->id === optional($empresaDefault)->id)
                        <form action="{{ route('empresa.quitar-default') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link p-0 text-warning" title="Quitar predeterminada">
                                <i class="fas fa-star"></i>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('empresa.establecer-default') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="empresa_id" value="{{ $empresa->id }}">
                            <button type="submit" class="btn btn-link p-0 text-muted" title="Establecer como predeterminada">
                                <i class="far fa-star"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </li>
        @endforeach
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
