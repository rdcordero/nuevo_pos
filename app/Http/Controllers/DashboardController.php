<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Caja;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener fecha actual y primer día del mes
        $hoy = Carbon::today();
        $inicioMes = Carbon::today()->startOfMonth();

        // Ventas del día
        $ventasDia = Venta::whereDate('created_at', $hoy)
            ->sum('total') ?? 0;

        // Ventas del mes
        $ventasMes = Venta::whereMonth('created_at', $hoy->month)
            ->whereYear('created_at', $hoy->year)
            ->sum('total') ?? 0;

        // Total de productos
        $totalProductos = Producto::where('activo', true)->count();

        // Cajas activas
        $cajasActivas = Caja::where('activo', true)->count();

        // Productos más vendidos - Compatible con SQL Server
        $productosMasVendidos = DB::table('venta_detalles')
            ->join('productos', 'venta_detalles.producto_id', '=', 'productos.id')
            ->select(
                'productos.nombre',
                DB::raw('SUM(venta_detalles.cantidad) as cantidad'),
                DB::raw('SUM(venta_detalles.subtotal) as total')
            )
            ->whereMonth('venta_detalles.created_at', $hoy->month)
            ->whereYear('venta_detalles.created_at', $hoy->year)
            ->groupBy('productos.nombre')
            ->orderByDesc(DB::raw('SUM(venta_detalles.cantidad)'))
            ->limit(5)
            ->get();

        // Ventas por mes para el gráfico - Compatible con SQL Server
        $ventasPorMes = DB::table('ventas')
            ->select(
                DB::raw('MONTH(created_at) as numero_mes'),
                DB::raw('SUM(total) as total')
            )
            ->whereYear('created_at', $hoy->year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('numero_mes')
            ->get()
            ->map(function ($venta) {
                return [
                    'mes' => Carbon::create()->month($venta->numero_mes)->locale('es')->monthName,
                    'total' => round($venta->total, 2)
                ];
            });

        // Últimas ventas
        $ultimasVentas = Venta::with(['cliente', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Productos con stock bajo
        $productosStockBajo = Producto::whereHas('inventario', function ($query) {
            $query->whereRaw('cantidad <= stock_minimo');
        })
            ->with('inventario')
            ->limit(5)
            ->get();

        // Preparar datos para el gráfico
        $mesesData = [];
        $totalesData = [];

        foreach ($ventasPorMes as $venta) {
            $mesesData[] = $venta['mes'];
            $totalesData[] = $venta['total'];
        }

        return view('dashboard', compact(
            'ventasDia',
            'ventasMes',
            'totalProductos',
            'cajasActivas',
            'productosMasVendidos',
            'ultimasVentas',
            'productosStockBajo',
            'mesesData',
            'totalesData'
        ));
    }
}