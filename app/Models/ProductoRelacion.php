<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoRelacion extends Model
{
    use HasFactory;

    protected $fillable = [
        "producto_id",
        "producto_relacion",
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function o_producto_relacion()
    {
        return $this->belongsTo(Producto::class, 'producto_relacion');
    }
}
