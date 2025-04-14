<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SucursalStoreRequest extends FormRequest
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
            "codigo" => "required|min:1|unique:sucursals,codigo",
            "nombre" => "required|min:1",
            "direccion" => "nullable|min:1",
            "fonos" => "required|min:1",
            "user_id" => "required",
        ];
    }

    public function messages(): array
    {
        return [
            "nombre.required" => "Este campo es obligatorio",
            "nombre.min" => "Debes ingresar al menos :min caracteres",
            "direccion.min" => "Debes ingresar al menos :min caracteres",
            "fonos.required" => "Este campo es obligatorio",
            "fonos.min" => "Debes ingresar al menos :min caracteres",
            "user_id.required" => "Este campo es obligatorio",
        ];
    }
}
