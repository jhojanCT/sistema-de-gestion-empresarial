<?php

namespace App\Http\Controllers;

use App\Models\Filtrado;
use App\Models\MateriaPrimaSinFiltrar;
use App\Models\MateriaPrimaFiltrada;
use Illuminate\Http\Request;

class FiltradoController extends Controller
{
    public function index()
    {
        $filtrados = Filtrado::with('materiaPrimaSinFiltrar')->get();
        return view('filtrado.index', compact('filtrados'));
    }

    public function create()
    {
        $materiasPrimas = MateriaPrimaSinFiltrar::all();
        return view('filtrado.create', compact('materiasPrimas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'materia_prima_sin_filtrar_id' => 'required|exists:materia_prima_sin_filtrar,id',
            'cantidad_entrada' => 'required|numeric|min:0.01',
            'cantidad_salida' => 'required|numeric|min:0.01|lte:cantidad_entrada',
            'fecha' => 'required|date'
        ]);

        $materiaPrima = MateriaPrimaSinFiltrar::find($request->materia_prima_sin_filtrar_id);

        if ($materiaPrima->stock < $request->cantidad_entrada) {
            return back()->with('error', 'No hay suficiente stock disponible');
        }

        // Crear registro de filtrado
        $filtrado = Filtrado::create([
            'materia_prima_sin_filtrar_id' => $request->materia_prima_sin_filtrar_id,
            'cantidad_entrada' => $request->cantidad_entrada,
            'cantidad_salida' => $request->cantidad_salida,
            'desperdicio' => $request->cantidad_entrada - $request->cantidad_salida,
            'fecha' => $request->fecha
        ]);

        // Actualizar stocks
        $materiaPrima->stock -= $request->cantidad_entrada;
        $materiaPrima->save();

        $materiaFiltrada = MateriaPrimaFiltrada::firstOrCreate(
            ['nombre' => $materiaPrima->nombre . ' filtrada'],
            ['unidad_medida' => $materiaPrima->unidad_medida, 'stock' => 0]
        );

        $materiaFiltrada->stock += $request->cantidad_salida;
        $materiaFiltrada->save();

        return redirect()->route('filtrado.index')->with('success', 'Proceso de filtrado registrado correctamente');
    }

    public function edit(Filtrado $filtrado)
    {
        $materiasPrimas = MateriaPrimaSinFiltrar::all();
        return view('filtrado.edit', compact('filtrado', 'materiasPrimas'));
    }

    public function update(Request $request, Filtrado $filtrado)
    {
        $request->validate([
            'materia_prima_sin_filtrar_id' => 'required|exists:materia_prima_sin_filtrar,id',
            'cantidad_entrada' => 'required|numeric|min:0.01',
            'cantidad_salida' => 'required|numeric|min:0.01|lte:cantidad_entrada',
            'fecha' => 'required|date'
        ]);

        // Revertir cambios anteriores en el stock
        $materiaPrimaAnterior = $filtrado->materiaPrimaSinFiltrar;
        $materiaPrimaAnterior->stock += $filtrado->cantidad_entrada;
        $materiaPrimaAnterior->save();

        $materiaFiltradaAnterior = MateriaPrimaFiltrada::where('nombre', $materiaPrimaAnterior->nombre . ' filtrada')->first();
        if ($materiaFiltradaAnterior) {
            $materiaFiltradaAnterior->stock -= $filtrado->cantidad_salida;
            $materiaFiltradaAnterior->save();
        }

        // Verificar nuevo stock disponible
        $materiaPrima = MateriaPrimaSinFiltrar::find($request->materia_prima_sin_filtrar_id);
        if ($materiaPrima->stock < $request->cantidad_entrada) {
            return back()->with('error', 'No hay suficiente stock disponible');
        }

        // Actualizar registro de filtrado
        $filtrado->update([
            'materia_prima_sin_filtrar_id' => $request->materia_prima_sin_filtrar_id,
            'cantidad_entrada' => $request->cantidad_entrada,
            'cantidad_salida' => $request->cantidad_salida,
            'desperdicio' => $request->cantidad_entrada - $request->cantidad_salida,
            'fecha' => $request->fecha
        ]);

        // Actualizar nuevos stocks
        $materiaPrima->stock -= $request->cantidad_entrada;
        $materiaPrima->save();

        $materiaFiltrada = MateriaPrimaFiltrada::firstOrCreate(
            ['nombre' => $materiaPrima->nombre . ' filtrada'],
            ['unidad_medida' => $materiaPrima->unidad_medida, 'stock' => 0]
        );

        $materiaFiltrada->stock += $request->cantidad_salida;
        $materiaFiltrada->save();

        return redirect()->route('filtrado.index')->with('success', 'Proceso de filtrado actualizado correctamente');
    }

    public function destroy(Filtrado $filtrado)
    {
        // Revertir cambios en el stock
        $materiaPrima = $filtrado->materiaPrimaSinFiltrar;
        $materiaPrima->stock += $filtrado->cantidad_entrada;
        $materiaPrima->save();

        $materiaFiltrada = MateriaPrimaFiltrada::where('nombre', $materiaPrima->nombre . ' filtrada')->first();
        if ($materiaFiltrada) {
            $materiaFiltrada->stock -= $filtrado->cantidad_salida;
            $materiaFiltrada->save();
        }

        $filtrado->delete();

        return redirect()->route('filtrado.index')->with('success', 'Proceso de filtrado eliminado correctamente');
    }
}