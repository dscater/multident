<?php

namespace App\Services;

use App\Models\HistorialAccion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class HistorialAccionService
{
    private $descripcion = "EL USUARIO ";
    /**
     * Registrar historial de accion
     *
     * @param string $modulo
     * @param string $accion
     * @param string $descripcion
     * @return void
     */
    public function registrarAccion(string $modulo, string $accion, string $descripcion = "", Model $modelo, Model $modelo_update = null): void
    {
        $user = Auth::user();
        $user_id = $user->id;
        $this->descripcion .= $user->usuario . " " . $descripcion;
        $datos_original = $modelo->makeHidden($modelo->getAppends())->toArray();
        $datos = [
            "user_id" => $user_id,
            "accion" => $accion,
            "descripcion" => $this->descripcion,
            'datos_original' => $datos_original,
            'datos_nuevo' => null,
            'modulo' => $modulo
        ];

        if ($modelo_update) {
            if ($modelo_update->wasChanged()) {
                // actualizacion
                $datos["datos_nuevo"] = $modelo_update ? $modelo_update->makeHidden($modelo_update->getAppends())->toArray() : null;
                $this->crearAccion($datos);
            }
        } else {
            $this->crearAccion($datos);
        }
    }

    /**
     * Registrar acciones de relaciones de un modelo
     *
     * @param string $modulo
     * @param string $accion
     * @param string $descripcion
     * @param [type] $datos_original
     * @param [type] $datos_nuevo
     * @return void
     */
    public function registrarAccionRelaciones(string $modulo, string $accion, string $descripcion, $datos_original, $datos_nuevo = null): void
    {
        $user = Auth::user();
        $this->descripcion = $user->usuario . " " . $descripcion;

        $this->crearAccion([
            "user_id" => $user->id,
            "accion" => $accion,
            "descripcion" => $this->descripcion,
            "datos_original" => $datos_original,
            'datos_nuevo' => $datos_nuevo,
            "modulo" => $modulo,
        ]);
    }

    private function crearAccion(array $datos): void
    {
        HistorialAccion::create([
            "user_id" => $datos["user_id"],
            "accion" => $datos["accion"],
            "descripcion" => $datos["descripcion"],
            'datos_original' => $datos["datos_original"],
            'datos_nuevo' => $datos["datos_nuevo"],
            'modulo' => $datos["modulo"],
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s')
        ]);
    }
}
