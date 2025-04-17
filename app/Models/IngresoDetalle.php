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
        "ubicacion_producto_id",
        "fecha_vencimiento",
        "descripcion",
        "fecha_registro",
    ];
}
