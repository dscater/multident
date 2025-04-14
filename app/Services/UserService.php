<?php

namespace App\Services;

use App\Models\Permiso;
use App\Models\Role;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    private $modulo = "USUARIOS";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

    /**
     * Obtener nombre de usuario
     *
     * @param string $nom
     * @param string $apep
     * @return string
     */
    public function getNombreUsuario(string $nom, string $apep): string
    {
        //determinando el nombre de usuario inicial del 1er_nombre+apep+tipoUser
        $nombre_user = substr(mb_strtoupper($nom), 0, 1); //inicial 1er_nombre
        $nombre_user .= mb_strtoupper($apep);
        return $nombre_user;
    }

    /**
     * Crear user
     *
     * @param array $datos
     * @return User
     */
    public function crear(array $datos): User
    {
        $total_sedes = Sede::get()->count();
        $sedes_todo = 0;
        if ($total_sedes === count($datos["array_sedes_id"])) {
            $sedes_todo = 1;
        }

        $user = User::create([
            "usuario" => $datos["correo"],
            "nombres" => mb_strtoupper($datos["nombres"]),
            "apellidos" => mb_strtoupper($datos["apellidos"]),
            "ci" => $datos["ci"],
            "ci_exp" => $datos["ci_exp"],
            "correo" => $datos["correo"],
            "password" => $datos["ci"],
            "role_id" => $datos["role_id"],
            "sedes_todo" => $sedes_todo,
            "acceso" => $datos["acceso"],
            "fecha_registro" => date("Y-m-d")
        ]);

        // cargar foto
        if ($datos["foto"] && !is_string($datos["foto"])) {
            $this->cargarFoto($user, $datos["foto"]);
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN USUARIO", $user);

        // registrar sedes
        if ($sedes_todo === 0) {
            $user->sedes()->sync($datos["array_sedes_id"]);
            // registrar sedes asignadas
            $sedes_original = [
                "sedes" => $user->sedes->pluck("id")
            ];
            $this->historialAccionService->registrarAccionRelaciones($this->modulo, "CREACIÓN", "REGISTRO LAS SEDES DEL USUARIO " . $user->usuario, $sedes_original);
        }

        return $user;
    }

    /**
     * Actualizar user
     *
     * @param array $datos
     * @param User $user
     * @return User
     */
    public function actualizar(array $datos, User $user): User
    {
        $old_user = User::find($user->id);

        $total_sedes = Sede::get()->count();
        $sedes_todo = 0;
        if ($total_sedes === count($datos["array_sedes_id"])) {
            $sedes_todo = 1;
        }

        $user->update([
            "usuario" => $datos["correo"],
            "nombres" => mb_strtoupper($datos["nombres"]),
            "apellidos" => mb_strtoupper($datos["apellidos"]),
            "ci" => $datos["ci"],
            "ci_exp" => $datos["ci_exp"],
            "correo" => $datos["correo"],
            "role_id" => $datos["role_id"],
            "sedes_todo" => $sedes_todo,
            "acceso" => $datos["acceso"],
            "fecha_registro" => date("Y-m-d")
        ]);

        // cargar foto
        if ($datos["foto"] && !is_string($datos["foto"])) {
            $this->cargarFoto($user, $datos["foto"]);
        }

        // registrar sedes
        if ($sedes_todo === 0) {
            $sedes_original = [
                "sedes" => $user->sedes->pluck("id")
            ];
            $changes = $user->sedes()->sync($datos["array_sedes_id"]);
            if (!empty($changes['attached']) || !empty($changes['detached']) || !empty($changes['updated'])) {
                // Se detectó un cambio en la relación
                // Log::info("Sedes actualizadas para el usuario {$user->id}", $changes);
                $this->historialAccionService->registrarAccionRelaciones($this->modulo, "MODIFICACIÓN", "ACTUALIZO LAS SEDES DEL USUARIO " . $user->usuario, $sedes_original, $changes);
            }
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN USUARIO", $old_user, $user->withoutRelations());

        return $user;
    }

    /**
     * Actualizar password
     *
     * @param array $datos
     * @param User $user
     * @return User
     */
    public function actualizarPassword(array $datos, User $user): User
    {
        $user->password = Hash::make($datos["password"]);
        $user->save();
        return $user;
    }

    /**
     * Cargar foto
     *
     * @param User $user
     * @param UploadedFile $foto
     * @return void
     */
    public function cargarFoto(User $user, UploadedFile $foto): void
    {
        if ($user->foto) {
            \File::delete(public_path("imgs/users/" . $this->user->foto));
        }

        $nombre = $user->id . time();
        $user->foto = $this->cargarArchivoService->cargarArchivo($foto, public_path("imgs/users"), $nombre);
        $user->save();
    }

    /**
     * Eliminar user
     *
     * @param Role $user
     * @return boolean
     */
    public function eliminar(User $user): bool
    {
        // no eliminar users predeterminados para el funcionamiento del sistema
        $old_user = User::find($user->id);
        $user->status = 0;
        $user->save();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ AL USUARIO " . $old_user->usuario, $old_user, $user);
        return true;
    }
}
