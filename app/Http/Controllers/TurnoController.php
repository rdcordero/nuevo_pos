<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\Caja;
use App\Models\Sucursal;
use App\Http\Requests\TurnoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TurnoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver turnos')->only(['index', 'show']);
        $this->middleware('permission:crear turnos')->only(['create', 'store']);
        $this->middleware('permission:editar turnos')->only(['edit', 'update']);
        $this->middleware('permission:eliminar turnos')->only('destroy');
        $this->middleware('permission:cerrar turnos')->only('cerrar');
    }
    public function index()
    {
        $empresaId = session('empresa_activa');
        $sucursalId = session('sucursal_activa');

        $turnos = Turno::with(['sucursal', 'caja', 'usuario'])
            ->empresa($empresaId)
            ->sucursal($sucursalId)
            ->orderBy('fecha_apertura', 'desc')
            ->paginate(10);

        return view('turnos.index', compact('turnos'));
    }

    public function create()
    {
        $empresaId = session('empresa_activa');
        $sucursalId = session('sucursal_activa');

        $sucursales = Sucursal::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $cajas = Caja::where('sucursal_id', $sucursalId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('turnos.create', compact('sucursales', 'cajas'));
    }

    public function store(TurnoRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $validated['usuario_id'] = auth()->id();
            $validated['estado'] = 'abierto';
            $validated['fecha_apertura'] = now();

            $turno = Turno::create($validated);

            DB::commit();

            return redirect()->route('turnos.show', $turno)
                ->with('success', 'Turno abierto exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al abrir el turno: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Turno $turno)
    {
        $turno->load(['sucursal', 'caja', 'usuario']);
        return view('turnos.show', compact('turno'));
    }

    public function edit(Turno $turno)
    {
        if (!$turno->estaAbierto()) {
            return back()->with('error', 'No se puede editar un turno cerrado.');
        }

        $empresaId = session('empresa_activa');
        $sucursalId = session('sucursal_activa');

        $sucursales = Sucursal::where('empresa_id', $empresaId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $cajas = Caja::where('sucursal_id', $sucursalId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('turnos.edit', compact('turno', 'sucursales', 'cajas'));
    }

    public function update(TurnoRequest $request, Turno $turno)
    {
        if (!$turno->estaAbierto()) {
            return back()->with('error', 'No se puede actualizar un turno cerrado.');
        }

        try {
            DB::beginTransaction();

            $turno->update($request->validated());

            DB::commit();

            return redirect()->route('turnos.show', $turno)
                ->with('success', 'Turno actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el turno: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Turno $turno)
    {
        if (!$turno->estaAbierto()) {
            return back()->with('error', 'No se puede eliminar un turno cerrado.');
        }

        try {
            DB::beginTransaction();

            $turno->delete();

            DB::commit();

            return redirect()->route('turnos.index')
                ->with('success', 'Turno eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar el turno: ' . $e->getMessage());
        }
    }

    public function cerrar(Request $request, Turno $turno)
    {
        // Validar que el turno esté abierto
        if (!$turno->estaAbierto()) {
            return back()->with('error', 'El turno ya está cerrado.');
        }

        // Validar datos del request
        $request->validate([
            'monto_sistema' => 'required|numeric|min:0',
            'monto_cierre' => 'required|numeric|min:0',
            'observaciones_cierre' => 'nullable|string|max:500'
        ], [
            'monto_sistema.required' => 'El monto en sistema es obligatorio',
            'monto_sistema.numeric' => 'El monto en sistema debe ser un número',
            'monto_sistema.min' => 'El monto en sistema no puede ser negativo',
            'monto_cierre.required' => 'El monto en caja es obligatorio',
            'monto_cierre.numeric' => 'El monto en caja debe ser un número',
            'monto_cierre.min' => 'El monto en caja no puede ser negativo',
            'observaciones_cierre.max' => 'Las observaciones no pueden tener más de 500 caracteres'
        ]);

        try {
            DB::beginTransaction();

            // Actualizar el turno
            $turno->monto_sistema = $request->monto_sistema;
            $turno->monto_cierre = $request->monto_cierre;
            $turno->fecha_cierre = now();
            $turno->observaciones_cierre = $request->observaciones_cierre;
            $turno->estado = 'cerrado';

            // Calcular diferencia
            $turno->calcularDiferencia();

            $turno->save();

            DB::commit();

            return redirect()->route('turnos.show', $turno)
                ->with('success', 'Turno cerrado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cerrar el turno: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function reporte(Turno $turno)
    {
        // Cargar todas las relaciones necesarias
        $turno->load([
            'sucursal',
            'caja',
            'usuario',
            // Cargar las ventas del turno con sus detalles
            'ventas' => function ($query) {
                //$query->orderBy('fecha', 'asc');
            },
            'ventas.detalles',
            'ventas.pagos.formaPago'
        ]);

        // Calcular totales por forma de pago
        $totalesPorFormaPago = $turno->ventas
            ->flatMap(function ($venta) {
                return $venta->pagos;
            })
            ->groupBy(function ($pago) {
                // Agrupar por ID de forma de pago para evitar duplicados
                return $pago->forma_pago_id;
            })
            ->map(function ($pagos) {
                $primerPago = $pagos->first();
                return [
                    'forma_pago' => $primerPago->formaPago->nombre,
                    'total' => $pagos->sum('monto')
                ];
            })
            ->sortBy('forma_pago'); // Ordenar por nombre de forma de pago

        return view('turnos.reporte', compact('turno', 'totalesPorFormaPago'));
    }
}
