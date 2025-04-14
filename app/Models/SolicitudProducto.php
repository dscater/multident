<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudProducto extends Model
{
    use HasFactory;

    protected $fillable = [
        "codigo_solicitud",
        "nro",
        "sede_id",
        "cliente_id",
        "nombre_cliente",
        "apellidos_cliente",
        "cel",
        "estado_solicitud",
        "precio_compra",
        "margen_ganancia",
        "estado_seguimiento",
        "observacion",
        "status",
        "fecha_solicitud",
        "fecha_verificacion",
    ];

    protected $appends = ["fecha_solicitud_t", "fecha_verificacion_t", "total_precio"];

    // appends
    public function getTotalPrecioAttribute()
    {
        $precio_compra = $this->precio_compra ? $this->precio_compra : 0;
        $margen_ganancia = $this->margen_ganancia ? $this->margen_ganancia : 0;
        $total = (float)$precio_compra + (float)$margen_ganancia;
        return $total;
    }

    public function getFechaSolicitudTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_solicitud));
    }

    public function getFechaVerificacionTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_verificacion));
    }

    // RELACIONES
    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function solicitudDetalles()
    {
        return $this->hasMany(SolicitudDetalle::class, 'solicitud_producto_id');
    }
}
