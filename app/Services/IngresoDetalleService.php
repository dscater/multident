<?php

namespace App\Services;

use App\Models\IngresoDetalle;
use App\Models\IngresoProducto;
use App\Models\Producto;
use App\Models\Sucursal;
use Exception;
use Illuminate\Support\Facades\Log;

class IngresoDetalleService
{

    public function __construct(private KardexProductoService $kardexProductoService, private ProductoSucursalService $productoSucursalService) {}

    public function crearActualizarIngresoDetalles(IngresoProducto $ingreso_producto, array $ingreso_detalles, int $sucursal_id)
    {
        $sucursal = Sucursal::find($sucursal_id);
        if (!$sucursal) {
            throw new Exception("Ocurrió un error, no se encontró la sucursal");
        }

        foreach ($ingreso_detalles as $item) {
            $datos = [
                "producto_id" => $item["producto_id"],
                "cantidad" => $item["cantidad"],
                "ubicacion_producto_id" => $item["ubicacion_producto_id"],
                "fecha_vencimiento" => $item["fecha_vencimiento"],
                "descripcion" => $item["descripcion"],
                "fecha_registro" => date("Y-m-d"),
            ];

            $producto = Producto::find($item["producto_id"]);

            if ($item["id"] == 0) {
                $ingreso_detalle = $ingreso_producto->ingreso_detalles()->create($datos);

                // registrar kardex
                $this->kardexProductoService->registroIngreso($sucursal->id, "INGRESO DE PRODUCTO", $producto, (float)$item["cantidad"], $producto->precio_pred,"", "IngresoDetalle", $ingreso_detalle->id);
            } else {
                unset($datos["fecha_registro"]);

                $ingreso_detalle = IngresoDetalle::find($item["id"]);

                if ($ingreso_detalle) {
                    //descontar stock
                    $this->productoSucursalService->decrementarStock($producto, (float)$ingreso_detalle->cantidad, $sucursal->id);

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
                    $this->kardexProductoService->actualizaRegistrosKardex($kardex->id, $producto->id, $sucursal->id);
                }
            }
        }
    }
}
