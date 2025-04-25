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

    
}
