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
use App\library\numero_a_letras\src\NumeroALetras;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PDF;

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
    public function listado($sucursal_id = 0): Collection
    {
        $orden_ventas = OrdenVenta::select("orden_ventas.*");

        if (Auth::user()->sucursals_todo == 0) {
            $orden_ventas->where("sucursal_id", Auth::user()->sucursal_id);
        }

        if ($sucursal_id != 0) {
            $orden_ventas->where("sucursal_id", $sucursal_id);
        }


        $orden_ventas->where("status", 1);
        $orden_ventas = $orden_ventas->get();
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
        $ordenVentas = OrdenVenta::with(["cliente", "sucursal", "user", "detalle_ordens.producto"])
            ->select(
                "orden_ventas.id",
                "orden_ventas.nro",
                "orden_ventas.user_id",
                "orden_ventas.sucursal_id",
                "orden_ventas.cliente_id",
                "orden_ventas.nit_ci",
                "orden_ventas.factura",
                "orden_ventas.tipo_pago",
                "orden_ventas.descripcion",
                "orden_ventas.fecha_registro",
                "orden_ventas.status",
                \DB::raw("SUM(detalle_ordens.cantidad) AS total_vendido")
            )
            ->leftJoin("detalle_ordens", "orden_ventas.id", "=", "detalle_ordens.orden_venta_id")
            ->where("orden_ventas.status", 1)
            ->groupBy(
                "orden_ventas.id",
                "orden_ventas.nro",
                "orden_ventas.user_id",
                "orden_ventas.sucursal_id",
                "orden_ventas.cliente_id",
                "orden_ventas.nit_ci",
                "orden_ventas.factura",
                "orden_ventas.tipo_pago",
                "orden_ventas.descripcion",
                "orden_ventas.fecha_registro",
                "orden_ventas.status",
            );

        if (Auth::user()->sucursals_todo == 0) {
            $ordenVentas->where("sucursal_id", Auth::user()->sucursal_id);
        }

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
            "user_id" => Auth::user()->id,
            "sucursal_id" => $datos["sucursal_id"],
            "cliente_id" => $cliente->id,
            "nit_ci" => $datos["nit_ci"],
            "factura" => $datos["factura"],
            "tipo_pago" => $datos["tipo_pago"],
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "fecha_registro" => date("Y-m-d"),
        ]);

        // registrar Detalle(detalle_ordens)
        $this->detalleOrdenService->registrarDetalle($ordenVenta, $datos["detalle_ordens"], $ordenVenta->sucursal_id);

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
        $cliente = Cliente::findOrFail($datos["cliente_id"]);
        $ordenVenta->update([
            // "sucursal_id" => $datos["sucursal_id"],
            "cliente_id" => $cliente->id,
            "nit_ci" => $datos["nit_ci"],
            "factura" => $datos["factura"],
            "tipo_pago" => $datos["tipo_pago"],
            "descripcion" => mb_strtoupper($datos["descripcion"]),
        ]);

        // registrar Detalle(detalle_ordens)
        $this->detalleOrdenService->registrarDetalle($ordenVenta, $datos["detalle_ordens"], $ordenVenta->sucursal_id);

        if (isset($datos["eliminados"])) {
            // eliminados
            $this->detalleOrdenService->eliminarDetalleOrdens($datos["eliminados"], $ordenVenta->sucursal_id, $old_ordenVenta->sucursal_id);
        }

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

        // eliminar detalles
        $id_eliminados = $ordenVenta->detalle_ordens->pluck("id")->toArray();
        $this->detalleOrdenService->eliminarDetalleOrdens($id_eliminados, $ordenVenta->sucursal_id);

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

    public function generarPdf(OrdenVenta $ordenVenta)
    {
        $convertir = new NumeroALetras();
        $array_monto = explode('.', number_format($ordenVenta->total, 2, ".", ""));
        $literal = $convertir->convertir($array_monto[0]);
        $literal .= " " . $array_monto[1] . "/100." . " BOLIVIANOS";

        $nro_factura = (int)$ordenVenta->nro;
        if ($nro_factura < 10) {
            $nro_factura = '000' . $nro_factura;
        } else if ($nro_factura < 100) {
            $nro_factura = '00' . $nro_factura;
        } else if ($nro_factura < 1000) {
            $nro_factura = '0' . $nro_factura;
        }

        $customPaper = [0, 0, 270.0, 700.0]; // [top, left, bottom, right]

        $pdf = PDF::loadView('reportes.ordenVenta', compact('ordenVenta', 'literal', 'nro_factura'))->setPaper($customPaper, 'portrait');
        return $pdf->stream('OrdenVenta.pdf');
    }
}
