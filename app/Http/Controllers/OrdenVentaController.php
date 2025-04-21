<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrdenVentaStoreRequest;
use App\Http\Requests\OrdenVentaUpdateRequest;
use App\Models\OrdenVenta;
use App\Services\HistorialAccionService;
use App\Services\OrdenVentaService;
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

class OrdenVentaController extends Controller
{
    public function __construct(private HistorialAccionService $historialAccionService, private OrdenVentaService $ordenVentaService) {}

    /**
     * Página index
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        return Inertia::render("Admin/OrdenVentas/Index");
    }

    /**
     * Lista de registros
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "orden_ventas" => $this->ordenVentaService->listado()
        ]);
    }

    /**
     * Endpoint para obtener la lista de orden_ventas paginado para data table
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

        $orden_ventas = $this->ordenVentaService->listadoPaginado($length, $page, $search);

        return response()->JSON([
            'data' => $orden_ventas->items(),
            'recordsTotal' => $orden_ventas->total(),
            'recordsFiltered' => $orden_ventas->total(),
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
        return Inertia::render("Admin/OrdenVentas/Create");
    }

    /**
     * Registrar un nuevo ingreso de producto
     *
     * @param ProductoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(OrdenVentaStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el OrdenVenta
            $this->ordenVentaService->crear($request->validated());
            DB::commit();
            return redirect()->route("orden_ventas.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function show(OrdenVenta $orden_venta) {}


    /**
     * Página edit
     *
     * @return InertiaResponse
     */
    public function edit(OrdenVenta $orden_venta): InertiaResponse
    {
        $orden_venta = $orden_venta->load(["ingreso_detalles.producto"]);
        return Inertia::render("Admin/OrdenVentas/Edit", compact('orden_venta'));
    }

    public function update(OrdenVenta $orden_venta, OrdenVentaUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar el OrdenVenta
            $this->ordenVentaService->actualizar($request->validated(), $orden_venta);

            DB::commit();
            return redirect()->route("orden_ventas.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }
    public function destroy(OrdenVenta $orden_venta)
    {
        DB::beginTransaction();
        try {
            // eliminar OrdenVenta
            $this->ordenVentaService->eliminar($orden_venta);
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
