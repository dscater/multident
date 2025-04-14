<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SolicitudesProductoRule implements ValidationRule
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
            if (!$item || trim('' . $item["nombre_producto"]) == '') {
                $fail("El campo Nombre producto* es obligatorio");
            }
            if (!$item || trim('' . $item["detalle_producto"]) == '') {
                $fail("El campo Detalle del producto* es obligatorio");
            }
            if (!$item || trim('' . $item["links_referencia"]) == '') {
                $fail("El campo Links de referencia del producto* es obligatorio");
            }
        }
    }
}
