<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfiguracionRequest extends FormRequest
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
            "nombre_sistema" => "required",
            "alias" => "required",
            "logo" => "required",
            "fono" => "required",
            "dir" => "required",
            "conf_correos.host" => "required",
            "conf_correos.correo" => "required",
            "conf_correos.driver" => "required",
            "conf_correos.nombre" => "required",
            "conf_correos.puerto" => "required",
            "conf_correos.password" => "required",
            "conf_correos.encriptado" => "required",
            "conf_moneda.abrev" => "required",
            "conf_moneda.moneda" => "required",
            "conf_captcha.claveSitio" => "required",
            "conf_captcha.claveBackend" => "required",
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
            "nombre_sistema.required" => "Debes completar este campo",
            "alias.required" => "Debes completar este campo",
            "logo.required" => "Debes completar este campo",
            "fono.required" => "Debes completar este campo",
            "dir.required" => "Debes completar este campo",
            "conf_correos.host.required" => "Debes completar este campo",
            "conf_correos.correo.required" => "Debes completar este campo",
            "conf_correos.driver.required" => "Debes completar este campo",
            "conf_correos.nombre.required" => "Debes completar este campo",
            "conf_correos.puerto.required" => "Debes completar este campo",
            "conf_correos.password.required" => "Debes completar este campo",
            "conf_correos.encriptado.required" => "Debes completar este campo",
            "conf_moneda.abrev.required" => "Debes completar este campo",
            "conf_moneda.moneda.required" => "Debes completar este campo",
            "conf_captcha.claveSitio.required" => "Debes completar este campo",
            "conf_captcha.claveBackend.required" => "Debes completar este campo",
        ];
    }
}
