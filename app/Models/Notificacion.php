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
        "tipo",
        "sucursal_id",
        "modulo",
        "registro_id",
    ];

    protected $appends = ["fecha_t", "fecha_hora_t", "hace"];

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

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
}
