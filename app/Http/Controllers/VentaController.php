<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\FormaPago;
use App\Models\VentaDetalle;
use App\Models\VentaPago;
use App\Http\Requests\VentaRequest;
use App\Models\CorrelativoDocumento;
use App\Models\TipoDocumentoVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class VentaController extends Controller
{
    
    public function index()
    {
        $empresaId = session('empresa_activa');
        $sucursalId = session('sucursal_activa');

        $ventas = Venta::with(['cliente', 'detalles', 'pagos'])
            ->empresa($empresaId)
            ->sucursal($sucursalId)
            ->orderBy('fecha', 'desc')
            ->paginate(10);

        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        $empresaId = session('empresa_activa');
        $sucursalId = session('sucursal_activa');
       // dd($empresaId);
        // Obtener solo los tipos de documento que tienen correlativos disponibles
        $tiposDocumentoIds = CorrelativoDocumento::where('empresa_id', $empresaId)
            ->where('sucursal_id', $sucursalId)
            ->where('activo', true)
            ->pluck('tipo_documento_id');
    
        $tiposDocumento = TipoDocumentoVenta::whereIn('id', $tiposDocumentoIds)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();
    
        $clientes = Cliente::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();
    
        $productos = Producto::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();
    
        $formasPago = FormaPago::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();
    
        return view('ventas.create', compact('tiposDocumento', 'clientes', 'productos', 'formasPago'));
    }

    public function store(VentaRequest $request)
    {
        try {
            DB::beginTransaction();

            $empresaId = session('empresa_activa');
            $sucursalId = session('sucursal_activa');
            $userId = auth()->id();
           
            // Crear la venta
            $venta = new Venta($request->validated());
            $venta->empresa_id = $empresaId;
            $venta->sucursal_id = $sucursalId;
            $venta->usuario_id = $userId;
            $venta->estado = 'completada';
            
            // Calcular totales
            $subtotal = 0;
            $descuento = 0;
            $impuesto = 0;

            // Guardar la venta
            $venta->save();

            // Procesar detalles
            foreach ($request->detalles as $detalle) {
                $producto = Producto::findOrFail($detalle['producto_id']);
                
                // Validar stock
                if ($producto->control_stock && $producto->stock < $detalle['cantidad']) {
                    throw new \Exception("Stock insuficiente para el producto {$producto->nombre}");
                }

                // Crear detalle
                $ventaDetalle = new VentaDetalle($detalle);
                $ventaDetalle->venta_id = $venta->id;
                $ventaDetalle->codigo = $producto->codigo;
                $ventaDetalle->descripcion = $producto->nombre;
                $ventaDetalle->calcularTotales();
                $ventaDetalle->save();

                // Actualizar totales
                $subtotal += $ventaDetalle->subtotal;
                $descuento += $ventaDetalle->descuento;
                $impuesto += $ventaDetalle->impuesto;

                // Actualizar stock
                if ($producto->control_stock) {
                    $producto->stock -= $detalle['cantidad'];
                    $producto->save();
                }
            }

            // Procesar pagos
            foreach ($request->pagos as $pago) {
                $ventaPago = new VentaPago($pago);
                $ventaPago->venta_id = $venta->id;
                $ventaPago->fecha = $venta->fecha;
                $ventaPago->save();
            }

            // Actualizar totales de la venta
            $venta->subtotal = $subtotal;
            $venta->descuento = $descuento;
            $venta->impuesto = $impuesto;
            $venta->total = $subtotal - $descuento + $impuesto;
            $venta->save();

            DB::commit();

            return redirect()->route('ventas.show', $venta)
                ->with('success', 'Venta registrada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la venta: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'detalles.producto', 'pagos.formaPago', 'usuario']);
        return view('ventas.show', compact('venta'));
    }

    public function edit(Venta $venta)
    {
        if ($venta->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden editar ventas pendientes.');
        }

        $empresaId = session('empresa_activa');
        
        $clientes = Cliente::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();
        
        $productos = Producto::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $formasPago = FormaPago::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('ventas.edit', compact('venta', 'clientes', 'productos', 'formasPago'));
    }

    public function update(VentaRequest $request, Venta $venta)
    {
        if ($venta->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden editar ventas pendientes.');
        }

        try {
            DB::beginTransaction();

            // Actualizar datos básicos
            $venta->fill($request->validated());
            
            // Restaurar stock de productos anteriores
            foreach ($venta->detalles as $detalle) {
                if ($detalle->producto && $detalle->producto->control_stock) {
                    $detalle->producto->stock += $detalle->cantidad;
                    $detalle->producto->save();
                }
            }

            // Eliminar detalles y pagos anteriores
            $venta->detalles()->delete();
            $venta->pagos()->delete();

            // Calcular nuevos totales
            $subtotal = 0;
            $descuento = 0;
            $impuesto = 0;

            // Procesar nuevos detalles
            foreach ($request->detalles as $detalle) {
                $producto = Producto::findOrFail($detalle['producto_id']);
                
                // Validar stock
                if ($producto->control_stock && $producto->stock < $detalle['cantidad']) {
                    throw new \Exception("Stock insuficiente para el producto {$producto->nombre}");
                }

                // Crear detalle
                $ventaDetalle = new VentaDetalle($detalle);
                $ventaDetalle->venta_id = $venta->id;
                $ventaDetalle->codigo = $producto->codigo;
                $ventaDetalle->descripcion = $producto->nombre;
                $ventaDetalle->calcularTotales();
                $ventaDetalle->save();

                // Actualizar totales
                $subtotal += $ventaDetalle->subtotal;
                $descuento += $ventaDetalle->descuento;
                $impuesto += $ventaDetalle->impuesto;

                // Actualizar stock
                if ($producto->control_stock) {
                    $producto->stock -= $detalle['cantidad'];
                    $producto->save();
                }
            }

            // Procesar nuevos pagos
            foreach ($request->pagos as $pago) {
                $ventaPago = new VentaPago($pago);
                $ventaPago->venta_id = $venta->id;
                $ventaPago->fecha = $venta->fecha;
                $ventaPago->save();
            }

            // Actualizar totales de la venta
            $venta->subtotal = $subtotal;
            $venta->descuento = $descuento;
            $venta->impuesto = $impuesto;
            $venta->total = $subtotal - $descuento + $impuesto;
            $venta->save();

            DB::commit();

            return redirect()->route('ventas.show', $venta)
                ->with('success', 'Venta actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la venta: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Venta $venta)
    {
        if ($venta->estado === 'anulada') {
            return back()->with('error', 'La venta ya está anulada.');
        }

        try {
            DB::beginTransaction();

            // Restaurar stock
            foreach ($venta->detalles as $detalle) {
                if ($detalle->producto && $detalle->producto->control_stock) {
                    $detalle->producto->stock += $detalle->cantidad;
                    $detalle->producto->save();
                }
            }

            // Anular la venta
            $venta->estado = 'anulada';
            $venta->save();

            DB::commit();

            return redirect()->route('ventas.index')
                ->with('success', 'Venta anulada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al anular la venta: ' . $e->getMessage());
        }
    }

    
    public function getCorrelativo(Request $request, $tipoDocumentoId)
    {

   
        
        try {
            $empresaId = session('empresa_activa');

          // dd(session('empresa_activa'));
            $sucursalId = session('sucursal_activa');
            //$d = Session::get('empresa_activa');
            //dd($d);
            $correlativo = CorrelativoDocumento::where('empresa_id', $empresaId)
                ->where('sucursal_id', $sucursalId)
                ->where('tipo_documento_id', $tipoDocumentoId)
                ->where('activo', true)
                ->first();
            //dd($correlativo);
            if (!$correlativo) {
                return response()->json([
                    'error' => 'No se encontró un correlativo válido para el tipo de documento seleccionado.'
                ], 404);
            }
    
            if (!$correlativo->disponible()) {
                return response()->json([
                    'error' => 'El correlativo no está disponible o ha vencido.'
                ], 422);
            }
    
            try {
                $numeroDocumento = $correlativo->generarNumeroDocumento();
                
                return response()->json([
                    'serie' => $correlativo->serie,
                    'siguiente' => $numeroDocumento,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage()
                ], 422);
            }
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener el correlativo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
    
}

