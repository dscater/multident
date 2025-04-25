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

class ProductoSucursalController extends Controller
{
    public function __construct(private ProductoSucursalService $productoSucursalService, private PromocionService $promocionService, private UbicacionProductoService $ubicacionProductoService) {}

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
