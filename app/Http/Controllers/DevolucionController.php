<?php

namespace App\Http\Controllers;

use App\Http\Requests\DevolucionStoreRequest;
use App\Http\Requests\DevolucionUpdateRequest;
use App\Models\Devolucion;
use App\Services\DevolucionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DevolucionController extends Controller
{
    public function __construct(private DevolucionService $devolucionService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): InertiaResponse
    {
        return Inertia::render("Admin/Devolucions/Index");
    }

    /**
     * Listado de devolucions
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "devolucions" => $this->devolucionService->listado()
        ]);
    }

    /**
     * Listado de devolucions para portal
     *
     * @return JsonResponse
     */
    public function listadoPortal(): JsonResponse
    {
        return response()->JSON([
            "devolucions" => $this->devolucionService->listado()
        ]);
    }

    /**
     * Endpoint para obtener la lista de devolucions paginado para datatable
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

        $usuarios = $this->devolucionService->listadoDataTable($length, $start, $page, $search);

        return response()->JSON([
            'data' => $usuarios->items(),
            'recordsTotal' => $usuarios->total(),
            'recordsFiltered' => $usuarios->total(),
            'draw' => intval($request->input('draw')),
        ]);
    }

    /**
     * Registrar un nuevo devolucion
     *
     * @param DevolucionStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(DevolucionStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Devolucion
            $this->devolucionService->crear($request->validated());
            DB::commit();
            return redirect()->route("devolucions.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un devolucion
     *
     * @param Devolucion $devolucion
     * @return JsonResponse
     */
    public function show(Devolucion $devolucion): JsonResponse
    {
        return response()->JSON($devolucion);
    }

    public function update(Devolucion $devolucion, DevolucionUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar devolucion
            $this->devolucionService->actualizar($request->validated(), $devolucion);
            DB::commit();
            return redirect()->route("devolucions.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar devolucion
     *
     * @param Devolucion $devolucion
     * @return JsonResponse|Response
     */
    public function destroy(Devolucion $devolucion): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->devolucionService->eliminar($devolucion);
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
