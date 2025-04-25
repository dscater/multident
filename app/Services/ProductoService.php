<?php

namespace App\Services;

use App\Models\DetalleVenta;
use App\Services\HistorialAccionService;
use App\Models\Producto;
use App\Models\ProductoRelacion;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProductoService
{
    private $modulo = "PRODUCTOS";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

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
     * Listar productos que no sean el $producto_id
     *
     * @param integer $producto_id
     * @return Collection
     */
    public function listadoSinProducto(int $producto_id): Collection
    {
        $productos = Producto::select("productos.*")->where("status", 1)->where("id", "!=", $producto_id)->get();
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
        $productos = Producto::select("productos.*")
            ->leftJoin("detalle_ordens", "productos.id", "=", "detalle_ordens.producto_id")
            ->selectRaw("SUM(detalle_ordens.cantidad) as total_vendido")
            ->groupBy("productos.id");

        $productos->where("productos.status", 1);

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value) && trim($value) != '') {
                if ($key == 'fecha_registro') {
                    $value = date("Y-m-d", strtotime($value));
                }
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
            "nombre" => mb_strtoupper($datos["nombre"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "precio_pred" => $datos["precio_pred"],
            "precio_min" => $datos["precio_min"],
            "precio_fac" => $datos["precio_fac"],
            "precio_sf" => $datos["precio_sf"],
            "stock_maximo" => $datos["stock_maximo"],
            "fecha_registro" => date("Y-m-d"),
        ]);


        // cargar foto
        if ($datos["foto"] && !is_string($datos["foto"])) {
            $this->cargarFoto($producto, $datos["foto"]);
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN PRODUCTO", $producto);

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
            "nombre" => mb_strtoupper($datos["nombre"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "precio_pred" => $datos["precio_pred"],
            "precio_min" => $datos["precio_min"],
            "precio_fac" => $datos["precio_fac"],
            "precio_sf" => $datos["precio_sf"],
            "stock_maximo" => $datos["stock_maximo"],
        ]);

        // cargar foto
        if ($datos["foto"] && !is_string($datos["foto"])) {
            $this->cargarFoto($producto, $datos["foto"]);
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN PRODUCTO", $old_producto, $producto);
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

        // verificar usos
        $usos = ProductoRelacion::where("producto_id", $producto->id)->get();
        if (count($usos) > 0) {
            throw ValidationException::withMessages([
                'error' =>  "No es posible eliminar este registro porque esta siendo utilizado por otros registros",
            ]);
        }

        // verificar usos
        $usos = ProductoRelacion::where("producto_relacion", $producto->id)->get();
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

    /**
     * Cargar foto
     *
     * @param Producto $producto
     * @param UploadedFile $foto
     * @return void
     */
    public function cargarFoto(Producto $producto, UploadedFile $foto): void
    {
        if ($producto->foto) {
            \File::delete(public_path("imgs/productos/" . $producto->foto));
        }

        $nombre = $producto->id . time();
        $producto->foto = $this->cargarArchivoService->cargarArchivo($foto, public_path("imgs/productos"), $nombre);
        $producto->save();
    }
}
