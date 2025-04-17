<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UbicacionProductoUpdateRequest extends FormRequest
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
            "lugar" => "required|min:2",
            "numero_filas" => "required|int|min:1",
        ];
    }

    public function messages(): array
    {
        return [
            "lugar.required" => "Debes completar este campo",
            "lugar.min" => "Debes ingresar al menos :min caracteres",
            "numero_filas.required" => "Debes completar este campo",
            "numero_filas.int" => "Debes ingresar un valor entero",
            "numero_filas.min" => "Debes ingresar al menos :min",
        ];
    }
}
