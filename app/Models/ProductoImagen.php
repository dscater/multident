<?php

namespace App\Models;

use App\Services\ProductoImagenService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoImagen extends Model
{
    use HasFactory;

    protected $fillable = [
        "producto_id",
        "imagen",
    ];

    protected $appends = ["url_imagen", "imagen_b64", "url_archivo", "url_file", "name", "ext"];

    public function getExtAttribute()
    {
        $array = explode(".", $this->imagen);
        return $array[1];
    }

    public function getNameAttribute()
    {
        return $this->imagen;
    }

    public function getUrlFileAttribute()
    {
        $array_imgs = ["jpg", "jpeg", "png", "webp", "gif"];
        $ext = ProductoImagenService::getExtNomImagen($this->imagen);
        if (in_array($ext, $array_imgs)) {
            return asset("/imgs/productos/" . $this->imagen);
        }
        return asset("/imgs/attach.png");
    }

    public function getUrlArchivoAttribute()
    {
        return asset("imgs/productos/" . $this->imagen);
    }

    public function getUrlImagenAttribute()
    {
        if ($this->imagen) {
            return asset("imgs/productos/" . $this->imagen);
        }
        return asset("imgs/productos/default.png");
    }

    public function getImagenB64Attribute()
    {
        $path = public_path("imgs/productos/" . $this->imagen);
        if (!$this->imagen || !file_exists($path)) {
            $path = public_path("imgs/productos/default.png");
        }
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
