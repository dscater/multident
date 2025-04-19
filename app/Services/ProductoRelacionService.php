<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\ProductoRelacion;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ProductoRelacionService
{
    private $modulo = "PRODUCTOS";

    public function __construct(private HistorialAccionService $historialAccionService) {}

    /**
     * Listar producto y relaciones de un $producto_id
     *
     * @param integer $producto_id
     * @return Collection
     */
    public function listadoPorProducto(int $producto_id, int $sucursal_id = 0): Collection
    {
        if ($sucursal_id != 0) {
            $producto_relacions = ProductoRelacion::with('producto_relacion')
                ->select('producto_relacions.*', 'producto_sucursals.stock_actual')
                ->leftJoin('producto_sucursals', function ($join) use ($sucursal_id) {
                    $join->on('producto_sucursals.producto_id', '=', 'producto_relacions.producto_relacion')
                        ->where('producto_sucursals.sucursal_id', '=', $sucursal_id);
                })
                ->where('producto_relacions.producto_id', $producto_id)
                ->get();
        } else {
            $producto_relacions = ProductoRelacion::with(["producto_relacion"])->where("producto_id", $producto_id)->get();
        }

        return $producto_relacions;
    }

    /**
     * Crear producto relacion
     *
     * @param array $datos
     * @return ProductoRelacion
     */
    public function crear(array $datos): ProductoRelacion
    {
        $existe = ProductoRelacion::where("producto_id", $datos["producto_id"])
            ->where("producto_relacion", $datos["producto_relacion"])->get()->first();
        if ($existe) {
            throw new Exception("Este producto ya fue agregado");
        }

        $producto_relacion = ProductoRelacion::create([
            "producto_id" => mb_strtoupper($datos["producto_id"]),
            "producto_relacion" => mb_strtoupper($datos["producto_relacion"]),
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO LA RELACIÓN DE UN PRODUCTO", $producto_relacion);

        return $producto_relacion;
    }

    /**
     * Eliminar producto_relacion
     *
     * @param ProductoRelacion $producto_relacion
     * @return boolean
     */
    public function eliminar(ProductoRelacion $producto_relacion): bool
    {
        $old_producto_relacion = clone $producto_relacion;
        $producto_relacion->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA RELACIÓN DE PRODUCTO", $old_producto_relacion);

        return true;
    }
}
