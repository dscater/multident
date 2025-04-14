<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>SeguimientoProductos</title>
    <style type="text/css">
        * {
            font-family: sans-serif;
        }

        @page {
            margin-top: 1.5cm;
            margin-bottom: 1cm;
            margin-left: 1cm;
            margin-right: 1cm;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 20px;
            page-break-before: avoid;
        }

        table thead tr th,
        tbody tr td {
            padding: 3px;
            word-wrap: break-word;
        }

        table thead tr th {
            font-size: 7pt;
        }

        table tbody tr td {
            font-size: 6pt;
        }


        .encabezado {
            width: 100%;
        }

        .logo img {
            position: absolute;
            height: 70px;
            top: -20px;
            left: 0px;
        }

        h2.titulo {
            width: 450px;
            margin: auto;
            margin-top: 0PX;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14pt;
        }

        .texto {
            width: 400px;
            text-align: center;
            margin: auto;
            margin-top: 15px;
            font-weight: bold;
            font-size: 1.1em;
        }

        .fecha {
            width: 400px;
            text-align: center;
            margin: auto;
            margin-top: 15px;
            font-weight: normal;
            font-size: 0.85em;
        }

        .total {
            text-align: right;
            padding-right: 15px;
            font-weight: bold;
        }

        table {
            width: 100%;
        }

        table thead {
            background: rgb(236, 236, 236)
        }

        tr {
            page-break-inside: avoid !important;
        }

        .centreado {
            padding-left: 0px;
            text-align: center;
        }

        .datos {
            margin-left: 15px;
            border-top: solid 1px;
            border-collapse: collapse;
            width: 400px;
        }

        .txt {
            font-weight: bold;
            text-align: right;
            padding-right: 5px;
        }

        .txt_center {
            font-weight: bold;
            text-align: center;
        }

        .cumplimiento {
            position: absolute;
            width: 150px;
            right: 0px;
            top: 86px;
        }

        .b_top {
            border-top: solid 1px black;
        }

        .gray {
            background: rgb(202, 202, 202);
        }

        .bg-principal {
            background: #153f59;
            color: white;
        }

        .page_break {
            page-break-after: always;
        }

        .img_celda img {
            width: 45px;
        }

        .lista {
            padding-left: 8px;
        }

        .bold {
            font-weight: bold;
        }

        .text-md {
            font-size: 1.2em;
        }

        .bg-ganador {
            background-color: #e7ffe7;
        }
    </style>
</head>

<body>
    @inject('configuracion', 'App\Models\Configuracion')
    <div class="encabezado">
        <div class="logo">
            <img src="{{ $configuracion->first()->logo_b64 }}">
        </div>
        <h2 class="titulo">
            {{ $configuracion->first()->nombre_sistema }}
        </h2>
        <h4 class="texto">LISTA DE SEGUIMIENTO DE SOLICITUD DE COMPRA DE PRODUCTOS</h4>
        <h4 class="fecha">Expedido: {{ date('d-m-Y') }}</h4>
    </div>

    @php
        $contador = 0;
    @endphp

    <table border="1">
        <thead>
            <tr>
                <th width="5%">CÓDIGO</th>
                <th>CLIENTE</th>
                <th>CELULAR</th>
                <th>CORREO</th>
                <th>SEDE</th>
                <th>PRODUCTO</th>
                <th>DETALLE</th>
                <th>ESTADO DE SOLICITUD</th>
                <th>PRECIO COMPRA
                    <br />{{ $configuracion->first()->conf_moneda ? $configuracion->first()->conf_moneda['abrev'] : '' }}
                </th>
                <th>MARGEN GANANCIA
                    <br />{{ $configuracion->first()->conf_moneda ? $configuracion->first()->conf_moneda['abrev'] : '' }}
                </th>
                <th>OBSERVACIÓN</th>
                <th>SEGUIMIENTO</th>
                <th width="9%">FECHA DE SOLICITUD</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($solicitud_productos as $solicitud_producto)
                <tr>
                    <td>{{ $solicitud_producto->codigo_solicitud }}</td>
                    <td>{{ $solicitud_producto->cliente->full_name }}</td>
                    <td>{{ $solicitud_producto->cliente->cel }}</td>
                    <td>{{ $solicitud_producto->cliente->correo }}</td>
                    <td>{{ $solicitud_producto->sede->nombre }}</td>
                    @php
                        $solicitudDetalle = App\Models\SolicitudDetalle::where(
                            'solicitud_producto_id',
                            $solicitud_producto->id,
                        )
                            ->get()
                            ->first();
                    @endphp

                    <td>{{ $solicitudDetalle->nombre_producto ?? '' }}</td>
                    <td>{{ $solicitudDetalle->detalle_producto ?? '' }}</td>
                    <td>{{ $solicitud_producto->estado_solicitud }}</td>
                    <td>{{ $solicitud_producto->precio_compra ?? '' }}</td>
                    <td>{{ $solicitud_producto->margen_ganancia ?? '' }}</td>
                    <td>{{ $solicitud_producto->observacion }}</td>
                    <td>{{ $solicitud_producto->estado_seguimiento ?? '' }}</td>
                    <td>{{ $solicitud_producto->fecha_solicitud_t }}</td>
                </tr>
                <tr>
                    <td colspan="13">{!! $solicitudDetalle->links_referencia ?? '' !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
