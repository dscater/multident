<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ProductoRelacion;
use App\Services\ProductoRelacionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProductoRelacionController extends Controller
{

    public function __construct(private ProductoRelacionService $productoRelacionService) {}

    /**
     * Listado de productos relacionados por producto
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listadoPorProducto(Request $request, Producto $producto): JsonResponse
    {
        return response()->JSON([
            "producto_relacions" => $this->productoRelacionService->listadoPorProducto($producto->id ?? 0, $request["sucursal_id"] ? (int)$request["sucursal_id"] : 0)
        ]);
    }

    public function store(Request $request, Producto $producto)
    {
        DB::beginTransaction();
        try {
            // crear el Producto
            $this->productoRelacionService->crear([
                "producto_relacion" => $request->producto_relacion_id,
                "producto_id" => $producto->id,
            ]);
            DB::commit();

            if ($request->ajax()) {
                return response()->JSON([
                    "message" => "Correcto",
                ]);
            }

            return redirect()->route("productos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function destroy(ProductoRelacion $producto_relacion)
    {
        DB::beginTransaction();
        try {
            $this->productoRelacionService->eliminar($producto_relacion);
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
