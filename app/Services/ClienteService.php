<?php

namespace App\Services;

use App\Models\Cliente;

class ClienteService
{
    private $modulo = "CLIENTES";

    public function __construct(private HistorialAccionService $historialAccionService) {}

    /**
     *
     * Actualizar informacion de cliente
     * @param array $datos
     * @return Cliente
     */
    public function updateInfoCliente(array $datos, Cliente $cliente): Cliente
    {
        $user = $cliente->user;

        $oldCliente = Cliente::find($cliente->id);
        $user->update([
            "usuario" => $datos["cliente"]["correo"],
            "nombres" => mb_strtoupper($datos["cliente"]["nombres"]),
            "apellidos" => mb_strtoupper($datos["cliente"]["apellidos"]),
            "correo" => $datos["cliente"]["correo"],
            "acceso" => $datos["acceso"],
        ]);

        $cliente->update([
            "nombres" => mb_strtoupper($datos["cliente"]["nombres"]),
            "apellidos" => mb_strtoupper($datos["cliente"]["apellidos"]),
            "cel" => $datos["cliente"]["cel"],
            "correo" => $datos["cliente"]["correo"],
        ]);

        // registrar accion
        $mensaje = "ACTUALIZÓ LA INFORMACIÓN DE UN CLIENTE";
        if ($datos["origen"] == "user") {
            $mensaje = "ACTUALIZÓ SU INFORMACIÓN";
        }
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", $mensaje, $oldCliente, $cliente);
        return $cliente;
    }
}
