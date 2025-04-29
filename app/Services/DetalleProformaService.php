<?php

namespace App\Services;

use App\Models\DetalleProforma;
use App\Models\Proforma;
use App\Models\Producto;
use App\Models\Sucursal;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class DetalleProformaService
{
    public function __construct(private ProductoSucursalService $productoSucursalService) {}

    /**
     * Guardar detalle de venta (detalle_proformas)
     *
     * @param Proforma $proforma
     * @param array $detalle_proformas
     * @return void
     */
    public function registrarDetalle(Proforma $proforma, array $detalle_proformas, int $sucursal_id, int $old_sucursal = 0): void
    {
        $sucursal = Sucursal::findOrFail($sucursal_id);
        if (!$sucursal) {
            throw new Exception("Ocurrió un error, no se encontró la sucursal");
        }

        foreach ($detalle_proformas as $item) {
            $arraProd = $item;
            $cantidad = (float)$item["cantidad"];
            $producto = Producto::findOrFail($item["producto_id"]);

            $datos = [
                "producto_id" => $producto->id,
                "promocion_id" => $arraProd["promocion_id"],
                "list_promocions" => $arraProd["list_promocions"] ?? NULL,
                "promocion_descuento" => $arraProd["promocion_descuento"],
                "cantidad" => $cantidad,
                "precio_reg" => $arraProd["precio_reg"],
                "precio" => $arraProd["precio"],
                "subtotal" => $arraProd["subtotal"],
            ];

            if ($item["id"] == 0) {
                // nuevo 
                $detalle_proforma = $proforma->detalle_proformas()->create($datos);
            } else {
                // por modificacion
                $detalle_proforma = DetalleProforma::find($item["id"]);
                if ($detalle_proforma) {
                    //actualizar
                    $detalle_proforma->update($datos);
                }
            }
        }
    }

    /**
     * Eliminar registros detalle proforma
     *
     * @param DetalleProforma $detalle_proforma
     * @param array $id_eliminados
     * @return void
     */
    public function eliminarDetalleProformas(array $id_eliminados = [], int $sucursal_id, int $old_sucursal = 0)
    {
        $sucursal = Sucursal::findOrFail($sucursal_id);
        if (!$sucursal) {
            throw new Exception("Ocurrió un error, no se encontró la sucursal");
        }
        if (count($id_eliminados) > 0) {
            foreach ($id_eliminados as $detalle_proforma_id) {
                $detalle_proforma = DetalleProforma::find($detalle_proforma_id);
                $detalle_proforma->status = 0;
                $detalle_proforma->save();
            }
        }
    }
}
