<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\OrdenVenta;
use App\Models\Role;
use App\Models\SolicitudProducto;
use App\Models\User;

class NotificacionService
{
    private $usersNotificados;

    public function __construct(private SedeUserService $sedeUserService) {}

    /**
     * Crear notificacion orden de venta
     *
     * @param OrdenVenta $ordenVenta
     * @return Notificacion
     */
    public function crearNotificacionOrdenVenta(OrdenVenta $ordenVenta): Notificacion
    {
        $notificacion = Notificacion::create([
            "descripcion" => "Nueva orden de venta <b>" . $ordenVenta->codigo . "</b>",
            "fecha" => date("Y-m-d"),
            "hora" => date("H:i"),
            "modulo" => "OrdenVenta",
            "registro_id" => $ordenVenta->id,
        ]);

        // notificar a los usuarios
        $this->generarUsuariosNotificacion("orden_ventas.todos");
        $this->notificarUsuarios($notificacion);

        return $notificacion;
    }
    /**
     * Crear notificacion orden de venta
     *
     * @param SolicitudProducto $solicitudProducto
     * @return Notificacion
     */
    public function crearNotificacionSolicitudProducto(SolicitudProducto $solicitudProducto): Notificacion
    {
        $notificacion = Notificacion::create([
            "descripcion" => "Nueva solicitud de producto <b>" . $solicitudProducto->codigo_solicitud . "</b>",
            "fecha" => date("Y-m-d"),
            "hora" => date("H:i"),
            "modulo" => "SolicitudProducto",
            "registro_id" => $solicitudProducto->id,
        ]);

        // notificar a los usuarios
        $this->generarUsuariosNotificacion("solicitud_productos.todos");
        $this->notificarUsuarios($notificacion);

        return $notificacion;
    }

    private function generarUsuariosNotificacion(string $modulo): void
    {
        $roles = Role::join("permisos", "permisos.role_id", "=", "roles.id")
            ->join("modulos", "modulos.id", "=", "permisos.modulo_id")
            ->where("modulos.nombre", $modulo)
            ->distinct()
            ->pluck("roles.id")
            ->toArray();
        $roles[] = 1;

        $this->usersNotificados = User::whereIn("role_id", $roles)->get();
    }

    /**
     * Notificar a los usuarios correspondientes
     *
     * @param Notificacion $notificacion
     * @return void
     */
    private function notificarUsuarios(Notificacion $notificacion): void
    {
        foreach ($this->usersNotificados as $user) {
            if ($notificacion->modulo == 'SolicitudProducto') {
                $sedes_user_id =  $this->sedeUserService->getArraySedesIdUser();
                $solicitudProducto = SolicitudProducto::find($notificacion->registro_id);
                if (in_array($solicitudProducto->sede_id, $sedes_user_id) || $user->sedes_todo === 1) {
                    $user->notificacions()->attach($notificacion->id, ["visto" => 0]);
                }
            } else {
                $user->notificacions()->attach($notificacion->id, ["visto" => 0]);
            }
        }
    }
}
