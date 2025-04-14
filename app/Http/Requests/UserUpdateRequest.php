<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            "nombres" => "required|min:2",
            "apellidos" => "required|min:1",
            "ci" => "required|numeric|digits_between:6,10|unique:users,ci," . $this->id,
            "ci_exp" => "required",
            "correo" => "required|email|unique:users,correo," . $this->id,
            "role_id" => "required",
            "array_sedes_id" => "required|array|min:1",
            "foto" => "nullable|image|mimes:png,jpg,jpeg,webp|max:4096",
            "acceso" => "required",
        ];
    }

    /**
     * Mensages validacion
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            "nombres.required" => "Este campo es obligatorio",
            "nombres.min" => "Debes ingresar al menos :min caracteres",
            "apellidos.required" => "Este campo es obligatorio",
            "apellidos.min" => "Debes ingresar al menos :min caracteres",
            "ci.required" => "Este campo es obligatorio",
            "ci.numeric" => "Debes ingresar un valor númerico",
            "ci.digits_between" => "Debes ingresar un valor entre 6 y 10 digitos",
            "ci.unique" => "Este número de C.I. ya fue registrado",
            "ci_exp.required" => "Este campo es obligatorio",
            "correo.required" => "Este campo es obligatorio",
            "correo.email" => "Debes ingresar un correo valido",
            "correo.unique" => "Este correo no esta disponible",
            "role_id.required" => "Este campo es obligatorio",
            "array_sedes_id.required" => "Este campo es obligatorio",
            "array_sedes_id.array" => "Debes enviar una lista de datos",
            "array_sedes_id.min" => "Debes enviar al menos :min elemento",
            "foto.image" => "Debes cargar una imagen",
            "foto.mimes" => "Solo puedes enviar formatos png,jpg,jpeg,webp",
            "foto.max" => "No puedes cargar una imagen con mas de 4096KB",
            "acceso.required" => "Este campo es obligatorio",
        ];
    }
}
