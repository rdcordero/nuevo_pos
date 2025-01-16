<?php

namespace App\Http\Controllers;

use App\Models\Transferencia;
use App\Models\Bodega;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferenciaController extends Controller
{
    public function index()
    {
        $empresaId = session('empresa_activa');
        $transferencias = Transferencia::where('empresa_id', $empresaId)
            ->with(['bodegaOrigen', 'bodegaDestino', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('inventario.transferencias.index', compact('transferencias'));
    }

    public function create()
    {
        $empresaId = session('empresa_activa');
        $bodegas = Bodega::whereHas('sucursal', function($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })->where('activo', true)->get();
        
        $productos = Producto::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->get();

        return view('inventario.transferencias.create', compact('bodegas', 'productos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bodega_origen_id' => 'required|exists:bodegas,id',
            'bodega_destino_id' => 'required|exists:bodegas,id|different:bodega_origen_id',
            'fecha' => 'required|date',
            'motivo' => 'required|string',
            'observacion' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.costo_unitario' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $empresaId = session('empresa_activa');

            // Crear la transferencia
            $transferencia = Transferencia::create([
                'numero_documento' => 'TR-' . date('YmdHis'),
                'empresa_id' => $empresaId,
                'bodega_origen_id' => $validated['bodega_origen_id'],
                'bodega_destino_id' => $validated['bodega_destino_id'],
                'fecha' => $validated['fecha'],
                'motivo' => $validated['motivo'],
                'observacion' => $validated['observacion'],
                'estado' => 'completada',
                'usuario_id' => auth()->id()
            ]);

            // Crear los detalles y registrar los movimientos
            foreach ($request->productos as $producto) {
                // Crear detalle
                $transferencia->detalles()->create([
                    'producto_id' => $producto['id'],
                    'cantidad' => $producto['cantidad'],
                    'costo_unitario' => $producto['costo_unitario']
                ]);

                // Registrar salida de la bodega origen
                InventarioMovimientoController::registrarMovimiento([
                    'producto_id' => $producto['id'],
                    'empresa_id' => $empresaId,
                    'sucursal_id' => $transferencia->bodegaOrigen->sucursal_id,
                    'bodega_id' => $validated['bodega_origen_id'],
                    'tipo_movimiento' => 'salida',
                    'origen_movimiento' => 'transferencia',
                    'documento_id' => $transferencia->id,
                    'tipo_documento' => 'transferencia',
                    'cantidad' => $producto['cantidad'],
                    'costo_unitario' => $producto['costo_unitario'],
                    'numero_documento' => $transferencia->numero_documento,
                    'observacion' => "Transferencia a " . $transferencia->bodegaDestino->nombre,
                    'usuario_id' => auth()->id()
                ]);

                // Registrar entrada en la bodega destino
                InventarioMovimientoController::registrarMovimiento([
                    'producto_id' => $producto['id'],
                    'empresa_id' => $empresaId,
                    'sucursal_id' => $transferencia->bodegaDestino->sucursal_id,
                    'bodega_id' => $validated['bodega_destino_id'],
                    'tipo_movimiento' => 'entrada',
                    'origen_movimiento' => 'transferencia',
                    'documento_id' => $transferencia->id,
                    'tipo_documento' => 'transferencia',
                    'cantidad' => $producto['cantidad'],
                    'costo_unitario' => $producto['costo_unitario'],
                    'numero_documento' => $transferencia->numero_documento,
                    'observacion' => "Transferencia desde " . $transferencia->bodegaOrigen->nombre,
                    'usuario_id' => auth()->id()
                ]);
            }

            DB::commit();
            return redirect()->route('inventario.transferencias.index')
                ->with('success', 'Transferencia registrada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la transferencia: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Transferencia $transferencia)
    {
        $transferencia->load(['bodegaOrigen', 'bodegaDestino', 'usuario', 'detalles.producto']);
        return view('inventario.transferencias.show', compact('transferencia'));
    }
}

