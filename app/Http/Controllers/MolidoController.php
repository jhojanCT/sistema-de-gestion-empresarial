<?php

namespace App\Http\Controllers;

use App\Models\Molido;
use App\Models\MateriaPrimaFiltrada;
use App\Models\MateriaPrimaMolida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MolidoController extends Controller
{
    public function index()
    {
        $molidos = Molido::with('materiaPrimaFiltrada', 'usuario')->orderBy('fecha', 'desc')->get();
        return view('molido.index', compact('molidos'));
    }

    public function create()
    {
        $materiasPrimas = MateriaPrimaFiltrada::where('stock', '>', 0)->get();
        return view('molido.create', compact('materiasPrimas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'materia_prima_filtrada_id' => 'required|exists:materia_prima_filtrada,id',
            'cantidad_entrada' => 'required|numeric|min:0.01',
            'cantidad_salida' => 'required|numeric|min:0.01|lte:cantidad_entrada',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $materiaPrima = MateriaPrimaFiltrada::findOrFail($request->materia_prima_filtrada_id);
            if ($materiaPrima->stock < $request->cantidad_entrada) {
                throw new \Exception('No hay suficiente stock de materia prima filtrada.');
            }

            // Registrar el proceso de molido
            $molido = Molido::create([
                'materia_prima_filtrada_id' => $request->materia_prima_filtrada_id,
                'cantidad_entrada' => $request->cantidad_entrada,
                'cantidad_salida' => $request->cantidad_salida,
                'fecha' => $request->fecha,
                'observaciones' => $request->observaciones,
                'usuario_id' => Auth::id(),
            ]);

            // Actualizar stock de materia prima filtrada
            $materiaPrima->decrement('stock', $request->cantidad_entrada);

            // Registrar en inventario molido
            MateriaPrimaMolida::create([
                'materia_prima_filtrada_id' => $request->materia_prima_filtrada_id,
                'cantidad' => $request->cantidad_salida,
                'fecha_molido' => $request->fecha,
                'observaciones' => $request->observaciones,
            ]);
        });

        return redirect()->route('molido.index')->with('success', 'Proceso de molido registrado correctamente');
    }

    public function destroy(Molido $molido)
    {
        DB::transaction(function () use ($molido) {
            // Revertir stock de materia prima filtrada
            $materiaPrima = $molido->materiaPrimaFiltrada;
            $materiaPrima->increment('stock', $molido->cantidad_entrada);

            // Eliminar inventario molido relacionado (opcional: solo si no se ha usado en producción)
            MateriaPrimaMolida::where([
                'materia_prima_filtrada_id' => $molido->materia_prima_filtrada_id,
                'cantidad' => $molido->cantidad_salida,
                'fecha_molido' => $molido->fecha,
            ])->delete();

            $molido->delete();
        });
        return redirect()->route('molido.index')->with('success', 'Proceso de molido eliminado correctamente');
    }
} 