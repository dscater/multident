<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoStoreRequest;
use App\Http\Requests\ProductoUpdateRequest;
use App\Models\Producto;
use App\Services\ProductoService;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ProductoController extends Controller
{
    public function __construct(private ProductoService $productoService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): InertiaResponse
    {
        return Inertia::render("Admin/Productos/Index");
    }

    /**
     * Listado de productos
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "productos" => $this->productoService->listado()
        ]);
    }

    /**
     * Obtener registro de productos paginado
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function productosPaginadoPortal(Request $request): JsonResponse
    {
        $perPage = $request->perPage;
        $page = (int)($request->input("page", 1));
        $search = (string)$request->input("search", "");
        $precioDesde = $request->precioDesde;
        $precioHasta = $request->precioHasta;
        $categoria_id = $request->categoria_id;
        $orderByCol = $request->orderByCol;
        $desc = $request->desc;

        $columnsSerachLike = ["nombre", "descripcion"];
        $columnsFilter = [
            "categoria_id" => $categoria_id,
            "publico" => "HABILITADO",
        ];
        $columnsBetweenFilter = [
            "precio_venta" => [$precioDesde, $precioHasta]
        ];

        $arrayOrderBy = [];
        if ($orderByCol && $desc) {
            $arrayOrderBy = [
                [$orderByCol, $desc]
            ];
        }

        $productos = $this->productoService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "total" => $productos->total(),
            "productos" => $productos->items(),
            "lastPage" => $productos->lastPage()
        ]);
    }

    /**
     * Endpoint para obtener la lista de productos paginado para data table
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

        $productos = $this->productoService->listadoPaginado($length, $page, $search);

        return response()->JSON([
            'data' => $productos->items(),
            'recordsTotal' => $productos->total(),
            'recordsFiltered' => $productos->total(),
            'draw' => intval($request->input('draw')),
        ]);
    }

    /**
     * Listado de productos por cantidad para mostrar en el portal
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function productosInicioPortal(Request $request): JsonResponse
    {
        $tomar = 9;
        if ($request->tomar && $request->tomar != '') {
            $tomar = $request->tomar;
        }

        $productos = Producto::with(["imagens"])
            ->where("status", 1)
            ->where("publico", "HABILITADO");


        if ($request->categoria_id && $request->categoria_id != '') {
            $productos->where("categoria_id", $request->categoria_id);
        }

        $productos = $productos->orderBy("id", "desc")->get()->take($tomar);

        return response()->JSON($productos);
    }


    /**
     * Lista 6 productos mas vendidos/populares
     *
     * @return JsonResponse
     */
    public function populares(): JsonResponse
    {
        $productos = Producto::with(["imagens", "categoria"])
            ->select("productos.*")
            ->leftJoin("detalle_ventas", "productos.id", "=", "detalle_ventas.producto_id")
            ->selectRaw("SUM(detalle_ventas.cantidad) as total_vendido")
            ->groupBy("productos.id");
        $productos = $productos->orderBy("total_vendido", "desc")->get()->take(6);
        return response()->JSON($productos);
    }

    /**
     * Registrar un nuevo producto
     *
     * @param ProductoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(ProductoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Producto
            $this->productoService->crear($request->validated());
            DB::commit();
            return redirect()->route("productos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un producto
     *
     * @param Producto $producto
     * @return JsonResponse
     */
    public function show(Producto $producto): JsonResponse
    {
        return response()->JSON($producto->load(["imagens"]));
    }

    public function update(Producto $producto, ProductoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar producto
            $this->productoService->actualizar($request->validated(), $producto);
            DB::commit();
            return redirect()->route("productos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar producto
     *
     * @param Producto $producto
     * @return JsonResponse|Response
     */
    public function destroy(Producto $producto): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->productoService->eliminar($producto);
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
