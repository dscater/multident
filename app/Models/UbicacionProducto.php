<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbicacionProducto extends Model
{
    use HasFactory;

    protected $fillable = [
        "lugar",
        "numero_filas",
        "fecha_registro",
        "status",
    ];

    protected $appends = ["fecha_registro_t"];
    
    public function getFechaRegistroTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_registro));
    }
}
