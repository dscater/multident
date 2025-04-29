<?php

namespace App\Http\Requests;

use App\Rules\ProformaDetalleRule;
use Illuminate\Foundation\Http\FormRequest;

class ProformaUpdateRequest extends FormRequest
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
            "sucursal_id" => "required",
            "cliente_id" => "required",
            "nit_ci" => "required|string|min:1",
            "factura" => "required",
            "fecha_validez" => "required|date",
            'detalle_proformas' => ["required", "array", "min:1", new ProformaDetalleRule],
            "eliminados" => "nullable|array",
            'list_promocions' => ["nullable", "array"],
        ];
    }

    public function messages(): array
    {
        return [
            "sucursal_id.required" => "No se identifico la Sucursal",
            "cliente_id.required" => "Debes seleccionar un cliente",
            "nit_ci.required" => "Debes ingresar el NIT/C.I.",
            "nit_ci.min" => "Debes ingresar un valor NIT/C.I. o poner 0",
            "factura.required" => "Debes indicar si es con factura o no",
            "fecha_validez.required" => "Debes ingresar la fecha de validez",
            "fecha_validez.date" => "Debes ingresar una fecha valida",
            "detalle_proformas.required" => "Debes agregar al menos 1 producto",
            "detalle_proformas.array" => "Formato incorrecto del detalle_proformas este debe ser un array de datos",
            "detalle_proformas.min" => "Debes ingresar al menos :min productos",
        ];
    }
}
