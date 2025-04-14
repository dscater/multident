<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriaStoreRequest;
use App\Http\Requests\CategoriaUpdateRequest;
use App\Models\Categoria;
use App\Services\CategoriaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CategoriaController extends Controller
{
    public function __construct(private CategoriaService $categoriaService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): InertiaResponse
    {
        return Inertia::render("Admin/Categorias/Index");
    }

    /**
     * Listado de categorias
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "categorias" => $this->categoriaService->listado()
        ]);
    }

    /**
     * Listado de categorias para portal
     *
     * @return JsonResponse
     */
    public function listadoPortal(): JsonResponse
    {
        return response()->JSON([
            "categorias" => $this->categoriaService->listado()
        ]);
    }

    /**
     * Endpoint para obtener la lista de categorias paginado para datatable
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

        $usuarios = $this->categoriaService->listadoDataTable($length, $start, $page, $search);

        return response()->JSON([
            'data' => $usuarios->items(),
            'recordsTotal' => $usuarios->total(),
            'recordsFiltered' => $usuarios->total(),
            'draw' => intval($request->input('draw')),
        ]);
    }

    /**
     * Registrar un nuevo categoria
     *
     * @param CategoriaStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(CategoriaStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Categoria
            $this->categoriaService->crear($request->validated());
            DB::commit();
            return redirect()->route("categorias.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un categoria
     *
     * @param Categoria $categoria
     * @return JsonResponse
     */
    public function show(Categoria $categoria): JsonResponse
    {
        return response()->JSON($categoria);
    }

    public function update(Categoria $categoria, CategoriaUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar categoria
            $this->categoriaService->actualizar($request->validated(), $categoria);
            DB::commit();
            return redirect()->route("categorias.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar categoria
     *
     * @param Categoria $categoria
     * @return JsonResponse|Response
     */
    public function destroy(Categoria $categoria): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->categoriaService->eliminar($categoria);
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
