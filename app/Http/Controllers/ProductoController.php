<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoStoreRequest;
use App\Http\Requests\ProductoUpdateRequest;
use App\Models\Producto;
use App\Services\ProductoService;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ProductoController extends Controller
{
    public function __construct(private ProductoService $productoService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): InertiaResponse
    {
        return Inertia::render("Admin/Productos/Index");
    }

    /**
     * Lista de productos a excepcion de producto_id
     *
     * @return Response
     */
    public function listadoSinProducto(Request $request): JsonResponse
    {
        return response()->JSON([
            "productos" => $this->productoService->listadoSinProducto($request->producto_id ?? 0)
        ]);
    }


    /**
     * Listado de productos
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "productos" => $this->productoService->listado()
        ]);
    }

    /**
     * Endpoint para obtener la lista de productos paginado para data table
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function api(Request $request): JsonResponse
    {
        $length = (int)$request->input('length', 10); // Valor de `length` enviado por DataTable
        $start = (int)$request->input('start', 0); // Índice de inicio enviado por DataTable
        $page = (int)(($start / $length) + 1); // Cálculo de la página actual
        $search = (string)$request->input('search', '');

        $columnsSerachLike = [
            "nombre",
            "descripcion",
            "fecha_registro"
        ];

        $productos = $this->productoService->listadoPaginado($length, $page, $search, $columnsSerachLike);

        return response()->JSON([
            'data' => $productos->items(),
            'recordsTotal' => $productos->total(),
            'recordsFiltered' => $productos->total(),
            'draw' => intval($request->input('draw')),
        ]);
    }

    /**
     * Registrar un nuevo producto
     *
     * @param ProductoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(ProductoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Producto
            $this->productoService->crear($request->validated());
            DB::commit();
            return redirect()->route("productos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un producto
     *
     * @param Producto $producto
     * @return JsonResponse
     */
    public function show(Producto $producto): JsonResponse
    {
        return response()->JSON($producto);
    }

    public function update(Producto $producto, ProductoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar producto
            $this->productoService->actualizar($request->validated(), $producto);
            DB::commit();
            return redirect()->route("productos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar producto
     *
     * @param Producto $producto
     * @return JsonResponse|Response
     */
    public function destroy(Producto $producto): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->productoService->eliminar($producto);
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'message' => 'El registro se eliminó correctamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }
}
