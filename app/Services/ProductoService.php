<?php

namespace App\Services;

use App\Models\DetalleVenta;
use App\Services\HistorialAccionService;
use App\Models\Producto;
use App\Models\ProductoImagen;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class ProductoService
{
    private $modulo = "PRODUCTOS";

    public function __construct(private HistorialAccionService $historialAccionService, private ProductoImagenService $productoImagenService) {}

    /**
     * Lista de todos los productos
     *
     * @return Collection
     */
    public function listado(): Collection
    {
        $productos = Producto::select("productos.*")->where("status", 1)->get();
        return $productos;
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
        $productos = Producto::with(["imagens", "categoria"])
            ->select("productos.*")
            ->leftJoin("detalle_ventas", "productos.id", "=", "detalle_ventas.producto_id")
            ->selectRaw("SUM(detalle_ventas.cantidad) as total_vendido")
            ->groupBy("productos.id");

        $productos->where("status", 1);

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $productos->where("productos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $productos->whereBetween("productos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $productos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("productos.$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $productos->orderBy($value[0], $value[1]);
            }
        }


        $productos = $productos->paginate($length, ['*'], 'page', $page);
        return $productos;
    }

    /**
     * Crear producto
     *
     * @param array $datos
     * @return Producto
     */
    public function crear(array $datos): Producto
    {
        $producto = Producto::create([
            "categoria_id" => mb_strtoupper($datos["categoria_id"]),
            "nombre" => mb_strtoupper($datos["nombre"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "stock_actual" => $datos["stock_actual"],
            "precio_compra" => $datos["precio_compra"],
            "precio_venta" => $datos["precio_venta"],
            "observaciones" => mb_strtoupper($datos["observaciones"]),
            "publico" => mb_strtoupper($datos["publico"]),
            "fecha_registro" => date("Y-m-d"),
        ]);


        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN PRODUCTO", $producto->load(["imagens"]));

        // registrar imagenes
        if (!empty($datos["imagens"])) {
            $datos_original = [];
            foreach ($datos["imagens"] as $key => $imagen) {
                $datos_original[] =  $this->productoImagenService->guardarImagenProducto($producto, $imagen["file"], $key);
            }

            // registrar imagens asignadas
            $this->historialAccionService->registrarAccionRelaciones($this->modulo, "CREACIÓN", "REGISTRÓ LAS IMAGENES DEL PRODUCTO " . $producto->nombre, $datos_original);
        }

        return $producto;
    }

    /**
     * Actualizar producto
     *
     * @param array $datos
     * @param Producto $producto
     * @return Producto
     */
    public function actualizar(array $datos, Producto $producto): Producto
    {
        $old_producto = Producto::find($producto->id);
        $producto->update([
            "categoria_id" => mb_strtoupper($datos["categoria_id"]),
            "nombre" => mb_strtoupper($datos["nombre"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "stock_actual" => $datos["stock_actual"],
            "precio_compra" => $datos["precio_compra"],
            "precio_venta" => $datos["precio_venta"],
            "observaciones" => mb_strtoupper($datos["observaciones"]),
            "publico" => mb_strtoupper($datos["publico"]),
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN PRODUCTO", $old_producto, $producto);

        // actualizar imagenes
        $existe_cambios = false;
        if (!empty($datos["imagens"])) {
            foreach ($datos["imagens"] as $key => $imagen) {
                if ($imagen["id"] == 0) {
                    $this->productoImagenService->guardarImagenProducto($producto, $imagen["file"], $key);
                    $existe_cambios = true;
                }
            }
        }

        // imagenes eliminadas
        if (!empty($datos["eliminados_imagens"])) {
            foreach ($datos["eliminados_imagens"] as $key => $eliminado) {
                $productoImagen = ProductoImagen::find($eliminado);
                if ($productoImagen) {
                    $this->productoImagenService->eliminarImagenProducto($productoImagen);
                    $existe_cambios = true;
                }
            }
        }

        if ($existe_cambios) {
            // registrar imagens asignadas
            $datos_original = $producto->imagens->map(function ($imagen) {
                return $imagen->makeHidden($imagen->getAppends())->toArray();
            });

            $datos_nuevo = $producto->imagens->map(function ($imagen) {
                return $imagen->makeHidden($imagen->getAppends())->toArray();
            });
            $this->historialAccionService->registrarAccionRelaciones($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ LAS IMAGENES DEL PRODUCTO " . $producto->nombre, $datos_original, $datos_nuevo);
        }
        return $producto;
    }

    /**
     * Eliminar producto
     *
     * @param Producto $producto
     * @return boolean
     */
    public function eliminar(Producto $producto): bool
    {
        // verificar usos
        $usos = DetalleVenta::where("producto_id", $producto->id)->get();
        if (count($usos) > 0) {
            throw ValidationException::withMessages([
                'error' =>  "No es posible eliminar este registro porque esta siendo utilizado por otros registros",
            ]);
        }

        // no eliminar productos predeterminados para el funcionamiento del sistema
        $old_producto = Producto::find($producto->id);
        $producto->status = 1;
        $producto->save();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN PRODUCTO", $old_producto);

        return true;
    }
}
