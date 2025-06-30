<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    /**
     * Constructor del controlador
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:ver-productos')->only(['index', 'show']);
        $this->middleware('can:crear-productos')->only(['create', 'store']);
        $this->middleware('can:editar-productos')->only(['edit', 'update']);
        $this->middleware('can:eliminar-productos')->only('destroy');
    }

    /**
     * Muestra el listado de productos con filtros y paginación
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Producto::query();

        // Búsqueda por nombre
        if ($request->has('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        // Filtro por tipo
        if ($request->has('tipo') && in_array($request->tipo, ['producido', 'comprado'])) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por stock bajo
        if ($request->has('stock_bajo')) {
            $query->whereRaw('stock <= stock_minimo');
        }

        // Ordenamiento
        $sortField = $request->get('sort', 'nombre');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Obtener productos sin caché
        $productos = $query->paginate(10);

        return view('productos.index', compact('productos'));
    }

    /**
     * Muestra el formulario para crear un nuevo producto
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('productos.create');
    }

    /**
     * Almacena un nuevo producto
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:productos',
            'tipo' => 'required|in:producido,comprado',
            'precio_venta' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'stock' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $producto = Producto::create($request->all());

            DB::commit();

            // Limpiar todo el caché de productos
            Cache::flush();

            return redirect()->route('productos.index')
                ->with('success', 'Producto creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    /**
     * Muestra los detalles de un producto
     *
     * @param Producto $producto
     * @return \Illuminate\View\View
     */
    public function show(Producto $producto)
    {
        $producto->load(['ventas' => function ($query) {
            $query->latest()->take(5);
        }]);

        return view('productos.show', compact('producto'));
    }

    /**
     * Muestra el formulario para editar un producto
     *
     * @param Producto $producto
     * @return \Illuminate\View\View
     */
    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    /**
     * Actualiza un producto existente
     *
     * @param Request $request
     * @param Producto $producto
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:productos,nombre,' . $producto->id,
            'tipo' => 'required|in:producido,comprado',
            'precio_venta' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'stock' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $producto->update($request->all());

            DB::commit();

            // Limpiar caché
            Cache::forget('productos.page.' . $request->get('page', 1));

            return redirect()->route('productos.index')
                ->with('success', 'Producto actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un producto
     *
     * @param Producto $producto
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Producto $producto)
    {
        try {
            DB::beginTransaction();

            // Verificar si el producto tiene registros relacionados
            if ($producto->ventas()->exists()) {
                return redirect()->route('productos.index')
                    ->with('error', 'No se puede eliminar el producto porque tiene ventas asociadas');
            }

            $producto->delete();

            DB::commit();

            // Limpiar caché
            Cache::forget('productos.page.' . request()->get('page', 1));

            return redirect()->route('productos.index')
                ->with('success', 'Producto eliminado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('productos.index')
                ->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }
}