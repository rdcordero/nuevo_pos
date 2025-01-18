<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class SessionDebugMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Capturar estado inicial de la sesión
        $initialState = [
            'empresa_activa' => session('empresa_activa'),
            'sucursal_activa' => session('sucursal_activa'),
            'route' => Route::currentRouteName(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'time' => now()->toDateTimeString()
        ];

        Log::channel('session_debug')->info('Pre-middleware session state', $initialState);

        // Procesar la solicitud
        $response = $next($request);

        // Capturar estado final de la sesión
        $finalState = [
            'empresa_activa' => session('empresa_activa'),
            'sucursal_activa' => session('sucursal_activa'),
            'session_id' => session()->getId(),
            'time' => now()->toDateTimeString()
        ];

        // Detectar cambios en la sesión
        if ($initialState['empresa_activa'] !== $finalState['empresa_activa'] || 
            $initialState['sucursal_activa'] !== $finalState['sucursal_activa']) {
            Log::channel('session_debug')->warning('Session variables changed during request', [
                'initial' => $initialState,
                'final' => $finalState,
                'changes' => [
                    'empresa_activa' => [
                        'from' => $initialState['empresa_activa'],
                        'to' => $finalState['empresa_activa']
                    ],
                    'sucursal_activa' => [
                        'from' => $initialState['sucursal_activa'],
                        'to' => $finalState['sucursal_activa']
                    ]
                ]
            ]);
        }

        // Si las variables se perdieron, registrar información adicional
        if (($initialState['empresa_activa'] && !$finalState['empresa_activa']) || 
            ($initialState['sucursal_activa'] && !$finalState['sucursal_activa'])) {
            Log::channel('session_debug')->error('Session variables lost', [
                'initial' => $initialState,
                'final' => $finalState,
                'headers' => $request->headers->all(),
                'session_data' => session()->all(),
                'cookies' => $request->cookies->all()
            ]);
        }

        return $response;
    }
}

