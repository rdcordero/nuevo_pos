<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        // Establecer empresa activa
        if ($user->empresa_default_id && $user->tieneAccesoAEmpresa($user->empresa_default_id)) {
            session(['empresa_activa' => $user->empresa_default_id]);
            
            // Si tiene una sucursal por defecto en esta empresa, establecerla
            if ($user->sucursal_default_id) {
                $sucursalDefault = $user->sucursalDefault;
                if ($sucursalDefault && 
                    $sucursalDefault->empresa_id == $user->empresa_default_id &&
                    $user->tieneAccesoASucursal($sucursalDefault->id)) {
                    session(['sucursal_activa' => $sucursalDefault->id]);
                }
            }
        } else {
            // Usar la primera empresa activa
            $primeraEmpresa = $user->empresasActivas()->first();
            if ($primeraEmpresa) {
                session(['empresa_activa' => $primeraEmpresa->id]);
                
                // Buscar una sucursal disponible en esta empresa
                $primeraSucursal = $user->sucursalesDeEmpresa($primeraEmpresa->id)->first();
                if ($primeraSucursal) {
                    session(['sucursal_activa' => $primeraSucursal->id]);
                }
            }
        }
    }
}