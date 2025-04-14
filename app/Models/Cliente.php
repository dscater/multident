<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "nombres",
        "apellidos",
        "cel",
        "correo",
        "fecha_registro",
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
