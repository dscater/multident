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
        "list_promocions",
        "promocion_descuento",
        "cantidad",
        "precio_reg",
        "precio",
        "subtotal",
        "status",
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'list_promocions' => "array",
        ];
    }


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
