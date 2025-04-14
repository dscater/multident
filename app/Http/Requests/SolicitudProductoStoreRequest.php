<?php

namespace App\Http\Requests;

use App\Rules\SolicitudesProductoRule;
use App\Rules\TokenCaptchaRule;
use Illuminate\Foundation\Http\FormRequest;

class SolicitudProductoStoreRequest extends FormRequest
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
            "cliente_id" => "required",
            "sede_id" => "required",
            "token_captcha" => ["required", new TokenCaptchaRule],
            'solicitudes' => ["required", "array", "min:1", new SolicitudesProductoRule],
        ];
    }

    public function messages(): array
    {
        return [
            "cliente_id.required" => "No es posible realizar el registro debido a que no es un usuario valido",
            "sede_id.required" => "Debes seleccionar el departamento",
            "token_captcha" => "Debes marcar la casilla No soy un robot",
            "solicitudes.required" => "Debes agregar al menos 1 producto al solicitudes",
            "solicitudes.array" => "Formato incorrecto del solicitudes este debe ser un array de datos",
            "solicitudes.min" => "Debes ingresar al menos :min productos",
        ];
    }
}
