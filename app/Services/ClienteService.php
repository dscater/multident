<?php

namespace App\Services;

use App\Models\OrdenVenta;
use App\Services\HistorialAccionService;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ClienteService
{
    private $modulo = "CLIENTES";

    public function __construct(private HistorialAccionService $historialAccionService) {}

    public function listado(): Collection
    {
        $clientes = Cliente::select("clientes.*")->where("status", 1)->get();
        return $clientes;
    }

    public function listadoDataTable(int $length, int $start, int $page, string $search): LengthAwarePaginator
    {
        $clientes = Cliente::select("clientes.*");
        if ($search && trim($search) != '') {
            $clientes->where("nombres", "LIKE", "%$search%");
            $clientes->orWhere("apellidos", "LIKE", "%$search%");
            $clientes->orWhere("ci", "LIKE", "%$search%");
            $clientes->orWhere("descripcion", "LIKE", "%$search%");
        }
        $clientes->where("status", 1);
        $clientes = $clientes->paginate($length, ['*'], 'page', $page);
        return $clientes;
    }

    /**
     * Crear cliente
     *
     * @param array $datos
     * @return Cliente
     */
    public function crear(array $datos): Cliente
    {

        $cliente = Cliente::create([
            "nombres" => mb_strtoupper($datos["nombres"]),
            "apellidos" => mb_strtoupper($datos["apellidos"]),
            "ci" => mb_strtoupper($datos["ci"]),
            "cel" => mb_strtoupper($datos["cel"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "fecha_registro" => date("Y-m-d")
        ]);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN CLIENTE", $cliente);

        return $cliente;
    }

    /**
     * Actualizar cliente
     *
     * @param array $datos
     * @param Cliente $cliente
     * @return Cliente
     */
    public function actualizar(array $datos, Cliente $cliente): Cliente
    {
        $old_cliente = Cliente::find($cliente->id);
        $cliente->update([
            "nombres" => mb_strtoupper($datos["nombres"]),
            "apellidos" => mb_strtoupper($datos["apellidos"]),
            "ci" => mb_strtoupper($datos["ci"]),
            "cel" => mb_strtoupper($datos["cel"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]),
        ]);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN CLIENTE", $old_cliente, $cliente);

        return $cliente;
    }

    /**
     * Eliminar cliente
     *
     * @param Cliente $cliente
     * @return boolean
     */
    public function eliminar(Cliente $cliente): bool
    {
        // verificar usos
        $usos = OrdenVenta::where("cliente_id", $cliente->id)->where("status", 1)->get();
        if (count($usos) > 0) {
            throw ValidationException::withMessages([
                'error' =>  "No es posible eliminar este registro porque esta siendo utilizado por otros registros",
            ]);
        }

        // no eliminar clientes predeterminados para el funcionamiento del sistema
        $old_cliente = clone $cliente;
        $cliente->status = 0;
        $cliente->save();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN CLIENTE", $old_cliente);

        return true;
    }
}
