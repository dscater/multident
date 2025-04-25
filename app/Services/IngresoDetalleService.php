<?php

namespace App\Services;

use App\Models\IngresoDetalle;
use App\Models\IngresoProducto;
use App\Models\KardexProducto;
use App\Models\Producto;
use App\Models\Sucursal;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class IngresoDetalleService
{

    public function __construct(private KardexProductoService $kardexProductoService, private ProductoSucursalService $productoSucursalService) {}

    /**
     * Registrar/Actualizar ingreso_detalle de un Ingreso
     *
     * @param IngresoProducto $ingreso_producto
     * @param array $ingreso_detalles
     * @param integer $sucursal_id
     * @param integer $old_sucursal
     * @param array $id_eliminados
     * @return void
     */
    public function crearActualizarIngresoDetalles(IngresoProducto $ingreso_producto, array $ingreso_detalles, int $sucursal_id, int $old_sucursal = 0, array $id_eliminados = [])
    {
        $sucursal = Sucursal::find($sucursal_id);
        if (!$sucursal) {
            throw new Exception("Ocurrió un error, no se encontró la sucursal");
        }

        foreach ($ingreso_detalles as $item) {
            $datos = [
                "producto_id" => $item["producto_id"],
                "cantidad" => $item["cantidad"],
                "disponible" => $item["cantidad"],
                "ubicacion_producto_id" => $item["ubicacion_producto_id"],
                "fecha_vencimiento" => $item["fecha_vencimiento"],
                "descripcion" => mb_strtoupper($item["descripcion"]),
                "fecha_registro" => date("Y-m-d"),
            ];

            $producto = Producto::find($item["producto_id"]);

            if ($item["id"] == 0) {
                $ingreso_detalle = $ingreso_producto->ingreso_detalles()->create($datos);
                // registrar kardex
                $this->kardexProductoService->registroIngreso($sucursal->id, "INGRESO DE PRODUCTO", $producto, (float)$item["cantidad"], $producto->precio_pred, "", "IngresoDetalle", $ingreso_detalle->id);
            } else {
                unset($datos["fecha_registro"]);

                $ingreso_detalle = IngresoDetalle::find($item["id"]);
                if ($ingreso_detalle) {
                    //descontar stock
                    $this->productoSucursalService->decrementarStock($producto, (float)$ingreso_detalle->cantidad, $old_sucursal != 0 ? $old_sucursal :  $sucursal->id);

                    //actualizar
                    $ingreso_detalle->update($datos);

                    //incrementar stock
                    $this->productoSucursalService->incrementarStock($producto, (float)$datos["cantidad"], $sucursal->id);

                    // actualizar kardex
                    $kardex = KardexProducto::where("producto_id", $ingreso_detalle->producto_id)
                        ->where("tipo_registro", "INGRESO DE PRODUCTO")
                        ->where("modulo", "IngresoDetalle")
                        ->where("registro_id", $ingreso_detalle->id)
                        ->where("sucursal_id", $sucursal->id);
                    $kardex = $kardex->get()->first();

                    $this->kardexProductoService->actualizaRegistrosKardex($kardex ? $kardex->id : 0, $producto->id, $sucursal->id);

                    if ($old_sucursal != 0 && ($old_sucursal != $sucursal->id)) {
                        // actualizar kardex
                        $kardex = KardexProducto::where("producto_id", $ingreso_detalle->producto_id)
                            ->where("tipo_registro", "INGRESO DE PRODUCTO")
                            ->where("modulo", "IngresoDetalle")
                            ->where("registro_id", $ingreso_detalle->id)
                            ->where("sucursal_id", $old_sucursal);
                        $kardex = $kardex->get()->first();

                        $this->kardexProductoService->actualizaRegistrosKardex($kardex ? $kardex->id : 0, $producto->id, $old_sucursal);
                    }
                }
            }
        }

        if (count($id_eliminados) > 0) {
            foreach ($id_eliminados as $ingreso_detalle_id) {
                $ingreso_detalle = IngresoDetalle::find($ingreso_detalle_id);
                $ingreso_detalle->status = 0;
                $ingreso_detalle->save();
                $producto = Producto::find($ingreso_detalle->producto_id);
                // registrar kardex
                $this->kardexProductoService->registroEgreso("INGRESO DE PRODUCTO", $producto, (float)$ingreso_detalle->cantidad, $producto->precio_pred, "ELIMINACIÓN DE LA LISTA DE INGRESO DE PRODUCTO", $old_sucursal != 0 ? $old_sucursal :  $sucursal->id, "IngresoDetalle", $ingreso_detalle->id);
            }
        }
    }

    public function eliminarIngresoDetalles(IngresoProducto $ingreso_producto, Collection $ingreso_detalles): void
    {
        foreach ($ingreso_detalles as $ingreso_detalle) {
            $ingreso_detalle->status = 0;
            $ingreso_detalle->save();

            $producto = Producto::find($ingreso_detalle->producto_id);
            // registrar kardex
            $this->kardexProductoService->registroEgreso("INGRESO DE PRODUCTO", $producto, (float)$ingreso_detalle->cantidad, $producto->precio_pred, "ELIMINACIÓN DE INGRESO DE PRODUCTO", $ingreso_producto->sucursal_id, "IngresoDetalle", $ingreso_detalle->id);
        }
    }
}
