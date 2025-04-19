<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromocionStoreRequest extends FormRequest
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
            "producto_id" => "required",
            "porcentaje" => "required|numeric|min:0",
            "fecha_ini" => "required|date",
            "fecha_fin" => "required|date",
            "descripcion" => "nullable|string|min:2",
        ];
    }

    public function messages(): array
    {
        return [
            "producto_id.required" => "Debes seleccionar un producto",
            "porcentaje.required" => "Debes completar este campo",
            "porcentaje.numeric" => "Debes ingresar un valor númerico",
            "porcentaje.min" => "Debes ingresar al menos :min",
            "fecha_ini.required" => "Debes completar este campo",
            "fecha_ini.date" => "Debes ingresar una fecha valida",
            "fecha_fin.required" => "Debes completar este campo",
            "fecha_fin.date" => "Debes ingresar una fecha valida",
            "descripcion.string" => "Debes ingresar solo texto",
            "descripcion.min" => "Debes ingresar al menos :min caracteres",
        ];
    }
}
