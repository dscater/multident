<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleUso extends Model
{
    use HasFactory;

    protected $fillable = [
        "orden_venta_id",
        "detalle_orden_id",
        "producto_id",
        "ingreso_detalle_id",
        "cantidad",
    ];

    public function orden_venta()
    {
        return $this->belongsTo(OrdenVenta::class, 'orden_venta_id');
    }

    public function detalle_orden()
    {
        return $this->belongsTo(DetalleOrden::class, 'detalle_orden_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function ingreso_detalle()
    {
        return $this->belongsTo(IngresoDetalle::class, 'ingreso_detalle_id');
    }
}
