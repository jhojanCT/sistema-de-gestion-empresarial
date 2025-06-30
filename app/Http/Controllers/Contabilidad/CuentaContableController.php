<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\CuentaContable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CuentaContableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CuentaContable::query();

        // Aplicar filtros de búsqueda
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('codigo', 'like', '%' . $searchTerm . '%')
                  ->orWhere('nombre', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->get('tipo'));
        }

        // Filtro por naturaleza
        if ($request->filled('naturaleza')) {
            $query->where('naturaleza', $request->get('naturaleza'));
        }

        // Filtro por nivel
        if ($request->filled('nivel')) {
            $query->where('nivel', $request->get('nivel'));
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('activo', $request->get('estado') === 'activo');
        }

        $cuentas = $query->orderBy('codigo')->get();

        // Obtener valores únicos para los filtros
        $tipos = CuentaContable::distinct()->pluck('tipo');
        $naturalezas = CuentaContable::distinct()->pluck('naturaleza');
        $niveles = CuentaContable::distinct()->pluck('nivel');

        return view('contabilidad.cuentas.index', compact('cuentas', 'tipos', 'naturalezas', 'niveles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cuentasPadre = CuentaContable::where('nivel', '<', 4)->orderBy('codigo')->get();
        return view('contabilidad.cuentas.create', compact('cuentasPadre'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:20',
                'unique:cuentas_contables',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^\d+(\.\d+)*$/', $value)) {
                        $fail('El código de cuenta debe contener solo números y puntos.');
                    }
                }
            ],
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|in:ACTIVO,PASIVO,PATRIMONIO,INGRESO,EGRESO,COSTO',
            'naturaleza' => 'required|in:DEUDORA,ACREEDORA',
            'cuenta_padre_id' => [
                'nullable',
                'exists:cuentas_contables,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $padre = CuentaContable::find($value);
                        if ($padre->nivel >= 4) {
                            $fail('La cuenta padre no puede ser de nivel 4 o superior.');
                        }
                    }
                }
            ],
            'nivel' => 'required|integer|min:1|max:5',
            'es_centro_costo' => 'boolean',
            'activo' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Verificar que el código es consistente con la cuenta padre
            if ($request->cuenta_padre_id) {
                $padre = CuentaContable::find($request->cuenta_padre_id);
                if (!str_starts_with($request->codigo, $padre->codigo . '.')) {
                    throw new \Exception('El código de la cuenta debe comenzar con el código de la cuenta padre.');
                }
            }

            CuentaContable::create($request->all());

            DB::commit();

            return redirect()->route('contabilidad.cuentas.index')
                ->with('success', 'Cuenta contable creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear la cuenta contable: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CuentaContable $cuenta)
    {
        $cuenta->load(['cuentaPadre', 'cuentasHijas', 'detallesAsiento.asiento']);
        return view('contabilidad.cuentas.show', compact('cuenta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CuentaContable $cuenta)
    {
        $cuentasPadre = CuentaContable::where('nivel', '<', 4)
            ->where('id', '!=', $cuenta->id)
            ->orderBy('codigo')
            ->get();
        return view('contabilidad.cuentas.edit', compact('cuenta', 'cuentasPadre'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CuentaContable $cuenta)
    {
        $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:20',
                'unique:cuentas_contables,codigo,' . $cuenta->id,
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^\d+(\.\d+)*$/', $value)) {
                        $fail('El código de cuenta debe contener solo números y puntos.');
                    }
                }
            ],
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|in:ACTIVO,PASIVO,PATRIMONIO,INGRESO,EGRESO,COSTO',
            'naturaleza' => 'required|in:DEUDORA,ACREEDORA',
            'cuenta_padre_id' => [
                'nullable',
                'exists:cuentas_contables,id',
                Rule::notIn([$cuenta->id]), // No puede ser su propia cuenta padre
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $padre = CuentaContable::find($value);
                        if ($padre->nivel >= 4) {
                            $fail('La cuenta padre no puede ser de nivel 4 o superior.');
                        }
                    }
                }
            ],
            'nivel' => 'required|integer|min:1|max:5',
            'es_centro_costo' => 'boolean',
            'activo' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Verificar si tiene movimientos antes de desactivar
            if (!$request->activo && $cuenta->activo && $cuenta->detallesAsiento()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'No se puede desactivar la cuenta porque tiene movimientos.')
                    ->withInput();
            }

            // Verificar que el código es consistente con la cuenta padre
            if ($request->cuenta_padre_id) {
                $padre = CuentaContable::find($request->cuenta_padre_id);
                if (!str_starts_with($request->codigo, $padre->codigo . '.')) {
                    throw new \Exception('El código de la cuenta debe comenzar con el código de la cuenta padre.');
                }
            }

            // Verificar que no tiene cuentas hijas si se va a desactivar
            if (!$request->activo && $cuenta->cuentasHijas()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'No se puede desactivar la cuenta porque tiene cuentas hijas.')
                    ->withInput();
            }

            $cuenta->update($request->all());

            DB::commit();

            return redirect()->route('contabilidad.cuentas.index')
                ->with('success', 'Cuenta contable actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar la cuenta contable: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CuentaContable $cuenta)
    {
        try {
            DB::beginTransaction();

            // Verificar si tiene movimientos
            if ($cuenta->detallesAsiento()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar la cuenta porque tiene movimientos.');
            }

            // Verificar si tiene cuentas hijas
            if ($cuenta->cuentasHijas()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar la cuenta porque tiene cuentas hijas.');
            }

            $cuenta->delete();

            DB::commit();

            return redirect()->route('contabilidad.cuentas.index')
                ->with('success', 'Cuenta contable eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar la cuenta contable: ' . $e->getMessage());
        }
    }
}
