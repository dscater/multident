<?php

namespace App\Rules;

use App\Models\Producto;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class IngresoDetalleRule implements ValidationRule
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
            $arraProd = $item;
            $producto = Producto::where("id", $arraProd["producto_id"])->get()->first();
            if ($producto) {
                if (trim('' . $arraProd["cantidad"]) == '') {
                    $fail("Debes ingresar la cantidad del producto $producto->nombre");
                }
                if (trim('' . $arraProd["ubicacion_producto_id"]) == '') {
                    $fail("Debes indicar la ubicación del producto $producto->nombre");
                }
            } else {
                $fail("El producto " . ($key + 1) . " no se encuentra en nuestro registros");
            }
        }
    }
}
