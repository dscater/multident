<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleProforma extends Model
{
    use HasFactory;

    protected $fillable = [
        "proforma_id",
        "producto_id",
        "promocion_id",
        "promocion_descuento",
        "cantidad",
        "precio_reg",
        "precio",
        "subtotal",
        "status",
    ];

    public function proforma()
    {
        return $this->belongsTo(Proforma::class, 'proforma_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function promocion()
    {
        return $this->belongsTo(Promocion::class, 'promocion_id');
    }
}
