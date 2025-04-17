<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\ProductoSucursal;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class ProductoSucursalService
{
    public function __construct(private HistorialAccionService $historialAccionService) {}

    /**
     * Obtener el stock por producto y sucursal
     *
     * @param integer $producto_id
     * @param integer $sucursal_id
     * @return ProductoSucursal
     */
    public function stockPorProductoSucursal(int $producto_id, int $sucursal_id): ProductoSucursal
    {
        $producto_sucursal = ProductoSucursal::with(["producto"])->where("sucursal_id", $sucursal_id)
            ->where("producto_id", $producto_id)->get()->first();
        return $producto_sucursal;
    }
}
