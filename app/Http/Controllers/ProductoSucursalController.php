<?php

namespace App\Http\Controllers;

use App\Services\ProductoSucursalService;
use App\Services\PromocionService;
use App\Services\UbicacionProductoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Client\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;

class ProductoSucursalController extends Controller
{
    public function __construct(private ProductoSucursalService $productoSucursalService, private PromocionService $promocionService, private UbicacionProductoService $ubicacionProductoService) {}

    /**
     * Página index
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        return Inertia::render("Admin/ProductoSucursals/Index");
    }

    /**
     * Lista de registros
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "producto_sucursals" => $this->productoSucursalService->listado()
        ]);
    }

    /**
     * Endpoint para obtener la lista de producto_sucursals paginado para data table
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
        $columnsSerachLike = ["nombre"];
        if ($request->sucursal_id) {

            $producto_sucursals = $this->productoSucursalService->listadoPaginado($length, $page, $search, $columnsSerachLike, [], [], [], $request->sucursal_id ?? 0);
        } else {
            $producto_sucursals = new LengthAwarePaginator(
                collect([]), // items
                0,           // total
                $length,     // per page
                $page        // current page
            );
        }

        return response()->JSON([
            'data' => $producto_sucursals->items(),
            'recordsTotal' => $producto_sucursals->total(),
            'recordsFiltered' => $producto_sucursals->total(),
            'draw' => intval($request->input('draw')),
        ]);
    }


    /**
     * Buscar un solo producto por Sucursal
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function getProductoSucursal(Request $request): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            return response()->JSON([
                "producto_sucursal" => $this->productoSucursalService->getProductoSucursal((int)$request["producto_id"], (int)$request["sucursal_id"]),
                "promocion" => $this->promocionService->verificaPromocion(date("Y-m-d"), (int)$request["producto_id"]),
                "ingreso_detalles_ubicacion" => $this->ubicacionProductoService->getUbicacionProductosSucursal((int)$request["sucursal_id"], (int)$request["producto_id"]) // para mostrar la ubicación de producots
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Buscar un producto por sucursales
     *
     * @return void
     */
    public function getProductoSucursales(Request $request)
    {
        $search = $request->input("search", "");

        return response()->JSON([
            "producto_sucursals" => trim($search) ? $this->productoSucursalService->buscarProductoSucursales($search ?? "", true) : []
        ]);
    }
}
