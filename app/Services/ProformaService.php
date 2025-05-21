<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Devolucion;
use App\Services\DetalleProformaService;
use App\Services\HistorialAccionService;
use App\Models\Proforma;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use App\library\numero_a_letras\src\NumeroALetras;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PDF;

class ProformaService
{
    private $modulo = "PROFORMAS";

    public function __construct(
        private HistorialAccionService $historialAccionService,
        private DetalleProformaService $detalleProformaService
    ) {}

    /**
     * Lista de todos los proformas
     *
     * @return Collection
     */
    public function listado(): Collection
    {
        $proformas = Proforma::select("proformas.*");

        if (Auth::user()->sucursals_todo == 0) {
            $proformas->where("sucursal_id", Auth::user()->sucursal_id);
        }

        $proformas->where("status", 1);
        $proformas = $proformas->get();
        return $proformas;
    }

    /**
     * Lista de proformas paginado con filtros
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
        array $orderBy = []
    ): LengthAwarePaginator {
        $proformas = Proforma::with(["cliente", "sucursal", "user", "detalle_proformas.producto"])
            ->select(
                "proformas.id",
                "proformas.nro",
                "proformas.user_id",
                "proformas.sucursal_id",
                "proformas.cliente_id",
                "proformas.nit_ci",
                "proformas.factura",
                "proformas.fecha_validez",
                "proformas.fecha_registro",
                "proformas.status",
                \DB::raw("SUM(detalle_proformas.cantidad) AS total_vendido")
            )
            ->leftJoin("detalle_proformas", "proformas.id", "=", "detalle_proformas.proforma_id")
            ->where("proformas.status", 1)
            ->groupBy(
                "proformas.id",
                "proformas.nro",
                "proformas.user_id",
                "proformas.sucursal_id",
                "proformas.cliente_id",
                "proformas.nit_ci",
                "proformas.factura",
                "proformas.fecha_validez",
                "proformas.fecha_registro",
                "proformas.status"
            );

        if (Auth::user()->sucursals_todo == 0) {
            $proformas->where("sucursal_id", Auth::user()->sucursal_id);
        }

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $proformas->where("proformas.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $proformas->whereBetween("proformas.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $proformas->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("proformas.$col", "LIKE", "%$search%");
                }
            });
        }

        // Proformaamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $proformas->orderBy($value[0], $value[1]);
            }
        }

        return $proformas->paginate($length, ['*'], 'page', $page);
    }

    /**
     * Crear proforma
     *
     * @param array $datos
     * @return Proforma
     */
    public function crear(array $datos): Proforma
    {
        $numero = $this->generarNumeroProforma();
        $cliente = Cliente::findOrFail($datos["cliente_id"]);
        $proforma = Proforma::create([
            "nro" => $numero,
            "user_id" => Auth::user()->id,
            "sucursal_id" => $datos["sucursal_id"],
            "cliente_id" => $cliente->id,
            "nit_ci" => $datos["nit_ci"],
            "factura" => $datos["factura"],
            "fecha_validez" => $datos["fecha_validez"],
            "fecha_registro" => date("Y-m-d"),
        ]);

        // registrar Detalle(detalle_proformas)
        $this->detalleProformaService->registrarDetalle($proforma, $datos["detalle_proformas"], $proforma->sucursal_id);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UNA PROFORMA", $proforma, null, ["detalle_proformas"]);

        return $proforma;
    }

    /**
     * Actualizar proforma
     *
     * @param array $datos
     * @param Proforma $proforma
     * @return Proforma
     */
    public function actualizar(array $datos, Proforma $proforma): Proforma
    {
        $old_proforma = clone $proforma;
        $old_proforma->loadMissing('detalle_proformas');
        $cliente = Cliente::findOrFail($datos["cliente_id"]);
        $proforma->update([
            // "sucursal_id" => $datos["sucursal_id"],
            "cliente_id" => $cliente->id,
            "nit_ci" => $datos["nit_ci"],
            "factura" => $datos["factura"],
            "fecha_validez" => $datos["fecha_validez"],
        ]);

        // registrar Detalle(detalle_proformas)
        $this->detalleProformaService->registrarDetalle($proforma, $datos["detalle_proformas"], $proforma->sucursal_id);

        if (isset($datos["eliminados"])) {
            // eliminados
            $this->detalleProformaService->eliminarDetalleProformas($datos["eliminados"], $proforma->sucursal_id, $old_proforma->sucursal_id);
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UNA PROFORMA", $old_proforma, $proforma, ["detalle_proformas"]);

        return $proforma;
    }

    /**
     * Eliminar proforma
     *
     * @param Proforma $proforma
     * @return boolean
     */
    public function eliminar(Proforma $proforma): bool
    {
        $old_proforma = clone $proforma;
        $old_proforma->loadMissing('detalle_proformas');
        $proforma->status = 0;
        $proforma->save();

        // eliminar detalles
        $id_eliminados = $proforma->detalle_proformas->pluck("id")->toArray();
        $this->detalleProformaService->eliminarDetalleProformas($id_eliminados, $proforma->sucursal_id);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UNA PROFORMA", $old_proforma);

        return true;
    }

    /**
     * Generar nuevo nro. de proforma
     *
     * @return int
     */
    private function generarNumeroProforma(): int
    {
        $nro = 1;
        $ultimaProforma = Proforma::orderBy("nro", "desc")->get()->first();
        if ($ultimaProforma) {
            $nro = (int)$ultimaProforma->nro + 1;
        }

        return $nro;
    }

    public function generarPdf(Proforma $proforma)
    {
        $convertir = new NumeroALetras();
        $array_monto = explode('.', number_format($proforma->total, 2, ".", ""));
        $literal = $convertir->convertir($array_monto[0]);
        $literal .= " " . $array_monto[1] . "/100." . " BOLIVIANOS";

        $nro_factura = (int)$proforma->nro;
        if ($nro_factura < 10) {
            $nro_factura = '000' . $nro_factura;
        } else if ($nro_factura < 100) {
            $nro_factura = '00' . $nro_factura;
        } else if ($nro_factura < 1000) {
            $nro_factura = '0' . $nro_factura;
        }

        $customPaper = [0, 0, 270.0, 700.0]; // [top, left, bottom, right]

        $pdf = PDF::loadView('reportes.proforma', compact('proforma', 'literal', 'nro_factura'))->setPaper($customPaper, 'portrait');
        return $pdf->stream('Proforma.pdf');
    }
}
