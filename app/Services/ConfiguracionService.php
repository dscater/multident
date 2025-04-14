<?php

namespace App\Services;

use App\Models\Configuracion;
use App\Services\CargarArchivoService;
use Illuminate\Http\UploadedFile;

class ConfiguracionService
{

    public function __construct(private CargarArchivoService $cargarArchivoService) {}

    /**
     * Actualizar configuracion
     *
     * @param array $datos
     * @return Configuracion
     */
    public function actualizar(array $datos): Configuracion
    {
        $configuracion = Configuracion::first();
        if (!$configuracion) {
            $configuracion = Configuracion::create([
                "nombre_sistema" => $datos["nombre_sistema"],
                "alias" => $datos["alias"],
                "razon_social" =>  $datos["razon_social"],
            ]);
        } else {
            $configuracion->update([
                "nombre_sistema" => $datos["nombre_sistema"],
                "alias" => $datos["alias"],
                "razon_social" =>  $datos["razon_social"],
            ]);
        }

        // cargar logo
        if (!is_string($datos["logo"])) {
            $this->cargarLogo($configuracion, $datos["logo"]);
        }

        return $configuracion;
    }

    /**
     * Cargar logo
     *
     * @param Configuracion $configuracion
     * @param UploadedFile $file
     * @return void
     */
    private function cargarLogo(Configuracion $configuracion, UploadedFile $file): void
    {
        $nombre = "lg" . time();
        $configuracion->logo = $this->cargarArchivoService->cargarArchivo($file, public_path("imgs"), $nombre);
        $configuracion->save();
    }
}
