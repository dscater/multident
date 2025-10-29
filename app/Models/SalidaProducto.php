<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalidaProducto extends Model
{
    use HasFactory;

    protected $fillable = [
        "sucursal_id",
        "producto_id",
        "cantidad",
        "descripcion",
        "fecha_registro",
        "status",
        "user_id"
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
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
