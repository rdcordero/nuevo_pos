<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaActivaController extends Controller
{
    public function cambiar(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id'
        ]);

        $empresa = Empresa::findOrFail($request->empresa_id);
        $user = auth()->user();

        if (!$user->tieneAccesoAEmpresa($empresa->id)) {
            return redirect()->back()->with('error', 'No tienes acceso a esta empresa.');
        }

        // Guardar la empresa activa en la sesión
        session(['empresa_activa' => $empresa->id]);

        // Si se marca como predeterminada, actualizar el usuario
        if ($request->has('establecer_default')) {
            $user->update(['empresa_default_id' => $empresa->id]);
            $mensaje = 'Empresa cambiada y establecida como predeterminada.';
        } else {
            $mensaje = 'Empresa cambiada exitosamente.';
        }

        return redirect()->back()->with('success', $mensaje);
    }

    public function establecerDefault(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|exists:empresas,id'
        ]);

        $empresa = Empresa::findOrFail($request->empresa_id);
        $user = auth()->user();

        if (!$user->tieneAccesoAEmpresa($empresa->id)) {
            return redirect()->back()->with('error', 'No tienes acceso a esta empresa.');
        }

        $user->update(['empresa_default_id' => $empresa->id]);

        return redirect()->back()->with('success', 'Empresa predeterminada actualizada.');
    }

    public function quitarDefault()
    {
        auth()->user()->update(['empresa_default_id' => null]);
        return redirect()->back()->with('success', 'Se ha eliminado la empresa predeterminada.');
    }
}