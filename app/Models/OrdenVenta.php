<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenVenta extends Model
{
    use HasFactory;

    protected $fillable = [
        "nro",
        "sucursal_id",
        "cliente_id",
        "nit_ci",
        "factura",
        "tipo_pago",
        "fecha_registro",
        "status",
    ];

    protected $appends = ["fecha_registro_t", "total"];

    public function getTotalAttribute()
    {
        return $this->detalle_ordens->sum("subtotal");
    }

    public function getFechaRegistroTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_registro));
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function detalle_ordens()
    {
        return $this->hasMany(DetalleOrden::class, 'orden_venta_id')->where("status", 1);
    }
}
