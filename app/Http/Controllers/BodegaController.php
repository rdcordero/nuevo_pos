<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class BodegaController extends Controller
{
    public function index()
    {
        $empresaId = session('empresa_activa');
        $bodegas = Bodega::whereHas('sucursal', function($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })->with('sucursal')->paginate(10);

        return view('bodegas.index', compact('bodegas'));
    }

    public function create()
    {
        $empresaId = session('empresa_activa');
        $sucursales = Sucursal::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->get();

        return view('bodegas.create', compact('sucursales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'sucursal_id' => 'required|exists:sucursales,id',
            'activo' => 'boolean'
        ]);

        Bodega::create($validated);

        return redirect()->route('bodegas.index')
            ->with('success', 'Bodega creada exitosamente.');
    }

    public function edit(Bodega $bodega)
    {
        $empresaId = session('empresa_activa');
        $sucursales = Sucursal::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->get();

        return view('bodegas.edit', compact('bodega', 'sucursales'));
    }

    public function update(Request $request, Bodega $bodega)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'sucursal_id' => 'required|exists:sucursales,id',
            'activo' => 'boolean'
        ]);

        $bodega->update($validated);

        return redirect()->route('bodegas.index')
            ->with('success', 'Bodega actualizada exitosamente.');
    }

    public function destroy(Bodega $bodega)
    {
        try {
            $bodega->delete();
            return redirect()->route('bodegas.index')
                ->with('success', 'Bodega eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('bodegas.index')
                ->with('error', 'No se puede eliminar la bodega porque tiene registros asociados.');
        }
    }
}

