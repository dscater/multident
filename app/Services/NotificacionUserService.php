<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\OrdenVenta;
use App\Models\SolicitudProducto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificacionUserService
{
    /**
     * Verificar el modulo y obtener el registro con el codigo para marcar visto
     *
     * @param string $codigo
     * @param string $modulo
     * @return void
     */
    public function verificarNotificacionUser(string $codigo, string $modulo): void
    {
        $user = Auth::user();
        if ($user) {
            // ORDEN VENTA
            if ($modulo == 'OrdenVenta') {
                $ordenVenta = OrdenVenta::where("codigo", $codigo)->get()->first();
                if ($ordenVenta) {
                    $notificacion = Notificacion::where("registro_id", $ordenVenta->id)->where("modulo", $modulo)->get()->first();
                    if ($notificacion) {
                        $this->marcarNotificaionUserVisto($notificacion->id, $user);
                    }
                }
            }
            // SOLICITUD PRODUCTO
            if ($modulo == 'SolicitudProducto') {
                $solicitudProducto = SolicitudProducto::where("codigo_solicitud", $codigo)->get()->first();
                if ($solicitudProducto) {
                    $notificacion = Notificacion::where("registro_id", $solicitudProducto->id)->where("modulo", $modulo)->get()->first();
                    if ($notificacion) {
                        $this->marcarNotificaionUserVisto($notificacion->id, $user);
                    }
                }
            }
        }
    }


    /**
     * Marcar la notificacion del usuario como visto(1)
     *
     * @param integer $notificacion_id
     * @param User $user
     * @return void
     */
    private function marcarNotificaionUserVisto(int $notificacion_id, User $user): void
    {
        $user->notificacions()->updateExistingPivot($notificacion_id, ['visto' => 1]);
    }
}
