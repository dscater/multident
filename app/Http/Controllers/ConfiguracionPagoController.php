<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfiguracionPagoStoreRequest;
use App\Http\Requests\ConfiguracionPagoUpdateRequest;
use App\Models\ConfiguracionPago;
use App\Services\ConfiguracionPagoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ConfiguracionPagoController extends Controller
{
    public function __construct(private ConfiguracionPagoService $configuracionPagoService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): InertiaResponse
    {
        return Inertia::render("Admin/ConfiguracionPagos/Index");
    }

    /**
     * Listado de configuracionPagos
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "configuracionPagos" => $this->configuracionPagoService->listado()
        ]);
    }

    /**
     * Listado de configuracionPagos para el portal
     *
     * @return JsonResponse
     */
    public function listadoPortal(): JsonResponse
    {
        return response()->JSON([
            "configuracionPagos" => $this->configuracionPagoService->listado()
        ]);
    }

    /**
     * Endpoint para obtener la lista de configuracionPagos paginado para datatable
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

        $usuarios = $this->configuracionPagoService->listadoDataTable($length, $start, $page, $search);

        return response()->JSON([
            'data' => $usuarios->items(),
            'recordsTotal' => $usuarios->total(),
            'recordsFiltered' => $usuarios->total(),
            'draw' => intval($request->input('draw')),
        ]);
    }

    /**
     * Registrar un nuevo configuracionPago
     *
     * @param ConfiguracionPagoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(ConfiguracionPagoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el ConfiguracionPago
            $this->configuracionPagoService->crear($request->validated());
            DB::commit();
            return redirect()->route("configuracion_pagos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un configuracionPago
     *
     * @param ConfiguracionPago $configuracionPago
     * @return JsonResponse
     */
    public function show(ConfiguracionPago $configuracionPago): JsonResponse
    {
        return response()->JSON($configuracionPago);
    }

    public function update(ConfiguracionPago $configuracionPago, ConfiguracionPagoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar configuracionPago
            $this->configuracionPagoService->actualizar($request->validated(), $configuracionPago);
            DB::commit();
            return redirect()->route("configuracion_pagos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar configuracionPago
     *
     * @param ConfiguracionPago $configuracionPago
     * @return JsonResponse|Response
     */
    public function destroy(ConfiguracionPago $configuracionPago): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->configuracionPagoService->eliminar($configuracionPago);
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
