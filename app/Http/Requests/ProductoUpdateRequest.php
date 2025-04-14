<?php

namespace App\Http\Requests;

use App\Rules\ProductoImagensRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductoUpdateRequest extends FormRequest
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
            "nombre" => "required|min:2",
            "descripcion" => "required|min:2",
            "precio_pred" => "required|numeric|min:1",
            "precio_min" => "required|numeric|min:1",
            "precio_fac" => "required|numeric|min:0",
            "precio_sf" => "required|numeric|min:0",
            "stock_maximo" => "required|int|min:1",
            "foto" => "nullable|image|mimes:png,jpg,jpeg,webp|max:4096",
        ];
    }

    public function messages(): array
    {
        return [
            "nombre.required" => "Debes completar este campo",
            "nombre.min" => "Debes ingresar al menos :min caracteres",
            "descripcion.required" => "Debes completar este campo",
            "descripcion.min" => "Debes ingresar al menos :min caracteres",
            "precio_pred.required" => "Debes completar este campo",
            "precio_min.required" => "Debes completar este campo",
            "precio_fac.required" => "Debes completar este campo",
            "precio_sf.required" => "Debes completar este campo",

            "precio_pred.numeric" => "Debes ingresar un valor númerico",
            "precio_min.numeric" => "Debes ingresar un valor númerico",
            "precio_fac.numeric" => "Debes ingresar un valor númerico",
            "precio_sf.numeric" => "Debes ingresar un valor númerico",

            "precio_pred.min" => "Debes ingresar al menos :min",
            "precio_min.min" => "Debes ingresar al menos :min",
            "precio_fac.min" => "Debes ingresar al menos :min",
            "precio_sf.min" => "Debes ingresar al menos :min",

            "stock_maximo.required" => "Debes completar este campo",
            "stock_maximo.min" => "Debes ingresar al menos :min",
            "stock_maximo.int" => "Debes ingresar un valor entero",

            "foto.image" => "Debes cargar una imagen",
            "foto.mimes" => "Solo puedes enviar formatos png,jpg,jpeg,webp",
            "foto.max" => "No puedes cargar una imagen con mas de 4096KB",
        ];
    }
}
