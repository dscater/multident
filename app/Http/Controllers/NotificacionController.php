<?php

namespace App\Http\Controllers;

use App\Models\NotificacionUser;
use App\Services\NotificacionUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificacionController extends Controller
{
    public function __construct(private NotificacionUserService $notificacionUserService) {}

    /**
     * Notificaciones por usuario
     *
     * @return JsonResponse
     */
    public function listadoPorUsuario(): JsonResponse
    {
        $user = Auth::user();
        $notificacion_users = NotificacionUser::with(["notificacion"])
            ->select("notificacion_users.*")
            ->where("user_id", $user->id)
            ->where("visto", 0)
            ->get();
        $sin_ver = count($notificacion_users);
        return response()->JSON([
            "notificacion_users" => $notificacion_users,
            "sin_ver" => $sin_ver,
        ]);
    }
    public function index(Request $request)
    {
        return Inertia::render("Admin/NotificacionUsers/Index");
    }

    /**
     * Endpoint para obtener la lista de notificacion_users paginado para data table
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function api(Request $request): JsonResponse
    {
        $length = (int)$request->input('length', 10); // Valor de `length` enviado por DataTable
        $start = (int)$request->input('start', 0); // Índice de inicio enviado por DataTable
        $page = (int)(($start / $length) + 1); // Cálculo de la página actual
        $search = (string)$request->input('search', '');

        $notificacion_users = $this->notificacionUserService->listadoPaginadoUser($length, $page, $search, Auth::user()->id);

        return response()->JSON([
            'data' => $notificacion_users->items(),
            'recordsTotal' => $notificacion_users->total(),
            'recordsFiltered' => $notificacion_users->total(),
            'draw' => intval($request->input('draw')),
        ]);
    }


    public function show(NotificacionUser $notificacion_user)
    {
        $notificacion_user->visto = 1;
        $notificacion_user->save();
        $notificacion_user = $notificacion_user->load(["notificacion"]);
        return Inertia::render("Admin/NotificacionUsers/Show", compact('notificacion_user'));
    }
}
