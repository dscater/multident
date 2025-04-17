<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        "nombre",
        "descripcion",
        "precio_pred",
        "precio_min",
        "precio_fac",
        "precio_sf",
        "stock_maximo",
        "foto",
        "fecha_registro",
        "status",
    ];

    protected $appends = ["url_foto", "foto_b64", "fecha_registro_t", "monto_cf", "monto_sf"];

    public function getMontoCFAttribute()
    {
        $incremento = 1 + ($this->precio_fac / 100);
        $monto = round((float)$this->precio_pred * $incremento, 2);
        return $monto;
    }
    public function getMontoSFAttribute()
    {
        $decremento = 1 - ($this->precio_sf / 100);
        $monto = round((float)$this->precio_pred * $decremento, 2);
        return $monto;
    }

    public function getUrlFotoAttribute()
    {
        if ($this->foto) {
            return asset("imgs/productos/" . $this->foto);
        }
        return asset("imgs/productos/default.png");
    }

    public function getFotoB64Attribute()
    {
        $path = public_path("imgs/productos/" . $this->foto);
        if (!$this->foto || !file_exists($path)) {
            $path = public_path("imgs/productos/default.png");
        }
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public function getFechaRegistroTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_registro));
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function producto_relacions()
    {
        return $this->hasMany(ProductoRelacion::class, 'producto_id');
    }
}
