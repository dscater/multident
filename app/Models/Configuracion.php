<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $fillable = [
        "nombre_sistema",
        "alias",
        "logo",
        "fono",
        "dir",
        "conf_correos",
        "conf_moneda",
        "conf_captcha",
    ];

    protected $appends = ["url_logo", "logo_b64"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'conf_correos' => "array",
            'conf_moneda' => "array",
            'conf_captcha' => "array",
        ];
    }

    /**
     * Url del logo
     *
     * @return string
     */
    public function getUrlLogoAttribute(): string
    {
        return asset("imgs/" . $this->logo);
    }

    /**
     * Base64 del logo
     *
     * @return string
     */
    public function getLogoB64Attribute(): string
    {
        $path = public_path("imgs/" . $this->logo);
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
}
