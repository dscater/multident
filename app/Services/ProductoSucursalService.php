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
     * @return App\Models\ProductoSucursal
     */
    public function incrementarStock(Producto $producto, float $cantidad, int $sucursal_id): ProductoSucursal
    {
        $sucursal_producto = ProductoSucursal::where("producto_id", $producto->id)
            ->where("sucursal_id", $sucursal_id)
            ->get()->first();
        if (!$sucursal_producto) {
            $sucursal_producto = $producto->producto_sucursals()->create([
                "sucursal_id" => $sucursal_id,
                "stock_actual" => $cantidad,
            ]);
        } else {
            $sucursal_producto->stock_actual = (float)$sucursal_producto->stock_actual + $cantidad;
            $sucursal_producto->save();
        }

        return $sucursal_producto;
    }

    /**
     * Decrementar el stock de un producto sucursal
     *
     * @param Producto $producto
     * @param float $cantidad
     * @param integer $sucursal_id
     * @return App\Models\ProductoSucursal
     */
    public function decrementarStock(Producto $producto, float $cantidad, int $sucursal_id): ProductoSucursal|null
    {
        $sucursal_producto = ProductoSucursal::where("producto_id", $producto->id)
            ->where("sucursal_id", $sucursal_id)
            ->get()->first();
        if ($sucursal_producto) {
            $sucursal_producto->stock_actual = (float)$sucursal_producto->stock_actual - $cantidad;
            $sucursal_producto->save();
        }

        return $sucursal_producto;
    }

    /**
     * Verificar el stock del producto
     *
     * @param integer $producto_id
     * @param integer $sucursal_id
     * @param float $cantidad
     * @return array[bool,float]
     */
    public function verificaStockSucursalProducto(int $producto_id, int $sucursal_id, float $cantidad): array
    {
        $resultado = [false, 0];
        $producto_sucursal = ProductoSucursal::where("producto_id", $producto_id)
            ->where("sucursal_id", $sucursal_id)
            ->get()->first();
        if ($producto_sucursal) {
            $stock_actual = (float)$producto_sucursal->stock_actual;
            $resultado[1] = $stock_actual;
            if ($stock_actual >= $cantidad) {
                $resultado[0] = true;
            }
        }

        return $resultado;
    }

    /**
     * Undocumented function
     *
     * @param integer $producto_id
     * @param integer $sucursal_id
     * @return ProductoSucursal
     */
    public function getProductoSucursal(int $producto_id, int $sucursal_id): ProductoSucursal
    {
        $producto_sucursal = ProductoSucursal::where("producto_id", $producto_id)
            ->where("sucursal_id", $sucursal_id)
            ->get()->first();

        if (!$producto_sucursal) {
            if ($producto_id == 0 || $sucursal_id == 0) {
                throw new Exception("Debes seleccionar la sucursal y el producto");
            }
            $producto_sucursal = ProductoSucursal::create([
                "sucursal_id" => $sucursal_id,
                "producto_id" => $producto_id,
                "stock_actual" => 0,
            ]);
        }

        return $producto_sucursal->load(["producto"]);
    }
}
