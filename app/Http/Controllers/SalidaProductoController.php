<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalidaProductoStoreRequest;
use App\Http\Requests\SalidaProductoUpdateRequest;
use App\Models\HistorialAccion;
use App\Models\SalidaProducto;
use App\Models\KardexProducto;
use App\Models\Producto;
use App\Services\HistorialAccionService;
use App\Services\SalidaProductoService;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;

class SalidaProductoController extends Controller
{
    public function __construct(private HistorialAccionService $historialAccionService, private SalidaProductoService $salidaProductoService) {}

    /**
     * Página index
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        return Inertia::render("Admin/SalidaProductos/Index");
    }

    /**
     * Lista de registros
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "salida_productos" => $this->salidaProductoService->listado()
        ]);
    }

    /**
     * Endpoint para obtener la lista de salida_productos paginado para data table
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

        $salida_productos = $this->salidaProductoService->listadoPaginado($length, $page, $search);

        return response()->JSON([
            'data' => $salida_productos->items(),
            'recordsTotal' => $salida_productos->total(),
            'recordsFiltered' => $salida_productos->total(),
            'draw' => intval($request->input('draw')),
        ]);
    }


    /**
     * Página create
     *
     * @return InertiaResponse
     */
    public function create(): InertiaResponse
    {
        return Inertia::render("Admin/SalidaProductos/Create");
    }

    /**
     * Registrar un nuevo salida de producto
     *
     * @param ProductoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(SalidaProductoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el SalidaProducto
            $this->salidaProductoService->crear($request->validated());
            DB::commit();
            return redirect()->route("salida_productos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function show(SalidaProducto $salida_producto) {}


    /**
     * Página edit
     *
     * @return InertiaResponse
     */
    public function edit(SalidaProducto $salida_producto): InertiaResponse
    {
        $salida_producto = $salida_producto->load(["producto"]);
        return Inertia::render("Admin/SalidaProductos/Edit", compact('salida_producto'));
    }

    public function update(SalidaProducto $salida_producto, SalidaProductoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar el SalidaProducto
            $this->salidaProductoService->actualizar($request->validated(), $salida_producto);

            DB::commit();
            return redirect()->route("salida_productos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }
    public function destroy(SalidaProducto $salida_producto)
    {
        DB::beginTransaction();
        try {
            // eliminar SalidaProducto
            $this->salidaProductoService->eliminar($salida_producto);
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
