<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\CentroCosto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CentroCostoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $centrosCosto = CentroCosto::orderBy('codigo')->get();
        return view('contabilidad.centros-costo.index', compact('centrosCosto'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('contabilidad.centros-costo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:20|unique:centros_costo',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            CentroCosto::create($request->all());

            DB::commit();

            return redirect()->route('contabilidad.centros-costo.index')
                ->with('success', 'Centro de costo creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear el centro de costo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $centroCosto = CentroCosto::findOrFail($id);
        return view('contabilidad.centros-costo.show', compact('centroCosto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CentroCosto $centroCosto)
    {
        return view('contabilidad.centros-costo.edit', compact('centroCosto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CentroCosto $centroCosto)
    {
        $request->validate([
            'codigo' => 'required|string|max:20|unique:centros_costo,codigo,' . $centroCosto->id,
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $centroCosto->update($request->all());

            DB::commit();

            return redirect()->route('contabilidad.centros-costo.index')
                ->with('success', 'Centro de costo actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar el centro de costo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CentroCosto $centroCosto)
    {
        try {
            DB::beginTransaction();

            // Verificar si tiene asientos
            if ($centroCosto->asientos()->count() > 0 || $centroCosto->detallesAsiento()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar el centro de costo porque tiene movimientos.');
            }

            $centroCosto->delete();

            DB::commit();

            return redirect()->route('contabilidad.centros-costo.index')
                ->with('success', 'Centro de costo eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar el centro de costo: ' . $e->getMessage());
        }
    }
}
