<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:ver-permisos')->only('index');
        $this->middleware('can:crear-permisos')->only(['create', 'store']);
        $this->middleware('can:editar-permisos')->only(['edit', 'update']);
        $this->middleware('can:eliminar-permisos')->only('destroy');
    }

    public function index()
    {
        $permissions = Permission::all()->groupBy('group');
        return view('permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('permissions.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions'],
            'description' => ['required', 'string', 'max:255'],
            'group' => ['required', 'string', 'max:255'],
        ]);

        Permission::create([
            'name' => $request->name,
            'description' => $request->description,
            'group' => $request->group,
            'guard_name' => 'web'
        ]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permiso creado exitosamente.');
    }

    public function edit(Permission $permission)
    {
        return view('permissions.form', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
            'description' => ['required', 'string', 'max:255'],
            'group' => ['required', 'string', 'max:255'],
        ]);

        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
            'group' => $request->group
        ]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permiso actualizado exitosamente.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Permiso eliminado exitosamente.');
    }
} 