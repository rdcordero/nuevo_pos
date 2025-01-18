<?php

use App\Http\Controllers\AjusteInventarioController;
use App\Http\Controllers\BodegaController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CorrelativoDocumentoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\EmpresaActivaController;
use App\Http\Controllers\SucursalActivaController;
use App\Http\Controllers\ImpuestoController;
use App\Http\Controllers\InventarioMovimientoController;
use App\Http\Controllers\SubCategoriaController;
use App\Http\Controllers\TransferenciaController;
use App\Http\Controllers\TurnoController;
use App\Models\AjusteInventarioDetalle;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});



Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Rutas para Empresas
    Route::group(['middleware' => ['permission:ver empresas', 'acceso.empresa']], function () {
        Route::resource('empresas', EmpresaController::class);
    });

    // Rutas para Sucursales
    Route::group(['middleware' => ['permission:ver sucursales', 'acceso.empresa']], function () {
        Route::resource('sucursales', SucursalController::class);
    });

    // Inside your existing routes group
    Route::resource('bodegas', BodegaController::class);

    Route::prefix('inventario')->name('inventario.')->group(function () {
        Route::resource('transferencias', TransferenciaController::class);
    });

    // Rutas para Productos
    Route::group(['middleware' => ['permission:ver productos']], function () {
        Route::resource('productos', ProductoController::class);
        Route::get('productos/{producto}/kardex', [ProductoController::class, 'kardex'])->name('productos.kardex');
    });

    // Rutas de Correlativo Documentos

    Route::resource('correlativos', CorrelativoDocumentoController::class);

    // Ruta para obtener subcategorías por producto
    Route::get('/productos/subcategorias/{categoria}', [ProductoController::class, 'getSubcategorias'])
        ->name('productos.subcategorias')
        ->middleware('can:ver productos');

    // Rutas Clientes

    Route::resource('clientes', ClienteController::class);
    Route::get('/api/paises/{pais}/actividades-economicas', [ClienteController::class, 'getActividadesEconomicas'])
        ->name('api.paises.actividades-economicas')
    ;

    //Rutas Cajas
    Route::resource('cajas', CajaController::class);

    // Rutas para Turnos
    Route::resource('turnos', TurnoController::class);
    Route::put('turnos/{turno}/cerrar', [TurnoController::class, 'cerrar'])->name('turnos.cerrar');
    Route::get('turnos/{turno}/reporte', [TurnoController::class, 'reporte'])->name('turnos.reporte');

    // Rutas para Ventas
    Route::group(['middleware' => ['permission:realizar venta']], function () {
        Route::resource('ventas', VentaController::class);
    });

    // Rutas para Usuarios
    Route::group(['middleware' => ['permission:ver usuarios']], function () {
        Route::resource('usuarios', UsuarioController::class);
    });

    // Rutas para Roles
    Route::group(['middleware' => ['permission:ver roles']], function () {
        Route::resource('roles', RolController::class);
    });

    // Rutas para Impuestos
    Route::group(['middleware' => ['permission:ver impuestos']], function () {
        Route::resource('impuestos', ImpuestoController::class);
    });

    // Rutas para Categorias
    Route::group(['middleware' => ['permission:ver categorias']], function () {
        Route::resource('categorias', CategoriaController::class);
    });

    // Rutas para SubCategorias
    Route::group(['middleware' => ['permission:ver subcategorias']], function () {
        Route::resource('subcategorias', SubCategoriaController::class);
    });
    // Ruta para obtener subcategorías por categoría
    Route::get('/subcategorias/por-categoria/{categoria}', [SubCategoriaController::class, 'getByCategoria'])
        ->name('subcategorias.por-categoria')
        ->middleware(['auth', 'verificar.empresa']);


    // Rutas para gestión de empresa activa
    Route::post('/empresa/cambiar', [EmpresaActivaController::class, 'cambiar'])
        ->name('empresa.cambiar');
    Route::post('/empresa/establecer-default', [EmpresaActivaController::class, 'establecerDefault'])
        ->name('empresa.establecer-default');
    Route::post('/empresa/quitar-default', [EmpresaActivaController::class, 'quitarDefault'])
        ->name('empresa.quitar-default');

    // Rutas para gestión de empresa activa
    Route::post('/empresa/cambiar', [EmpresaActivaController::class, 'cambiar'])
        ->name('empresa.cambiar');
    Route::post('/empresa/establecer-default', [EmpresaActivaController::class, 'establecerDefault'])
        ->name('empresa.establecer-default');
    Route::post('/empresa/quitar-default', [EmpresaActivaController::class, 'quitarDefault'])
        ->name('empresa.quitar-default');

    // Rutas para gestión de sucursal activa
    Route::post('/sucursal/cambiar', [SucursalActivaController::class, 'cambiar'])
        ->name('sucursal.cambiar');
    Route::post('/sucursal/establecer-default', [SucursalActivaController::class, 'establecerDefault'])
        ->name('sucursal.establecer-default');
    Route::post('/sucursal/quitar-default', [SucursalActivaController::class, 'quitarDefault'])
        ->name('sucursal.quitar-default');

    // Rutas de Inventario
    Route::prefix('inventario')->name('inventario.')->group(function () {
        // Movimientos de inventario
        Route::get('movimientos', [InventarioMovimientoController::class, 'index'])->name('movimientos.index');
        Route::get('movimientos/{movimiento}', [InventarioMovimientoController::class, 'show'])->name('movimientos.show');

        // Ajustes de inventario
        Route::resource('ajustes', AjusteInventarioController::class);
    });

    Route::get('/debug/session', function () {
        
        
        return response()->json([
            'session_id' => session()->getId(),
            'session_data' => [
                'empresa_activa' => session('empresa_activa'),
                'sucursal_activa' => session('sucursal_activa'),
                'all_session_data' => session()->all()
            ],
            'user_data' => [
                'id' => auth()->id(),
                'empresa_default' => auth()->user()->empresa_default_id,
                'sucursal_default' => auth()->user()->sucursal_default_id
            ],
            'database_session' => DB::table('sessions')
                ->where('id', session()->getId())
                ->first(),
            'request_info' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'headers' => request()->headers->all()
            ]
        ]);
    });
    
    // Vista de diagnóstico
    Route::get('/debug/monitor', function () {
       
        return view('debug.session-monitor');
    });
    
});
