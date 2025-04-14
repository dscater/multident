<?php

namespace App\Http\Requests;

use App\Rules\OrdenVentaCarritoRule;
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
            "cliente_id" => "required",
            "configuracion_pago_id" => "required",
            "token_captcha" => ["required", new TokenCaptchaRule],
            'carrito' => ["required", "array", "min:1", new OrdenVentaCarritoRule],
            "comprobante" => ["required", "file", "mimes:pdf,png,jpg,jpeg,webp", "max:8192"]
        ];
    }

    public function messages(): array
    {
        return [
            "cliente_id.required" => "No es posible realizar el registro debido a que no es un usuario valido",
            "token_captcha" => "Debes marcar la casilla No soy un robot",
            "carrito.required" => "Debes agregar al menos 1 producto al carrito",
            "carrito.array" => "Formato incorrecto del carrito este debe ser un array de datos",
            "carrito.min" => "Debes ingresar al menos :min productos",
            "comprobante.required" => "Debes el comprobante del pago",
            "comprobante.file" => "Debes cargar el comprobante de pago",
            "comprobante.mimes" => "Solo puedes cargar archivos de tipo: pdf,png,jpg,jpeg,webp",
            "comprobante.max" => "El archivo no puede pesar mas de 8MB",
        ];
    }
}
