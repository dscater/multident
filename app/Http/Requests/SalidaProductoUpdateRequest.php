<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalidaProductoUpdateRequest extends FormRequest
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
            'sucursal_id' => 'required',
            "producto_id" => "required",
            "cantidad" => "required|numeric|min:1",
            "descripcion" => "nullable|string|min:2"
        ];
    }

    public function messages(): array
    {
        return [
            "sucursal_id.required" => "Este campo es obligatorio",
            "producto_id.required" => "Este campo es obligatorio",
            "cantidad.required" => "Este campo es obligatorio",
            "cantidad.numeric" => "Debes ingresar un valor númerico",
            "cantidad.min" => "Debes ingresar al menos :min",
            "descripcion.string" => "Debes ingresar un texto",
            "descripcion.min" => "Debes ingresar al menos :min carácteres",
        ];
    }
}
