<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Empresa;
use App\Models\Sucursal;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $empresaActiva = null;
                $sucursalActiva = null;
                
                // Manejar empresa activa
                if (session()->has('empresa_activa')) {
                    $empresaActiva = Empresa::find(session('empresa_activa'));
                    
                    // Si la empresa activa no existe o el usuario no tiene acceso
                    if (!$empresaActiva || !auth()->user()->tieneAccesoAEmpresa($empresaActiva->id)) {
                        // Intentar usar la empresa por defecto
                        if (auth()->user()->empresa_default_id && 
                            auth()->user()->tieneAccesoAEmpresa(auth()->user()->empresa_default_id)) {
                            $empresaActiva = Empresa::find(auth()->user()->empresa_default_id);
                            session(['empresa_activa' => $empresaActiva->id]);
                        } else {
                            // Usar la primera empresa disponible
                            $empresaActiva = auth()->user()->empresasActivas()->first();
                            if ($empresaActiva) {
                                session(['empresa_activa' => $empresaActiva->id]);
                            }
                        }
                    }
                } else {
                    // Si no hay empresa activa en sesión, intentar establecer una
                    if (auth()->user()->empresa_default_id && 
                        auth()->user()->tieneAccesoAEmpresa(auth()->user()->empresa_default_id)) {
                        $empresaActiva = Empresa::find(auth()->user()->empresa_default_id);
                        session(['empresa_activa' => $empresaActiva->id]);
                    } else {
                        $empresaActiva = auth()->user()->empresasActivas()->first();
                        //dd($empresaActiva);
                        if ($empresaActiva) {
                            session(['empresa_activa' => $empresaActiva->id]);
                        }
                    }
                }

                // Manejar sucursal activa
                if (session()->has('sucursal_activa')) {
                    $sucursalActiva = Sucursal::find(session('sucursal_activa'));
                    
                    // Verificar que la sucursal pertenezca a la empresa activa y el usuario tenga acceso
                    if (!$sucursalActiva || 
                        !auth()->user()->tieneAccesoASucursal($sucursalActiva->id) ||
                        $sucursalActiva->empresa_id != session('empresa_activa')) {
                        
                        // Intentar usar la sucursal por defecto si pertenece a la empresa activa
                        if (auth()->user()->sucursal_default_id) {
                            $sucursalDefault = Sucursal::find(auth()->user()->sucursal_default_id);
                            if ($sucursalDefault && 
                                $sucursalDefault->empresa_id == session('empresa_activa') &&
                                auth()->user()->tieneAccesoASucursal($sucursalDefault->id)) {
                                $sucursalActiva = $sucursalDefault;
                                session(['sucursal_activa' => $sucursalActiva->id]);
                            }
                        }
                        
                        // Si no hay sucursal por defecto válida, usar la primera sucursal disponible de la empresa activa
                        if (!$sucursalActiva) {
                            $sucursalActiva = auth()->user()->sucursalesDeEmpresa(session('empresa_activa'))->first();
                            if ($sucursalActiva) {
                                session(['sucursal_activa' => $sucursalActiva->id]);
                            }
                        }
                    }
                } else {
                    // Si no hay sucursal activa en sesión, intentar establecer una
                    if (auth()->user()->sucursal_default_id) {
                        $sucursalDefault = Sucursal::find(auth()->user()->sucursal_default_id);
                        if ($sucursalDefault && 
                            $sucursalDefault->empresa_id == session('empresa_activa') &&
                            auth()->user()->tieneAccesoASucursal($sucursalDefault->id)) {
                            $sucursalActiva = $sucursalDefault;
                            session(['sucursal_activa' => $sucursalActiva->id]);
                        }
                    }
                    
                    // Si no hay sucursal por defecto válida, usar la primera sucursal disponible de la empresa activa
                    if (!$sucursalActiva) {
                        $sucursalActiva = auth()->user()->sucursalesDeEmpresa(session('empresa_activa'))->first();
                        if ($sucursalActiva) {
                            session(['sucursal_activa' => $sucursalActiva->id]);
                        }
                    }
                }
                
                $view->with('empresaActiva', $empresaActiva);
                $view->with('sucursalActiva', $sucursalActiva);
            }
        });

        $this->app['session']->extend('database', function ($app) {
            $connection = $app['config']['session.connection'];
            $table = $app['config']['session.table'];
            $lifetime = $app['config']['session.lifetime'];
            $handler = new \Illuminate\Session\DatabaseSessionHandler(
                $app['db']->connection($connection), $table, $lifetime, $app
            );

            // Agregar logging cuando la sesión se regenera o destruye
            $handler->setExists(function ($id) {
                $exists = \DB::table('sessions')->where('id', $id)->exists();
                if (!$exists) {
                    Log::channel('session_debug')->warning('Session not found in database', [
                        'session_id' => $id,
                        'time' => now()->toDateTimeString(),
                        'user_id' => auth()->id() ?? 'guest'
                    ]);
                }
                return $exists;
            });

            return $handler;
        });
    }
}