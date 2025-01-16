<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Sucursal;
use App\Http\Requests\CajaRequest;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function index()
    {
        $empresaId = session('empresa_activa');
        $sucursalId = session('sucursal_activa');

        $cajas = Caja::with(['sucursal'])
            ->empresa($empresaId)
            ->sucursal($sucursalId)
            ->orderBy('nombre')
            ->paginate(10);

        return view('cajas.index', compact('cajas'));
    }

    public function create()
    {
        $empresaId = session('empresa_activa');
        $sucursales = Sucursal::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('cajas.create', compact('sucursales'));
    }

    public function store(CajaRequest $request)
    {
        try {
            $empresaId = session('empresa_activa');
            $validated = $request->validated();
            $validated['empresa_id'] = $empresaId;

            Caja::create($validated);

            return redirect()->route('cajas.index')
                ->with('success', 'Caja creada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear la caja: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Caja $caja)
    {
        return view('cajas.show', compact('caja'));
    }

    public function edit(Caja $caja)
    {
        $empresaId = session('empresa_activa');
        $sucursales = Sucursal::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('cajas.edit', compact('caja', 'sucursales'));
    }

    public function update(CajaRequest $request, Caja $caja)
    {
        try {
            $caja->update($request->validated());

            return redirect()->route('cajas.index')
                ->with('success', 'Caja actualizada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar la caja: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Caja $caja)
    {
        try {
            $caja->delete();
            return redirect()->route('cajas.index')
                ->with('success', 'Caja eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la caja: ' . $e->getMessage());
        }
    }
}

