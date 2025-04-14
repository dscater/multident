<?php

namespace App\Http\Controllers;

use App\Http\Requests\SucursalStoreRequest;
use App\Http\Requests\SucursalUpdateRequest;
use App\Models\Sucursal;
use App\Services\SucursalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class SucursalController extends Controller
{
    public function __construct(private SucursalService $sucursalService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): InertiaResponse
    {
        return Inertia::render("Admin/Sucursals/Index");
    }

    /**
     * Listado de sucursals
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "sucursals" => $this->sucursalService->listado()
        ]);
    }

    /**
     * Listado de sucursals para portal
     *
     * @return JsonResponse
     */
    public function listadoPortal(): JsonResponse
    {
        return response()->JSON([
            "sucursals" => $this->sucursalService->listado()
        ]);
    }

    /**
     * Endpoint para obtener la lista de sucursals paginado para datatable
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

        $usuarios = $this->sucursalService->listadoDataTable($length, $start, $page, $search);

        return response()->JSON([
            'data' => $usuarios->items(),
            'recordsTotal' => $usuarios->total(),
            'recordsFiltered' => $usuarios->total(),
            'draw' => intval($request->input('draw')),
        ]);
    }

    /**
     * Registrar un nuevo sucursal
     *
     * @param SucursalStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(SucursalStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Sucursal
            $this->sucursalService->crear($request->validated());
            DB::commit();
            return redirect()->route("sucursals.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un sucursal
     *
     * @param Sucursal $sucursal
     * @return JsonResponse
     */
    public function show(Sucursal $sucursal): JsonResponse
    {
        return response()->JSON($sucursal);
    }

    public function update(Sucursal $sucursal, SucursalUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar sucursal
            $this->sucursalService->actualizar($request->validated(), $sucursal);
            DB::commit();
            return redirect()->route("sucursals.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar sucursal
     *
     * @param Sucursal $sucursal
     * @return JsonResponse|Response
     */
    public function destroy(Sucursal $sucursal): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->sucursalService->eliminar($sucursal);
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
