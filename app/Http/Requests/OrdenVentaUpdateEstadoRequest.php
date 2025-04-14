<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrdenVentaUpdateEstadoRequest extends FormRequest
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
            "estado_orden" => "required",
            "observacion" => "nullable|min:2|string"
        ];
    }

    public function messages(): array
    {
        return [
            "estado_orden.required" => "Debes enviar un estado",
            "observacion.min" => "La observación debe tener al menos :min caracteres",
            "observacion.string" => "La observación debe contener solo texto",
        ];
    }
}
