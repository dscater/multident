<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenVenta extends Model
{
    use HasFactory;

    protected $fillable = [
        "codigo",
        "nro",
        "cliente_id",
        "nombre_cliente",
        "apellidos_cliente",
        "cel",
        "estado_orden",
        "estado_pago",
        "configuracion_pago_id",
        "comprobante",
        "observacion",
        "status",
        "fecha_orden",
        "fecha_confirmacion",
    ];

    protected $appends = ["fecha_orden_t", "fecha_confirmacion_t", "total", "url_comprobante"];

    public function getUrlComprobanteAttribute()
    {
        return asset("imgs/ordenVentas/" . $this->comprobante);
    }

    public function getTotalAttribute()
    {
        return $this->detalleVenta->sum("subtotal");
    }

    public function getFechaOrdenTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_orden));
    }

    public function getFechaConfirmacionTAttribute()
    {
        return $this->fecha_confirmacion ? date("d/m/Y", strtotime($this->fecha_confirmacion)) : '';
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function detalleVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'orden_venta_id');
    }
}
