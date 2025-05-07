<?php

namespace App\Services;

use App\Models\DetalleOrden;
use App\Services\HistorialAccionService;
use App\Models\Devolucion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class DevolucionService
{
    private $modulo = "DEVOLUCIONES";

    public function __construct(private HistorialAccionService $historialAccionService, private KardexProductoService $kardexProductoService, private DetalleUsoService $detalleUsoService) {}

    public function listado(): Collection
    {
        $devolucions = Devolucion::with(["sucursal", "orden_venta", "producto", "detalle_orden"])->select("devolucions.*");

        if (Auth::user()->sucursals_todo == 0) {
            $devolucions->where("sucursal_id", Auth::user()->sucursal_id);
        }

        $devolucions = $devolucions->get();
        return $devolucions;
    }

    public function listadoDataTable(int $length, int $start, int $page, string $search): LengthAwarePaginator
    {
        $devolucions = Devolucion::with(["sucursal", "orden_venta", "producto", "detalle_orden"])->select("devolucions.*");
        if ($search && trim($search) != '') {
            // $devolucions->where("nombres", "LIKE", "%$search%"); // relacion producto
            $devolucions->orWhere("fecha_ini", "LIKE", "%$search%");
            $devolucions->orWhere("fecha_fin", "LIKE", "%$search%");
        }

        if (Auth::user()->sucursals_todo == 0) {
            $devolucions->where("sucursal_id", Auth::user()->sucursal_id);
        }

        $devolucions = $devolucions->paginate($length, ['*'], 'page', $page);
        return $devolucions;
    }

    /**
     * Crear devolucion
     *
     * @param array $datos
     * @return Devolucion
     */
    public function crear(array $datos): Devolucion
    {
        $detalle_orden = DetalleOrden::findOrFail($datos["detalle_orden_id"]);

        $devolucion = Devolucion::create([
            "sucursal_id" => $datos["sucursal_id"],
            "orden_venta_id" => $datos["orden_venta_id"],
            "producto_id" => $detalle_orden->producto_id,
            "detalle_orden_id" => $detalle_orden->id,
            "razon" => $datos["razon"],
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "fecha_registro" => date("Y-m-d")
        ]);

        // kardex ingreso
        $detalle_orden->status = 0;
        $detalle_orden->save();

        $this->kardexProductoService->registroIngreso($devolucion->sucursal_id, "DEVOLUCIÓN", $detalle_orden->producto, $detalle_orden->cantidad, $detalle_orden->producto->precio_pred, "INGRESO POR DEVOLUCIÓN DE PRODUCTO POR " . $devolucion->razon, "Devolucion", $devolucion->id);

        $this->detalleUsoService->restablecerUso($detalle_orden);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA DEVOLUCIÓN", $devolucion);

        return $devolucion;
    }

    /**
     * Actualizar devolucion
     *
     * @param array $datos
     * @param Devolucion $devolucion
     * @return Devolucion
     */
    public function actualizar(array $datos, Devolucion $devolucion): Devolucion
    {
        $old_devolucion = clone $devolucion;
        $detalle_orden_old = DetalleOrden::findOrFail($old_devolucion->detalle_orden_id);
        $detalle_orden = DetalleOrden::findOrFail($datos["detalle_orden_id"]);

        $this->detalleUsoService->eliminarUsos($detalle_orden_old);
        // kardex egreso
        $detalle_orden_old->status = 1;
        $detalle_orden_old->save();
        $this->kardexProductoService->registroEgreso("DEVOLUCIÓN", $detalle_orden_old->producto, $detalle_orden_old->cantidad, $detalle_orden_old->producto->precio_pred, "EGRESO POR MODIFICACIÓN DE DEVOLUCIÓN" . $old_devolucion->razon, $old_devolucion->sucursal_id, "Devolucion", $old_devolucion->id);
        $this->detalleUsoService->registrarUsos($detalle_orden_old);

        $devolucion->update([
            "sucursal_id" => $datos["sucursal_id"],
            "orden_venta_id" => $datos["orden_venta_id"],
            "producto_id" => $detalle_orden->producto_id,
            "detalle_orden_id" => $detalle_orden->id,
            "razon" => $datos["razon"],
            "descripcion" => mb_strtoupper($datos["descripcion"]),
        ]);
        $this->detalleUsoService->eliminarUsos($detalle_orden);

        // kardex ingreso
        $detalle_orden->status = 0;
        $detalle_orden->save();
        $this->kardexProductoService->registroIngreso($devolucion->sucursal_id, "DEVOLUCIÓN", $detalle_orden->producto, $detalle_orden->cantidad, $detalle_orden->producto->precio_pred, "INGRESO POR DEVOLUCIÓN POR PRODUCTO POR " . $devolucion->razon, "Devolucion", $devolucion->id);

        $this->detalleUsoService->registrarUsos($detalle_orden);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA DEVOLUCIÓN", $old_devolucion, $devolucion);

        return $devolucion;
    }

    /**
     * Eliminar devolucion
     *
     * @param Devolucion $devolucion
     * @return boolean
     */
    public function eliminar(Devolucion $devolucion): bool
    {
        $old_devolucion = clone $devolucion;
        $detalle_orden = DetalleOrden::findOrFail($devolucion->detalle_orden_id);
        $devolucion->delete();

        // kardex egreso
        $detalle_orden->status = 1;
        $detalle_orden->save();

        $this->detalleUsoService->eliminarUsos($detalle_orden->id);

        $this->kardexProductoService->registroEgreso("DEVOLUCIÓN", $detalle_orden->producto, $detalle_orden->cantidad, $detalle_orden->producto->precio_pred, "EGRESO POR MODIFICACIÓN DE DEVOLUCIÓN" . $old_devolucion->razon, $old_devolucion->sucursal_id, "Devolucion", $old_devolucion->id);

        $this->detalleUsoService->registrarUsos($detalle_orden->id);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA DEVOLUCIÓN", $old_devolucion);

        return true;
    }
}
