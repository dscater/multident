<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>OrdenVentas</title>
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
            background: rgb(236, 236, 236)
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
            font-size: 9pt;
        }

        .derecha {
            text-align: right;
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
        <h4 class="texto">LISTA DE ORDENES DE VENTAS</h4>
        <h4 class="fecha">Expedido: {{ date('d-m-Y') }}</h4>
    </div>

    @php
        $contador = 0;
    @endphp

    <table border="1">
        <thead>
            <tr class="bg-principal">
                <th width="3%">NRO.</th>
                <th>SUCURSAL</th>
                <th>USUARIO</th>
                <th>CLIENTE</th>
                <th>NIT/C.I.</th>
                <th>DESCRIPCIÓN</th>
                <th width="7%">PRODUCTO</th>
                <th width="7%">CANTIDAD</th>
                <th width="7%">P/U</th>
                <th width="7%">DESC. PROMOCIÓN %</th>
                <th width="7%">SUBTOTAL</th>
                <th width="7%">IMPORTE TOTAL</th>
                <th width="5%">TIPO DE PAGO</th>
                <th width="5%">FACTURA</th>
                <th width="9%">FECHA</th>
            </tr>
        </thead>
        <tbody>
            @php
                $suma_total = 0;
            @endphp
            @foreach ($orden_ventas as $key => $orden_venta)
                @php
                    $class = '';
                    if ($key % 2 != 0) {
                        $class = 'gray';
                    }

                    $total_filas = count($orden_venta->detalle_ordens);
                @endphp
                <tr class="{{ $class }}">
                    <td rowspan="{{ $total_filas }}">{{ $orden_venta->nro }}</td>
                    <td rowspan="{{ $total_filas }}">{{ $orden_venta->sucursal->nombre }}</td>
                    <td rowspan="{{ $total_filas }}">{{ $orden_venta->user->usuario }}</td>
                    <td rowspan="{{ $total_filas }}">{{ $orden_venta->cliente->full_name }}
                    </td>
                    <td rowspan="{{ $total_filas }}">{{ $orden_venta->nit_ci }}</td>
                    <td rowspan="{{ $total_filas }}">{{ $orden_venta->descripcion }}</td>
                    @php
                        $primero = App\Models\DetalleOrden::where('orden_venta_id', $orden_venta->id)
                            ->where('status', 1)
                            ->get()
                            ->first();
                        $sgtes = App\Models\DetalleOrden::where('orden_venta_id', $orden_venta->id)
                            ->where('id', '!=', $primero->id)
                            ->where('status', 1)
                            ->get();
                    @endphp
                    <td>
                        {{ $primero->producto->nombre }}
                    </td>
                    <td class="centreado">
                        {{ $primero->cantidad }}
                    </td>
                    <td class="derecha">
                        {{ number_format($primero->precio, 2, '.', ',') }}
                    </td>
                    <td class="derecha">
                        {{ $primero->promocion_descuento }}%
                    </td>
                    <td class="derecha">
                        {{ number_format($primero->subtotal, 2, '.', ',') }}
                    </td>
                    <td class="derecha">
                        @if (count($sgtes) == 0)
                            {{ number_format($orden_venta->total, 2, '.', ',') }}
                        @endif
                    </td>
                    <td rowspan="{{ $total_filas }}" class="centreado">{{ $orden_venta->tipo_pago }}</td>
                    <td rowspan="{{ $total_filas }}" class="centreado">{{ $orden_venta->factura }}</td>
                    <td rowspan="{{ $total_filas }}">{{ $orden_venta->fecha_registro_t }}</td>
                </tr>
                @foreach ($sgtes as $key_det => $det)
                    <tr class="{{ $class }}">
                        <td>
                            {{ $det->producto->nombre }}
                        </td>
                        <td class="centreado">
                            {{ $det->cantidad }}
                        </td>
                        <td class="derecha">
                            {{ number_format($det->precio, 2, '.', ',') }}
                        </td>
                        <td class="derecha">
                            {{ $det->promocion_descuento }}%
                        </td>
                        <td class="derecha">
                            {{ number_format($det->subtotal, 2, '.', ',') }}
                        </td>
                        <td class="derecha">
                            @if ($key_det == count($sgtes) - 1)
                                {{ number_format($orden_venta->total, 2, '.', ',') }}
                            @endif
                        </td>
                    </tr>
                @endforeach
                @php
                    $suma_total += (float) $orden_venta->total;
                @endphp
            @endforeach
            <tr class="bg-principal">
                <td colspan="11" class="text-md bold derecha">
                    TOTAL
                </td>
                <td class="text-md bold derecha">{{ number_format($suma_total, 2, '.', ',') }}</td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
