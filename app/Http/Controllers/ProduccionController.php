<?php

namespace App\Http\Controllers;

use App\Models\Produccion;
use App\Models\MateriaPrimaMolida;
use App\Models\Producto;
use App\Services\ContabilidadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduccionController extends Controller
{
    protected $contabilidadService;

    public function __construct(ContabilidadService $contabilidadService)
    {
        $this->contabilidadService = $contabilidadService;
    }

    public function index()
    {
        $producciones = Produccion::orderBy('fecha', 'desc')->paginate(10);
            
        return view('produccion.index', compact('producciones'));
    }

    public function create()
    {
        $materiasPrimas = MateriaPrimaMolida::where('cantidad', '>', 0)->get();
        $productos = Producto::where('tipo', 'producido')->get();
        return view('produccion.create', compact('materiasPrimas', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'materia_prima_molida_id' => 'required|exists:materia_prima_molida,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad_utilizada' => 'required|numeric|min:0.01',
            'cantidad_producida' => 'required|numeric|min:0.01',
            'fecha' => 'required|date',
            'costo_adicional' => 'nullable|numeric|min:0'
        ]);

        DB::transaction(function () use ($request) {
            $materiaPrima = MateriaPrimaMolida::findOrFail($request->materia_prima_molida_id);

            if ($materiaPrima->cantidad < $request->cantidad_utilizada) {
                throw new \Exception("No hay suficiente materia prima molida disponible");
            }

            $costoProduccion = ($request->cantidad_utilizada * 0) + ($request->costo_adicional ?? 0); // Ajusta el costo si tienes campo de costo promedio

            $produccion = Produccion::create([
                'materia_prima_molida_id' => $request->materia_prima_molida_id,
                'producto_id' => $request->producto_id,
                'cantidad_utilizada' => $request->cantidad_utilizada,
                'cantidad_producida' => $request->cantidad_producida,
                'costo_produccion' => $costoProduccion,
                'costo_unitario' => $costoProduccion / $request->cantidad_producida,
                'fecha' => $request->fecha,
                'observaciones' => $request->observaciones
            ]);

            // Actualizar stocks
            $materiaPrima->decrement('cantidad', $request->cantidad_utilizada);

            $producto = Producto::findOrFail($request->producto_id);
            $producto->increment('stock', $request->cantidad_producida);
            
            // Actualizar costo promedio del producto
            $nuevoCostoPromedio = $this->calcularCostoPromedio($producto);
            $producto->update(['costo_promedio' => $nuevoCostoPromedio]);

            // Generar asiento contable para la producción
            $this->contabilidadService->generarAsientoProduccion($produccion);
        });

        return redirect()->route('produccion.index')->with('success', 'Producción registrada correctamente');
    }

    public function show(Produccion $produccion)
    {
        $produccion->load(['materiaPrimaMolida', 'producto']);
        return view('produccion.show', compact('produccion'));
    }

    public function edit(Produccion $produccion)
    {
        if ($produccion->fecha < now()->subDays(30)) {
            return redirect()->route('produccion.index')->with('error', 'No se pueden editar producciones con más de 30 días');
        }

        $materiasPrimas = MateriaPrimaMolida::where('cantidad', '>', 0)->get();
        $productos = Producto::where('tipo', 'producido')->get();
        
        return view('produccion.edit', compact('produccion', 'materiasPrimas', 'productos'));
    }

    public function update(Request $request, Produccion $produccion)
    {
        $request->validate([
            'materia_prima_molida_id' => 'required|exists:materia_prima_molida,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad_utilizada' => 'required|numeric|min:0.01',
            'cantidad_producida' => 'required|numeric|min:0.01',
            'fecha' => 'required|date',
            'costo_adicional' => 'nullable|numeric|min:0'
        ]);

        DB::transaction(function () use ($request, $produccion) {
            // Revertir stocks anteriores
            $materiaPrimaAnterior = MateriaPrimaMolida::find($produccion->materia_prima_molida_id);
            $materiaPrimaAnterior->increment('cantidad', $produccion->cantidad_utilizada);
            
            $productoAnterior = Producto::find($produccion->producto_id);
            $productoAnterior->decrement('stock', $produccion->cantidad_producida);
            
            // Obtener nueva materia prima
            $materiaPrima = MateriaPrimaMolida::findOrFail($request->materia_prima_molida_id);
            
            if ($materiaPrima->cantidad < $request->cantidad_utilizada) {
                throw new \Exception("No hay suficiente materia prima molida disponible");
            }
            
            $costoProduccion = ($request->cantidad_utilizada * 0) + ($request->costo_adicional ?? 0); // Ajusta el costo si tienes campo de costo promedio
            
            // Actualizar producción
            $produccion->update([
                'materia_prima_molida_id' => $request->materia_prima_molida_id,
                'producto_id' => $request->producto_id,
                'cantidad_utilizada' => $request->cantidad_utilizada,
                'cantidad_producida' => $request->cantidad_producida,
                'costo_produccion' => $costoProduccion,
                'costo_unitario' => $costoProduccion / $request->cantidad_producida,
                'fecha' => $request->fecha,
                'observaciones' => $request->observaciones
            ]);
            
            // Actualizar stocks
            $materiaPrima->decrement('cantidad', $request->cantidad_utilizada);
            
            $producto = Producto::findOrFail($request->producto_id);
            $producto->increment('stock', $request->cantidad_producida);
            
            // Recalcular costos promedios para ambos productos (anterior y nuevo)
            $this->calcularCostoPromedio($productoAnterior);
            $this->calcularCostoPromedio($producto);

            // Generar nuevo asiento contable
            $this->contabilidadService->generarAsientoProduccion($produccion);
        });
        
        return redirect()->route('produccion.show', $produccion)->with('success', 'Producción actualizada correctamente');
    }

    public function destroy(Produccion $produccion)
    {
        DB::transaction(function () use ($produccion) {
            // Revertir stocks
            $materiaPrima = MateriaPrimaMolida::find($produccion->materia_prima_molida_id);
            $materiaPrima->increment('cantidad', $produccion->cantidad_utilizada);
            
            $producto = Producto::find($produccion->producto_id);
            $producto->decrement('stock', $produccion->cantidad_producida);
            
            // Recalcular costo promedio del producto
            $this->calcularCostoPromedio($producto);
            
            $produccion->delete();
        });
        
        return redirect()->route('produccion.index')->with('success', 'Producción eliminada correctamente');
    }
    
    private function calcularCostoPromedio(Producto $producto)
    {
        $producciones = Produccion::where('producto_id', $producto->id)->get();
        
        if ($producciones->isEmpty()) {
            return 0;
        }
        
        $totalCosto = $producciones->sum('costo_produccion');
        $totalUnidades = $producciones->sum('cantidad_producida');
        
        return $totalCosto / $totalUnidades;
    }
}