<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;

    protected $fillable = [
        "codigo",
        "nombre",
        "direccion",
        "fonos",
        "user_id",
        "fecha_registro",
        "status",
    ];

    protected $appends = ["fecha_registro_t", "status_t"];

    public function getFechaRegistroTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_registro));
    }

    public function getStatusTAttribute()
    {
        return $this->status == 1 ? 'HABILITADO' : 'DESHABILITADO';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
