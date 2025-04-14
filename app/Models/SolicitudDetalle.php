<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        "solicitud_producto_id",
        "nombre_producto",
        "detalle_producto",
        "links_referencia",
    ];
}
