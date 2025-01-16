<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $empresaId = session('empresa_activa');
        $categorias = Categoria::where('empresa_id', $empresaId)->paginate(10);
        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $empresaId = session('empresa_activa');
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        $validated['empresa_id'] = $empresaId;
        $validated['activo'] = $request->has('activo');

        Categoria::create($validated);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(Categoria $categoria)
    {
        $empresaId = session('empresa_activa');
        
        if ($categoria->empresa_id !== $empresaId) {
            abort(403, 'No tiene acceso a esta categoría.');
        }

        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $empresaId = session('empresa_activa');
        
        if ($categoria->empresa_id !== $empresaId) {
            abort(403, 'No tiene acceso a esta categoría.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        $validated['activo'] = $request->has('activo');

        $categoria->update($validated);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Categoria $categoria)
    {
        $empresaId = session('empresa_activa');
        
        if ($categoria->empresa_id !== $empresaId) {
            abort(403, 'No tiene acceso a esta categoría.');
        }

        try {
            $categoria->delete();
            return redirect()->route('categorias.index')
                ->with('success', 'Categoría eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar la categoría porque tiene productos o subcategorías asociados.');
        }
    }
}