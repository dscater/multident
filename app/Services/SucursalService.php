<?php

namespace App\Services;

use App\Models\IngresoProducto;
use App\Services\HistorialAccionService;
use App\Models\Sucursal;
use App\Models\Producto;
use App\Models\ProductoSucursal;
use App\Models\SalidaProducto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SucursalService
{
    private $modulo = "SUCURSALES";

    public function __construct(private HistorialAccionService $historialAccionService) {}

    public function listado(): Collection
    {
        $sucursals = Sucursal::select("sucursals.*")->where("status", 1)->get();
        return $sucursals;
    }

    public function listadoDataTable(int $length, int $start, int $page, string $search): LengthAwarePaginator
    {
        $sucursals = Sucursal::with(["user"])->select("sucursals.*");
        if ($search && trim($search) != '') {
            $sucursals->where("nombre", "LIKE", "%$search%");
        }
        $sucursals->where("status", 1);
        $sucursals = $sucursals->paginate($length, ['*'], 'page', $page);
        return $sucursals;
    }

    /**
     * Crear sucursal
     *
     * @param array $datos
     * @return Sucursal
     */
    public function crear(array $datos): Sucursal
    {

        $sucursal = Sucursal::create([
            "codigo" => mb_strtoupper($datos["codigo"]),
            "nombre" => mb_strtoupper($datos["nombre"]),
            "direccion" => mb_strtoupper($datos["direccion"]),
            "fonos" => mb_strtoupper($datos["fonos"]),
            "user_id" => $datos["user_id"],
            "fecha_registro" => date("Y-m-d")
        ]);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA SUCURSAL", $sucursal);

        return $sucursal;
    }

    /**
     * Actualizar sucursal
     *
     * @param array $datos
     * @param Sucursal $sucursal
     * @return Sucursal
     */
    public function actualizar(array $datos, Sucursal $sucursal): Sucursal
    {
        $old_sucursal = Sucursal::find($sucursal->id);
        $sucursal->update([
            "codigo" => mb_strtoupper($datos["codigo"]),
            "nombre" => mb_strtoupper($datos["nombre"]),
            "direccion" => mb_strtoupper($datos["direccion"]),
            "fonos" => mb_strtoupper($datos["fonos"]),
            "user_id" => $datos["user_id"],
            "fecha_registro" => date("Y-m-d")
        ]);
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA SUCURSAL", $old_sucursal, $sucursal);

        return $sucursal;
    }

    /**
     * Eliminar sucursal
     *
     * @param Sucursal $sucursal
     * @return boolean
     */
    public function eliminar(Sucursal $sucursal): bool
    {
        // verificar usos
        $usos = IngresoProducto::where("sucursal_id", $sucursal->id)->where("status", 1)->get();
        if (count($usos) > 0) {
            throw ValidationException::withMessages([
                'error' =>  "No es posible eliminar este registro porque esta siendo utilizado por otros registros",
            ]);
        }

        $usos = SalidaProducto::where("sucursal_id", $sucursal->id)->where("status", 1)->get();
        if (count($usos) > 0) {
            throw ValidationException::withMessages([
                'error' =>  "No es posible eliminar este registro porque esta siendo utilizado por otros registros",
            ]);
        }

        $usos = ProductoSucursal::where("sucursal_id", $sucursal->id)->get();
        if (count($usos) > 0) {
            throw ValidationException::withMessages([
                'error' =>  "No es posible eliminar este registro porque esta siendo utilizado por otros registros",
            ]);
        }

        // no eliminar sucursals predeterminados para el funcionamiento del sistema
        $old_sucursal = Sucursal::find($sucursal->id);
        $sucursal->status = 0;
        $sucursal->save();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA SUCURSAL", $old_sucursal);

        return true;
    }
}
