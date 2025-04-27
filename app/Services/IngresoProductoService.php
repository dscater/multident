<?php

namespace App\Services;

use App\Models\IngresoDetalle;
use App\Services\HistorialAccionService;
use App\Models\IngresoProducto;
use App\Models\Producto;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class IngresoProductoService
{
    private $modulo = "INGRESO DE PRODUCTOS";

    public function __construct(private HistorialAccionService $historialAccionService, private IngresoDetalleService $ingresoDetalleService) {}

    public function listado(): Collection
    {
        $ingreso_productos = IngresoProducto::with(["sucursal", "ingreso_detalles"])->select("ingreso_productos.*");

        if (Auth::user()->sucursals_todo == 0) {
            $ingreso_productos->where("sucursal_id", Auth::user()->sucursal_id);
        }

        $ingreso_productos = $ingreso_productos->where("status", 1)->get();
        return $ingreso_productos;
    }

    /**
     * Lista de productos paginado con filtros
     *
     * @param integer $length
     * @param integer $page
     * @param string $search
     * @param array $columnsSerachLike
     * @param array $columnsFilter
     * @return LengthAwarePaginator
     */
    public function listadoPaginado(int $length, int $page, string $search, array $columnsSerachLike = [], array $columnsFilter = [], array $columnsBetweenFilter = [], array $orderBy = []): LengthAwarePaginator
    {
        $ingreso_productos = IngresoProducto::with(["sucursal", "ingreso_detalles"])->select("ingreso_productos.*")
            ->leftJoin("ingreso_detalles", "ingreso_productos.id", "=", "ingreso_detalles.ingreso_producto_id")
            ->leftJoin("productos", "productos.id", "=", "ingreso_detalles.producto_id")
            ->groupBy("ingreso_productos.id");

        $ingreso_productos->where("ingreso_productos.status", 1);

        if (Auth::user()->sucursals_todo == 0) {
            $ingreso_productos->where("sucursal_id", Auth::user()->sucursal_id);
        }

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value) && trim($value) != '') {
                $ingreso_productos->where("ingreso_productos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $ingreso_productos->whereBetween("ingreso_productos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $ingreso_productos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    if ($col == 'fecha_registro') {
                        $array_fecha = explode("/", $search);
                        $array_fecha = array_reverse($array_fecha);
                        if (count($array_fecha) > 0) {
                            $search = "";
                            foreach ($array_fecha as $key => $text) {
                                $search .= $text . ($key < count($array_fecha) - 1 ? '-' : '');
                            }
                        }
                    }
                    $query->orWhere("productos.$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $ingreso_productos->orderBy($value[0], $value[1]);
            }
        }

        $ingreso_productos = $ingreso_productos->paginate($length, ['*'], 'page', $page);
        return $ingreso_productos;
    }


    /**
     * Crear ingreso_producto
     *
     * @param array $datos
     * @return IngresoProducto
     */
    public function crear(array $datos): IngresoProducto
    {
        $ingreso_producto = IngresoProducto::create([
            "sucursal_id" => mb_strtoupper($datos["sucursal_id"]),
            "descripcion" => "",
            "fecha_registro" => date("Y-m-d")
        ]);

        // ingreso_detalles
        $this->ingresoDetalleService->crearActualizarIngresoDetalles($ingreso_producto, $datos["ingreso_detalles"], $ingreso_producto->sucursal_id);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN INGRESO DE PRODUCTOS", $ingreso_producto, null, ["ingreso_detalles"]);

        return $ingreso_producto;
    }

    /**
     * Actualizar ingreso_producto
     *
     * @param array $datos
     * @param IngresoProducto $ingreso_producto
     * @return IngresoProducto
     */
    public function actualizar(array $datos, IngresoProducto $ingreso_producto): IngresoProducto
    {
        $old_ingreso_producto = clone $ingreso_producto;
        $old_ingreso_producto->loadMissing('ingreso_detalles');

        $ingreso_producto->update([
            "sucursal_id" => mb_strtoupper($datos["sucursal_id"]),
            "descripcion" => "",
        ]);

        // ingreso_detalles
        $this->ingresoDetalleService->crearActualizarIngresoDetalles($ingreso_producto, $datos["ingreso_detalles"], $ingreso_producto->sucursal_id, $old_ingreso_producto->sucursal_id, $datos["eliminados"] ?? []);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN INGRESO DE PRODUCTOS", $old_ingreso_producto, $ingreso_producto, ["ingreso_detalles"]);

        return $ingreso_producto;
    }

    /**
     * Eliminar ingreso_producto
     *
     * @param IngresoProducto $ingreso_producto
     * @return boolean
     */
    public function eliminar(IngresoProducto $ingreso_producto): bool
    {
        $old_ingreso_producto = clone $ingreso_producto;
        $old_ingreso_producto->loadMissing('ingreso_detalles');

        $existe_uso = IngresoDetalle::where("ingreso_producto_id", $ingreso_producto->id)
            ->where("status", 1)
            ->whereColumn("cantidad", "!=", "disponible")
            ->get();
        if (count($existe_uso) > 0) {
            throw new Exception("No se puede eliminar el registro debido a que ya se usarón algunos de sus registros");
        }

        $ingreso_producto->status = 0;
        $ingreso_producto->save();

        // eliminar ingreso_detalles
        $this->ingresoDetalleService->eliminarIngresoDetalles($ingreso_producto, $ingreso_producto->ingreso_detalles);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN INGRESO DE PRODUCTOS", $old_ingreso_producto, $ingreso_producto, ["ingreso_detalles"]);

        return true;
    }
}
