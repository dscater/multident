<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfiguracionPagoStoreRequest extends FormRequest
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
            "nombre_banco" => "required|min:2|string",
            "titular_cuenta" => "required|min:2|string",
            "nro_cuenta" => "required|string",
            "imagen_qr" => "required|image|mimes:jpg,jpeg,png,webp|max:4096",
        ];
    }

    /**
     * Mensajes validacion
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            "nombre_banco.required" => "Debes completar este campo",
            "nombre_banco.min" => "Debes ingresar al menos :min caracteres",
            "nombre_banco.string" => "Debes ingresar un texto valido",
            "titular_cuenta.required" => "Debes completar este campo",
            "titular_cuenta.min" => "Debes ingresar al menos :min caracteres",
            "titular_cuenta.string" => "Debes ingresar un texto valido",
            "nro_cuenta.required" => "Debes completar este campo",
            "nro_cuenta.string" => "Debes ingresar un texto valido",
            "imagen_qr.required" => "Debes seleccionar una imagen",
            "imagen_qr.image" => "Debes cargar una imagen",
            "imagen_qr.mimes" => "Solo puedes enviar formatos png,jpg,jpeg,webp",
            "imagen_qr.max" => "No puedes cargar una imagen con mas de 4096KB",
        ];
    }
}
