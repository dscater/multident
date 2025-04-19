<?php

namespace App\Services;

use App\Models\KardexProducto;
use App\Services\HistorialAccionService;
use App\Models\SalidaProducto;
use App\Models\Producto;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SalidaProductoService
{
    private $modulo = "SALIDA DE PRODUCTOS";

    public function __construct(
        private HistorialAccionService $historialAccionService,
        private KardexProductoService $kardexProductoService,
        private ProductoSucursalService $productoSucursalService
    ) {}

    public function listado(): Collection
    {
        $salida_productos = SalidaProducto::with(["sucursal"])->select("salida_productos.*")->where("status", 1)->get();
        return $salida_productos;
    }

    /**
     * Lista de productos paginado con filtros
     *
     * @param integer $length
     * @param integer $page
     * @param string $search
     * @param array $columnsSerachLike
     * @param array $columnsFilter
     * @return LengthAwarePaginator
     */
    public function listadoPaginado(int $length, int $page, string $search, array $columnsSerachLike = [], array $columnsFilter = [], array $columnsBetweenFilter = [], array $orderBy = []): LengthAwarePaginator
    {
        $salida_productos = SalidaProducto::with(["sucursal", "producto"])->select("salida_productos.*")
            ->leftJoin("productos", "productos.id", "=", "salida_productos.producto_id")
            ->groupBy("salida_productos.id");

        $salida_productos->where("salida_productos.status", 1);

        if (Auth::user()->sucursals_todo == 0) {
            $salida_productos->where("sucursal_id", Auth::user()->sucursal_id);
        }

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value) && trim($value) != '') {
                $salida_productos->where("salida_productos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $salida_productos->whereBetween("salida_productos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $salida_productos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    if ($col == 'fecha_registro') {
                        $array_fecha = explode("/", $search);
                        $array_fecha = array_reverse($array_fecha);
                        if (count($array_fecha) > 0) {
                            $search = "";
                            foreach ($array_fecha as $key => $text) {
                                $search .= $text . ($key < count($array_fecha) - 1 ? '-' : '');
                            }
                        }
                    }
                    $query->orWhere("productos.$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $salida_productos->orderBy($value[0], $value[1]);
            }
        }

        $salida_productos = $salida_productos->paginate($length, ['*'], 'page', $page);
        return $salida_productos;
    }


    /**
     * Crear salida_producto
     *
     * @param array $datos
     * @return SalidaProducto
     */
    public function crear(array $datos): SalidaProducto
    {
        $producto = Producto::findOrFail($datos["producto_id"]);

        // verificar stock
        $verificacion = $this->productoSucursalService->verificaStockSucursalProducto($producto->id, (int)$datos["sucursal_id"], (float)$datos["cantidad"]);
        if (!$verificacion[0]) {
            throw new Exception("La cantidad ingresada " . $datos["cantidad"] . " no puede ser superior al stock actual de " . $verificacion[1]);
        }

        $salida_producto = SalidaProducto::create([
            "sucursal_id" => $datos["sucursal_id"],
            "producto_id" => $datos["producto_id"],
            "cantidad" => $datos["cantidad"],
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "fecha_registro" => date("Y-m-d")
        ]);

        // registrar kardex
        $this->kardexProductoService->registroEgreso("SALIDA DE PRODUCTO", $producto, (float)$salida_producto->cantidad, (float)$producto->precio_pred, $salida_producto->descripcion, $salida_producto->sucursal_id, "SalidaProducto", $salida_producto->id);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA SALIDA DE PRODUCTOS", $salida_producto, null);

        return $salida_producto;
    }

    /**
     * Actualizar salida_producto
     *
     * @param array $datos
     * @param SalidaProducto $salida_producto
     * @return SalidaProducto
     */
    public function actualizar(array $datos, SalidaProducto $salida_producto): SalidaProducto
    {
        $old_salida_producto = clone $salida_producto;
        $old_sucursal = $old_salida_producto->sucursal_id;

        // incrementar el stock
        $producto = Producto::findOrFail($datos["producto_id"]);
        $this->productoSucursalService->incrementarStock($producto, (float)$salida_producto->cantidad, $old_salida_producto->sucursal_id);

        // verificar stock
        $verificacion = $this->productoSucursalService->verificaStockSucursalProducto($producto->id, (int)$datos["sucursal_id"], (float)$datos["cantidad"]);
        if (!$verificacion[0]) {
            throw new Exception("La cantidad ingresada " . $datos["cantidad"] . " no puede ser mayor a " . $verificacion[1]);
        }

        $salida_producto->update([
            "sucursal_id" => $datos["sucursal_id"],
            "producto_id" => $datos["producto_id"],
            "cantidad" => $datos["cantidad"],
            "descripcion" => mb_strtoupper($datos["descripcion"]),
        ]);

        //descontar stock
        $this->productoSucursalService->decrementarStock($producto, (float)$salida_producto->cantidad, $salida_producto->sucursal_id);

        // actualizar kardex
        $kardex = KardexProducto::where("producto_id", $salida_producto->producto_id)
            ->where("tipo_registro", "SALIDA DE PRODUCTO")
            ->where("modulo", "SalidaProducto")
            ->where("registro_id", $salida_producto->id)
            ->where("sucursal_id", $salida_producto->sucursal_id);
        $kardex = $kardex->get()->first();

        $this->kardexProductoService->actualizaRegistrosKardex($kardex ? $kardex->id : 0, $producto->id, $salida_producto->sucursal_id);

        if ($old_sucursal && $old_sucursal != 0 && ($old_sucursal != $salida_producto->sucursal_id)) {
            // actualizar kardex
            $kardex = KardexProducto::where("producto_id", $salida_producto->producto_id)
                ->where("tipo_registro", "SALIDA DE PRODUCTO")
                ->where("modulo", "SalidaProducto")
                ->where("registro_id", $salida_producto->id)
                ->where("sucursal_id", $old_sucursal);
            $kardex = $kardex->get()->first();

            $this->kardexProductoService->actualizaRegistrosKardex($kardex ? $kardex->id : 0, $producto->id, $old_sucursal);
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA SALIDA DE PRODUCTOS", $old_salida_producto, $salida_producto);

        return $salida_producto;
    }

    /**
     * Eliminar salida_producto
     *
     * @param SalidaProducto $salida_producto
     * @return boolean
     */
    public function eliminar(SalidaProducto $salida_producto): bool
    {
        $old_salida_producto = clone $salida_producto;

        $salida_producto->status = 0;
        $salida_producto->save();

        // registrar kardex
        $this->kardexProductoService->registroIngreso($salida_producto->sucursal_id, "SALIDA DE PRODUCTO", $salida_producto->producto, (float)$salida_producto->cantidad, (float)$salida_producto->producto->precio_pred, "ELIMINACIÓN DE SALIDA DE PRODUCTO", "SalidaProducto", $salida_producto->id);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA SALIDA DE PRODUCTOS", $old_salida_producto, $salida_producto);

        return true;
    }
}
