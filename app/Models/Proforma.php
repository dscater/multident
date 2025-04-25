<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proforma extends Model
{
    use HasFactory;

    protected $fillable = [
        "nro",
        "user_id",
        "sucursal_id",
        "cliente_id",
        "nit_ci",
        "factura",
        "fecha_validez",
        "fecha_registro",
        "status",
    ];

    protected $appends = ["fecha_registro_t", "fecha_validez_t", "total", "fecha", "hora", "url_pdf"];

    public function getUrlPdfAttribute()
    {
        return route("proformas.generarPdf", $this->id);
    }

    public function getFechaAttribute()
    {
        return $this->created_at ? $this->created_at->format('d/m/Y') : null;
    }
    public function getHoraAttribute()
    {
        return $this->created_at ? $this->created_at->format('H:i:s') : null;
    }

    public function getTotalAttribute()
    {
        return $this->detalle_proformas->sum("subtotal");
    }

    public function getFechaValidezTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_validez));
    }

    public function getFechaRegistroTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_registro));
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function detalle_proformas()
    {
        return $this->hasMany(DetalleProforma::class, 'proforma_id')->where("status", 1);
    }
}
