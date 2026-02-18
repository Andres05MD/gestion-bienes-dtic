<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Muestra el listado de usuarios con búsqueda y filtros.
     */
    public function index(Request $request): View
    {
        $query = User::with('roles')->latest();

        // Búsqueda por nombre o email
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('name', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%");
            });
        }

        // Filtro por rol
        if ($request->filled('rol')) {
            $rol = $request->input('rol');
            $query->whereHas('roles', fn ($q) => $q->where('name', $rol));
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('usuarios.index', compact('users', 'roles'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create(): View
    {
        $roles = Role::all();
        return view('usuarios.create', compact('roles'));
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Muestra el formulario para editar el usuario especificado.
     */
    public function edit(User $usuario): View
    {
        $roles = Role::all();
        $userRole = $usuario->roles->pluck('name')->first();
        return view('usuarios.edit', compact('usuario', 'roles', 'userRole'));
    }

    /**
     * Actualiza el usuario especificado.
     */
    public function update(UpdateUserRequest $request, User $usuario): RedirectResponse
    {
        $usuario->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $usuario->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $usuario->syncRoles($request->role);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Elimina el usuario especificado.
     */
    public function destroy(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * Resetea la contraseña de un usuario (solo admin).
     */
    public function resetPassword(Request $request, User $usuario): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $usuario->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', "Contraseña de {$usuario->name} actualizada correctamente.");
    }
}
