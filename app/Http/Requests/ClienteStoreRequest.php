<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteStoreRequest extends FormRequest
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
            "nombres" => "required|string|min:2",
            "apellidos" => "nullable|string|min:2",
            "ci" => "nullable|string|min:1",
            "cel" => "nullable|string|min:2",
            "descripcion" => "nullable|string|min:2",
        ];
    }

    public function messages(): array
    {
        return [
            "nombres.required" => "Debes completar este campo",
            "nombres.string" => "Debes ingresar solo texto",
            "nombres.min" => "Debes ingresar al menos :min caracteres",
            "apellidos.required" => "Debes completar este campo",
            "apellidos.string" => "Debes ingresar solo texto",
            "apellidos.min" => "Debes ingresar al menos :min caracteres",
            "ci.string" => "Debes ingresar solo texto",
            "ci.min" => "Debes ingresar al menos :min caracteres",
            "cel.string" => "Debes ingresar solo texto",
            "cel.min" => "Debes ingresar al menos :min caracteres",
            "descripcion.string" => "Debes ingresar solo texto",
            "descripcion.min" => "Debes ingresar al menos :min caracteres",
        ];
    }
}
