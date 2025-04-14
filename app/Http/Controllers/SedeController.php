<?php

namespace App\Http\Controllers;

use App\Models\Sede;
use Illuminate\Http\Request;

class SedeController extends Controller
{
    public function listado()
    {
        $sedes = Sede::select("sedes.*")->get();
        return response()->JSON([
            "sedes" => $sedes
        ]);
    }
}
