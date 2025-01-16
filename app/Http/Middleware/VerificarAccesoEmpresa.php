<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Empresa;

class VerificarAccesoEmpresa
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        // Si no hay empresa activa en sesión
        if (!session()->has('empresa_activa')) {
            // Intentar usar la empresa por defecto primero
            if ($user->empresa_default_id && $user->tieneAccesoAEmpresa($user->empresa_default_id)) {
                session(['empresa_activa' => $user->empresa_default_id]);
            } else {
                // Si no hay empresa por defecto o no tiene acceso, usar la primera empresa disponible
                $primeraEmpresa = $user->empresasActivas()->first();
                
                if (!$primeraEmpresa) {
                    abort(403, 'No tienes acceso a ninguna empresa.');
                }
                
                session(['empresa_activa' => $primeraEmpresa->id]);
            }
        }

        // Verificar que la empresa activa sea válida
        if (!$user->tieneAccesoAEmpresa(session('empresa_activa'))) {
            // Si la empresa activa no es válida, intentar con la empresa por defecto
            if ($user->empresa_default_id && $user->tieneAccesoAEmpresa($user->empresa_default_id)) {
                session(['empresa_activa' => $user->empresa_default_id]);
            } else {
                // Si no hay empresa por defecto válida, usar la primera empresa disponible
                $primeraEmpresa = $user->empresasActivas()->first();
                
                if (!$primeraEmpresa) {
                    abort(403, 'No tienes acceso a ninguna empresa.');
                }
                
                session(['empresa_activa' => $primeraEmpresa->id]);
            }
        }

        // Para rutas que manejan una empresa específica
        $empresaId = $request->route('empresa') ? $request->route('empresa')->id : $request->input('empresa_id');
        
        if ($empresaId && !$user->tieneAccesoAEmpresa($empresaId)) {
            abort(403, 'No tienes acceso a esta empresa.');
        }

        // Asegurarse que la empresa activa esté disponible en todas las vistas
        view()->share('empresaActiva', Empresa::find(session('empresa_activa')));

        return $next($request);
    }
}