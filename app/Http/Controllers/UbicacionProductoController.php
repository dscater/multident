<?php

namespace App\Http\Controllers;

use App\Http\Requests\UbicacionProductoStoreRequest;
use App\Http\Requests\UbicacionProductoUpdateRequest;
use App\Models\UbicacionProducto;
use App\Services\UbicacionProductoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class UbicacionProductoController extends Controller
{
    public function __construct(private UbicacionProductoService $ubicacionProductoService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): InertiaResponse
    {
        return Inertia::render("Admin/UbicacionProductos/Index");
    }

    /**
     * Listado de ubicacion_productos
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "ubicacion_productos" => $this->ubicacionProductoService->listado()
        ]);
    }

    /**
     * Listado de ubicacion_productos para portal
     *
     * @return JsonResponse
     */
    public function listadoPortal(): JsonResponse
    {
        return response()->JSON([
            "ubicacion_productos" => $this->ubicacionProductoService->listado()
        ]);
    }

    /**
     * Endpoint para obtener la lista de ubicacion_productos paginado para datatable
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

        $usuarios = $this->ubicacionProductoService->listadoDataTable($length, $start, $page, $search);

        return response()->JSON([
            'data' => $usuarios->items(),
            'recordsTotal' => $usuarios->total(),
            'recordsFiltered' => $usuarios->total(),
            'draw' => intval($request->input('draw')),
        ]);
    }

    /**
     * Registrar un nuevo ubicacion_producto
     *
     * @param UbicacionProductoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(UbicacionProductoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el UbicacionProducto
            $this->ubicacionProductoService->crear($request->validated());
            DB::commit();
            return redirect()->route("ubicacion_productos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un ubicacion_producto
     *
     * @param UbicacionProducto $ubicacion_producto
     * @return JsonResponse
     */
    public function show(UbicacionProducto $ubicacion_producto): JsonResponse
    {
        return response()->JSON($ubicacion_producto);
    }

    public function update(UbicacionProducto $ubicacion_producto, UbicacionProductoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar ubicacion_producto
            $this->ubicacionProductoService->actualizar($request->validated(), $ubicacion_producto);
            DB::commit();
            return redirect()->route("ubicacion_productos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar ubicacion_producto
     *
     * @param UbicacionProducto $ubicacion_producto
     * @return JsonResponse|Response
     */
    public function destroy(UbicacionProducto $ubicacion_producto): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->ubicacionProductoService->eliminar($ubicacion_producto);
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
