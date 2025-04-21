<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    use HasFactory;

    protected $fillable = [
        "sucursal_id",
        "orden_venta_id",
        "producto_id",
        "detalle_orden_id",
        "razon",
        "descripcion",
        "fecha_registro",
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function orden_venta()
    {
        return $this->belongsTo(OrdenVenta::class, 'orden_venta_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function detalle_orden()
    {
        return $this->belongsTo(DetalleOrden::class, 'detalle_orden_id');
    }
}
