<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionPago extends Model
{
    use HasFactory;

    protected $fillable = [
        "nombre_banco",
        "titular_cuenta",
        "nro_cuenta",
        "imagen_qr",
        "fecha_registro",
    ];

    protected $appends = ["fecha_registro_t", "url_imagen_qr"];

    public function getUrlImagenQrAttribute()
    {
        return asset("imgs/configuracion_pagos/" . $this->imagen_qr);
    }

    public function getFechaRegistroTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_registro));
    }
}
