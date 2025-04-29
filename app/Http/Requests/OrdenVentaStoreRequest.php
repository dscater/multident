<?php

namespace App\Http\Requests;

use App\Rules\OrdenVentaCarritoRule;
use App\Rules\OrdenVentaDetalleRule;
use App\Rules\TokenCaptchaRule;
use Illuminate\Foundation\Http\FormRequest;

class OrdenVentaStoreRequest extends FormRequest
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
            "tipo_pago" => "required",
            'detalle_ordens' => ["required", "array", "min:1", new OrdenVentaDetalleRule],
            'list_promocions' => ["nullable", "array"],
            "descripcion" => "nullable|string"
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
            "tipo_pago.required" => "Debes seleccionar el tipo de pago",
            "detalle_ordens.required" => "Debes agregar al menos 1 producto",
            "detalle_ordens.array" => "Formato incorrecto del detalle_ordens este debe ser un array de datos",
            "detalle_ordens.min" => "Debes ingresar al menos :min productos",
            "descripcion.string" => "Debes ingresar un texto",
        ];
    }
}
