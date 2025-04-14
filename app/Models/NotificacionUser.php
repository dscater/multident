<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacionUser extends Model
{
    use HasFactory;

    protected $fillable = [
        "notificacion_id",
        "user_id",
        "visto"
    ];

    public function notificacion()
    {
        return $this->belongsTo(Notificacion::class, 'notificacion_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
