<?php

namespace App\Http\Controllers;

use App\Models\MateriaPrimaSinFiltrar;
use App\Models\MateriaPrimaFiltrada;
use App\Models\Filtrado;
use App\Models\CompraItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MateriaPrimaSinFiltrarController extends Controller
{
    public function index()
    {
        $materiasPrimas = MateriaPrimaSinFiltrar::all();
        return view('materia-prima-sin-filtrar.index', compact('materiasPrimas'));
    }

    public function create()
    {
        return view('materia-prima-sin-filtrar.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string',
            'stock' => 'required|numeric|min:0'
        ]);

        DB::transaction(function () use ($validated) {
            // Crear la materia prima sin filtrar
            $materiaPrimaSinFiltrar = MateriaPrimaSinFiltrar::create($validated);

            // Crear automáticamente la materia prima filtrada correspondiente
            MateriaPrimaFiltrada::create([
                'nombre' => $materiaPrimaSinFiltrar->nombre . ' filtrada',
                'unidad_medida' => $materiaPrimaSinFiltrar->unidad_medida,
                'stock' => 0
            ]);
        });

        return redirect()->route('materia-prima-sin-filtrar.index')->with('success', 'Materia prima creada');
    }

    public function show(MateriaPrimaSinFiltrar $materiaPrima)
    {
        return view('materia-prima-sin-filtrar.show', compact('materiaPrima'));
    }

    public function edit(MateriaPrimaSinFiltrar $materia_prima_sin_filtrar)
    {
        return view('materia-prima-sin-filtrar.edit', [
            'materiaPrima' => $materia_prima_sin_filtrar
        ]);
    }
    
    public function update(Request $request, MateriaPrimaSinFiltrar $materia_prima_sin_filtrar)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string',
            'stock' => 'required|numeric|min:0'
        ]);

        DB::transaction(function () use ($validated, $materia_prima_sin_filtrar) {
            // Actualizar la materia prima sin filtrar
            $materia_prima_sin_filtrar->update($validated);

            // Actualizar el nombre de la materia prima filtrada correspondiente
            $materiaFiltrada = MateriaPrimaFiltrada::where('nombre', $materia_prima_sin_filtrar->getOriginal('nombre') . ' filtrada')->first();
            if ($materiaFiltrada) {
                $materiaFiltrada->update([
                    'nombre' => $validated['nombre'] . ' filtrada',
                    'unidad_medida' => $validated['unidad_medida']
                ]);
            }
        });

        return redirect()->route('materia-prima-sin-filtrar.index')->with('success', 'Materia prima actualizada');
    }

    public function destroy($materia_prima_sin_filtrar)
    {
        try {
            DB::beginTransaction();

            // Buscar la materia prima por ID
            $materiaPrima = MateriaPrimaSinFiltrar::find($materia_prima_sin_filtrar);

            if (!$materiaPrima) {
                return redirect()->route('materia-prima-sin-filtrar.index')
                    ->with('error', 'Error: No se encontró la materia prima')
                    ->withoutSession(['success']);
            }

            // Verificar si hay registros relacionados
            $filtrados = Filtrado::where('materia_prima_sin_filtrar_id', $materiaPrima->id)->get();
            $compras = CompraItem::where('materia_prima_id', $materiaPrima->id)->get();
            $materiaFiltrada = MateriaPrimaFiltrada::where('nombre', $materiaPrima->nombre . ' filtrada')->first();

            if ($filtrados->count() > 0) {
                DB::rollBack();
                return redirect()->route('materia-prima-sin-filtrar.index')
                    ->with('error', 'No se puede eliminar la materia prima porque tiene ' . $filtrados->count() . ' registros de filtrado asociados.')
                    ->withoutSession(['success']);
            }

            if ($compras->count() > 0) {
                DB::rollBack();
                return redirect()->route('materia-prima-sin-filtrar.index')
                    ->with('error', 'No se puede eliminar la materia prima porque tiene ' . $compras->count() . ' registros de compras asociados.')
                    ->withoutSession(['success']);
            }

            if ($materiaFiltrada) {
                DB::rollBack();
                return redirect()->route('materia-prima-sin-filtrar.index')
                    ->with('error', 'No se puede eliminar la materia prima porque tiene una materia prima filtrada asociada.')
                    ->withoutSession(['success']);
            }

            // Intentar eliminar la materia prima
            try {
                $eliminado = $materiaPrima->delete();
                
                if (!$eliminado) {
                    DB::rollBack();
                    return redirect()->route('materia-prima-sin-filtrar.index')
                        ->with('error', 'No se pudo eliminar la materia prima. Verifique que no tenga registros relacionados.')
                        ->withoutSession(['success']);
                }
            } catch (\Exception $e) {
                throw $e;
            }

            DB::commit();

            return redirect()->route('materia-prima-sin-filtrar.index')
                ->with('success', 'Materia prima eliminada correctamente')
                ->withoutSession(['error']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('materia-prima-sin-filtrar.index')
                ->with('error', 'No se pudo eliminar la materia prima: ' . $e->getMessage())
                ->withoutSession(['success']);
        }
    }
}