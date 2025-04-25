<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacionUser extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "notificacion_id",
        "visto",
    ];

    protected $appends = ["url_modulo"];

    public function getUrlModuloAttribute()
    {
        return route("notificacions.show", $this->id);
    }

    public function notificacion()
    {
        return $this->belongsTo(Notificacion::class, 'notificacion_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
