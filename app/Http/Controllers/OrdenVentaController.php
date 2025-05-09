<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrdenVentaStoreRequest;
use App\Http\Requests\OrdenVentaUpdateRequest;
use App\Models\DetalleOrden;
use App\Models\Devolucion;
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
    public function listado(Request $request): JsonResponse
    {
        $sucursal_id = isset($request->sucursal_id) && $request->sucursal_id ? $request->sucursal_id : 0;
        return response()->JSON([
            "orden_ventas" => $this->ordenVentaService->listado($sucursal_id)
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

        $orderBy = [
            ["orden_ventas.id", "desc"],
        ];

        $orden_ventas = $this->ordenVentaService->listadoPaginado($length, $page, $search, [], [], [], $orderBy);

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
            // Log::debug($request->validated());
            $orden_venta = $this->ordenVentaService->crear($request->validated());

            // Guardar el ID en sesión
            session(['venta_id' => $orden_venta->id]);

            DB::commit();
            return redirect()->route("orden_ventas.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function show(OrdenVenta $orden_venta, Request $request)
    {
        $detalle_ordens = [];
        if (isset($request->detalle_orden_id)) {
            $detalle_ordens = DetalleOrden::with(["producto"])->where("status", 1)
                ->where("orden_venta_id", $orden_venta->id)
                ->orWhere("id", $request->detalle_orden_id)
                ->get();
        } else {
            $detalle_ordens = DetalleOrden::with(["producto"])->where("status", 1)
                ->where("orden_venta_id", $orden_venta->id)
                ->get();
        }

        return response()->JSON([
            "orden_venta" => $orden_venta->load(["detalle_ordens.producto"]),
            "detalle_ordens" => $detalle_ordens,
        ]);
    }

    public function generarPdf(OrdenVenta $orden_venta)
    {
        return $this->ordenVentaService->generarPdf($orden_venta);
    }


    /**
     * Página edit
     *
     * @return InertiaResponse
     */
    public function edit(OrdenVenta $orden_venta): InertiaResponse
    {
        $orden_venta = $orden_venta->load(["detalle_ordens.producto"]);
        return Inertia::render("Admin/OrdenVentas/Edit", compact('orden_venta'));
    }

    public function update(OrdenVenta $orden_venta, OrdenVentaUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar el OrdenVenta
            $orden_venta = $this->ordenVentaService->actualizar($request->validated(), $orden_venta);
            // Guardar el ID en sesión
            session(['venta_id' => $orden_venta->id]);

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
