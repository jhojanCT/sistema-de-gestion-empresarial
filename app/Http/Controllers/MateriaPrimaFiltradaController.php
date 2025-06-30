<?php

namespace App\Http\Controllers;

use App\Models\MateriaPrimaFiltrada;
use Illuminate\Http\Request;

class MateriaPrimaFiltradaController extends Controller
{
    public function index()
    {
        $materiasPrimas = MateriaPrimaFiltrada::orderBy('nombre')->get();
        return view('materia-prima-filtrada.index', compact('materiasPrimas'));
    }

    public function create()
    {
        return view('materia-prima-filtrada.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'unidad_medida' => 'required|string',
            'stock' => 'required|numeric|min:0'
        ]);

        MateriaPrimaFiltrada::create($validated);

        return redirect()->route('materia-prima-filtrada.index')
            ->with('success', 'Materia prima filtrada creada correctamente');
    }

    public function show(MateriaPrimaFiltrada $materia_prima_filtrada)
    {
        return view('materia-prima-filtrada.show', compact('materia_prima_filtrada'));
    }

    public function edit(MateriaPrimaFiltrada $materia_prima_filtrada)
    {
        return view('materia-prima-filtrada.edit', [
            'materiaPrima' => $materia_prima_filtrada
        ]);
    }

    public function update(Request $request, MateriaPrimaFiltrada $materia_prima_filtrada)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'unidad_medida' => 'required|string',
            'stock' => 'required|numeric|min:0'
        ]);

        $materia_prima_filtrada->update($validated);

        return redirect()->route('materia-prima-filtrada.index')
            ->with('success', 'Materia prima filtrada actualizada correctamente');
    }
}