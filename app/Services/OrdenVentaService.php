<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Devolucion;
use App\Services\DetalleOrdenService;
use App\Services\HistorialAccionService;
use App\Models\OrdenVenta;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class OrdenVentaService
{
    private $modulo = "ORDENES DE VENTA";

    public function __construct(
        private HistorialAccionService $historialAccionService,
        private DetalleOrdenService $detalleOrdenService
    ) {}

    /**
     * Lista de todos los orden_ventas
     *
     * @return Collection
     */
    public function listado(): Collection
    {
        $orden_ventas = OrdenVenta::select("orden_ventas.*")->where("status", 1)->get();
        return $orden_ventas;
    }

    /**
     * Lista de ordenVentas paginado con filtros
     *
     * @param integer $length
     * @param integer $page
     * @param string $search
     * @param array $columnsSerachLike
     * @param array $columnsFilter
     * @param array $columnsBetweenFilter
     * @param array $orderBy
     * @return LengthAwarePaginator
     */
    public function listadoPaginado(
        int $length,
        int $page,
        string $search = '',
        array $columnsSerachLike = [],
        array $columnsFilter = [],
        array $columnsBetweenFilter = [],
        array $orderBy = []
    ): LengthAwarePaginator {
        $ordenVentas = OrdenVenta::with(["cliente", "detalle_ordens.producto"])
            ->select("orden_ventas.*", \DB::raw("SUM(detalle_ordens.cantidad) AS total_vendido"))
            ->leftJoin("detalle_ordens", "orden_ventas.id", "=", "detalle_ordens.orden_venta_id")
            ->groupBy("orden_ventas.id")
            ->where("orden_ventas.status", 1);

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $ordenVentas->where("orden_ventas.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $ordenVentas->whereBetween("orden_ventas.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $ordenVentas->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("orden_ventas.$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $ordenVentas->orderBy($value[0], $value[1]);
            }
        }

        return $ordenVentas->paginate($length, ['*'], 'page', $page);
    }

    /**
     * Crear ordenVenta
     *
     * @param array $datos
     * @return OrdenVenta
     */
    public function crear(array $datos): OrdenVenta
    {
        $numero = $this->generarNumeroOrden();
        $cliente = Cliente::findOrFail($datos["cliente_id"]);
        $ordenVenta = OrdenVenta::create([
            "nro" => $numero,
            "sucursal_id" => $datos["sucursal_id"],
            "cliente_id" => $cliente->id,
            "factura" => $datos["factura"],
            "tipo_pago" => $datos["tipo_pago"],
            "fecha_registro" => date("Y-m-d"),
        ]);

        // registrar Detalle(carrito)
        $this->detalleOrdenService->registrarDetalle($ordenVenta, $datos["carrito"], $ordenVenta->sucursal_id);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA ORDEN DE VENTA", $ordenVenta, null, ["detalle_ordens"]);

        return $ordenVenta;
    }

    /**
     * Actualizar ordenVenta
     *
     * @param array $datos
     * @param OrdenVenta $ordenVenta
     * @return OrdenVenta
     */
    public function actualizar(array $datos, OrdenVenta $ordenVenta): OrdenVenta
    {
        $old_ordenVenta = clone $ordenVenta;
        $old_ordenVenta->loadMissing('detalle_ordens');

        $ordenVenta->update([
            "categoria_id" => mb_strtoupper($datos["categoria_id"]),
            "nombre" => mb_strtoupper($datos["nombre"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "stock_actual" => $datos["stock_actual"],
            "precio_compra" => $datos["precio_compra"],
            "precio_venta" => $datos["precio_venta"],
            "observaciones" => mb_strtoupper($datos["observaciones"]),
            "publico" => mb_strtoupper($datos["publico"]),
        ]);

        // registrar Detalle(carrito)
        $this->detalleOrdenService->registrarDetalle($ordenVenta, $datos["carrito"], $ordenVenta->sucursal_id);

        // eliminados
        $this->detalleOrdenService->eliminarDetalleOrdens($datos["eliminados"], $ordenVenta->sucursal_id, $old_ordenVenta->sucursal_id);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA ORDEN DE VENTA", $old_ordenVenta, $ordenVenta, ["detalle_ordens"]);

        return $ordenVenta;
    }

    /**
     * Eliminar ordenVenta
     *
     * @param OrdenVenta $ordenVenta
     * @return boolean
     */
    public function eliminar(OrdenVenta $ordenVenta): bool
    {
        // verificar usos
        $usos = Devolucion::where("orden_venta_id", $ordenVenta->id)->get();
        if (count($usos) > 0) {
            throw ValidationException::withMessages([
                'error' =>  "No es posible eliminar este registro porque esta siendo utilizado por otros registros",
            ]);
        }
        $old_ordenVenta = clone $ordenVenta;
        $old_ordenVenta->loadMissing('detalle_ordens');
        $ordenVenta->status = 0;
        $ordenVenta->save();
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA ORDEN DE VENTA", $old_ordenVenta);

        return true;
    }

    /**
     * Generar nuevo nro. de orden
     *
     * @return int
     */
    private function generarNumeroOrden(): int
    {
        $nro = 1;
        $ultimaOrdenVenta = OrdenVenta::orderBy("nro", "desc")->get()->first();
        if ($ultimaOrdenVenta) {
            $nro = (int)$ultimaOrdenVenta->nro + 1;
        }

        return $nro;
    }
}
