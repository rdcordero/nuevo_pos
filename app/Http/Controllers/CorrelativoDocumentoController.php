<?php

namespace App\Http\Controllers;

use App\Models\CorrelativoDocumento;
use App\Models\TipoDocumentoVenta;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class CorrelativoDocumentoController extends Controller
{
    public function index()
    {
        $empresaId = session('empresa_activa');

        $correlativos = CorrelativoDocumento::where('empresa_id', $empresaId)
            ->with(['sucursal', 'tipoDocumento'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('correlativos.index', compact('correlativos'));
    }

    public function create()
    {
        $empresaId = session('empresa_activa');

        $sucursales = Sucursal::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->get();

        $tiposDocumento = TipoDocumentoVenta::where('activo', true)->get();

        return view('correlativos.create', compact('sucursales', 'tiposDocumento'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sucursal_id' => 'required|exists:sucursales,id',
            'tipo_documento_id' => 'required|exists:tipos_documento_venta,id',
            'serie' => 'nullable|string|max:20',
            'correlativo_inicial' => 'required|integer|min:1',
            'correlativo_final' => 'required|integer|gt:correlativo_inicial',
            'fecha_inicio' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_inicio',
            'activo' => 'boolean'
        ]);

        $empresaId = session('empresa_activa');

        // Verificar si ya existe un correlativo activo para esta sucursal y tipo de documento
        $existeActivo = CorrelativoDocumento::where('empresa_id', $empresaId)
            ->where('sucursal_id', $validated['sucursal_id'])
            ->where('tipo_documento_id', $validated['tipo_documento_id'])
            ->where('activo', true)
            ->exists();

        if ($existeActivo && $validated['activo']) {
            return back()->with('error', 'Ya existe un correlativo activo para esta sucursal y tipo de documento.')
                ->withInput();
        }

        $correlativo = new CorrelativoDocumento($validated);
        $correlativo->empresa_id = $empresaId;
        $correlativo->correlativo_actual = $validated['correlativo_inicial'] - 1;
        $correlativo->save();

        return redirect()->route('correlativos.index')
            ->with('success', 'Correlativo creado exitosamente.');
    }

    public function edit(CorrelativoDocumento $correlativo)
    {
        $empresaId = session('empresa_activa');

        $sucursales = Sucursal::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->get();

        $tiposDocumento = TipoDocumentoVenta::where('activo', true)->get();

        return view('correlativos.edit', compact('correlativo', 'sucursales', 'tiposDocumento'));
    }

    public function update(Request $request, CorrelativoDocumento $correlativo)
    {
        $validated = $request->validate([
            'serie' => 'nullable|string|max:20',
            'fecha_vencimiento' => 'required|date|after:fecha_inicio',
            'activo' => 'boolean'
        ]);

        // Verificar si ya existe otro correlativo con la misma serie
        if ($validated['serie'] !== null) {
            $existeOtro = CorrelativoDocumento::where('empresa_id', $correlativo->empresa_id)
                ->where('sucursal_id', $correlativo->sucursal_id)
                ->where('tipo_documento_id', $correlativo->tipo_documento_id)
                ->where('serie', $validated['serie'])
                ->where('id', '!=', $correlativo->id)
                ->exists();

            if ($existeOtro) {
                return back()->with('error', 'Ya existe un correlativo con esta serie para esta sucursal y tipo de documento.')
                    ->withInput();
            }
        }

        if (!$validated['activo'] && $correlativo->activo) {
            // Si se está desactivando, verificar que no haya otro correlativo activo
            $existeOtroActivo = CorrelativoDocumento::where('empresa_id', $correlativo->empresa_id)
                ->where('sucursal_id', $correlativo->sucursal_id)
                ->where('tipo_documento_id', $correlativo->tipo_documento_id)
                ->where('id', '!=', $correlativo->id)
                ->where('activo', true)
                ->exists();

            if (!$existeOtroActivo) {
                return back()->with('error', 'No se puede desactivar el único correlativo activo.')
                    ->withInput();
            }
        }

        $correlativo->update($validated);

        return redirect()->route('correlativos.index')
            ->with('success', 'Correlativo actualizado exitosamente.');
    }

    public function destroy(CorrelativoDocumento $correlativo)
    {
        try {
            // Verificar si hay documentos emitidos con este correlativo
            $existenDocumentos = Venta::where('sucursal_id', $correlativo->sucursal_id)
                ->where('tipo_documento_id', $correlativo->tipo_documento_id)
                ->when($correlativo->serie, function ($query) use ($correlativo) {
                    return $query->where('numero_documento', 'like', $correlativo->serie . '%');
                })
                ->exists();

            if ($existenDocumentos) {
                return back()->with('error', 'No se puede eliminar el correlativo porque ya existen documentos emitidos.');
            }

            // Verificar que no sea el único correlativo activo
            if ($correlativo->activo) {
                $existeOtroActivo = CorrelativoDocumento::where('empresa_id', $correlativo->empresa_id)
                    ->where('sucursal_id', $correlativo->sucursal_id)
                    ->where('tipo_documento_id', $correlativo->tipo_documento_id)
                    ->where('id', '!=', $correlativo->id)
                    ->where('activo', true)
                    ->exists();

                if (!$existeOtroActivo) {
                    return back()->with('error', 'No se puede eliminar el único correlativo activo.');
                }
            }

            $correlativo->delete();
            return redirect()->route('correlativos.index')
                ->with('success', 'Correlativo eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el correlativo: ' . $e->getMessage());
        }
    }
}
