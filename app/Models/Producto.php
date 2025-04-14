<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        "categoria_id",
        "nombre",
        "descripcion",
        "stock_actual",
        "precio_compra",
        "precio_venta",
        "observaciones",
        "publico",
        "fecha_registro",
        "status"
    ];

    protected $appends = ["fecha_registro_t"];

    public function getFechaRegistroTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_registro));
    }

    public function imagens()
    {
        return $this->hasMany(ProductoImagen::class, 'producto_id');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}
