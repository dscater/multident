<?php

namespace App\Http\Controllers;

use App\Models\Parametrizacion;
use App\Services\SedeUserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InicioController extends Controller
{
    public function __construct(private SedeUserService $sedeUserService) {}

    public function inicio()
    {
        $array_infos = UserController::getInfoBoxUser($this->sedeUserService);
        return Inertia::render('Admin/Home', compact('array_infos'));
    }

    public function getParcialDatosPago()
    {
        $parametrizacion = Parametrizacion::first();
        $o_datos_banco = $parametrizacion->o_datos_banco;

        $html = vieW("parcials.datos_pago", compact("o_datos_banco"))->render();

        return response()->JSON($html);
    }
}
