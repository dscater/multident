<?php

namespace App\Services;

use App\Models\Producto;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class DetalleVentaService
{

    /**
     * Descontar stock de productos
     *
     * @param array $detallVenta
     * @return void
     */
    public function descuentaStockProductos(Collection $detallVenta): void
    {
        foreach ($detallVenta as $item) {
            $producto = Producto::findOrFail($item->producto_id);
            if ($producto->stock_actual < $item->cantidad) {
                throw new Exception("El stock actual del producto " . $producto->nombre . " es insuficiente.<br/> Stock actual: " . $producto->stock_actual);
            }
            $producto->stock_actual = (int)$producto->stock_actual - $item->cantidad;
            $producto->save();
        }
    }
}
