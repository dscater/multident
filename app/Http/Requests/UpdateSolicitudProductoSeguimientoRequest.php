<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSolicitudProductoSeguimientoRequest extends FormRequest
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
            "estado_seguimiento" => "required|string|in:PENDIENTE,EN PROCESO,EN ALMACÉN,ENTREGADO",
            "observacion" => "nullable|min:2|string",
        ];
    }


    /**
     * Mensajes validacion
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            "estado_seguimiento.required" => "Debes seleccionar un estado de seguimiento",
            "estado_seguimiento.string" => "Debes enviar un texto",
            "estado_seguimiento.in" => "El estado solo puede ser: PENDIENTE, EN PROCESO, EN ALMACÉN, ENTREGADO",
            "observacion.min" => "Debes ingresar al menos :min caracteres",
            "observacion.string" => "Debes ingresar solo texto",
        ];
    }
}
