<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'POS') - {{ config('app.name') }}</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">{{ config('app.name') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                @auth
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    @can('ver empresas')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('empresas.index') }}">
                            <i class="fas fa-building"></i> Empresas
                        </a>
                    </li>
                    @endcan
                    @can('ver sucursales')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('sucursales.index') }}">
                            <i class="fas fa-store"></i> Sucursales
                        </a>
                    </li>
                    @endcan
                    @can('ver productos')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('productos.index') }}">
                            <i class="fas fa-box"></i> Productos
                        </a>
                    </li>
                    @endcan
                    @can('realizar venta')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('ventas.index') }}">
                            <i class="fas fa-cash-register"></i> Ventas
                        </a>
                    </li>
                    @endcan
                    @can('ver usuarios')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('usuarios.index') }}">
                            <i class="fas fa-users"></i> Usuarios
                        </a>
                    </li>
                    @endcan
                    @can('ver roles')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('roles.index') }}">
                            <i class="fas fa-user-tag"></i> Roles
                        </a>
                    </li>
                    @endcan
                    <li>
                        <a class="dropdown-item" href="{{ route('correlativos.index') }}">
                            <i class="fas fa-list-ol"></i> Correlativos de Documentos
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <!-- Empresa Activa -->


                    <!-- Selector de Empresa -->
                    @include('components.empresa-selector')
                    <!-- Sucursal Activa y Selector -->

                    @include('components.sucursal-selector')

                    <!-- Usuario -->
                    <div class="dropdown">
                        <!-- ... código del dropdown de usuario ... -->
                    </div>
                    <!-- Usuario -->
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    @auth
    @if ($empresaActiva)
    <div class="bg-light border-bottom">
        <div class="container py-2">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0">
                        <i class="fas fa-building text-primary"></i>
                        Trabajando en: <strong>{{ $empresaActiva->nombre }}</strong>
                        @if ($empresaActiva->id === optional(auth()->user()->empresaDefault)->id)
                        <i class="fas fa-star text-warning" title="Empresa predeterminada"></i>
                        @endif
                    </h6>
                </div>
                <div>
                    <small class="text-muted">
                        NIT: {{ $empresaActiva->nit }}
                    </small>
                </div>
                @if ($sucursalActiva)
                <div>
                    <h6 class="mb-0">
                        <i class="fas fa-store text-success"></i>
                        Sucursal: <strong>{{ $sucursalActiva->nombre }}</strong>
                        @if ($sucursalActiva->id === optional(auth()->user()->sucursalDefault)->id)
                        <i class="fas fa-star text-warning" title="Sucursal predeterminada"></i>
                        @endif
                    </h6>
                    <small class="text-muted">{{ $sucursalActiva->direccion }}</small>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
    @endauth

    <main class="py-4">
        @if (session('success'))
        <div class="container">
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        @endif

        @if (session('error'))
        <div class="container">
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>