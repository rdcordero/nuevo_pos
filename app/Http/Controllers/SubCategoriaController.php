<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Subcategoria;
use Illuminate\Http\Request;

class SubCategoriaController extends Controller
{
    public function index()
    {
        $empresaId = session('empresa_activa');
        $subcategorias = Subcategoria::with('categoria')
            ->whereHas('categoria', function ($query) use ($empresaId) {
                $query->where('empresa_id', $empresaId);
            })
            ->paginate(10);

        return view('subcategorias.index', compact('subcategorias'));
    }

    public function create()
    {
        $empresaId = session('empresa_activa');
        $categorias = Categoria::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->get();

        return view('subcategorias.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $empresaId = session('empresa_activa');

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => [
                'required',
                'exists:categorias,id',
                function ($attribute, $value, $fail) use ($empresaId) {
                    $categoria = Categoria::find($value);
                    if ($categoria->empresa_id !== $empresaId) {
                        $fail('La categoría seleccionada no es válida.');
                    }
                }
            ],
            'activo' => 'boolean'
        ]);

        $validated['empresa_id'] = $empresaId;
        $validated['activo'] = $request->has('activo');

        Subcategoria::create($validated);

        return redirect()->route('subcategorias.index')
            ->with('success', 'Subcategoría creada exitosamente.');
    }

    public function edit(Subcategoria $subcategoria)
    {
        $empresaId = session('empresa_activa');

        if ($subcategoria->categoria->empresa_id !== $empresaId) {
            abort(403, 'No tiene acceso a esta subcategoría.');
        }

        $categorias = Categoria::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->get();

        return view('subcategorias.edit', compact('subcategoria', 'categorias'));
    }

    public function update(Request $request, Subcategoria $subcategoria)
    {
        $empresaId = session('empresa_activa');

        if ($subcategoria->categoria->empresa_id !== $empresaId) {
            abort(403, 'No tiene acceso a esta subcategoría.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => [
                'required',
                'exists:categorias,id',
                function ($attribute, $value, $fail) use ($empresaId) {
                    $categoria = Categoria::find($value);
                    if ($categoria->empresa_id !== $empresaId) {
                        $fail('La categoría seleccionada no es válida.');
                    }
                }
            ],
            'activo' => 'boolean'
        ]);

        $validated['activo'] = $request->has('activo');

        $subcategoria->update($validated);

        return redirect()->route('subcategorias.index')
            ->with('success', 'Subcategoría actualizada exitosamente.');
    }

    public function destroy(Subcategoria $subcategoria)
    {
        $empresaId = session('empresa_activa');

        if ($subcategoria->categoria->empresa_id !== $empresaId) {
            abort(403, 'No tiene acceso a esta subcategoría.');
        }

        try {
            $subcategoria->delete();
            return redirect()->route('subcategorias.index')
                ->with('success', 'Subcategoría eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar la subcategoría porque tiene productos asociados.');
        }
    }

    public function getByCategoria(Categoria $categoria)
    {
        $empresaId = session('empresa_activa');

        if ($categoria->empresa_id !== $empresaId) {
            return response()->json(['error' => 'No tiene acceso a esta categoría.'], 403);
        }

        $subcategorias = Subcategoria::where('categoria_id', $categoria->id)
            ->where('activo', true)
            ->get(['id', 'nombre']);

        return response()->json($subcategorias);
    }
}
