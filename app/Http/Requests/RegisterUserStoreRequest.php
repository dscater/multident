<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserStoreRequest extends FormRequest
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
            "nombres" => "required|regex:/^[\pL\s\.\,áéíóúÁÉÍÓÚñÑ]+$/u",
            "apellidos" => "required|regex:/^[\pL\s\.\,áéíóúÁÉÍÓÚñÑ]+$/u",
            "cel" => "required|numeric|digits_between:7,10",
            "correo" => "required|email|unique:clientes,correo|unique:users,correo",
            "password" => "required|confirmed|min:8|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,}$/"
        ];
    }

    /**
     * Mensajes validacion
     *
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            "nombres.required" => "Este campo es obligatorio",
            "nombres.regex" => "Debes ingresar solo texto",
            "apellidos.required" => "Este campo es obligatorio",
            "apellidos.regex" => "Debes ingresar solo texto",
            "materno.regex" => "Debes ingresar solo texto",
            "ci.required" => "Este campo es obligatorio",
            "ci.numeric" => "Debes ingresar solo números",
            "ci.digits_between" => "Debes ingresar un valor entre 7-10 digitos",
            "ci.unique" => "Este documento de identidad ya fue registrado",
            "ci_exp.required" => "Este campo es obligatorio",
            "cel.required" => "Este campo es obligatorio",
            "cel.numeric" => "Debes ingresar solo números",
            "cel.digits_between" => "Debes ingresar un valor entre 7-10 digitos",
            "dpto_residencia.required" => "Este campo es obligatorio",
            "correo.required" => "Este campo es obligatorio",
            "correo.email" => "Debes ingresar un correo valido",
            "correo.unique" => "Este correo ya fue registrado",
            "foto_ci_anverso.required" => "Este campo es obligatorio",
            "foto_ci_reverso.required" => "Este campo es obligatorio",
            "banco.required" => "Este campo es obligatorio",
            "nro_cuenta.required" => "Este campo es obligatorio",
            "moneda.required" => "Este campo es obligatorio",
            "password.required" => "Debes ingresar tu contraseña",
            "password.confirmed" => "La contraseña no coincide",
            "password.min" => "Debes ingresar una contraseña de al menos :min caracteres",
            "password.regex" => "La contraseña debe tener al menos 8 caracteres, incluyendo una letra mayúscula, un número y un símbolo (@$!%*?&).",
        ];
    }
}
