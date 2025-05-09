<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\ProductoSucursal;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductoSucursalService
{

    /**
     * Lista de todos los producto_sucursals
     *
     * @return Collection
     */
    public function listado($sucursal_id = -1, bool $con_stock = false): Collection
    {
        $producto_sucursals = ProductoSucursal::with(["sucursal", "producto"])->select("producto_sucursals.*");

        if (Auth::user()->sucursals_todo == 0) {
            $producto_sucursals->where("sucursal_id", Auth::user()->sucursal_id);
        }

        if ($sucursal_id != -1) {
            $producto_sucursals->where("sucursal_id", $sucursal_id);
        }

        if ($con_stock) {
            $producto_sucursals->where("stock_actual", ">", 0)->get();
        }

        $producto_sucursals = $producto_sucursals->get();
        return $producto_sucursals;
    }

    /**
     * Lista de producto_sucursals paginado con filtros
     *
     * @param integer $length
     * @param integer $page
     * @param string $search
     * @param array $columnsSerachLike
     * @param array $columnsFilter
     * @param array $columnsBetweenFilter
     * @param array $orderBy
     * @return LengthAwarePaginator
     */
    public function listadoPaginado(
        int $length,
        int $page,
        string $search = '',
        array $columnsSerachLike = [],
        array $columnsFilter = [],
        array $columnsBetweenFilter = [],
        array $orderBy = [],
        int $sucursal_id = 0
    ): LengthAwarePaginator {

        if (Auth::user()->sucursals_todo == 0) {
            $sucursal_id = Auth::user()->sucursal_id;
        }

        $producto_sucursals = Producto::select(
            'productos.*',
            DB::raw('(
            SELECT COALESCE(stock_actual, 0)
                FROM producto_sucursals
                WHERE producto_sucursals.producto_id = productos.id
                AND producto_sucursals.sucursal_id = ' . $sucursal_id . '
                LIMIT 1
            ) as stock_actual')
        );

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $producto_sucursals->where("producto_sucursals.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $producto_sucursals->whereBetween("producto_sucursals.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $producto_sucursals->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("productos.$col", "LIKE", "%$search%");
                }
            });
        }

        // ProductoSucursalamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $producto_sucursals->orderBy($value[0], $value[1]);
            }
        }

        return $producto_sucursals = $producto_sucursals->where("status", 1)->paginate($length, ['*'], 'page', $page);
    }

    /**
     * Incrementar el stock del producto sucursal
     *
     * @param Producto $producto
     * @param float $cantidad
     * @param integer $sucursal_id
     * @return App\Models\ProductoSucursal
     */
    public function incrementarStock(Producto $producto, float $cantidad, int $sucursal_id): ProductoSucursal
    {
        $sucursal_producto = ProductoSucursal::where("producto_id", $producto->id)
            ->where("sucursal_id", $sucursal_id)
            ->get()->first();
        if (!$sucursal_producto) {
            $sucursal_producto = $producto->producto_sucursals()->create([
                "sucursal_id" => $sucursal_id,
                "stock_actual" => $cantidad,
            ]);
        } else {
            $sucursal_producto->stock_actual = (float)$sucursal_producto->stock_actual + $cantidad;
            $sucursal_producto->save();
        }

        return $sucursal_producto;
    }

    /**
     * Decrementar el stock de un producto sucursal
     *
     * @param Producto $producto
     * @param float $cantidad
     * @param integer $sucursal_id
     * @return App\Models\ProductoSucursal
     */
    public function decrementarStock(Producto $producto, float $cantidad, int $sucursal_id): ProductoSucursal|null
    {
        $sucursal_producto = ProductoSucursal::where("producto_id", $producto->id)
            ->where("sucursal_id", $sucursal_id)
            ->get()->first();
        if ($sucursal_producto) {
            Log::debug($sucursal_producto);
            $sucursal_producto->stock_actual = (float)$sucursal_producto->stock_actual - $cantidad;
            $sucursal_producto->save();
            Log::debug("BBB");
        }

        return $sucursal_producto;
    }

    /**
     * Verificar el stock del producto
     *
     * @param integer $producto_id
     * @param integer $sucursal_id
     * @param float $cantidad
     * @return array[bool,float]
     */
    public function verificaStockSucursalProducto(int $producto_id, int $sucursal_id, float $cantidad): array
    {
        $resultado = [false, 0];
        $producto_sucursal = ProductoSucursal::where("producto_id", $producto_id)
            ->where("sucursal_id", $sucursal_id)
            ->get()->first();
        if ($producto_sucursal) {
            $stock_actual = (float)$producto_sucursal->stock_actual;
            $resultado[1] = $stock_actual;
            if ($stock_actual >= $cantidad) {
                $resultado[0] = true;
            }
        }

        return $resultado;
    }

    /**
     * Obtener un producto de una sucursal
     *
     * @param integer $producto_id
     * @param integer $sucursal_id
     * @return ProductoSucursal
     */
    public function getProductoSucursal(int $producto_id, int $sucursal_id): ProductoSucursal
    {
        $producto_sucursal = ProductoSucursal::where("producto_id", $producto_id)
            ->where("sucursal_id", $sucursal_id)
            ->get()->first();

        if (!$producto_sucursal) {
            if ($producto_id == 0 || $sucursal_id == 0) {
                throw new Exception("Debes seleccionar la sucursal y el producto");
            }
            $producto_sucursal = ProductoSucursal::create([
                "sucursal_id" => $sucursal_id,
                "producto_id" => $producto_id,
                "stock_actual" => 0,
            ]);
        }

        return $producto_sucursal->load(["producto"]);
    }


    /**
     * Buscar produto en las sucursales
     *
     * @param string $search
     * @return Collection
     */
    public function buscarProductoSucursales(string $search = "", bool $orderStock = false): Collection
    {
        $producto_sucursals = ProductoSucursal::with(["producto", "sucursal"])
            ->select("producto_sucursals.*")
            ->join("productos", "productos.id", "=", "producto_sucursals.producto_id")
            ->where("stock_actual", ">", 0);

        if ($search) {
            $producto_sucursals->where("productos.nombre", "LIKE", "%$search%");
        }

        if ($orderStock) {
            $producto_sucursals->orderBy("stock_actual", "desc");
        }
        $producto_sucursals = $producto_sucursals->get();

        return $producto_sucursals;
    }
}
