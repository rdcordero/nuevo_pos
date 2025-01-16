<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalActivaController extends Controller
{
    public function cambiar(Request $request)
    {
        $request->validate([
            'sucursal_id' => 'required|exists:sucursales,id'
        ]);

        $sucursal = Sucursal::findOrFail($request->sucursal_id);
        $user = auth()->user();

        if (!$user->tieneAccesoASucursal($sucursal->id)) {
            return redirect()->back()->with('error', 'No tienes acceso a esta sucursal.');
        }

        // Verificar que la sucursal pertenezca a la empresa activa
        if ($sucursal->empresa_id != session('empresa_activa')) {
            return redirect()->back()->with('error', 'La sucursal debe pertenecer a la empresa activa.');
        }

        // Guardar la sucursal activa en la sesión
        session(['sucursal_activa' => $sucursal->id]);

        // Si se marca como predeterminada, actualizar el usuario
        if ($request->has('establecer_default')) {
            $user->update(['sucursal_default_id' => $sucursal->id]);
            $mensaje = 'Sucursal cambiada y establecida como predeterminada.';
        } else {
            $mensaje = 'Sucursal cambiada exitosamente.';
        }

        return redirect()->back()->with('success', $mensaje);
    }

    public function establecerDefault(Request $request)
    {
        $request->validate([
            'sucursal_id' => 'required|exists:sucursales,id'
        ]);

        $sucursal = Sucursal::findOrFail($request->sucursal_id);
        $user = auth()->user();

        if (!$user->tieneAccesoASucursal($sucursal->id)) {
            return redirect()->back()->with('error', 'No tienes acceso a esta sucursal.');
        }

        $user->update(['sucursal_default_id' => $sucursal->id]);

        return redirect()->back()->with('success', 'Sucursal predeterminada actualizada.');
    }

    public function quitarDefault()
    {
        auth()->user()->update(['sucursal_default_id' => null]);
        return redirect()->back()->with('success', 'Se ha eliminado la sucursal predeterminada.');
    }
}