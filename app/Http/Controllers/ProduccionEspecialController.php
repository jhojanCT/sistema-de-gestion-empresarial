<?php
namespace App\Http\Controllers;

use App\Models\ProduccionEspecial;
use App\Models\MateriaPrimaSinFiltrar;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduccionEspecialController extends Controller
{
    public function index()
    {
        $producciones = ProduccionEspecial::with('materiaPrimaSinFiltrar', 'producto')->orderBy('fecha', 'desc')->get();
        return view('produccion_especial.index', compact('producciones'));
    }

    public function create()
    {
        $materiasPrimas = MateriaPrimaSinFiltrar::where('stock', '>', 0)->get();
        $productos = Producto::where('tipo', 'producido')->get();
        return view('produccion_especial.create', compact('materiasPrimas', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'materia_prima_sin_filtrar_id' => 'required|exists:materia_prima_sin_filtrar,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad_utilizada' => 'required|numeric|min:0.01',
            'cantidad_producida' => 'required|numeric|min:0.01',
            'fecha' => 'required|date',
            'costo_produccion' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $materiaPrima = MateriaPrimaSinFiltrar::findOrFail($request->materia_prima_sin_filtrar_id);
            if ($materiaPrima->stock < $request->cantidad_utilizada) {
                throw new \Exception('No hay suficiente materia prima sin filtrar disponible.');
            }

            $produccion = ProduccionEspecial::create($request->all());
            $materiaPrima->decrement('stock', $request->cantidad_utilizada);
            $producto = Producto::findOrFail($request->producto_id);
            $producto->increment('stock', $request->cantidad_producida);
        });

        return redirect()->route('produccion-especial.index')->with('success', 'Producción especial registrada correctamente');
    }

    public function edit(ProduccionEspecial $produccionEspecial)
    {
        $materiasPrimas = MateriaPrimaSinFiltrar::where('stock', '>', 0)->get();
        $productos = Producto::where('tipo', 'producido')->get();
        return view('produccion_especial.edit', compact('produccionEspecial', 'materiasPrimas', 'productos'));
    }

    public function update(Request $request, ProduccionEspecial $produccionEspecial)
    {
        $request->validate([
            'materia_prima_sin_filtrar_id' => 'required|exists:materia_prima_sin_filtrar,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad_utilizada' => 'required|numeric|min:0.01',
            'cantidad_producida' => 'required|numeric|min:0.01',
            'fecha' => 'required|date',
            'costo_produccion' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $produccionEspecial) {
            // Revertir stocks anteriores
            $materiaPrimaAnterior = MateriaPrimaSinFiltrar::find($produccionEspecial->materia_prima_sin_filtrar_id);
            $materiaPrimaAnterior->increment('stock', $produccionEspecial->cantidad_utilizada);
            $productoAnterior = Producto::find($produccionEspecial->producto_id);
            $productoAnterior->decrement('stock', $produccionEspecial->cantidad_producida);

            // Actualizar producción especial
            $produccionEspecial->update($request->all());

            // Actualizar stocks nuevos
            $materiaPrima = MateriaPrimaSinFiltrar::findOrFail($request->materia_prima_sin_filtrar_id);
            $materiaPrima->decrement('stock', $request->cantidad_utilizada);
            $producto = Producto::findOrFail($request->producto_id);
            $producto->increment('stock', $request->cantidad_producida);
        });

        return redirect()->route('produccion-especial.index')->with('success', 'Producción especial actualizada correctamente');
    }

    public function destroy(ProduccionEspecial $produccionEspecial)
    {
        DB::transaction(function () use ($produccionEspecial) {
            $materiaPrima = MateriaPrimaSinFiltrar::find($produccionEspecial->materia_prima_sin_filtrar_id);
            $materiaPrima->increment('stock', $produccionEspecial->cantidad_utilizada);
            $producto = Producto::find($produccionEspecial->producto_id);
            $producto->decrement('stock', $produccionEspecial->cantidad_producida);
            $produccionEspecial->delete();
        });
        return redirect()->route('produccion-especial.index')->with('success', 'Producción especial eliminada correctamente');
    }
} 