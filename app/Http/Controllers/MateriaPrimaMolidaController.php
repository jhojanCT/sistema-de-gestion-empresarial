<?php

namespace App\Http\Controllers;

use App\Models\MateriaPrimaMolida;
use App\Models\MateriaPrimaFiltrada;
use Illuminate\Http\Request;

class MateriaPrimaMolidaController extends Controller
{
    public function index()
    {
        $inventario = MateriaPrimaMolida::with('materiaPrimaFiltrada')->orderBy('fecha_molido', 'desc')->get();
        return view('molido.inventario', compact('inventario'));
    }

    public function create()
    {
        $materiasFiltradas = MateriaPrimaFiltrada::all();
        return view('molido.create_molida', compact('materiasFiltradas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'materia_prima_filtrada_id' => 'required|exists:materia_prima_filtrada,id',
            'cantidad' => 'required|numeric|min:0.01',
            'fecha_molido' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);
        MateriaPrimaMolida::create($request->all());
        return redirect()->route('molido.inventario')->with('success', 'Materia prima molida agregada correctamente');
    }

    public function edit(MateriaPrimaMolida $materiaPrimaMolida)
    {
        $materiasFiltradas = MateriaPrimaFiltrada::all();
        return view('molido.edit_molida', compact('materiaPrimaMolida', 'materiasFiltradas'));
    }

    public function update(Request $request, MateriaPrimaMolida $materiaPrimaMolida)
    {
        $request->validate([
            'materia_prima_filtrada_id' => 'required|exists:materia_prima_filtrada,id',
            'cantidad' => 'required|numeric|min:0.01',
            'fecha_molido' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);
        $materiaPrimaMolida->update($request->all());
        return redirect()->route('molido.inventario')->with('success', 'Materia prima molida actualizada correctamente');
    }

    public function destroy(MateriaPrimaMolida $materiaPrimaMolida)
    {
        $materiaPrimaMolida->delete();
        return redirect()->route('molido.inventario')->with('success', 'Materia prima molida eliminada correctamente');
    }
} 