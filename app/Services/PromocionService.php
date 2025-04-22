<?php

namespace App\Services;

use App\Models\DetalleOrden;
use App\Services\HistorialAccionService;
use App\Models\Promocion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PromocionService
{
    private $modulo = "PROMOCIONES";

    public function __construct(private HistorialAccionService $historialAccionService) {}

    public function listado(): Collection
    {
        $promocions = Promocion::with(["producto"])->select("promocions.*")->get();
        return $promocions;
    }

    public function listadoDataTable(int $length, int $start, int $page, string $search): LengthAwarePaginator
    {
        $promocions = Promocion::with(["producto"])->select("promocions.*");
        if ($search && trim($search) != '') {
            // $promocions->where("nombres", "LIKE", "%$search%"); // relacion producto
            $promocions->orWhere("fecha_ini", "LIKE", "%$search%");
            $promocions->orWhere("fecha_fin", "LIKE", "%$search%");
        }
        $promocions = $promocions->paginate($length, ['*'], 'page', $page);
        return $promocions;
    }

    /**
     * Crear promocion
     *
     * @param array $datos
     * @return Promocion
     */
    public function crear(array $datos): Promocion
    {

        $promocion = Promocion::create([
            "producto_id" => $datos["producto_id"],
            "porcentaje" => $datos["porcentaje"],
            "fecha_ini" => $datos["fecha_ini"],
            "fecha_fin" => $datos["fecha_fin"],
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "fecha_registro" => date("Y-m-d")
        ]);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA PROMOCIÓN", $promocion);

        return $promocion;
    }

    /**
     * Actualizar promocion
     *
     * @param array $datos
     * @param Promocion $promocion
     * @return Promocion
     */
    public function actualizar(array $datos, Promocion $promocion): Promocion
    {
        $old_promocion = Promocion::find($promocion->id);
        $promocion->update([
            "producto_id" => $datos["producto_id"],
            "porcentaje" => $datos["porcentaje"],
            "fecha_ini" => $datos["fecha_ini"],
            "fecha_fin" => $datos["fecha_fin"],
            "descripcion" => mb_strtoupper($datos["descripcion"]),
        ]);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA PROMOCIÓN", $old_promocion, $promocion);

        return $promocion;
    }

    /**
     * Eliminar promocion
     *
     * @param Promocion $promocion
     * @return boolean
     */
    public function eliminar(Promocion $promocion): bool
    {
        // verificar usos
        $usos = DetalleOrden::where("promocion_id", $promocion->id)->where("status", 1)->get();
        if (count($usos) > 0) {
            throw ValidationException::withMessages([
                'error' =>  "No es posible eliminar este registro porque esta siendo utilizado por otros registros",
            ]);
        }
        $old_promocion = clone $promocion;
        $promocion->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA PROMOCIÓN", $old_promocion);

        return true;
    }

    public function verificaPromocion($fecha, int $producto_id): Promocion|NULL
    {
        $promocion = Promocion::where("producto_id", $producto_id)
            ->whereDate("fecha_ini", "<=", $fecha)
            ->whereDate("fecha_fin", ">=", $fecha)
            ->get()->first();
        return $promocion;
    }
}
