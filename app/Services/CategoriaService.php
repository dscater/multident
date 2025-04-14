<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CategoriaService
{
    private $modulo = "CATEGORÍAS";

    public function __construct(private HistorialAccionService $historialAccionService) {}

    public function listado(): Collection
    {
        $categorias = Categoria::select("categorias.*")->get();
        return $categorias;
    }

    public function listadoDataTable(int $length, int $start, int $page, string $search): LengthAwarePaginator
    {
        $categorias = Categoria::select("categorias.*");
        if ($search && trim($search) != '') {
            $categorias->where("nombre", "LIKE", "%$search%");
        }
        $categorias = $categorias->paginate($length, ['*'], 'page', $page);
        return $categorias;
    }

    /**
     * Crear categoria
     *
     * @param array $datos
     * @return Categoria
     */
    public function crear(array $datos): Categoria
    {

        $categoria = Categoria::create([
            "nombre" => mb_strtoupper($datos["nombre"]),
            "fecha_registro" => date("Y-m-d")
        ]);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA CATEGORÍA", $categoria);

        return $categoria;
    }

    /**
     * Actualizar categoria
     *
     * @param array $datos
     * @param Categoria $categoria
     * @return Categoria
     */
    public function actualizar(array $datos, Categoria $categoria): Categoria
    {
        $old_categoria = Categoria::find($categoria->id);
        $categoria->update([
            "nombre" => mb_strtoupper($datos["nombre"])
        ]);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA CATEGORÍA", $old_categoria, $categoria);

        return $categoria;
    }

    /**
     * Eliminar categoria
     *
     * @param Categoria $categoria
     * @return boolean
     */
    public function eliminar(Categoria $categoria): bool
    {
        // verificar usos
        $usos = Producto::where("categoria_id", $categoria->id)->get();
        if (count($usos) > 0) {
            throw ValidationException::withMessages([
                'error' =>  "No es posible eliminar este registro porque esta siendo utilizado por otros registros",
            ]);
        }

        // no eliminar categorias predeterminados para el funcionamiento del sistema
        $old_categoria = Categoria::find($categoria->id);
        $categoria->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA CATEGORÍA", $old_categoria);

        return true;
    }
}
