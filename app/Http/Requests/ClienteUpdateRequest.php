<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteUpdateRequest extends FormRequest
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
            "cliente.nombres" => "required|string|min:2",
            "cliente.apellidos" => "required|string|min:2",
            "cliente.correo" => [
                "required",
                "email",
                Rule::unique("clientes", "correo")->ignore($this->cliente["id"]),
                Rule::unique("users", "correo")->ignore($this->id),
            ],
            "cliente.cel" => "required|numeric|digits_between:6,10",
            "origen" => "required|string|in:admin,user",
            "acceso" => "required_if:origen,admin"
        ];
    }

    public function messages(): array
    {
        return [
            "cliente.nombres.required" => "Debes completar este campo",
            "cliente.nombres.string" => "Debes ingresar solo texto",
            "cliente.nombres.min" => "Debes ingresar al menos :min caracteres",
            "cliente.apellidos.required" => "Debes completar este campo",
            "cliente.apellidos.string" => "Debes ingresar solo texto",
            "cliente.apellidos.min" => "Debes ingresar al menos :min caracteres",
            "cliente.correo.required" => "Debes completar este campo",
            "cliente.correo.email" => "Debes ingresar un correo valido",
            "cliente.correo.unique" => "Este correo no esta disponible",
            "cliente.cel.required" => "Debes completar este campo",
            "cliente.cel.numeric" => "Debes ingresar un valor númerico",
            "cliente.cel.digits_between" => "Debes ingresar un entre 6 y 10 digitos",
            "origen.required" => "Debes enviar el origen de modificacion",
            "origen.string" => "Debes enviar un texto",
            "origen.in" => "Debes enviar si es un admin o user",
            "acceso.required_if" => "Debes enviar el acceso",
        ];
    }
}
