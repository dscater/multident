<?php

namespace App\Rules;

use App\Models\Producto;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OrdenVentaCarritoRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail("No se encontró ningun producto agregado");
        }
        foreach ($value as $key => $item) {
            $arraProd = json_decode($item, true);
            $producto = Producto::select("nombre")->where("id", $arraProd["producto_id"])->get()->first();
            if (!$producto) {
                $fail("El producto " . ($key + 1) . " no se encuentra en nuestro registros");
            }
            if (trim('' . $arraProd["cantidad"]) == '') {
                $fail("No se ingreso ninguna cantidad del producto $producto->nombre");
            }
        }
    }
}
