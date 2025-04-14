<?php

namespace App\Http\Controllers;

use App\Models\NotificacionUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    /**
     * Notificaciones por usuario
     *
     * @return JsonResponse
     */
    public function listadoPorUsuario(): JsonResponse
    {
        $user = Auth::user();
        $notificacion_users = $user->notificacions()
            ->wherePivot("visto", 0)
            ->orderBy("notificacions.created_at", "desc")
            ->get();
        $sin_ver = count($notificacion_users);
        return response()->JSON([
            "notificacion_users" => $notificacion_users,
            "sin_ver" => $sin_ver,
        ]);
    }
}
