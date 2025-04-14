<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $fillable = [
        "descripcion",
        "fecha",
        "hora",
        "modulo",
        "registro_id",
    ];

    protected $appends = ["fecha_t", "fecha_hora_t", "hace", "url_modulo"];

    public function getUrlModuloAttribute()
    {
        $url = "";
        if ($this->modulo == 'OrdenVenta') {
            $ordenVenta = OrdenVenta::find($this->registro_id);
            $url = route("orden_ventas.index") . "?cod=" . $ordenVenta->codigo;
        }
        if ($this->modulo == 'SolicitudProducto') {
            $solicitudProducto = SolicitudProducto::find($this->registro_id);
            $url = route("solicitud_productos.index") . "?cod=" . $solicitudProducto->codigo_solicitud;
        }

        return $url;
    }

    public function getFechaTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha));
    }

    public function getFechaHoraTAttribute()
    {
        return date("d/m/Y H:i", strtotime($this->fecha . ' ' . $this->hora));
    }

    public function getHaceAttribute()
    {
        $tiempo = $this->created_at->diffForHumans();
        return $tiempo;
    }
}
