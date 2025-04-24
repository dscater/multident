<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DevolucionStoreRequest extends FormRequest
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
            "orden_venta_id" => "required",
            "detalle_orden_id" => "required",
            "razon" => "required",
            "descripcion" => "nullable|string|min:2",
        ];
    }

    public function messages(): array
    {
        return [
            "producto_id.required" => "Debes seleccionar una sucursal",
            "orden_venta_id.required" => "Debes seleccionar el nro. de orden de venta",
            "detalle_orden_id.required" => "Debes seleccionar el producto",
            "razon.required" => "Debes seleccionar la razón de devolución",
            "descripcion.string" => "Debes ingresar solo texto",
            "descripcion.min" => "Debes ingresar al menos :min caracteres",
        ];
    }
}
