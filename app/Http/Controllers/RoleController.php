<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Models\HistorialAccion;
use App\Models\Modulo;
use App\Models\Permiso;
use App\Models\Role;
use App\Models\User;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as ResponseInertia;

class RoleController extends Controller
{

    public function __construct(private RoleService $roleService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Roles/Index");
    }

    /**
     * Listado de roles sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "roles" => $this->roleService->listado()
        ]);
    }

    /**
     * Endpoint para obtener la lista de roles paginado para datatable
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

        $usuarios = $this->roleService->listadoPaginado($length, $start, $page, $search);

        return response()->JSON([
            'data' => $usuarios->items(),
            'recordsTotal' => $usuarios->total(),
            'recordsFiltered' => $usuarios->total(),
            'draw' => intval($request->input('draw')),
        ]);
    }

    /**
     * Registrar un nuevo role
     *
     * @param RoleStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(RoleStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Role
            $this->roleService->crear($request->validated());
            DB::commit();
            return redirect()->route("roles.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un role
     *
     * @param Role $role
     * @return JsonResponse
     */
    public function show(Role $role): JsonResponse
    {
        return response()->JSON($role);
    }

    public function edit(Role $role): ResponseInertia
    {
        $modulos_group = Modulo::select('modulo')->distinct()->pluck('modulo');

        $array_modulos = [];
        $array_permisos = [];
        foreach ($modulos_group as $value) {
            $array_modulos[$value] = Modulo::where("modulo", $value)->get();
            $array_permisos[$value] = Permiso::select("modulos.nombre", "modulos.accion")->join("modulos", "modulos.id", "=", "permisos.modulo_id")
                ->where("role_id", $role->id)
                ->where("modulo", $value)->get();
        }

        return Inertia::render("Admin/Roles/Permisos", compact("role", "modulos_group", "array_modulos", "array_permisos"));
    }

    public function actualizaPermiso(Role $role, Request $request)
    {
        $sw_cambio = $request->sw_cambio;
        $modulo = $request->modulo;
        $accion = $request->accion;
        $o_modulo = Modulo::where("modulo", $modulo)->where("accion", $accion)->get()->first();
        $permiso = Permiso::where("role_id", $role->id)
            ->where("modulo_id", $o_modulo->id)
            ->get()->first();
        if ($sw_cambio == 1) {
            if (!$permiso) {
                $role->o_permisos()->create([
                    "modulo_id" => $o_modulo->id
                ]);
            }
        } else {
            if ($permiso) {
                $permiso->delete();
            }
        }

        $array_permisos = Permiso::select("modulos.nombre", "modulos.accion")
            ->join("modulos", "modulos.id", "=", "permisos.modulo_id")
            ->where("role_id", $role->id)
            ->where("modulos.modulo", $o_modulo->modulo)->get();

        return response()->JSON([
            "array_permisos" => $array_permisos
        ]);
    }

    public function update(Role $role, RoleUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar role
            $this->roleService->actualizar($request->validated(), $role);
            DB::commit();
            return redirect()->route("roles.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar role
     *
     * @param Role $role
     * @return JsonResponse|Response
     */
    public function destroy(Role $role): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->roleService->eliminar($role);
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'message' => 'El registro se eliminó correctamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }
}
