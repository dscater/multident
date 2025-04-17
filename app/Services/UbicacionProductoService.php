<?php

namespace App\Services;

use App\Models\IngresoDetalle;
use App\Services\HistorialAccionService;
use App\Models\UbicacionProducto;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UbicacionProductoService
{
    private $modulo = "UBICACIÓN DE PRODUCTOS";

    public function __construct(private HistorialAccionService $historialAccionService) {}

    public function listado(): Collection
    {
        $ubicacion_productos = UbicacionProducto::select("ubicacion_productos.*")->where("status", 1)->get();
        return $ubicacion_productos;
    }

    public function listadoDataTable(int $length, int $start, int $page, string $search): LengthAwarePaginator
    {
        $ubicacion_productos = UbicacionProducto::select("ubicacion_productos.*");
        if ($search && trim($search) != '') {
            $ubicacion_productos->where("nombre", "LIKE", "%$search%");
        }
        $ubicacion_productos->where("status", 1);
        $ubicacion_productos = $ubicacion_productos->paginate($length, ['*'], 'page', $page);
        return $ubicacion_productos;
    }

    /**
     * Crear ubicacion_producto
     *
     * @param array $datos
     * @return UbicacionProducto
     */
    public function crear(array $datos): UbicacionProducto
    {

        $ubicacion_producto = UbicacionProducto::create([
            "lugar" => mb_strtoupper($datos["lugar"]),
            "numero_filas" => mb_strtoupper($datos["numero_filas"]),
            "fecha_registro" => date("Y-m-d")
        ]);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA UBICACIÓN DE PRODUCTO", $ubicacion_producto);

        return $ubicacion_producto;
    }

    /**
     * Actualizar ubicacion_producto
     *
     * @param array $datos
     * @param UbicacionProducto $ubicacion_producto
     * @return UbicacionProducto
     */
    public function actualizar(array $datos, UbicacionProducto $ubicacion_producto): UbicacionProducto
    {
        $old_ubicacion_producto = UbicacionProducto::find($ubicacion_producto->id);
        $ubicacion_producto->update([
            "lugar" => mb_strtoupper($datos["lugar"]),
            "numero_filas" => mb_strtoupper($datos["numero_filas"]),
        ]);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA UBICACIÓN DE PRODUCTO", $old_ubicacion_producto, $ubicacion_producto);
        return $ubicacion_producto;
    }

    /**
     * Eliminar ubicacion_producto
     *
     * @param UbicacionProducto $ubicacion_producto
     * @return boolean
     */
    public function eliminar(UbicacionProducto $ubicacion_producto): bool
    {
        // verificar usos
        $usos = IngresoDetalle::where("ubicacion_producto_id", $ubicacion_producto->id)->get();
        if (count($usos) > 0) {
            throw ValidationException::withMessages([
                'error' =>  "No es posible eliminar este registro porque esta siendo utilizado por otros registros",
            ]);
        }

        // no eliminar ubicacion_productos predeterminados para el funcionamiento del sistema
        $old_ubicacion_producto = UbicacionProducto::find($ubicacion_producto->id);
        $ubicacion_producto->status = 0;
        $ubicacion_producto->save();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA UBICACIÓN DE PRODUCTO", $old_ubicacion_producto);

        return true;
    }
}
