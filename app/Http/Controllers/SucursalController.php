<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\Empresa;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index()
    {
        $sucursales = Sucursal::with('empresa')->paginate(10);
        return view('sucursales.index', compact('sucursales'));
    }

    public function create()
    {
        $empresas = Empresa::where('activo', true)->pluck('nombre', 'id');
        return view('sucursales.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string',
            'telefono' => 'required|string',
            'activo' => 'boolean'
        ]);

        // Asegurarse de que el campo activo sea booleano
        $validated['activo'] = $request->has('activo');

        Sucursal::create($validated);

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal creada exitosamente.');
    }

    public function edit(Sucursal $sucursal)
    {
        $empresas = Empresa::where('activo', true)->pluck('nombre', 'id');
        return view('sucursales.edit', compact('sucursal', 'empresas'));
    }

    public function update(Request $request, Sucursal $sucursal)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string',
            'telefono' => 'required|string',
            'activo' => 'boolean'
        ]);

        // Asegurarse de que el campo activo sea booleano
        $validated['activo'] = $request->has('activo');

        $sucursal->update($validated);

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal actualizada exitosamente.');
    }

    public function destroy(Sucursal $sucursal)
    {
        $sucursal->delete();

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal eliminada exitosamente.');
    }
}