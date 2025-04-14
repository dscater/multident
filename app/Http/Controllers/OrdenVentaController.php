<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrdenVentaStoreRequest;
use App\Http\Requests\OrdenVentaUpdateEstadoRequest;
use App\Http\Requests\OrdenVentaUpdateRequest;
use App\Models\OrdenVenta;
use App\Services\NotificacionUserService;
use App\Services\OrdenVentaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class OrdenVentaController extends Controller
{
    public function __construct(
        private OrdenVentaService $ordenVentaService,
        private NotificacionUserService $notificacionUserService
    ) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(Request $request): InertiaResponse
    {
        $codigo = $request->cod;

        if (trim($codigo) != '') {
            $this->notificacionUserService->verificarNotificacionUser(trim($codigo), "OrdenVenta");
        }

        return Inertia::render("Admin/OrdenVentas/Index", compact("codigo"));
    }

    /**
     * Listado de ordenVentas
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "ordenVentas" => $this->ordenVentaService->listado()
        ]);
    }

    /**
     * Listado de ordenVentas para portal
     *
     * @return JsonResponse
     */
    public function listadoPortal(): JsonResponse
    {
        return response()->JSON([
            "ordenVentas" => $this->ordenVentaService->listado()
        ]);
    }


    public function ordenesCliente(Request $request): JsonResponse
    {
        $perPage = $request->perPage;
        $page = (int)($request->input("page", 1));
        $search = (string)$request->input("search", "");
        $orderByCol = $request->orderByCol;
        $desc = $request->desc;

        $columnsSerachLike = ["nombre", "descripcion"];
        $columnsFilter = [];
        $arrayOrderBy = [];
        if ($orderByCol && $desc) {
            $arrayOrderBy = [
                [$orderByCol, $desc]
            ];
        }

        $cliente_id = (int)(Auth::user()->cliente ? Auth::user()->cliente->id : 0);

        $ordenVentas =  $this->ordenVentaService->listadoPaginadoCliente($cliente_id, $perPage, $page, $search, $columnsSerachLike, $columnsFilter, [], $arrayOrderBy);

        return response()->JSON([
            "total" => $ordenVentas->total(),
            "ordenVentas" => $ordenVentas->items(),
            "lastPage" => $ordenVentas->lastPage()
        ]);
    }

    /**
     * Endpoint para obtener la lista de ordenVentas paginado para datatable
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

        $usuarios = $this->ordenVentaService->listadoPaginado($length, $page, $search);

        return response()->JSON([
            'data' => $usuarios->items(),
            'recordsTotal' => $usuarios->total(),
            'recordsFiltered' => $usuarios->total(),
            'draw' => intval($request->input('draw')),
        ]);
    }

    /**
     * Endpoint para obtener la lista de ordenVentas paginado
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function paginado(Request $request): JsonResponse
    {

        $perPage = $request->perPage;
        $page = (int)($request->input("page", 1));
        $search = (string)$request->input("search", "");
        $orderByCol = $request->orderByCol;
        $desc = $request->desc;
        $estado_orden = $request->estado_orden;
        $fecha_orden = $request->fecha;

        $columnsSerachLike = ["codigo"];
        $columnsFilter = [];

        if ($estado_orden) {
            $columnsFilter["estado_orden"]  = $estado_orden;
        }

        if ($fecha_orden) {
            $columnsFilter["fecha_orden"]  = $fecha_orden;
        }

        $arrayOrderBy = [];
        if ($orderByCol && $desc) {
            $arrayOrderBy = [
                [$orderByCol, $desc]
            ];
        }

        $ordenVentas =  $this->ordenVentaService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, [], $arrayOrderBy);

        return response()->JSON([
            "total" => $ordenVentas->total(),
            "ordenVentas" => $ordenVentas->items(),
            "lastPage" => $ordenVentas->lastPage()
        ]);
    }

    /**
     * Registrar un nuevo ordenVenta
     *
     * @param OrdenVentaStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(OrdenVentaStoreRequest $request): RedirectResponse|Response|JsonResponse
    {
        DB::beginTransaction();
        try {
            // crear el OrdenVenta
            $this->ordenVentaService->crear($request->validated());
            DB::commit();

            $mensaje = 'Tu compra fue registrada correctamente.<br/> Puedes realizar su seguimiento en la sección de "Mis compras"';
            session()->flash("bien", $mensaje);
            if ($request->ajax()) {
                return response()->JSON([
                    "sw" => true,
                    "message" => $mensaje
                ]);
            }

            return redirect()->route("ordenVentas.index");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un ordenVenta
     *
     * @param OrdenVenta $ordenVenta
     * @return JsonResponse
     */
    public function show(OrdenVenta $ordenVenta): JsonResponse
    {
        return response()->JSON($ordenVenta);
    }

    public function update(OrdenVenta $ordenVenta, OrdenVentaUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar ordenVenta
            $this->ordenVentaService->actualizar($request->validated(), $ordenVenta);
            DB::commit();
            return redirect()->route("ordenVentas.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Actualizar estado de orden de venta
     *
     * @param OrdenVenta $ordenVenta
     * @param OrdenVentaUpdateEstadoRequest $request
     * @return JsonResponse|Response
     */
    public function update_estado(OrdenVenta $ordenVenta, OrdenVentaUpdateEstadoRequest $request): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            // actualizar ordenVenta
            $this->ordenVentaService->actualizarEstado($ordenVenta, $request->validated());
            DB::commit();
            session()->flash("bien", "Registro actualizado correctamente");
            return response()->JSON([
                "message" => "Registro actualizado correctamente"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar ordenVenta
     *
     * @param OrdenVenta $ordenVenta
     * @return JsonResponse|Response
     */
    public function destroy(OrdenVenta $ordenVenta): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->ordenVentaService->eliminar($ordenVenta);
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
