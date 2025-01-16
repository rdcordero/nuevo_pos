<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pais;
use App\Models\ActividadEconomica;
use App\Http\Requests\ClienteRequest;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $empresaId = session('empresa_activa');
        $clientes = Cliente::where('empresa_id', $empresaId)
            ->with(['pais', 'actividadEconomica'])
            ->orderBy('nombre')
            ->paginate(10);

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        $paises = Pais::orderBy('nombre')->get();
        $actividadesEconomicas = ActividadEconomica::where('codigo_iso', $paises->first()->codigo)
            ->orderBy('codigo_iso')
            ->get();

        return view('clientes.create', compact('paises', 'actividadesEconomicas'));
    }

    public function store(ClienteRequest $request)
    {
        $empresaId = session('empresa_activa');
        $validated = $request->validated();
        $validated['empresa_id'] = $empresaId;

        // Obtener el país por código
        $pais = Pais::where('codigo', $validated['pais_id'])->first();
        if (!$pais) {
            return back()->with('error', 'El país seleccionado no es válido.')
                ->withInput();
        }
        $validated['pais_id'] = $pais->id;

        // Validar actividad económica si es contribuyente
        if ($validated['tipo_cliente'] === 'contribuyente' && !empty($validated['actividad_economica_codigo'])) {
            $actividadEconomica = ActividadEconomica::where('codigo', $validated['actividad_economica_codigo'])
                ->where('codigo_iso', $pais->codigo)
                ->first();
            
            if (!$actividadEconomica) {
                return back()->with('error', 'La actividad económica seleccionada no es válida para el país.')
                    ->withInput();
            }
        }

        try {
            Cliente::create($validated);
            return redirect()->route('clientes.index')
                ->with('success', 'Cliente creado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear el cliente: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['pais', 'actividadEconomica', 'ventas' => function($query) {
            $query->latest()->take(5);
        }]);
        
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        $paises = Pais::orderBy('nombre')->get();
        $actividadesEconomicas = ActividadEconomica::where('codigo_iso', $cliente->pais->codigo)
            ->orderBy('codigo_iso')
            ->get();

        return view('clientes.edit', compact('cliente', 'paises', 'actividadesEconomicas'));
    }

    public function update(ClienteRequest $request, Cliente $cliente)
    {
        $validated = $request->validated();

        // Obtener el país por código
        $pais = Pais::where('codigo', $validated['pais_id'])->first();
        if (!$pais) {
            return back()->with('error', 'El país seleccionado no es válido.')
                ->withInput();
        }
        $validated['pais_id'] = $pais->id;

        // Validar actividad económica si es contribuyente
        if ($validated['tipo_cliente'] === 'contribuyente' && !empty($validated['actividad_economica_codigo'])) {
            $actividadEconomica = ActividadEconomica::where('codigo', $validated['actividad_economica_codigo'])
                ->where('codigo_iso', $pais->codigo)
                ->first();
            
            if (!$actividadEconomica) {
                return back()->with('error', 'La actividad económica seleccionada no es válida para el país.')
                    ->withInput();
            }
        }

        try {
            $cliente->update($validated);
            return redirect()->route('clientes.index')
                ->with('success', 'Cliente actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el cliente: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Cliente $cliente)
    {
        try {
            // Verificar si tiene ventas asociadas
            if ($cliente->ventas()->exists()) {
                return back()->with('error', 'No se puede eliminar el cliente porque tiene ventas asociadas.');
            }
            
            $cliente->delete();
            return redirect()->route('clientes.index')
                ->with('success', 'Cliente eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }

    public function getActividadesEconomicas(Request $request, $paisCodigo)
    {
        $actividadesEconomicas = ActividadEconomica::where('codigo_iso', $paisCodigo)
            ->orderBy('codigo_iso')
            ->get();

        return response()->json($actividadesEconomicas);
    }
}

