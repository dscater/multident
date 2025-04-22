<?php

namespace App\Services;

use App\Models\DetalleOrden;
use App\Models\OrdenVenta;
use App\Models\Producto;
use App\Models\Sucursal;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class DetalleOrdenService
{
    public function __construct(private ProductoSucursalService $productoSucursalService, private KardexProductoService $kardexProductoService) {}

    /**
     * Guardar detalle de venta (detalle_ordens)
     *
     * @param OrdenVenta $ordenVenta
     * @param array $detalle_ordens
     * @return void
     */
    public function registrarDetalle(OrdenVenta $ordenVenta, array $detalle_ordens, int $sucursal_id, int $old_sucursal = 0): void
    {
        $sucursal = Sucursal::findOrFail($sucursal_id);
        if (!$sucursal) {
            throw new Exception("Ocurrió un error, no se encontró la sucursal");
        }

        foreach ($detalle_ordens as $item) {
            $arraProd = $item;
            $cantidad = (float)$item["cantidad"];
            $producto = Producto::findOrFail($item["producto_id"]);

            $datos = [
                "producto_id" => $producto->id,
                "promocion_id" => $arraProd["promocion_id"],
                "promocion_descuento" => $arraProd["promocion_descuento"],
                "cantidad" => $cantidad,
                "precio_reg" => $arraProd["precio_reg"],
                "precio" => $arraProd["precio"],
                "subtotal" => $arraProd["subtotal"],
            ];

            if ($item["id"] == 0) {
                // nuevo 
                //validar stock
                $resStock = $this->productoSucursalService->verificaStockSucursalProducto((int)$item["producto_id"], $sucursal->id, $cantidad);

                if ($resStock[0]) {
                    $detalle_orden = $ordenVenta->detalle_ordens()->create($datos);

                    //registrar kardex
                    $this->kardexProductoService->registroEgreso("ORDEN DE VENTA", $producto, $cantidad, $producto->precio_pred, "VENTA DE PRODUCTO", $sucursal->id, "DetalleOrden", $detalle_orden->id);
                } else {
                    throw new Exception("Stock insuficiente del producto $producto->nombre; su stock actual es de $resStock[1]");
                }
            } else {
                // por modificacion
                $detalle_orden = DetalleOrden::find($item["id"]);
                if ($detalle_orden) {
                    //registrar kardex por modificacion
                    $this->kardexProductoService->registroIngreso($old_sucursal != 0 ? $old_sucursal :  $sucursal->id, "ORDEN DE VENTA", $producto, $detalle_orden->cantidad, $producto->precio_pred, "POR MODIFICACIÓN DE ORDEN DE VENTA", "DetalleOrden", $detalle_orden->id);
                    //actualizar
                    $detalle_orden->update($datos);

                    //registrar kardex
                    $this->kardexProductoService->registroEgreso("ORDEN DE VENTA", $producto, $cantidad, $producto->precio_pred, "VENTA DE PRODUCTO (MODIFICACIÓN)", $sucursal->id, "DetalleOrden", $detalle_orden->id);
                }
            }
        }
    }

    /**
     * Eliminar registros detalle orden
     *
     * @param DetalleOrden $detalle_orden
     * @param array $id_eliminados
     * @return void
     */
    public function eliminarDetalleOrdens(array $id_eliminados = [], int $sucursal_id, int $old_sucursal = 0)
    {
        $sucursal = Sucursal::findOrFail($sucursal_id);
        if (!$sucursal) {
            throw new Exception("Ocurrió un error, no se encontró la sucursal");
        }
        if (count($id_eliminados) > 0) {
            foreach ($id_eliminados as $detalle_orden_id) {
                $detalle_orden = DetalleOrden::find($detalle_orden_id);
                $detalle_orden->status = 0;
                $detalle_orden->save();
                $producto = Producto::find($detalle_orden->producto_id);
                // registrar kardex
                $this->kardexProductoService->registroIngreso($old_sucursal != 0 ? $old_sucursal :  $sucursal->id, "ORDEN DE VENTA", $producto, (float)$detalle_orden->cantidad, $producto->precio_pred, "ELIMINACIÓN DE PRODUCTO DETALLE DE ORDEN", "DetalleOrden", $detalle_orden->id);
            }
        }
    }
}
