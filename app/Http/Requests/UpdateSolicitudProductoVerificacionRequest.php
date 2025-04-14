<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSolicitudProductoVerificacionRequest extends FormRequest
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
            "estado_solicitud" => "required|string|in:APROBADO,RECHAZADO,PENDIENTE",
            "observacion" => "nullable|min:2|string",
            "precio_compra" => [
                "required_if:estado_solicitud,APROBADO",
                "nullable",
                "numeric",
                "min:0"
            ],
            "margen_ganancia" => [
                "required_if:estado_solicitud,APROBADO",
                "nullable",
                "numeric",
                "min:0"
            ],
        ];
    }

    public function messages(): array
    {
        return [
            "estado_solicitud.required" => "Debes seleccionar un estado de solicitud",
            "estado_solicitud.string" => "Debes enviar un texto",
            "estado_solicitud.in" => "El estado solo puede ser: APROBADO, RECHAZADO O PENDIENTE",
            "observacion.min" => "Debes ingresar al menos :min caracteres",
            "observacion.string" => "Debes ingresar solo texto",
            "precio_compra.required_if" => "Debes ingresar un precio de compra",
            "precio_compra.numeric" => "Debes ingresar un valor númerico",
            "precio_compra.min" => "Debes ingresar al menos :min",
            "margen_ganancia.required_if" => "Debes ingresar un margen de ganancia",
            "margen_ganancia.numeric" => "Debes ingresar un valor númerico",
            "margen_ganancia.min" => "Debes ingresar al menos :min",
        ];
    }
}
