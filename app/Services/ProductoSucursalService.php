<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\ProductoSucursal;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ProductoSucursalService
{
    /**
     * Incrementar el stock del producto sucursal
     *
     * @param Producto $producto
     * @param float $cantidad
     * @param integer $sucursal_id
     * @return void
     */
    public function incrementarStock(Producto $producto, float $cantidad, int $sucursal_id): void
    {
        $sucursal_producto = ProductoSucursal::where("producto_id", $producto->id)
            ->where("sucursal_id", $sucursal_id)
            ->get()->first();
        if (!$sucursal_producto) {
            $producto->producto_sucursals()->create([
                "sucursal_id" => $sucursal_id,
                "stock_actual" => $cantidad,
            ]);
        } else {
            $sucursal_producto->stock_actual = (float)$sucursal_producto->stock_actual + $cantidad;
            $sucursal_producto->save();
        }
    }


    /**
     * Decrementar el stock de un producto sucursal
     *
     * @param Producto $producto
     * @param float $cantidad
     * @param integer $sucursal_id
     * @return void
     */
    public function decrementarStock(Producto $producto, float $cantidad, int $sucursal_id): void
    {
        $sucursal_producto = ProductoSucursal::where("producto_id", $producto->id)
            ->where("sucursal_id", $sucursal_id)
            ->get()->first();
        if ($sucursal_producto) {
            $sucursal_producto->stock_actual = (float)$sucursal_producto->stock_actual - $cantidad;
            $sucursal_producto->save();
        }
    }
}
