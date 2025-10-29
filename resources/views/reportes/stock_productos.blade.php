<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Stock de productos</title>
    <style type="text/css">
        * {
            font-family: sans-serif;
        }

        @page {
            margin-top: 1.5cm;
            margin-bottom: 0.3cm;
            margin-left: 0.3cm;
            margin-right: 0.3cm;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            table-layout: fixed;
            page-break-before: avoid;
            margin-top: 20px;
            margin: auto;
        }

        table thead tr th,
        tbody tr td {
            padding: 3px;
            word-wrap: break-word;
        }

        table thead tr th {
            font-size: 8pt;
        }

        table tbody tr td {
            font-size: 7pt;
        }


        .encabezado {
            width: 100%;
        }

        .logo img {
            position: absolute;
            height: 100px;
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
            width: 250px;
            text-align: center;
            margin: auto;
            margin-top: 15px;
            font-weight: bold;
            font-size: 1.1em;
        }

        .fecha {
            width: 250px;
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
            width: 250px;
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

        .rojo {
            background-color: #f3b2b2;
        }

        .amarillo {
            background-color: #f2fdb3;
        }

        .verde {
            background-color: #c0faaa;
        }

        .img_celda img {
            width: 45px;
        }

        .nueva_pagina {
            page-break-after: always;
        }
    </style>
</head>

<body>
    @inject('configuracion', 'App\Models\Configuracion')
    @php
        $contador_su = 0;
    @endphp


    @foreach ($sucursals as $sucursal)
        <div class="encabezado">
            <div class="logo">
                <img src="{{ $configuracion->first()->logo_b64 }}">
            </div>
            <h2 class="titulo">
                {{ $configuracion->first()->nombre_sistema }}
            </h2>
            <h4 class="texto">STOCK DE PRODUCTOS</h4>
            <h4 class="texto">{{ $sucursal->nombre }}</h4>
            <h4 class="fecha">Expedido: {{ date('d-m-Y') }}</h4>
        </div>
        <table border="1">
            <thead class="bg-principal">
                <tr>
                    <th>PRODUCTO</th>
                    <th width="10%">STOCK ACTUAL</th>
                    <th width="10%">STOCK MÁXIMO</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $producto_sucursals = [];
                    $producto_sucursals = App\Models\Producto::select(
                        'productos.*',

                        // Subquery para el stock actual
                        DB::raw(
                            'COALESCE((
        SELECT ps.stock_actual
        FROM producto_sucursals ps
        WHERE ps.producto_id = productos.id
          AND ps.sucursal_id = ' .
                                $sucursal->id .
                                '
        LIMIT 1
    ), 0) as stock_actual',
                        ),

                        // Subquery para la fecha de vencimiento más cercana (solo si hay disponible)
                        DB::raw('(
        SELECT MIN(idet.fecha_vencimiento)
        FROM ingreso_detalles idet
        WHERE idet.producto_id = productos.id
          AND idet.fecha_vencimiento IS NOT NULL
          AND idet.disponible > 0
    ) as fecha_vencimiento'),
                    )->where('productos.status', 1);

                    if ($stock != 'todos') {
                        $producto_sucursals->havingRaw('stock_actual <= (productos.stock_maximo * 0.5)');
                    }

                    // Filtrar vencidos / por vencer
                    if ($vencido != 'todos') {
                        $today = now()->toDateString();
                        $threeMonths = now()->addMonths(3)->toDateString();
                        if ($vencido == 'vencidos') {
                            $producto_sucursals->havingRaw("fecha_vencimiento < '{$today}'");
                        }

                        if ($vencido == 'porvencer') {
                            $producto_sucursals->havingRaw("fecha_vencimiento BETWEEN '{$today}' AND '{$threeMonths}'");
                        }
                    }
                    $producto_sucursals = $producto_sucursals->get();
                @endphp
                @foreach ($producto_sucursals as $producto_sucursal)
                    @php
                        $color = 'verde';
                        $mitad = (float) $producto_sucursal->stock_maximo * 0.5;
                        $cuarta = (float) $producto_sucursal->stock_maximo * 0.25;
                        $diez = (float) $producto_sucursal->stock_maximo * 0.1;
                        if ($producto_sucursal->stock_actual <= $cuarta) {
                            $color = 'rojo';
                        } elseif ($producto_sucursal->stock_actual <= $mitad) {
                            $color = 'amarillo';
                        }
                    @endphp
                    <tr class="{{ $color }}">
                        <td>{{ $producto_sucursal->nombre }}</td>
                        <td class="centreado">{{ $producto_sucursal->stock_actual ?? 0 }}</td>
                        <td class="centreado">{{ $producto_sucursal->stock_maximo }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $contador_su++;
        @endphp
        @if ($contador_su < count($sucursals))
            <div class="nueva_pagina"></div>
        @endif
    @endforeach

</body>

</html>
