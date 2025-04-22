<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOrden extends Model
{
    use HasFactory;

    protected $fillable = [
        "orden_venta_id",
        "producto_id",
        "promocion_id",
        "promocion_descuento",
        "cantidad",
        "precio_reg",
        "precio",
        "subtotal",
        "status",
    ];

    public function orden_venta()
    {
        return $this->belongsTo(OrdenVenta::class, 'orden_venta_id');
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
