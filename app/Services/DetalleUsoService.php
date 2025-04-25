<?php

namespace App\Services;

use App\Models\DetalleOrden;
use App\Models\DetalleUso;
use App\Models\IngresoDetalle;
use App\Models\OrdenVenta;
use App\Models\Producto;
use App\Models\Sucursal;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class DetalleUsoService
{
    public function __construct(private ProductoSucursalService $productoSucursalService, private KardexProductoService $kardexProductoService) {}

    /**
     * Registrar usos
     *
     * @param DetalleOrden $detalle_orden
     * @return void
     */
    public function registrarUsos(DetalleOrden $detalle_orden): void
    {
        $cantidad_requerida = $detalle_orden->cantidad;

        $orden_venta = $detalle_orden->orden_venta;

        $ingreso_detalles = IngresoDetalle::select("ingreso_detalles.*")
            ->join("ingreso_productos", "ingreso_productos.id", "=", "ingreso_detalles.ingreso_producto_id")
            ->where("producto_id", $detalle_orden->producto_id)
            ->where("ingreso_productos.sucursal_id", $orden_venta->sucursal_id)
            ->where("disponible", ">", 0)
            ->orderBy("id", "asc")
            ->get();

        $restante = $cantidad_requerida;
        $cantidad_uso = 0;
        foreach ($ingreso_detalles as $ingreso_detalle) {
            if ($restante > $ingreso_detalle->disponible) {
                $cantidad_uso = $ingreso_detalle->disponible;
                $restante = (float)$restante - $ingreso_detalle->disponible;
                $ingreso_detalle->disponible = 0;
            } else {
                $ingreso_detalle->disponible = (float)$ingreso_detalle->disponible - (float)$restante;
                $cantidad_uso = $restante;
                $restante = 0;
            }
            $ingreso_detalle->save();

            DetalleUso::create([
                "orden_venta_id" => $orden_venta->id,
                "detalle_orden_id" => $detalle_orden->id,
                "producto_id" => $detalle_orden->producto_id,
                "ingreso_detalle_id" => $ingreso_detalle->id,
                "cantidad" => $cantidad_uso,
            ]);
            if ($restante == 0) {
                break;
            }
        }
    }

    /**
     * Eliminar usos en un detalle de orden
     * Usado para actualizar un detalle de orden
     *
     * @param DetalleOrden $detalle_orden
     * @return void
     */
    public function eliminarUsos(DetalleOrden $detalle_orden): void
    {
        $detalle_usos = DetalleUso::where("detalle_orden_id", $detalle_orden->id)
            ->get();

        foreach ($detalle_usos as $detalle_uso) {
            $ingreso_detalle = IngresoDetalle::find($detalle_uso->ingreso_detalle_id);
            if ($ingreso_detalle) {
                $ingreso_detalle->disponible = (float)$ingreso_detalle->disponible + (float)$detalle_uso->cantidad;
                $ingreso_detalle->save();
            }
            $detalle_uso->delete();
        }
    }

    /**
     * Reestablecer las cantidades disponibles en ingresos detalles
     * Usado para eliminación logica de un detalle de orden
     *
     * @param DetalleOrden $detalle_orden
     * @return void
     */
    public function restablecerUso(DetalleOrden $detalle_orden): void
    {
        $detalle_usos = DetalleUso::where("detalle_orden_id", $detalle_orden->id)
            ->get();

        foreach ($detalle_usos as $detalle_uso) {
            $ingreso_detalle = IngresoDetalle::find($detalle_uso->ingreso_detalle_id);
            if ($ingreso_detalle) {
                $ingreso_detalle->disponible = (float)$ingreso_detalle->disponible + (float)$detalle_uso->cantidad;
                $ingreso_detalle->save();
            }
        }
    }
}
