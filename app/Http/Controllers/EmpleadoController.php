<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::orderBy('nombre')->get();
        return view('empleados.index', compact('empleados'));
    }

    public function create()
    {
        return view('empleados.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'ci' => 'nullable|string|max:20|unique:empleados,ci',
            'cargo' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            
            Empleado::create($request->all());
            
            DB::commit();
            return redirect()->route('empleados.index')
                ->with('success', 'Empleado creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear el empleado: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Empleado $empleado)
    {
        return view('empleados.show', compact('empleado'));
    }

    public function edit(Empleado $empleado)
    {
        return view('empleados.edit', compact('empleado'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'ci' => 'nullable|string|max:20|unique:empleados,ci,' . $empleado->id,
            'cargo' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            
            $empleado->update($request->all());
            
            DB::commit();
            return redirect()->route('empleados.index')
                ->with('success', 'Empleado actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar el empleado: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Empleado $empleado)
    {
        try {
            DB::beginTransaction();
            
            $empleado->delete();
            
            DB::commit();
            return redirect()->route('empleados.index')
                ->with('success', 'Empleado eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar el empleado: ' . $e->getMessage());
        }
    }
} 