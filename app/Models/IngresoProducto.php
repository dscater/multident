<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngresoProducto extends Model
{
    use HasFactory;

    protected $fillable = [
        "sucursal_id",
        "fecha_registro",
        "descripcion",
        "status",
    ];

    protected $appends = ["fecha_registro_t"];

    public function getFechaRegistroTAttribute()
    {
        if (!$this->fecha_registro) {
            return "";
        }
        return date("d/m/Y", strtotime($this->fecha_registro));
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function ingreso_detalles()
    {
        return $this->hasMany(IngresoDetalle::class, 'ingreso_producto_id')->where("status", 1);
    }
}
