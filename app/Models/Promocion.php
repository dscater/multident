<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    use HasFactory;

    protected $fillable = [
        "producto_id",
        "porcentaje",
        "fecha_ini",
        "fecha_fin",
        "descripcion",
        "fecha_registro",
    ];
    protected $appends = ["fecha_registro_t", "fecha_ini_t", "fecha_fin_t"];

    public function getFechaIniTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_ini));
    }

    public function getFechaFinTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_fin));
    }

    public function getFechaRegistroTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_registro));
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
