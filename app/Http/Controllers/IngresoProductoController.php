<?php

namespace App\Http\Controllers;

use App\Http\Requests\IngresoProductoStoreRequest;
use App\Http\Requests\IngresoProductoUpdateRequest;
use App\Models\HistorialAccion;
use App\Models\IngresoProducto;
use App\Models\KardexProducto;
use App\Models\Producto;
use App\Services\HistorialAccionService;
use App\Services\IngresoProductoService;
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

class IngresoProductoController extends Controller
{
    public function __construct(private HistorialAccionService $historialAccionService, private IngresoProductoService $ingresoProductoService) {}

    /**
     * Página index
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        return Inertia::render("Admin/IngresoProductos/Index");
    }

    /**
     * Lista de registros
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "ingreso_productos" => $this->ingresoProductoService->listado()
        ]);
    }

    /**
     * Endpoint para obtener la lista de ingreso_productos paginado para data table
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

        $ingreso_productos = $this->ingresoProductoService->listadoPaginado($length, $page, $search);

        return response()->JSON([
            'data' => $ingreso_productos->items(),
            'recordsTotal' => $ingreso_productos->total(),
            'recordsFiltered' => $ingreso_productos->total(),
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
        return Inertia::render("Admin/IngresoProductos/Create");
    }

    /**
     * Registrar un nuevo ingreso de producto
     *
     * @param ProductoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(IngresoProductoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el IngresoProducto
            $this->ingresoProductoService->crear($request->validated());
            DB::commit();
            return redirect()->route("ingreso_productos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function show(IngresoProducto $ingreso_producto) {}


    /**
     * Página edit
     *
     * @return InertiaResponse
     */
    public function edit(IngresoProducto $ingreso_producto): InertiaResponse
    {
        $ingreso_producto = $ingreso_producto->load(["ingreso_detalles.producto"]);
        return Inertia::render("Admin/IngresoProductos/Edit", compact('ingreso_producto'));
    }

    public function update(IngresoProducto $ingreso_producto, IngresoProductoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar el IngresoProducto
            $this->ingresoProductoService->actualizar($request->validated(), $ingreso_producto);

            DB::commit();
            return redirect()->route("ingreso_productos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }
    public function destroy(IngresoProducto $ingreso_producto)
    {
        DB::beginTransaction();
        try {
            // eliminar IngresoProducto
            $this->ingresoProductoService->eliminar($ingreso_producto);
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
