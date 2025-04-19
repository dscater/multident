<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromocionStoreRequest;
use App\Http\Requests\PromocionUpdateRequest;
use App\Models\Promocion;
use App\Services\PromocionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PromocionController extends Controller
{
    public function __construct(private PromocionService $promocionService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): InertiaResponse
    {
        return Inertia::render("Admin/Promocions/Index");
    }

    /**
     * Listado de promocions
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "promocions" => $this->promocionService->listado()
        ]);
    }

    /**
     * Listado de promocions para portal
     *
     * @return JsonResponse
     */
    public function listadoPortal(): JsonResponse
    {
        return response()->JSON([
            "promocions" => $this->promocionService->listado()
        ]);
    }

    /**
     * Endpoint para obtener la lista de promocions paginado para datatable
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

        $usuarios = $this->promocionService->listadoDataTable($length, $start, $page, $search);

        return response()->JSON([
            'data' => $usuarios->items(),
            'recordsTotal' => $usuarios->total(),
            'recordsFiltered' => $usuarios->total(),
            'draw' => intval($request->input('draw')),
        ]);
    }

    /**
     * Registrar un nuevo promocion
     *
     * @param PromocionStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(PromocionStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Promocion
            $this->promocionService->crear($request->validated());
            DB::commit();
            return redirect()->route("promocions.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un promocion
     *
     * @param Promocion $promocion
     * @return JsonResponse
     */
    public function show(Promocion $promocion): JsonResponse
    {
        return response()->JSON($promocion);
    }

    public function update(Promocion $promocion, PromocionUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar promocion
            $this->promocionService->actualizar($request->validated(), $promocion);
            DB::commit();
            return redirect()->route("promocions.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar promocion
     *
     * @param Promocion $promocion
     * @return JsonResponse|Response
     */
    public function destroy(Promocion $promocion): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->promocionService->eliminar($promocion);
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
