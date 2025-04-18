<?php

namespace App\Http\Requests;

use App\Rules\IngresoDetalleRule;
use Illuminate\Foundation\Http\FormRequest;

class IngresoProductoUpdateRequest extends FormRequest
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
            "ingreso_detalles" => ["required", "array", "min:1", new IngresoDetalleRule],
            "eliminados" => ["nullable", "array"]
        ];
    }

    public function messages(): array
    {
        return [
            "sucursal_id.required" => "Este campo es obligatorio",
            "ingreso_detalles.required" => "Este campo es obligatorio",
            "ingreso_detalles.min" => "Debes agregar al menos :min producto",
        ];
    }
}
