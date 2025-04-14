<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Services\HistorialAccionService;
use App\Services\EnviarCorreoService;
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
        private CargarArchivoService $cargarArchivoService,
        private EnviarCorreoService $enviarCorreoService,
        private DetalleVentaService $detalleVentaService,
        private NotificacionService $notificacionService
    ) {}

    /**
     * Lista de todos los ordenVentas
     *
     * @return Collection
     */
    public function listado(): Collection
    {
        $ordenVentas = OrdenVenta::select("ordenVentas.*")->where("status", 1)->get();
        return $ordenVentas;
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
        $ordenVentas = OrdenVenta::with(["cliente", "detalleVenta.producto.imagens"])
            ->select("orden_ventas.*", \DB::raw("SUM(detalle_ventas.cantidad) AS total_vendido"))
            ->leftJoin("detalle_ventas", "orden_ventas.id", "=", "detalle_ventas.orden_venta_id")
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
     * Lista de ordenVentas paginado con filtros
     *
     * @param integer $length
     * @param integer $page
     * @param string $search
     * @param array $columnsSerachLike
     * @param array $columnsFilter
     * @return LengthAwarePaginator
     */
    public function listadoPaginadoCliente(
        int $cliente_id,
        int $length,
        int $page,
        string $search,
        array $columnsSerachLike = [],
        array $columnsFilter = [],
        array $columnsBetweenFilter = [],
        array $orderBy = []
    ): LengthAwarePaginator {
        $ordenVentas = OrdenVenta::with(["detalleVenta.producto.imagens", "cliente"])
            ->select("orden_ventas.*");


        $ordenVentas->where("cliente_id", $cliente_id);
        if (!empty($columnsFilter)) {
            foreach ($columnsFilter as $key => $value) {
                if ($value) {
                    $ordenVentas->where($key, $value);
                }
            }
        }

        if (!empty($columnsBetweenFilter)) {
            foreach ($columnsBetweenFilter as $key => $value) {
                if ($value[0] && $value[1]) {
                    $ordenVentas->whereBetween($key, $value);
                }
            }
        }

        if ($search && trim($search) != '') {
            if (!empty($columnsSerachLike)) {
                foreach ($columnsSerachLike as $col) {
                    $ordenVentas->orWhere($col, "LIKE", "%$search%");
                }
            }
        }


        $ordenVentas->where("status", 1);

        if (!empty($orderBy)) {
            foreach ($orderBy as $value) {
                $ordenVentas->orderBy($value[0], $value[1]);
            }
        }


        $ordenVentas = $ordenVentas->paginate($length, ['*'], 'page', $page);
        return $ordenVentas;
    }


    /**
     * Crear ordenVenta
     *
     * @param array $datos
     * @return OrdenVenta
     */
    public function crear(array $datos): OrdenVenta
    {
        $codigo = $this->generarCodigoOrden();
        $cliente = Cliente::findOrFail($datos["cliente_id"]);
        $ordenVenta = OrdenVenta::create([
            "codigo" => $codigo[0],
            "nro" => $codigo[1],
            "cliente_id" => $cliente->id,
            "nombre_cliente" => $cliente->nombres,
            "apellidos_cliente" => $cliente->apellidos,
            "cel" => $cliente->cel,
            "configuracion_pago_id" => $datos["configuracion_pago_id"],
            "fecha_orden" => date("Y-m-d"),
        ]);


        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA ORDEN DE VENTA", $ordenVenta);

        // registrar comprobante
        $this->guardarComprobante($ordenVenta, $datos["comprobante"]);

        // registrar Detalle(carrito)
        $this->registrarCarrito($ordenVenta, $datos["carrito"]);

        // registrar notificacion
        $this->notificacionService->crearNotificacionOrdenVenta($ordenVenta);

        // enviar correo
        $this->enviarCorreoService->nuevaOrdenVenta($ordenVenta);

        return $ordenVenta;
    }

    /**
     * Actualizar estado Orden Venta
     *
     * @param OrdenVenta $ordenVenta
     * @param array $datos
     * @return OrdenVenta
     */
    public function actualizarEstado(OrdenVenta $ordenVenta, array $datos): OrdenVenta
    {
        if ($ordenVenta->estado_orden != $datos["estado_orden"]) {
            $old_ordenVenta = OrdenVenta::find($ordenVenta->id);

            $estado_pago = 0;
            if ($datos["estado_orden"] == 'CONFIRMADO') {
                $estado_pago = 1;
            }

            $ordenVenta->update([
                "estado_orden" => $datos["estado_orden"],
                "observacion" => mb_strtoupper($datos["observacion"]),
                "estado_pago" => $estado_pago,
                "fecha_confirmacion" => date("Y-m-d"),
            ]);

            if ($datos["estado_orden"] == 'CONFIRMADO') {
                $this->detalleVentaService->descuentaStockProductos($ordenVenta->detalleVenta);
            }

            // registrar accion
            $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "MODIFICÓ EL ESTADO DE UNA ORDEN DE VENTA", $old_ordenVenta, $ordenVenta);

            // enviar correo
            $this->enviarCorreoService->updateEstadoOrdenVenta($ordenVenta);
        }

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
        $old_ordenVenta = OrdenVenta::find($ordenVenta->id);
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

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA ORDEN DE VENTA", $old_ordenVenta, $ordenVenta);

        // actualizar imagenes
        $existe_cambios = false;
        if (!empty($datos["imagens"])) {
            foreach ($datos["imagens"] as $key => $imagen) {
                if ($imagen["id"] == 0) {
                    $this->ordenVentaImagenService->guardarImagenOrdenVenta($ordenVenta, $imagen["file"], $key);
                    $existe_cambios = true;
                }
            }
        }


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
        $usos = DetalleVenta::where("ordenVenta_id", $ordenVenta->id)->get();
        if (count($usos) > 0) {
            throw ValidationException::withMessages([
                'error' =>  "No es posible eliminar este registro porque esta siendo utilizado por otros registros",
            ]);
        }

        // no eliminar ordenVentas predeterminados para el funcionamiento del sistema
        $old_ordenVenta = OrdenVenta::find($ordenVenta->id);
        $ordenVenta->status = 1;
        $ordenVenta->save();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA ORDEN DE VENTA", $old_ordenVenta);

        return true;
    }

    /**
     * Generar nuevo código de orden
     *
     * @return array<string,int>
     */
    private function generarCodigoOrden(): array
    {
        $codigo = "ORD.";
        $nro = 1;
        $ultimaOrdenVenta = OrdenVenta::orderBy("id", "desc")->get()->first();
        if ($ultimaOrdenVenta) {
            $nro = (int)$ultimaOrdenVenta->nro + 1;
        }

        return [$codigo . $nro, $nro];
    }

    /**
     * Guardar comprobante
     *
     * @param Configuracion $ordenVenta
     * @param UploadedFile $file
     * @return void
     */
    private function guardarComprobante(OrdenVenta $ordenVenta, UploadedFile $file): void
    {
        $nombre = "ov" . $ordenVenta->id . time();
        $ordenVenta->comprobante = $this->cargarArchivoService->cargarArchivo($file, public_path("imgs/ordenVentas"), $nombre);
        $ordenVenta->save();
    }

    /**
     * Guardar detalle de venta (carrito)
     *
     * @param OrdenVenta $ordenVenta
     * @param array $carrito
     * @return void
     */
    private function registrarCarrito(OrdenVenta $ordenVenta, array $carrito): void
    {
        foreach ($carrito as $item) {
            $arraProd = json_decode($item, true);
            $ordenVenta->detalleVenta()->create([
                "producto_id" => $arraProd["producto_id"],
                "cantidad" => $arraProd["cantidad"],
                "precio" => $arraProd["precio"],
                "subtotal" => $arraProd["subtotal"],
            ]);
        }
    }
}
