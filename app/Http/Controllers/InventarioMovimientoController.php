<?php

namespace App\Http\Controllers;

use App\Models\InventarioMovimiento;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioMovimientoController extends Controller
{
    public function index()
    {
        $empresaId = session('empresa_activa');
        $sucursalId = session('sucursal_activa');

        $movimientos = InventarioMovimiento::with(['producto', 'usuario'])
            ->where('empresa_id', $empresaId)
            ->where('sucursal_id', $sucursalId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('inventario.movimientos.index', compact('movimientos'));
    }

    public function show(InventarioMovimiento $movimiento)
    {
        $movimiento->load(['producto', 'usuario', 'empresa', 'sucursal']);
        return view('inventario.movimientos.show', compact('movimiento'));
    }

    public static function registrarMovimiento($data)
    {
        try {
            DB::beginTransaction();

            $movimiento = InventarioMovimiento::create($data);

            // Actualizar el stock del producto
            $producto = Producto::findOrFail($data['producto_id']);
            
            if ($data['tipo_movimiento'] === 'entrada') {
                // Sumar al stock
                $producto->increment('stock', $data['cantidad']);
            } else {
                // Restar al stock
                $producto->decrement('stock', $data['cantidad']);
            }

            DB::commit();
            return $movimiento;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

