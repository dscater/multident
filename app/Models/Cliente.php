<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        "nombres",
        "apellidos",
        "ci",
        "cel",
        "descripcion",
        "fecha_registro",
        "status",
    ];

    protected $appends = ["fecha_registro_t", "full_name"];

    public function getFechaRegistroTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_registro));
    }
    public function getFullNameAttribute()
    {
        return $this->nombres . ' ' . $this->apellidos;
    }
}
