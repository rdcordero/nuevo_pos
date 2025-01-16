<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::with(['roles', 'empresas'])->paginate(10);
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'id');
        $empresas = Empresa::where('activo', true)->pluck('nombre', 'id');
        $sucursales = Sucursal::where('activo', true)->get()
            ->groupBy('empresa_id')
            ->map(function ($items) {
                return $items->pluck('nombre', 'id');
            });

        return view('usuarios.create', compact('roles', 'empresas', 'sucursales'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'empresas' => 'required|array|min:1',
            'empresas.*' => 'exists:empresas,id',
            'sucursales' => 'required|array|min:1',
            'sucursales.*' => 'exists:sucursales,id'
        ]);

        $usuario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $roles = Role::whereIn('id', $request->roles)->get();
        $usuario->syncRoles($roles);

        $usuario->empresas()->attach($request->empresas, ['activo' => true]);
        $usuario->sucursales()->attach($request->sucursales, ['activo' => true]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $usuario)
    {
        $roles = Role::pluck('name', 'id');
        $empresas = Empresa::where('activo', true)->pluck('nombre', 'id');
        $sucursales = Sucursal::where('activo', true)->get()
            ->groupBy('empresa_id')
            ->map(function ($items) {
                return $items->pluck('nombre', 'id');
            });

        $usuarioRoles = $usuario->roles->pluck('id')->toArray();
        $usuarioEmpresas = $usuario->empresas->pluck('id')->toArray();
        $usuarioSucursales = $usuario->sucursales->pluck('id')->toArray();

        return view('usuarios.edit', compact(
            'usuario',
            'roles',
            'empresas',
            'sucursales',
            'usuarioRoles',
            'usuarioEmpresas',
            'usuarioSucursales'
        ));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'empresas' => 'required|array|min:1',
            'empresas.*' => 'exists:empresas,id',
            'sucursales' => 'required|array|min:1',
            'sucursales.*' => 'exists:sucursales,id'
        ]);

        $usuario->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $usuario->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $roles = Role::whereIn('id', $request->roles)->get();
        $usuario->syncRoles($roles);

        $usuario->empresas()->sync($request->empresas);
        $usuario->sucursales()->sync($request->sucursales);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->empresas()->detach();
        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}
