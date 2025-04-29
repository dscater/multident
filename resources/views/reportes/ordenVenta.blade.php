<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OrdenVenta</title>
    <style>
        @page {
            margin: 0;
            width: 9.5cm;
        }

        body {
            width: 9.5cm !important;
        }

        #principal {
            width: 9.5cm !important;
        }

        #contenedor_imprimir {
            font-size: 0.94em;
            width: 9.5cm !important;
            padding-top: 15px;
            padding-bottom: 15px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .elemento {
            text-align: center;
        }

        .elemento.logo img {
            width: 60%;
        }

        .separador {
            padding: 0px;
            margin: 0px;
        }

        .fono,
        .lp {
            font-size: 0.75em;
        }

        .txt_fo {
            margin-top: 3px;
            border-top: solid 1px black;
        }

        .detalle {
            border-top: solid 1px black;
            border-bottom: solid 1px black;
        }

        .act_eco {
            font-size: 0.73em;
        }

        .info1 {
            text-align: center;
            font-weight: bold;
            font-size: 0.75em;
        }

        .info2 {
            text-align: center;
            font-weight: bold;
            font-size: 0.7em;
        }

        .izquierda {
            text-align: left;
            padding-left: 5px;
        }

        .derecha {
            text-align: right;
            padding-right: 5px;
        }

        .informacion {
            padding: 5px;
            width: 100%;
        }

        .bold {
            font-weight: bold;
        }

        .cobro {
            width: 100%;
            padding: 5px;
        }

        .cobro table {
            width: 100%;
        }

        .centreado {
            text-align: center;
        }

        .cobro table tr td {
            font-size: 0.9em;
        }

        .literal {
            font-size: 0.89em;
        }

        .cod_control,
        .fecha_emision {
            font-size: 0.9em;
        }

        .cobro table {
            border-collapse: collapse;
        }

        .cobro table tr.punteado td {
            border-top: solid 1px black;
            border-bottom: solid 1px black;
        }

        .total {
            font-size: 0.9em !important;
        }

        .contenedor_qr {
            display: block;
            width: 100%;
        }

        img.qr {
            width: 160px;
            height: 160px;
        }
        .bold{

        }
    </style>
</head>

<body>
    @inject('mConfiguracion', 'App\Models\Configuracion')
    @php
        $oConfiguracion = $mConfiguracion->first();
    @endphp
    <div class="contenedor_factura ml-auto mr-auto" id="contenedor_imprimir">
        {{-- <div class="elemento logo">
        <img
            src="{{ asset('imgs/' . $empresa->logo) }}"
            alt="Logo"
        />
    </div> --}}
        <div class="elemento nom_empresa">
            {{ $oConfiguracion->razon_social }}
        </div>
        <div class="elemento direccion bold">
            ORDEN DE VENTA
        </div>
        <div class="elemento direccion bold">
            Sucursal: {{ $ordenVenta->sucursal->nombre }}
        </div>
        <div class="elemento direccion">
            Dirección: Zona central calle 2 nro. 233
        </div>
        <div class="elemento direccion">
            Teléfonos: 2558547 - 76544875 - 76522474
        </div>
        <div class="elemento detalle izquierda">
            Nro. Orden: {{ $nro_factura }}<br/>
            Fecha:
            {{ $ordenVenta->fecha }}
            &nbsp;&nbsp; Hora:
            {{ $ordenVenta->hora }}
            <br />
            Cliente:
            {{ $ordenVenta->cliente ? $ordenVenta->cliente->full_name : '' }}
            <br />
            NIT/C.I.: {{ $ordenVenta->nit_ci }}
            <br />
            Caja:
            {{ $ordenVenta->user ? $ordenVenta->user->usuario : '' }}
            <br />
        </div>
        <div class="elemento bold">DETALLE</div>
        <div class="cobro">
            <table>
                <tr class="punteado">
                    <td class="centreado">
                        CANTIDAD
                    </td>
                    <td class="centreado">
                        PRODUCTO
                    </td>
                    <td class="centreado">
                        P/U
                    </td>
                    <td class="centreado">
                        D/P %
                    </td>
                    <td class="centreado">
                        SUBTOTAL
                    </td>
                </tr>
                @foreach ($ordenVenta->detalle_ordens as $item)
                    <tr>
                        <td class="centreado">
                            {{ $item->cantidad }}
                        </td>
                        <td class="izquierda">
                            {{ $item->producto->nombre }}
                        </td>
                        <td class="centreado">
                            {{ $item->precio }}
                        </td>
                        <td class="centreado">
                            {{ $item->promocion_descuento }}%
                        </td>
                        <td class="centreado">
                            {{ $item->subtotal }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" class="bold elemento detalle"
                        style="
                        text-align: right;
                        padding-right: 4px;
                    ">
                        TOTAL
                    </td>
                    <td class="centreado bold detalle" style="">
                        {{ $ordenVenta->total }}
                    </td>
                </tr>
            </table>
        </div>
        <div class="centreado literal">
            Son: {{ $literal }}
        </div>
    </div>
    <script type="text/javascript">
        try {
            this.print();
        } catch (e) {
            window.onload = window.print;
        }
    </script>
</body>

</html>
