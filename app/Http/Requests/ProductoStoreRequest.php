<?php

namespace App\Http\Requests;

use App\Rules\ProductoImagensRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductoStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "categoria_id" => "required",
            "nombre" => "required|min:2",
            "descripcion" => "required|min:10",
            "stock_actual" => "required|int|min:0",
            "precio_compra" => "required|numeric|min:0",
            "precio_venta" => "required|numeric|min:0",
            "observaciones" => "nullable|min:4",
            "publico" => "required",
            "imagens" => ["required", "array", "min:1", "max:5", new ProductoImagensRule],
        ];
    }

    public function messages(): array
    {
        return [
            "categoria_id.required" => "Debes  seleccionar una categoría",
            "nombre.required" => "Debes completar este campo",
            "nombre.min" => "Debes ingresar al menos :min caracteres",
            "descripcion.required" => "Debes completar este campo",
            "descripcion.min" => "Debes ingresar al menos :min caracteres",
            "stock_actual.required" => "Debes completar este campo",
            "stock_actual.int" => "Debes ingresar un valor entero",
            "stock_actual.min" => "Debes ingresar al menos :min",
            "precio_compra.required" => "Debes completar este campo",
            "precio_compra.numeric" => "Debes ingresar un valor númerico",
            "precio_compra.min" => "Debes ingresar al menos :min",
            "precio_venta.required" => "Debes completar este campo",
            "precio_venta.numeric" => "Debes ingresar un valor númerico",
            "precio_venta.min" => "Debes ingresar al menos :min",
            "observaciones.required" => "Debes completar este campo",
            "observaciones.min" => "Debes ingresar al menos :min caracteres",
            "publico.required" => "Debes seleccionar un valor",
            "imagens.required" => "Debes cargar al menos 1 imagen",
            "imagens.min" => "Debes cargar al menos :min imagen",
            "imagens.max" => "Solo puedes cargar :max imagenes",
        ];
    }
}
