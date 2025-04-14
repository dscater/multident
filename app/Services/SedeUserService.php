<?php

namespace App\Services;

use App\Models\Sede;
use Illuminate\Support\Facades\Auth;

class SedeUserService
{

    public function getArraySedesIdUser(): array
    {
        $sedes_id = [];
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->sedes_todo != 1) {
                $sedes_id = $user->sedes()->pluck("sedes.id")->toArray();
            } else {
                $sedes_id = Sede::get()->pluck("id")->toArray();
            }
        }
        return $sedes_id;
    }
}
