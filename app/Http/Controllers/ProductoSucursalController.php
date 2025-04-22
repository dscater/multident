<?php

namespace App\Http\Controllers;

use App\Services\ProductoSucursalService;
use App\Services\PromocionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductoSucursalController extends Controller
{
    public function __construct(private ProductoSucursalService $productoSucursalService, private PromocionService $promocionService) {}
    public function getProductoSucursal(Request $request)
    {
        DB::beginTransaction();
        try {
            return response()->JSON([
                "producto_sucursal" => $this->productoSucursalService->getProductoSucursal((int)$request["producto_id"], (int)$request["sucursal_id"]),
                "promocion" => $this->promocionService->verificaPromocion(date("Y-m-d"), (int)$request["producto_id"])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }
}
