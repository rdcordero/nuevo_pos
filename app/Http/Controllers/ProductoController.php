<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Models\Impuesto;
use App\Models\CodigoBarra;
use App\Models\InventarioMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index()
    {
        $empresaId = session('empresa_activa');
        $productos = Producto::with(['categoria', 'subcategoria', 'impuestos', 'codigosBarra'])
            ->where('empresa_id', $empresaId)
            ->paginate(10);

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $empresaId = session('empresa_activa');

        $categorias = Categoria::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->get();

        // Obtener subcategorías activas de la primera categoría
        $subcategorias = [];
        if ($categorias->isNotEmpty()) {
            $subcategorias = Subcategoria::where('categoria_id', $categorias->first()->id)
                ->where('activo', true)
                ->get();
        }

        $impuestos = Impuesto::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->get();

        $productos = Producto::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->get();

        return view('productos.create', compact('categorias', 'subcategorias', 'impuestos', 'productos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'subcategoria_id' => 'required|exists:subcategorias,id',
            'tipo' => 'required|in:simple,compuesto',
            'unidad_medida' => 'required|string|max:10',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'stock_maximo' => 'required|integer|min:0',
            'punto_reorden' => 'required|integer|min:0',
            'ubicacion' => 'nullable|string|max:255',
            'imagen' => 'nullable|image|max:2048',
            'activo' => 'boolean',
            'impuestos' => 'array',
            'impuestos.*' => 'exists:impuestos,id',
            'codigos_barra' => 'array',
            'codigos_barra.*' => 'string|distinct',
            'componentes' => 'required_if:tipo,compuesto|array',
            'componentes.*.id' => 'required_if:tipo,compuesto|exists:productos,id',
            'componentes.*.cantidad' => 'required_if:tipo,compuesto|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            $empresaId = session('empresa_activa');
            $validated['empresa_id'] = $empresaId;

            // Handle image upload
            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->store('public/productos');
                $validated['imagen'] = str_replace('public/', '', $path);
            }

            $producto = Producto::create($validated);

            // Sync impuestos
            if ($request->has('impuestos')) {
                $producto->impuestos()->sync($request->impuestos);
            }

            // Save códigos de barra
            if ($request->has('codigos_barra')) {
                foreach ($request->codigos_barra as $codigo) {
                    if (!empty($codigo)) {
                        $producto->codigosBarra()->create(['codigo' => $codigo]);
                    }
                }
            }

            // Save componentes for productos compuestos
            if ($producto->tipo === 'compuesto' && $request->has('componentes')) {
                $componentes = collect($request->componentes)
                    ->filter(function ($componente) {
                        return !empty($componente['id']);
                    })
                    ->mapWithKeys(function ($componente) {
                        return [$componente['id'] => ['cantidad' => $componente['cantidad']]];
                    });
                
                $producto->componentes()->sync($componentes);
            }

            DB::commit();
            return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear producto: ' . $e->getMessage());
            return back()->with('error', 'Error al crear el producto: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Producto $producto)
    {
        $empresaId = session('empresa_activa');

        if ($producto->empresa_id !== $empresaId) {
            abort(403, 'No tiene acceso a este producto.');
        }

        $categorias = Categoria::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->get();

        // Obtener subcategorías de la categoría del producto
        $subcategorias = Subcategoria::where('categoria_id', $producto->categoria_id)
            ->where('activo', true)
            ->get();

        $impuestos = Impuesto::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->get();

        $productos = Producto::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->where('id', '!=', $producto->id)
            ->get();

        return view('productos.edit', compact('producto', 'categorias', 'subcategorias', 'impuestos', 'productos'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'subcategoria_id' => 'required|exists:subcategorias,id',
            'tipo' => 'required|in:simple,compuesto',
            'unidad_medida' => 'required|string|max:10',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'stock_maximo' => 'required|integer|min:0',
            'punto_reorden' => 'required|integer|min:0',
            'ubicacion' => 'nullable|string|max:255',
            'imagen' => 'nullable|image|max:2048',
            'activo' => 'boolean',
            'impuestos' => 'array',
            'impuestos.*' => 'exists:impuestos,id',
            'codigos_barra' => 'array',
            'codigos_barra.*' => 'string|distinct',
            'componentes' => 'required_if:tipo,compuesto|array',
            'componentes.*.id' => 'required_if:tipo,compuesto|exists:productos,id',
            'componentes.*.cantidad' => 'required_if:tipo,compuesto|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            $empresaId = session('empresa_activa');
            $validated['empresa_id'] = $empresaId;

            // Update imagen if provided
            if ($request->hasFile('imagen')) {
                // Delete old image
                if ($producto->imagen) {
                    Storage::delete('public/' . $producto->imagen);
                }
                $path = $request->file('imagen')->store('public/productos');
                $validated['imagen'] = str_replace('public/', '', $path);
            }

            $producto->update($validated);

            // Sync impuestos
            if ($request->has('impuestos')) {
                $producto->impuestos()->sync($request->impuestos);
            } else {
                $producto->impuestos()->detach();
            }

            // Update códigos de barra
            if ($request->has('codigos_barra')) {
                // Delete existing códigos
                $producto->codigosBarra()->delete();
                
                // Create new códigos
                foreach ($request->codigos_barra as $codigo) {
                    if (!empty($codigo)) {
                        $producto->codigosBarra()->create(['codigo' => $codigo]);
                    }
                }
            }

            // Update componentes for productos compuestos
            if ($producto->tipo === 'compuesto' && $request->has('componentes')) {
                $componentes = collect($request->componentes)
                    ->filter(function ($componente) {
                        return !empty($componente['id']);
                    })
                    ->mapWithKeys(function ($componente) {
                        return [$componente['id'] => ['cantidad' => $componente['cantidad']]];
                    });
                
                $producto->componentes()->sync($componentes);
            } else {
                $producto->componentes()->detach();
            }

            DB::commit();
            return redirect()->route('productos.index')->with('success', 'Producto actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el producto: ' . $e->getMessage())->withInput();
        }
    }




    public function destroy(Producto $producto)
    {
        $empresaId = session('empresa_activa');

        if ($producto->empresa_id !== $empresaId) {
            abort(403, 'No tiene acceso a este producto.');
        }

        try {
            $producto->delete();
            return redirect()->route('productos.index')
                ->with('success', 'Producto eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    public function getSubcategorias(Categoria $categoria)
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

    public function kardex(Producto $producto, Request $request)
{
    $empresaId = session('empresa_activa');
    $sucursalId = session('sucursal_activa');

    $query = InventarioMovimiento::with(['usuario'])
        ->where('empresa_id', $empresaId)
        ->where('sucursal_id', $sucursalId)
        ->where('producto_id', $producto->id);

    // Aplicar filtros si existen
    if ($request->has('fecha_inicio')) {
        $query->whereDate('created_at', '>=', $request->fecha_inicio);
    }
    if ($request->has('fecha_fin')) {
        $query->whereDate('created_at', '<=', $request->fecha_fin);
    }
    if ($request->has('tipo_movimiento')) {
        $query->where('tipo_movimiento', $request->tipo_movimiento);
    }

    $movimientos = $query->orderBy('created_at', 'desc')->paginate(10);

    // Calcular saldos
    $saldo = 0;
    $movimientosConSaldo = $movimientos->map(function ($movimiento) use (&$saldo) {
        if ($movimiento->tipo_movimiento === 'entrada') {
            $saldo += $movimiento->cantidad;
        } else {
            $saldo -= $movimiento->cantidad;
        }
        $movimiento->saldo = $saldo;
        return $movimiento;
    });

    return view('productos.kardex', compact('producto', 'movimientos', 'movimientosConSaldo'));
}


}
