<?php

namespace App\Http\Controllers;

use App\Models\Impuesto;
use Illuminate\Http\Request;

class ImpuestoController extends Controller
{
    public function index()
    {
        $empresaId = session('empresa_activa');
        $impuestos = Impuesto::where('empresa_id', $empresaId)->paginate(10);
        return view('impuestos.index', compact('impuestos'));
    }

    public function create()
    {
        return view('impuestos.create');
    }

    public function store(Request $request)
    {
        $empresaId = session('empresa_activa');
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'porcentaje' => 'required|numeric|min:0|max:100',
            'activo' => 'boolean'
        ]);

        $validated['empresa_id'] = $empresaId;
        $validated['activo'] = $request->has('activo');

        Impuesto::create($validated);

        return redirect()->route('impuestos.index')
            ->with('success', 'Impuesto creado exitosamente.');
    }

    public function edit(Impuesto $impuesto)
    {
        $empresaId = session('empresa_activa');
        
        if ($impuesto->empresa_id !== $empresaId) {
            abort(403, 'No tiene acceso a este impuesto.');
        }

        return view('impuestos.edit', compact('impuesto'));
    }

    public function update(Request $request, Impuesto $impuesto)
    {
        $empresaId = session('empresa_activa');
        
        if ($impuesto->empresa_id !== $empresaId) {
            abort(403, 'No tiene acceso a este impuesto.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'porcentaje' => 'required|numeric|min:0|max:100',
            'activo' => 'boolean'
        ]);

        $validated['activo'] = $request->has('activo');

        $impuesto->update($validated);

        return redirect()->route('impuestos.index')
            ->with('success', 'Impuesto actualizado exitosamente.');
    }

    public function destroy(Impuesto $impuesto)
    {
        $empresaId = session('empresa_activa');
        
        if ($impuesto->empresa_id !== $empresaId) {
            abort(403, 'No tiene acceso a este impuesto.');
        }

        try {
            $impuesto->delete();
            return redirect()->route('impuestos.index')
                ->with('success', 'Impuesto eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar el impuesto porque está en uso.');
        }
    }
}