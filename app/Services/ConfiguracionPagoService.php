<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\ConfiguracionPago;
use App\Models\OrdenVenta;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class ConfiguracionPagoService
{
    private $modulo = "CONFIGURACIÓN DE PAGOS";

    public function __construct(private HistorialAccionService $historialAccionService, private CargarArchivoService $cargarArchivoService) {}

    public function listado(): Collection
    {
        $configuracionPagos = ConfiguracionPago::select("configuracion_pagos.*")->get();
        return $configuracionPagos;
    }

    public function listadoDataTable(int $length, int $start, int $page, string $search): LengthAwarePaginator
    {
        $configuracionPagos = ConfiguracionPago::select("configuracion_pagos.*");
        if ($search && trim($search) != '') {
            $configuracionPagos->where("nombre", "LIKE", "%$search%");
        }
        $configuracionPagos = $configuracionPagos->paginate($length, ['*'], 'page', $page);
        return $configuracionPagos;
    }

    /**
     * Crear configuracionPago
     *
     * @param array $datos
     * @return ConfiguracionPago
     */
    public function crear(array $datos): ConfiguracionPago
    {
        $configuracionPago = ConfiguracionPago::create([
            "nombre_banco" => mb_strtoupper($datos["nombre_banco"]),
            "titular_cuenta" => mb_strtoupper($datos["titular_cuenta"]),
            "nro_cuenta" => $datos["nro_cuenta"],
            "fecha_registro" => date("Y-m-d")
        ]);

        $this->cargarImagenQR($configuracionPago, $datos["imagen_qr"]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA CONFIGURACIÓN DE PAGO", $configuracionPago);

        return $configuracionPago;
    }

    /**
     * Actualizar configuracionPago
     *
     * @param array $datos
     * @param ConfiguracionPago $configuracionPago
     * @return ConfiguracionPago
     */
    public function actualizar(array $datos, ConfiguracionPago $configuracionPago): ConfiguracionPago
    {
        $old_configuracionPago = ConfiguracionPago::find($configuracionPago->id);
        $configuracionPago->update([
            "nombre_banco" => mb_strtoupper($datos["nombre_banco"]),
            "titular_cuenta" => mb_strtoupper($datos["titular_cuenta"]),
            "nro_cuenta" => $datos["nro_cuenta"],
        ]);

        if ($datos["imagen_qr"] && !is_string($datos["imagen_qr"])) {
            $this->cargarImagenQR($configuracionPago, $datos["imagen_qr"]);
        }
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA CONFIGURACIÓN DE PAGO", $old_configuracionPago, $configuracionPago);

        return $configuracionPago;
    }

    /**
     * Eliminar configuracionPago
     *
     * @param ConfiguracionPago $configuracionPago
     * @return boolean
     */
    public function eliminar(ConfiguracionPago $configuracionPago): bool
    {
        // verificar usos
        $usos = OrdenVenta::where("configuracion_pago_id", $configuracionPago->id)->get();
        if (count($usos) > 0) {
            throw ValidationException::withMessages([
                'error' =>  "No es posible eliminar este registro porque esta siendo utilizado por otros registros",
            ]);
        }

        $old_configuracionPago = ConfiguracionPago::find($configuracionPago->id);
        $configuracionPago->delete();

        // eliminar qr
        \File::delete(public_path("imgs/configuracion_pagos/" . $old_configuracionPago->imagen_qr));

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA CONFIGURACIÓN DE PAGO", $old_configuracionPago);

        return true;
    }

    /**
     * Cargar imagen QR
     *
     * @param ConfiguracionPago $configuracionPago
     * @param UploadedFile $file
     * @return void
     */
    private function cargarImagenQR(ConfiguracionPago $configuracionPago, UploadedFile $file): void
    {
        $nombre = "qr" . time();
        $configuracionPago->imagen_qr = $this->cargarArchivoService->cargarArchivo($file, public_path("imgs/configuracion_pagos"), $nombre);
        $configuracionPago->save();
    }
}
