<?php

namespace App\Http\Controllers;

use App\Models\AjusteInventario;
use App\Models\Bodega;
use App\Models\Producto;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjusteInventarioController extends Controller
{
    public function index()
    {
        $empresaId = session('empresa_activa');
        $sucursalId = session('sucursal_activa');

        $ajustes = AjusteInventario::with(['usuario'])
            ->where('empresa_id', $empresaId)
            ->where('sucursal_id', $sucursalId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('inventario.ajustes.index', compact('ajustes'));
    }

    public function create()
    {
        $empresaId = session('empresa_activa');
        $sucursalId = session('sucursal_activa');

        $productos = Producto::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $sucursales = Sucursal::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->get();

        $bodegas = Bodega::whereHas('sucursal', function ($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })->where('activo', true)->get();

        return view('inventario.ajustes.create', compact('productos', 'bodegas', 'sucursales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:entrada,salida',
            'fecha' => 'required|date',
            'motivo' => 'required|string',
            'sucursal_id' => 'required|exists:sucursales,id',
            'bodega_id' => 'required|exists:bodegas,id',
            'observacion' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.costo_unitario' => 'required|numeric|min:0'
        ]);
    
        try {
            DB::beginTransaction();
    
            $empresaId = session('empresa_activa');
    
            // Crear el ajuste
            $ajuste = AjusteInventario::create([
                'empresa_id' => $empresaId,
                'sucursal_id' => $validated['sucursal_id'],
                'bodega_id' => $validated['bodega_id'],
                'numero_documento' => 'AJ-' . date('YmdHis'),
                'tipo' => $validated['tipo'],
                'fecha' => $validated['fecha'],
                'motivo' => $validated['motivo'],
                'observacion' => $validated['observacion'],
                'usuario_id' => auth()->id()
            ]);
    
            // Crear los detalles y registrar los movimientos
            foreach ($request->productos as $producto) {
                // Crear detalle
                $ajuste->detalles()->create([
                    'producto_id' => $producto['id'],
                    'cantidad' => $producto['cantidad'],
                    'costo_unitario' => $producto['costo_unitario']
                ]);
    
                // Registrar movimiento de inventario
                InventarioMovimientoController::registrarMovimiento([
                    'producto_id' => $producto['id'],
                    'empresa_id' => $empresaId,
                    'sucursal_id' => $validated['sucursal_id'],
                    'bodega_id' => $validated['bodega_id'],
                    'tipo_movimiento' => $validated['tipo'],
                    'origen_movimiento' => 'ajuste',
                    'documento_id' => $ajuste->id,
                    'tipo_documento' => 'ajuste_inventario',
                    'cantidad' => $producto['cantidad'],
                    'costo_unitario' => $producto['costo_unitario'],
                    'numero_documento' => $ajuste->numero_documento,
                    'observacion' => $validated['motivo'],
                    'usuario_id' => auth()->id()
                ]);
            }
    
            DB::commit();
            return redirect()->route('inventario.ajustes.index')
                ->with('success', 'Ajuste de inventario registrado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el ajuste: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    
    public function show(AjusteInventario $ajuste)
    {
        $ajuste->load(['detalles.producto', 'usuario', 'empresa', 'sucursal']);
        return view('inventario.ajustes.show', compact('ajuste'));
    }
}
