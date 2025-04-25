<?php

namespace App\Rules;

use App\Models\Producto;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProformaDetalleRule implements ValidationRule
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
            // $arraProd = json_decode($item, true);
            $arraProd = $item;
            $producto = Producto::select("nombre")->where("id", $arraProd["producto_id"])->get()->first();
            if (!$producto) {
                $fail("El producto " . ($key + 1) . " no se encuentra en nuestro registros");
            }
            if (trim('' . $arraProd["cantidad"]) == '' || (float)$arraProd["cantidad"] <= 0) {
                $fail("No se ingreso ninguna cantidad del producto $producto->nombre");
            }
            if (trim('' . $arraProd["precio"]) == '' || (float)$arraProd["precio"] <= 0) {
                $fail("No se ingreso ningún precio en el producto $producto->nombre");
            }
            if (trim('' . $arraProd["subtotal"]) == '' || (float)$arraProd["subtotal"] <= 0) {
                $fail("El subtotal del producto $producto->nombre no puede ser menor o igual a 0");
            }

            if ($arraProd["precio"]) {
                if ((float)$arraProd["precio"] < (float)$producto->precio_min) {
                    $fail("El precio del producto $producto->nombre no puede ser menor a $producto->precio_min");
                }
            }
        }
    }
}
