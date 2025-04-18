<?php

namespace App\Http\Controllers;

use App\Http\Requests\IngresoProductoStoreRequest;
use App\Http\Requests\IngresoProductoUpdateRequest;
use App\Models\HistorialAccion;
use App\Models\IngresoProducto;
use App\Models\KardexProducto;
use App\Models\Producto;
use App\Services\HistorialAccionService;
use App\Services\IngresoProductoService;
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

class IngresoProductoController extends Controller
{
    private $modulo = "INGRESO DE PRODUCTOS";

    public function __construct(private HistorialAccionService $historialAccionService, private IngresoProductoService $ingresoProductoService) {}

    /**
     * Página index
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        return Inertia::render("Admin/IngresoProductos/Index");
    }

    /**
     * Lista de registros
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "ingreso_productos" => $this->ingresoProductoService->listado()
        ]);
    }

    /**
     * Endpoint para obtener la lista de ingreso_productos paginado para data table
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

        $ingreso_productos = $this->ingresoProductoService->listadoPaginado($length, $page, $search);

        return response()->JSON([
            'data' => $ingreso_productos->items(),
            'recordsTotal' => $ingreso_productos->total(),
            'recordsFiltered' => $ingreso_productos->total(),
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
        return Inertia::render("Admin/IngresoProductos/Create");
    }

    /**
     * Registrar un nuevo ingreso de producto
     *
     * @param ProductoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(IngresoProductoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el IngresoProducto
            $this->ingresoProductoService->crear($request->validated());
            DB::commit();
            return redirect()->route("ingreso_productos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function show(IngresoProducto $ingreso_producto) {}


    /**
     * Página edit
     *
     * @return InertiaResponse
     */
    public function edit(IngresoProducto $ingreso_producto): InertiaResponse
    {
        $ingreso_producto = $ingreso_producto->load(["ingreso_detalles.producto"]);
        return Inertia::render("Admin/IngresoProductos/Edit", compact('ingreso_producto'));
    }

    public function update(IngresoProducto $ingreso_producto, IngresoProductoUpdateRequest $request)
    {
        $request->validate($this->validacion, $this->mensajes);
        DB::beginTransaction();
        try {
            $old_ingreso_producto = clone $ingreso_producto;

            // descontar el stock
            Producto::decrementarStock($ingreso_producto->producto, $ingreso_producto->cantidad);

            $datos_original = HistorialAccion::getDetalleRegistro($ingreso_producto, "ingreso_productos");
            $ingreso_producto->update(array_map('mb_strtoupper', $request->all()));
            // INCREMENTAR STOCK
            Producto::incrementarStock($ingreso_producto->producto, $ingreso_producto->cantidad);

            // actualizar kardex
            $kardex = KardexProducto::where("producto_id", $ingreso_producto->producto_id)
                ->where("tipo_registro", "INGRESO")
                ->where("registro_id", $ingreso_producto->id)
                ->get()->first();
            KardexProducto::actualizaRegistrosKardex($kardex->id, $kardex->producto_id);

            // registrar accion
            $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN INGRESO DE PRODUCTO", $old_ingreso_producto, $ingreso_producto);

            DB::commit();
            return redirect()->route("ingreso_productos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }
    public function destroy(IngresoProducto $ingreso_producto)
    {
        DB::beginTransaction();
        try {
            $old_ingreso_producto = clone $ingreso_producto;

            $eliminar_kardex = KardexProducto::where("tipo_registro", "INGRESO")
                ->where("registro_id", $ingreso_producto->id)
                ->where("producto_id", $ingreso_producto->producto_id)
                ->get()
                ->first();

            $id_kardex = $eliminar_kardex->id;
            $id_producto = $eliminar_kardex->producto_id;
            $eliminar_kardex->delete();

            $anterior = KardexProducto::where("producto_id", $id_producto)
                ->where("id", "<", $id_kardex)
                ->get()
                ->last();
            $actualiza_desde = null;
            if ($anterior) {
                $actualiza_desde = $anterior;
            } else {
                // comprobar si existen registros posteriorres al actualizado
                $siguiente = KardexProducto::where("producto_id", $id_producto)
                    ->where("id", ">", $id_kardex)
                    ->get()->first();
                if ($siguiente)
                    $actualiza_desde = $siguiente;
            }

            if ($actualiza_desde) {
                // actualizar a partir de este registro los sgtes. registros
                KardexProducto::actualizaRegistrosKardex($actualiza_desde->id, $actualiza_desde->producto_id);
            }

            // descontar el stock
            Producto::decrementarStock($ingreso_producto->producto, $ingreso_producto->cantidad);

            $ingreso_producto->delete();

            // registrar accion
            $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN INGRESO DE PRODUCTO", $old_ingreso_producto);

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
