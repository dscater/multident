<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Services\HistorialAccionService;
use App\Services\EnviarCorreoService;
use App\Models\SolicitudProducto;
use App\Models\SolicitudProductoImagen;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SolicitudProductoService
{
    private $modulo = "SOLICITUD DE PRODUCTO";

    public function __construct(
        private HistorialAccionService $historialAccionService,
        private CargarArchivoService $cargarArchivoService,
        private EnviarCorreoService $enviarCorreoService,
        private NotificacionService $notificacionService,
        private SedeUserService $sedeUserService
    ) {}

    /**
     * Lista de todos los solicitudProductos
     *
     * @return Collection
     */
    public function listado(): Collection
    {
        $solicitudProductos = SolicitudProducto::select("solicitudProductos.*");

        // Filtro por usuario
        $user = Auth::user();
        if ($user->sedes_todo != 1) {
            $sedes_id = $this->sedeUserService->getArraySedesIdUser();
            $solicitudProductos->whereIn("solicitud_productos.sede_id", $sedes_id);
        }

        $solicitudProductos = $solicitudProductos->where("status", 1)->get();

        return $solicitudProductos;
    }

    /**
     * Lista de solicitudProductos paginado con filtros
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
        $solicitudProductos = SolicitudProducto::with(["cliente", "solicitudDetalles", "sede"])
            ->select("solicitud_productos.*")
            ->join("solicitud_detalles", "solicitud_productos.id", "=", "solicitud_detalles.solicitud_producto_id");

        // Filtro por usuario
        $user = Auth::user();
        if ($user->sedes_todo != 1) {
            $sedes_id = $this->sedeUserService->getArraySedesIdUser();
            $solicitudProductos->whereIn("solicitud_productos.sede_id", $sedes_id);
        }

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $solicitudProductos->where("solicitud_productos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $solicitudProductos->whereBetween("solicitud_productos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $solicitudProductos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("solicitud_productos.$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $solicitudProductos->orderBy("solicitud_productos.{$value[0]}", $value[1]);
            }
        }

        return $solicitudProductos->paginate($length, ['*'], 'page', $page);
    }

    /**
     * Lista de solicitudProductos paginado con filtros
     *
     * @param integer $length
     * @param integer $page
     * @param string $search
     * @param array $columnsSerachLike
     * @param array $columnsFilter
     * @return LengthAwarePaginator
     */
    public function listadoPaginadoCliente(int $cliente_id, int $length, int $page, string $search, array $columnsSerachLike = [], array $columnsFilter = [], array $columnsBetweenFilter = [], array $orderBy = []): LengthAwarePaginator
    {
        $solicitudProductos = SolicitudProducto::with(["solicitudDetalles", "cliente", "sede"])
            ->select("solicitud_productos.*");

        $solicitudProductos->where("cliente_id", $cliente_id);
        if (!empty($columnsFilter)) {
            foreach ($columnsFilter as $key => $value) {
                if ($value) {
                    $solicitudProductos->where($key, $value);
                }
            }
        }

        if (!empty($columnsBetweenFilter)) {
            foreach ($columnsBetweenFilter as $key => $value) {
                if ($value[0] && $value[1]) {
                    $solicitudProductos->whereBetween($key, $value);
                }
            }
        }

        if ($search && trim($search) != '') {
            if (!empty($columnsSerachLike)) {
                foreach ($columnsSerachLike as $col) {
                    $solicitudProductos->orWhere($col, "LIKE", "%$search%");
                }
            }
        }

        $solicitudProductos->where("status", 1);

        if (!empty($orderBy)) {
            foreach ($orderBy as $value) {
                $solicitudProductos->orderBy($value[0], $value[1]);
            }
        }


        $solicitudProductos = $solicitudProductos->paginate($length, ['*'], 'page', $page);
        return $solicitudProductos;
    }


    /**
     * Crear solicitudProducto
     *
     * @param array $datos
     * @return SolicitudProducto
     */
    public function crear(array $datos): SolicitudProducto
    {
        $codigo = $this->generarCodigoSolicitud();
        $cliente = Cliente::findOrFail($datos["cliente_id"]);
        $solicitudProducto = SolicitudProducto::create([
            "codigo_solicitud" => $codigo[0],
            "nro" => $codigo[1],
            "sede_id" => $datos["sede_id"],
            "cliente_id" => $cliente->id,
            "nombre_cliente" => $cliente->nombres,
            "apellidos_cliente" => $cliente->apellidos,
            "cel" => $cliente->cel,
            "fecha_solicitud" => date("Y-m-d"),
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA SOLICITUD DE PRODUCTO", $solicitudProducto);

        // registrar Detalle(solicitudes)
        $this->registrarSolicitudes($solicitudProducto, $datos["solicitudes"]);

        // enviar correo
        $this->enviarCorreoService->nuevaSolicitudProducto($solicitudProducto);

        // registrar notificacion
        $this->notificacionService->crearNotificacionSolicitudProducto($solicitudProducto);

        return $solicitudProducto;
    }

    /**
     * Actualizar estado verificacion solicitud producto
     *
     * @param SolicitudProducto $solicitudProducto
     * @param array $datos
     * @return SolicitudProducto
     */
    public function actualizarEstadoVerificacion(SolicitudProducto $solicitudProducto, array $datos): SolicitudProducto
    {
        if ($solicitudProducto->estado_solicitud != $datos["estado_solicitud"]) {
            $old_solicitudProducto = SolicitudProducto::find($solicitudProducto->id);

            $solicitudProducto->update([
                "estado_solicitud" => $datos["estado_solicitud"],
                "estado_seguimiento" => $datos["estado_solicitud"] == 'APROBADO' ? 'PENDIENTE' : NULL,
                "observacion" => mb_strtoupper(nl2br($datos["observacion"])),
                "precio_compra" => $datos["estado_solicitud"] == 'APROBADO' ? $datos["precio_compra"] : null,
                "margen_ganancia" => $datos["estado_solicitud"] == 'APROBADO' ? $datos["margen_ganancia"] : null,
                "fecha_verificacion" => date("Y-m-d"),
            ]);

            // registrar accion
            $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "MODIFICÓ EL ESTADO DE UNA SOLICITUD DE PRODUCTO", $old_solicitudProducto, $solicitudProducto);

            // enviar correo
            $this->enviarCorreoService->updateSolicitudProductoVerificacion($solicitudProducto);
        }

        return $solicitudProducto;
    }

    /**
     * Actualizar estado seguimiento solicitud producto
     *
     * @param SolicitudProducto $solicitudProducto
     * @param array $datos
     * @return SolicitudProducto
     */
    public function actualizarEstadoSeguimiento(SolicitudProducto $solicitudProducto, array $datos): SolicitudProducto
    {
        if ($solicitudProducto->estado_seguimiento != $datos["estado_seguimiento"]) {
            $old_solicitudProducto = SolicitudProducto::find($solicitudProducto->id);
            $solicitudProducto->update([
                "estado_seguimiento" => $datos["estado_seguimiento"],
                "observacion" => mb_strtoupper($datos["observacion"]),
            ]);

            // registrar accion
            $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "MODIFICÓ EL ESTADO DE UNA SOLICITUD DE PRODUCTO", $old_solicitudProducto, $solicitudProducto);

            // enviar correo
            $this->enviarCorreoService->updateSolicitudProductoSeguimiento($solicitudProducto);
        }

        return $solicitudProducto;
    }

    /**
     * Actualizar solicitudProducto
     *
     * @param array $datos
     * @param SolicitudProducto $solicitudProducto
     * @return SolicitudProducto
     */
    public function actualizar(array $datos, SolicitudProducto $solicitudProducto): SolicitudProducto
    {
        $old_solicitudProducto = SolicitudProducto::find($solicitudProducto->id);
        $solicitudProducto->update([
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
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA SOLICITUD DE PRODUCTO", $old_solicitudProducto, $solicitudProducto);

        // actualizar imagenes
        $existe_cambios = false;
        if (!empty($datos["imagens"])) {
            foreach ($datos["imagens"] as $key => $imagen) {
                if ($imagen["id"] == 0) {
                    $this->solicitudProductoImagenService->guardarImagenSolicitudProducto($solicitudProducto, $imagen["file"], $key);
                    $existe_cambios = true;
                }
            }
        }

        // imagenes eliminadas
        if (!empty($datos["eliminados_imagens"])) {
            foreach ($datos["eliminados_imagens"] as $key => $eliminado) {
                $solicitudProductoImagen = SolicitudProductoImagen::find($eliminado);
                if ($solicitudProductoImagen) {
                    $this->solicitudProductoImagenService->eliminarImagenSolicitudProducto($solicitudProductoImagen);
                    $existe_cambios = true;
                }
            }
        }

        if ($existe_cambios) {
            // registrar imagens asignadas
            $datos_original = $solicitudProducto->imagens->map(function ($imagen) {
                return $imagen->makeHidden($imagen->getAppends())->toArray();
            });

            $datos_nuevo = $solicitudProducto->imagens->map(function ($imagen) {
                return $imagen->makeHidden($imagen->getAppends())->toArray();
            });
            $this->historialAccionService->registrarAccionRelaciones($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ LAS IMAGENES DEL PRODUCTO " . $solicitudProducto->nombre, $datos_original, $datos_nuevo);
        }
        return $solicitudProducto;
    }

    /**
     * Eliminar solicitudProducto
     *
     * @param SolicitudProducto $solicitudProducto
     * @return boolean
     */
    public function eliminar(SolicitudProducto $solicitudProducto): bool
    {
        // verificar usos
        $usos = DetalleVenta::where("solicitudProducto_id", $solicitudProducto->id)->get();
        if (count($usos) > 0) {
            throw ValidationException::withMessages([
                'error' =>  "No es posible eliminar este registro porque esta siendo utilizado por otros registros",
            ]);
        }

        // no eliminar solicitudProductos predeterminados para el funcionamiento del sistema
        $old_solicitudProducto = SolicitudProducto::find($solicitudProducto->id);
        $solicitudProducto->status = 1;
        $solicitudProducto->save();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA SOLICITUD DE PRODUCTO", $old_solicitudProducto);

        return true;
    }

    /**
     * Generar nuevo código de orden
     *
     * @return array<string,int>
     */
    private function generarCodigoSolicitud(): array
    {
        $codigo = "SOL.";
        $nro = 1;
        $ultimaSolicitudProducto = SolicitudProducto::orderBy("id", "desc")->get()->first();
        if ($ultimaSolicitudProducto) {
            $nro = (int)$ultimaSolicitudProducto->nro + 1;
        }

        return [$codigo . $nro, $nro];
    }

    /**
     * Guardar detalle de venta (solicitudes)
     *
     * @param SolicitudProducto $solicitudProducto
     * @param array $solicitudes
     * @return void
     */
    private function registrarSolicitudes(SolicitudProducto $solicitudProducto, array $solicitudes): void
    {
        foreach ($solicitudes as $item) {
            $solicitudProducto->solicitudDetalles()->create([
                "nombre_producto" => $item["nombre_producto"],
                "detalle_producto" => $item["detalle_producto"],
                "links_referencia" => nl2br($item["links_referencia"]),
            ]);
        }
    }
}
