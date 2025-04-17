<?php

namespace App\Http\Controllers;

use App\Services\ProductoSucursalService;
use Illuminate\Http\Request;

class ProductoSucursalController extends Controller
{
    public function __construct(private ProductoSucursalService $productoSucursalService) {}
    public function getStockProductoSucursal(Request $request)
    {
        return response()->JSON([
            "producto_sucursal" => $this->productoSucursalService->stockPorProductoSucursal((int)$request["producto_id"], (int)$request["sucursal_id"])
        ]);
    }
}
