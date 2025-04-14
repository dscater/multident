<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class RoleService
{

    private $modulo = "ROLES";
    private $no_delete_role = [1]; // super administrador|cliente

    public function __construct(private HistorialAccionService $historialAccionService) {}

    public function listado(): Collection
    {
        $roles = Role::select("roles.*")->where("usuarios", 1)->get();
        return $roles;
    }

    public function listadoPaginado(int $length, int $start, int $page, string $search): LengthAwarePaginator
    {
        $roles = Role::select("roles.*");
        if ($search && trim($search) != '') {
            $roles->where("nombre", "LIKE", "%$search%");
        }
        $roles = $roles->paginate($length, ['*'], 'page', $page);
        return $roles;
    }

    /**
     * Crear role
     *
     * @param array $datos
     * @return Role
     */
    public function crear(array $datos): Role
    {

        $role = Role::create([
            "nombre" => mb_strtoupper($datos["nombre"])
        ]);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN ROLE", $role);

        return $role;
    }

    /**
     * Actualizar role
     *
     * @param array $datos
     * @param Role $role
     * @return Role
     */
    public function actualizar(array $datos, Role $role): Role
    {
        $old_role = Role::find($role->id);
        $role->update([
            "nombre" => mb_strtoupper($datos["nombre"])
        ]);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN ROLE", $old_role, $role);

        return $role;
    }

    /**
     * Eliminar role
     *
     * @param Role $role
     * @return boolean
     */
    public function eliminar(Role $role): bool|Exception
    {
        // verificar usos
        $usos = User::where("role_id", $role->id)->get();
        if (count($usos) > 0) {
            throw ValidationException::withMessages([
                'error' =>  "No es posible eliminar este registro porque esta siendo utilizado por otros registros",
            ]);
        }

        // no eliminar roles predeterminados para el funcionamiento del sistema
        if (!in_array($role->id, $this->no_delete_role)) {
            $old_role = Role::find($role->id);
            $role->o_permisos()->delete();
            $role->delete();

            // registrar accion
            $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN ROLE", $old_role);

            return true;
        }

        throw new Exception("No es posible eliminar este role");
    }
}
