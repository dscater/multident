<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProformaStoreRequest;
use App\Http\Requests\ProformaUpdateRequest;
use App\Models\DetalleOrden;
use App\Models\Proforma;
use App\Services\HistorialAccionService;
use App\Services\ProformaService;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;

class ProformaController extends Controller
{
    public function __construct(private HistorialAccionService $historialAccionService, private ProformaService $proformaService) {}

    /**
     * Página index
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        return Inertia::render("Admin/Proformas/Index");
    }

    /**
     * Lista de registros
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "proformas" => $this->proformaService->listado()
        ]);
    }

    /**
     * Endpoint para obtener la lista de proformas paginado para data table
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

        $proformas = $this->proformaService->listadoPaginado($length, $page, $search);

        return response()->JSON([
            'data' => $proformas->items(),
            'recordsTotal' => $proformas->total(),
            'recordsFiltered' => $proformas->total(),
            'draw' => intval($request->input('draw')),
        ]);
    }


    /**
     * Página create
     *
     * @return InertiaResponse
     */
    public function create(): InertiaResponse
    {
        return Inertia::render("Admin/Proformas/Create");
    }

    /**
     * Registrar un nuevo ingreso de producto
     *
     * @param ProductoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(ProformaStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Proforma
            // Log::debug($request->validated());
            $proforma = $this->proformaService->crear($request->validated());

            // Guardar el ID en sesión
            session(['venta_id' => $proforma->id]);

            DB::commit();
            return redirect()->route("proformas.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function show(Proforma $proforma, Request $request)
    {
        $detalle_proformas = [];
        if (isset($request->detalle_proforma_id)) {
            $detalle_proformas = DetalleOrden::where("status", 1)
                ->where("proforma_id", $proforma->id)
                ->orWhere("id", $request->detalle_proforma_id)
                ->get();
        }

        return response()->JSON([
            "proforma" => $proforma->load(["detalle_proformas.producto"]),
            "detalle_proformas" => $detalle_proformas
        ]);
    }

    public function generarPdf(Proforma $proforma)
    {
        return $this->proformaService->generarPdf($proforma);
    }


    /**
     * Página edit
     *
     * @return InertiaResponse
     */
    public function edit(Proforma $proforma): InertiaResponse
    {
        $proforma = $proforma->load(["detalle_proformas.producto"]);
        return Inertia::render("Admin/Proformas/Edit", compact('proforma'));
    }

    public function update(Proforma $proforma, ProformaUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar el Proforma
            $proforma = $this->proformaService->actualizar($request->validated(), $proforma);
            // Guardar el ID en sesión
            session(['venta_id' => $proforma->id]);

            DB::commit();
            return redirect()->route("proformas.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }
    public function destroy(Proforma $proforma)
    {
        DB::beginTransaction();
        try {
            // eliminar Proforma
            $this->proformaService->eliminar($proforma);
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
