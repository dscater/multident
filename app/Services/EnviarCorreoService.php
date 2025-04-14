<?php

namespace App\Services;

use App\Mail\NuevaOrdenVentaMail;
use App\Mail\NuevaSolicitudProducto;
use App\Mail\SolicitudProductoSeguimientoMail;
use App\Mail\SolicitudProductoVerificacionMail;
use App\Mail\updateEstadoOrdenMail;
use App\Models\Configuracion;
use App\Models\OrdenVenta;
use App\Models\SolicitudProducto;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\library\numero_a_letras\src\NumeroALetras;
use Illuminate\Support\Facades\Log;

class EnviarCorreoService
{

    private $configuracion;
    private $abrev_moneda;
    public function __construct()
    {
        $this->configuracion = null;
        $this->abrev_moneda = "";
        $configuracion = Configuracion::first();
        $servidor_correo = $configuracion->conf_correos;
        if ($configuracion) {
            Config::set(
                [
                    'mail.mailers.default' => $servidor_correo["driver"] ?? 'smtp',
                    'mail.mailers.smtp.host' => $servidor_correo["host"] ?? 'smtp.hostinger.com',
                    'mail.mailers.smtp.port' => $servidor_correo["puerto"] ?? '587',
                    'mail.mailers.smtp.encryption' => $servidor_correo["encriptado"] ?? 'tls',
                    'mail.mailers.smtp.username' => $servidor_correo["correo"] ?? 'mensaje@emsytsrl.com',
                    'mail.mailers.smtp.password' => $servidor_correo["password"] ?? '8Z@d>&kj^y',
                    'mail.from.address' => $servidor_correo["correo"] ?? 'mensaje@emsytsrl.com',
                    'mail.from.name' => $servidor_correo["nombre"] ?? 'GOIRDROP',
                ]
            );
        }
        $this->configuracion = $configuracion;
        if ($configuracion && $configuracion->conf_moneda) {
            $this->abrev_moneda = $configuracion->conf_moneda["abrev"];
        }
    }

    /**
     * Enviar correo de nueva orden de venta
     *
     * @param OrdenVenta $ordenVenta
     * @return void
     */
    public function nuevaOrdenVenta(OrdenVenta $ordenVenta): void
    {

        $mensaje = "Hola " . $ordenVenta->cliente->full_name . '<br/>';
        $mensaje .= 'Tu orden de venta fue registrada correctamente, nosotros nos comunicaremos contigo para finalizar la compra';

        $datos = [
            "mensaje" => $mensaje
        ];

        Mail::to($ordenVenta->cliente->correo)
            ->send(new NuevaOrdenVentaMail($datos));
    }

    /**
     * Enviar correo por cambio de estado orden de venta
     *
     * @param OrdenVenta $ordenVenta
     * @return void
     */
    public function updateEstadoOrdenVenta(OrdenVenta $ordenVenta): void
    {
        $mensaje = "Hola " . $ordenVenta->cliente->full_name . '<br/>';
        $texto_estado = "<b>";
        if ($ordenVenta->estado_orden == 'CONFIRMADO') {
            $texto_estado .= "CONFIRMADA";
        }
        if ($ordenVenta->estado_orden == 'RECHAZADO') {
            $texto_estado .= "RECHAZADO";
        }
        $texto_estado .= "</b>";

        $mensaje .= 'Tu compra con código <b>' . $ordenVenta->codigo . '</b>; fue ' . $texto_estado;

        if ($ordenVenta->estado_orden == 'PENDIENTE') {
            $mensaje = "Hola " . $ordenVenta->cliente->full_name . '<br/>';
            $mensaje .= 'Tu compra con código <b>' . $ordenVenta->codigo . '</b>; aún esta PENDIENTE';
        }


        $datos = [
            "mensaje" => $mensaje,
            "detalleVenta" => $ordenVenta->detalleVenta,
            "total" => $ordenVenta->total,
            "literal" => $this->getLiteralMonto(number_format($ordenVenta->total, 2, ".", "")),
            "abrev_moneda" => $this->abrev_moneda
        ];

        Mail::to($ordenVenta->cliente->correo)
            ->send(new updateEstadoOrdenMail($datos));
    }

    /**
     * Enviar correo de nueva solicitud de producto
     *
     * @param SolicitudProducto $solicitudProducto
     * @return void
     */
    public function nuevaSolicitudProducto(SolicitudProducto $solicitudProducto): void
    {
        $mensaje = "Hola " . $solicitudProducto->cliente->full_name . '<br/>';
        $mensaje .= 'Tu solicitud fue registrada correctamente, nosotros nos comunicaremos contigo.';
        $datos = [
            "mensaje" => $mensaje,
            "solicitudProducto" => $solicitudProducto
        ];

        Mail::to($solicitudProducto->cliente->correo)
            ->send(new NuevaSolicitudProducto($datos));
    }

    /**
     * Enviar correo por cambio de estado verificacion solicitud producto
     *
     * @param SolicitudProducto $solicitudProducto
     * @return void
     */
    public function updateSolicitudProductoVerificacion(SolicitudProducto $solicitudProducto): void
    {
        $mensaje = "Hola " . $solicitudProducto->cliente->full_name . '<br/>';
        $texto_estado = "<b>";
        if ($solicitudProducto->estado_solicitud == 'APROBADO') {
            $texto_estado .= "APROBADA";
        }
        if ($solicitudProducto->estado_solicitud == 'RECHAZADO') {
            $texto_estado .= "RECHAZADA";
        }
        $texto_estado .= "</b>";

        $mensaje .= 'Tu solicitud de producto con código <b>' . $solicitudProducto->codigo_solicitud . '</b>; fue ' . $texto_estado;

        if ($solicitudProducto->estado_solicitud == 'PENDIENTE') {
            $mensaje = "Hola " . $solicitudProducto->cliente->full_name . '<br/>';
            $mensaje .= 'Tu solicitud de producto con código <b>' . $solicitudProducto->codigo_solicitud . '</b>; aún esta PENDIENTE';
        }

        $datos = [
            "mensaje" => $mensaje,
            "solicitudDetalles" => $solicitudProducto->solicitudDetalles,
            "total_precio" => $solicitudProducto->estado_solicitud == 'APROBADO' ? $solicitudProducto->total_precio : null,
            "abrev_moneda" => $this->abrev_moneda
        ];

        Mail::to($solicitudProducto->cliente->correo)
            ->send(new SolicitudProductoVerificacionMail($datos));
    }


    /**
     * Enviar correo por cambio de estado seguimiento solicitud producto
     *
     * @param SolicitudProducto $solicitudProducto
     * @return void
     */
    public function updateSolicitudProductoSeguimiento(SolicitudProducto $solicitudProducto): void
    {
        if ($solicitudProducto->estado_seguimiento == 'PENDIENTE') {
            $mensaje = "Hola " . $solicitudProducto->cliente->full_name . '<br/>';
            $mensaje .= 'Tu solicitud de producto con código <b>' . $solicitudProducto->codigo_solicitud . '</b>; aún esta PENDIENTE';
        }

        if ($solicitudProducto->estado_seguimiento == 'EN PROCESO') {
            $mensaje = "Hola " . $solicitudProducto->cliente->full_name . '<br/>';
            $mensaje .= 'Tu solicitud de producto con código <b>' . $solicitudProducto->codigo_solicitud . '</b>; ya se encuentra <b>EN PROCESO</b>';
        }

        if ($solicitudProducto->estado_seguimiento == 'EN ALMACÉN') {
            $mensaje = "Hola " . $solicitudProducto->cliente->full_name . '<br/>';
            $mensaje .= 'Tu solicitud de producto con código <b>' . $solicitudProducto->codigo_solicitud . '</b>; ya se encuentra <b>EN ALMACÉN</b>';
        }

        if ($solicitudProducto->estado_seguimiento == 'ENTREGADO') {
            $mensaje = "Hola " . $solicitudProducto->cliente->full_name . '<br/>';
            $mensaje .= 'Tu solicitud de producto con código <b>' . $solicitudProducto->codigo_solicitud . '</b>; ya fue <b>ENTREGADO</b>';
        }

        $datos = [
            "mensaje" => $mensaje,
            "solicitudDetalles" => $solicitudProducto->solicitudDetalles,
            "total_precio" => $solicitudProducto->estado_solicitud == 'APROBADO' ? $solicitudProducto->total_precio : null,
            "abrev_moneda" => $this->abrev_moneda
        ];

        Mail::to($solicitudProducto->cliente->correo)
            ->send(new SolicitudProductoSeguimientoMail($datos));
    }

    /**
     * Obtener el literal de un monto
     *
     * @param string $monto
     * @return string
     */
    private function getLiteralMonto(string $monto): string
    {
        $configuracion = Configuracion::first();
        $literal = "";
        $convertir = new NumeroALetras();
        $array_monto = explode('.', $monto);
        $literal = $convertir->convertir($array_monto[0]);
        $literal .= " " . $array_monto[1];
        $literal = ucfirst($literal) . "/100." . " " . $configuracion["conf_moneda"]["moneda"];

        return $literal;
    }
}
