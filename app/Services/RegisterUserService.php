<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterUserService
{
    /**
     * Crear user cliente
     *
     * @param array $datos
     * @return User
     */
    public function crear(array $datos): User
    {
        // crear usuario
        $user = User::create([
            "usuario" => $datos["correo"],
            "nombres" => mb_strtoupper($datos["nombres"]),
            "apellidos" => mb_strtoupper($datos["apellidos"]),
            "correo" => $datos["correo"],
            "password" => Hash::make($datos["password"]),
            "role_id" => 2,
            "acceso" => 1,
            "fecha_registro" => date("Y-m-d")
        ]);

        // asignar a cliente
        $user->cliente()->create([
            "nombres" => mb_strtoupper($datos["nombres"]),
            "apellidos" => mb_strtoupper($datos["apellidos"]),
            "cel" => $datos["cel"],
            "correo" => $datos["correo"],
            "fecha_registro" => date("Y-m-d")
        ]);
        return $user;
    }
}
