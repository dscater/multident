<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfiguracionRequest;
use App\Models\Configuracion;
use App\Services\ConfiguracionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ConfiguracionController extends Controller
{

    public function __construct(private ConfiguracionService $configuracionService) {}

    /**
     * Página configuracion
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $configuracion = Configuracion::first();
        return Inertia::render("Admin/Configuracions/Index", compact("configuracion"));
    }

    /**
     * Obtener configuracion
     *
     * @return JsonResponse
     */
    public function getConfiguracion(): JsonResponse
    {
        $configuracion = Configuracion::first();
        return response()->JSON([
            "configuracion" => $configuracion
        ], 200);
    }

    /**
     * Actualizar configuración
     *
     * @param Configuracion $configuracion
     * @param ConfiguracionRequest $request
     * @return RedirectResponse
     */
    public function update(ConfiguracionRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $this->configuracionService->actualizar($request->validated());
            DB::commit();
            return redirect()->route("configuracions.index")->with("success", "Registro correcto");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function show(Configuracion $configuracion) {}
}
