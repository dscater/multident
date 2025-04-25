<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngresoDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        "ingreso_producto_id",
        "producto_id",
        "cantidad",
        "disponible",
        "ubicacion_producto_id",
        "fecha_vencimiento",
        "descripcion",
        "fecha_registro",
        "status"
    ];

    protected $appends = ["fecha_registro_t", "fecha_vencimiento_t"];

    public function getFechaVencimientoTAttribute()
    {
        if (!$this->fecha_vencimiento) {
            return "";
        }
        return date("d/m/Y", strtotime($this->fecha_vencimiento));
    }

    public function getFechaRegistroTAttribute()
    {
        if (!$this->fecha_registro) {
            return "";
        }
        return date("d/m/Y", strtotime($this->fecha_registro));
    }

    // RELACIONES

    public function ingreso_producto()
    {
        return $this->belongsTo(IngresoProducto::class, 'ingreso_producto_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function ubicacion_producto()
    {
        return $this->belongsTo(UbicacionProducto::class, 'ubicacion_producto_id');
    }
}
