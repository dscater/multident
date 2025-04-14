<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\DetalleVenta;
use App\Models\HistorialOferta;
use App\Models\OrdenVenta;
use App\Models\Producto;
use App\Models\Publicacion;
use App\Models\PublicacionDetalle;
use App\Models\SolicitudDetalle;
use App\Models\SolicitudProducto;
use App\Models\SubastaCliente;
use App\Models\User;
use App\Services\SedeUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use PDF;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReporteController extends Controller
{
    public $titulo = [
        'font' => [
            'bold' => true,
            'size' => 12,
            'family' => 'Times New Roman'
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
            ],
        ],
    ];

    public $textoBold = [
        'font' => [
            'bold' => true,
            'size' => 10,
        ],
    ];

    public $headerTabla = [
        'font' => [
            'bold' => true,
            'size' => 10,
            'color' => ['argb' => 'ffffff'],
        ],
        'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => '203764']
        ],
    ];

    public $bodyTabla = [
        'font' => [
            'size' => 10,
        ],
        'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            // 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ];

    public $textLeft = [
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        ],
    ];

    public $textCenter = [
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
    ];


    public $bgGanador = [
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'e7ffe7']
        ],
    ];

    private $configuracion = null;
    public function __construct(private SedeUserService $sedeUserService)
    {
        $this->configuracion = Configuracion::first();
        if (!$this->configuracion) {
            $this->configuracion = new Configuracion([
                "nombre_sistema" => "GOIRDROP S.A.",
                "alias" => "GOIRDROP",
                "logo" => "logo.png",
                "fono" => "2222222",
                "dir" => "LOS OLIVOS",
            ]);
        }
    }

    // REPORTE USUARIOS
    public function usuarios()
    {
        return Inertia::render("Admin/Reportes/Usuarios");
    }

    public function r_usuarios(Request $request)
    {
        $role_id =  $request->role_id;
        $formato =  $request->formato;
        $usuarios = User::select("users.*")
            ->where('id', '!=', 1);

        if ($role_id != 'todos') {
            $request->validate([
                'role_id' => 'required',
            ]);
            if ($role_id != 'externo') {
                $usuarios->where('role_id', $role_id);
            } else {
                $usuarios->where('role_id', 2); //CLIENTES
            }
        }

        $usuarios = $usuarios->where("status", 1)->orderBy("apellidos", "ASC")->get();

        if ($formato === 'pdf') {
            $pdf = PDF::loadView('reportes.usuarios', compact('usuarios'))->setPaper('legal', 'landscape');

            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('usuarios.pdf');
        } else {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()
                ->setCreator("ADMIN")
                ->setLastModifiedBy('Administración')
                ->setTitle('Registros')
                ->setSubject('Registros')
                ->setDescription('Registros')
                ->setKeywords('PHPSpreadsheet')
                ->setCategory('Listado');

            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');

            $fila = 1;
            if (file_exists(public_path() . '/imgs/' . $this->configuracion->logo)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('logo');
                $drawing->setDescription('logo');
                $drawing->setPath(public_path() . '/imgs/' . $this->configuracion->logo); // put your path and image here
                $drawing->setCoordinates('A' . $fila);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(0);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            $fila = 2;
            $sheet->setCellValue('A' . $fila, $this->configuracion->nombre_sistema);
            $sheet->mergeCells("A" . $fila . ":J" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':J' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':J' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "LISTA DE USUARIOS");
            $sheet->mergeCells("A" . $fila . ":J" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':J' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':J' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $fila++;
            $sheet->setCellValue('A' . $fila, 'N°');
            $sheet->setCellValue('B' . $fila, 'APELLIDOS');
            $sheet->setCellValue('C' . $fila, 'NOMBRES');
            $sheet->setCellValue('D' . $fila, 'C.I.');
            $sheet->setCellValue('E' . $fila, 'CORREO');
            $sheet->setCellValue('F' . $fila, 'TELÉFONO/CELULAR');
            $sheet->setCellValue('G' . $fila, 'SEDE(S)');
            $sheet->setCellValue('H' . $fila, 'ROLE');
            $sheet->setCellValue('I' . $fila, 'ACCESO');
            $sheet->setCellValue('J' . $fila, 'FECHA DE REGISTRO');
            $sheet->getStyle('A' . $fila . ':J' . $fila)->applyFromArray($this->headerTabla);
            $fila++;

            foreach ($usuarios as $key => $user) {
                $sheet->setCellValue('A' . $fila, $key + 1);
                $sheet->setCellValue('B' . $fila, $user->apellidos);
                $sheet->setCellValue('C' . $fila, $user->nombres);
                $sheet->setCellValue('D' . $fila, $user->full_ci);
                $sheet->setCellValue('E' . $fila, $user->correo);
                $sheet->setCellValue('F' . $fila, $user->cliente ? $user->cliente->cel : '');
                $sheet->setCellValue('G' . $fila, $user->nom_sedes);
                $sheet->setCellValue('H' . $fila, $user->role->nombre);
                $sheet->setCellValue('I' . $fila, $user->acceso == 1 ? 'HABILITADO' : 'DENEGADO');
                $sheet->setCellValue('J' . $fila, $user->fecha_registro_t);
                $fila++;
            }

            $sheet->getColumnDimension('A')->setWidth(6);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(10);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(12);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(13);
            $sheet->getColumnDimension('J')->setWidth(12);

            foreach (range('A', 'J') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:J');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            // DESCARGA DEL ARCHIVO
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="usuarios' . time() . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }
    }

    // REPORTE PRODUCTOS
    public function productos()
    {
        return Inertia::render("Admin/Reportes/Productos");
    }

    public function r_productos(Request $request)
    {
        $formato =  $request->formato;
        $categoria =  $request->categoria;
        $productos = Producto::select("productos.*");

        if ($categoria != 'todos') {
            $productos->where("categoria_id", $categoria);
        }
        $productos = $productos->where("status", 1)->orderBy("nombre", "asc")->get();

        if ($formato == "pdf") {
            $pdf = PDF::loadView('reportes.productos', compact('productos'))->setPaper('letter', 'portrait');

            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('productos.pdf');
        } else {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()
                ->setCreator("ADMIN")
                ->setLastModifiedBy('Administración')
                ->setTitle('Formularios')
                ->setSubject('Formularios')
                ->setDescription('Formularios')
                ->setKeywords('PHPSpreadsheet')
                ->setCategory('Listado');

            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');

            $fila = 1;
            if (file_exists(public_path() . '/imgs/' . $this->configuracion->logo)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('logo');
                $drawing->setDescription('logo');
                $drawing->setPath(public_path() . '/imgs/' . $this->configuracion->logo); // put your path and image here
                $drawing->setCoordinates('A' . $fila);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(0);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            $fila = 2;
            $sheet->setCellValue('A' . $fila, $this->configuracion->nombre_sistema);
            $sheet->mergeCells("A" . $fila . ":J" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':J' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':J' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "LISTA DE PRODUCTOS");
            $sheet->mergeCells("A" . $fila . ":J" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':J' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':J' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $fila++;
            $fila++;
            $sheet->setCellValue('A' . $fila, 'N°');
            $sheet->setCellValue('B' . $fila, 'NOMBRE');
            $sheet->setCellValue('C' . $fila, 'CATEGORÍA');
            $sheet->setCellValue('D' . $fila, 'DESCRIPCIÓN');
            $sheet->setCellValue('E' . $fila, 'STOCK ACTUAL');
            $sheet->setCellValue('F' . $fila, "PRECIO COMPRA \n" . ($this->configuracion->conf_moneda ? $this->configuracion->conf_moneda["abrev"] : ''));
            $sheet->setCellValue('G' . $fila, "PRECIO VENTA \n" . ($this->configuracion->conf_moneda ? $this->configuracion->conf_moneda["abrev"] : ''));
            $sheet->setCellValue('H' . $fila, 'OBSERVACIONES');
            $sheet->setCellValue('I' . $fila, 'PÚBLICO');
            $sheet->setCellValue('J' . $fila, 'FECHA DE REGISTRO');
            $sheet->getStyle('A' . $fila . ':J' . $fila)->applyFromArray($this->headerTabla);
            $fila++;
            $cont = 1;
            foreach ($productos as $producto) {
                $sheet->setCellValue('A' . $fila, $cont++);
                $sheet->setCellValue('B' . $fila, $producto->nombre);
                $sheet->setCellValue('C' . $fila, $producto->categoria->nombre);
                $sheet->setCellValue('D' . $fila, $producto->descripcion);
                $sheet->setCellValue('E' . $fila, $producto->stock_actual);
                $sheet->setCellValue('F' . $fila, number_format($producto->precio_compra, 2, ".", ","));
                $sheet->setCellValue('G' . $fila, number_format($producto->precio_venta, 2, ".", ","));
                $sheet->setCellValue('H' . $fila, $producto->observaciones);
                $sheet->setCellValue('I' . $fila, $producto->publico);
                $sheet->setCellValue('J' . $fila, $producto->fecha_registro_t);
                $sheet->getStyle('A' . $fila . ':J' . $fila)->applyFromArray($this->bodyTabla);
                $fila++;
            }

            $sheet->getColumnDimension('A')->setWidth(6);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(13);
            $sheet->getColumnDimension('F')->setWidth(13);
            $sheet->getColumnDimension('G')->setWidth(13);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(13);
            $sheet->getColumnDimension('J')->setWidth(13);

            foreach (range('A', 'J') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:J');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            // DESCARGA DEL ARCHIVO
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="productos_' . time() . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }
    }

    //ORDENES DE VENTAS
    public function orden_ventas()
    {
        return Inertia::render("Admin/Reportes/OrdenVentas");
    }

    public function r_orden_ventas(Request $request)
    {
        $formato =  $request->formato;
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;
        $estado =  $request->estado;
        $orden_ventas = OrdenVenta::select("orden_ventas.*");

        if ($estado != 'todos') {
            $orden_ventas->where("estado_orden", $estado);
        }

        if ($fecha_ini && $fecha_fin) {
            $orden_ventas->whereBetween("fecha_orden", [$fecha_ini, $fecha_fin]);
        }

        $orden_ventas = $orden_ventas->where("status", 1)->orderBy("id", "asc")->get();
        if ($formato == "pdf") {
            $pdf = PDF::loadView('reportes.orden_ventas', compact('orden_ventas', 'fecha_ini', 'fecha_fin'))->setPaper('legal', 'landscape');

            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('orden_ventas.pdf');
        } else {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()
                ->setCreator("ADMIN")
                ->setLastModifiedBy('Administración')
                ->setTitle('Registros')
                ->setSubject('Registros')
                ->setDescription('Registros')
                ->setKeywords('PHPSpreadsheet')
                ->setCategory('Listado');

            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');

            $fila = 1;
            if (file_exists(public_path() . '/imgs/' . $this->configuracion->logo)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('logo');
                $drawing->setDescription('logo');
                $drawing->setPath(public_path() . '/imgs/' . $this->configuracion->logo); // put your path and image here
                $drawing->setCoordinates('A' . $fila);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(0);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            $fila = 2;
            $sheet->setCellValue('A' . $fila, $this->configuracion->nombre_sistema);
            $sheet->mergeCells("A" . $fila . ":L" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':L' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':L' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "LISTA DE ORDENDES DE VENTAS");
            $sheet->mergeCells("A" . $fila . ":L" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':L' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':L' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $fila++;

            $sheet->setCellValue('A' . $fila, 'CÓDIGO');
            $sheet->setCellValue('B' . $fila, 'CLIENTE');
            $sheet->setCellValue('C' . $fila, 'CELULAR');
            $sheet->setCellValue('D' . $fila, 'CORREO');
            $sheet->setCellValue('E' . $fila, 'ESTADO DE ORDEN');
            $sheet->setCellValue('F' . $fila, 'COMPROBANTE');
            $sheet->setCellValue('G' . $fila, 'PRODUCTO');
            $sheet->setCellValue('H' . $fila, 'CANTIDAD');
            $sheet->setCellValue('I' . $fila, 'PRECIO COMPRA');
            $sheet->setCellValue('J' . $fila, 'SUBTOTAL');
            $sheet->setCellValue('K' . $fila, 'OBSERVACIÓN');
            $sheet->setCellValue('L' . $fila, 'FECHA DE ORDEN');
            $sheet->getStyle('A' . $fila . ':L' . $fila)->applyFromArray($this->headerTabla);
            $fila++;
            $cont = 1;

            $fila_comb = $fila;
            $suma_total = 0;
            foreach ($orden_ventas as $orden_venta) {
                $fila_comb = $fila;
                $sheet->setCellValue('A' . $fila, $orden_venta->codigo);
                $sheet->setCellValue('B' . $fila, $orden_venta->cliente->full_name);
                $sheet->setCellValue('C' . $fila, $orden_venta->cliente->cel);
                $sheet->setCellValue('D' . $fila, $orden_venta->cliente->correo);
                $sheet->setCellValue('E' . $fila, $orden_venta->estado_orden);
                $sheet->setCellValue('F' . $fila, $orden_venta->comprobante ? 'SI' : 'NO');
                $sheet->setCellValue('K' . $fila, $orden_venta->observacion);
                $sheet->setCellValue('L' . $fila, $orden_venta->fecha_orden_t);
                foreach ($orden_venta->detalleVenta as $det) {
                    $sheet->setCellValue('G' . $fila, $det->producto->nombre . ' - ' . $fila);
                    $sheet->setCellValue('H' . $fila, $det->cantidad);
                    $sheet->setCellValue('I' . $fila, number_format($det->precio, 2, ".", ","));
                    $sheet->setCellValue('J' . $fila, number_format($det->subtotal, 2, ".", ","));
                    $sheet->getStyle('A' . $fila . ':L' . $fila)->applyFromArray($this->bodyTabla);
                    // $sheet->getStyle('G' . $fila . ':J' . $fila)->applyFromArray($this->bodyTabla);
                    $fila++;
                }
                $sheet->mergeCells("A" . $fila_comb . ":A" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("B" . $fila_comb . ":B" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("C" . $fila_comb . ":C" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("D" . $fila_comb . ":D" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("E" . $fila_comb . ":E" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("F" . $fila_comb . ":F" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("K" . $fila_comb . ":K" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("L" . $fila_comb . ":L" . $fila - 1);  //COMBINAR CELDAS
                // $fila++;
                $suma_total += (float) $orden_venta->total;
            }
            // $fila++;
            $sheet->setCellValue('A' . $fila, "TOTAL");
            $sheet->mergeCells("A" . $fila . ":I" . $fila);  //COMBINAR CELDAS
            $sheet->setCellValue('J' . $fila, number_format($suma_total, 2, ".", ","));
            $sheet->getStyle("J" . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('A' . $fila . ':L' . $fila)->applyFromArray($this->headerTabla);

            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(13);
            $sheet->getColumnDimension('D')->setWidth(18);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(10);
            $sheet->getColumnDimension('G')->setWidth(26);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(15);
            $sheet->getColumnDimension('J')->setWidth(15);
            $sheet->getColumnDimension('K')->setWidth(15);
            $sheet->getColumnDimension('L')->setWidth(15);

            foreach (range('A', 'L') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:L');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            // DESCARGA DEL ARCHIVO
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="orden_ventas_' . time() . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }
    }

    //SOLICITUD PRODUCTOS
    public function solicitud_productos()
    {
        return Inertia::render("Admin/Reportes/SolicitudProductos");
    }

    public function r_solicitud_productos(Request $request)
    {
        $formato =  $request->formato;
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;
        $estado =  $request->estado;
        $solicitud_productos = SolicitudProducto::select("solicitud_productos.*");

        if ($estado != 'todos') {
            $solicitud_productos->where("estado_solicitud", $estado);
        }

        if ($fecha_ini && $fecha_fin) {
            $solicitud_productos->whereBetween("fecha_solicitud", [$fecha_ini, $fecha_fin]);
        }

        // Filtro por usuario
        $user = Auth::user();
        if ($user->sedes_todo != 1) {
            $sedes_id = $this->sedeUserService->getArraySedesIdUser();
            $solicitud_productos->whereIn("solicitud_productos.sede_id", $sedes_id);
        }

        $solicitud_productos = $solicitud_productos->where("status", 1)->orderBy("id", "asc")->get();
        if ($formato == "pdf") {
            $pdf = PDF::loadView('reportes.solicitud_productos', compact('solicitud_productos', 'fecha_ini', 'fecha_fin'))->setPaper('letter', 'landscape');

            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('solicitud_productos.pdf');
        } else {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()
                ->setCreator("ADMIN")
                ->setLastModifiedBy('Administración')
                ->setTitle('Registros')
                ->setSubject('Registros')
                ->setDescription('Registros')
                ->setKeywords('PHPSpreadsheet')
                ->setCategory('Listado');

            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');

            $fila = 1;
            if (file_exists(public_path() . '/imgs/' . $this->configuracion->logo)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('logo');
                $drawing->setDescription('logo');
                $drawing->setPath(public_path() . '/imgs/' . $this->configuracion->logo); // put your path and image here
                $drawing->setCoordinates('A' . $fila);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(0);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            $fila = 2;
            $sheet->setCellValue('A' . $fila, $this->configuracion->nombre_sistema);
            $sheet->mergeCells("A" . $fila . ":M" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':M' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "LISTA DE SOLICITUD DE COMPRA DE PRODUCTOS");
            $sheet->mergeCells("A" . $fila . ":M" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':M' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $fila++;

            $sheet->setCellValue('A' . $fila, 'CÓDIGO');
            $sheet->setCellValue('B' . $fila, 'CLIENTE');
            $sheet->setCellValue('C' . $fila, 'CELULAR');
            $sheet->setCellValue('D' . $fila, 'CORREO');
            $sheet->setCellValue('E' . $fila, 'SEDE');
            $sheet->setCellValue('F' . $fila, 'PRODUCTO');
            $sheet->setCellValue('G' . $fila, 'DETALLE');
            $sheet->setCellValue('H' . $fila, 'ESTADO DE SOLICITUD');
            $sheet->setCellValue('I' . $fila, "PRECIO COMPRA \n" . ($this->configuracion->conf_moneda ? $this->configuracion->conf_moneda["abrev"] : ''));
            $sheet->setCellValue('J' . $fila, "MARGEN GANANCIA \n" . ($this->configuracion->conf_moneda ? $this->configuracion->conf_moneda["abrev"] : ''));
            $sheet->setCellValue('K' . $fila, 'OBSERVACIÓN');
            $sheet->setCellValue('L' . $fila, 'SEGUIMIENTO');
            $sheet->setCellValue('M' . $fila, 'FECHA DE SOLICITUD');
            $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->headerTabla);
            $fila++;
            $cont = 1;

            foreach ($solicitud_productos as $solicitud_producto) {
                $sheet->setCellValue('A' . $fila, $solicitud_producto->codigo_solicitud);
                $sheet->setCellValue('B' . $fila, $solicitud_producto->cliente->full_name);
                $sheet->setCellValue('C' . $fila, $solicitud_producto->cliente->cel);
                $sheet->setCellValue('D' . $fila, $solicitud_producto->cliente->correo);
                $sheet->setCellValue('E' . $fila, $solicitud_producto->sede->nombre);
                $solicitudDetalle = SolicitudDetalle::where(
                    'solicitud_producto_id',
                    $solicitud_producto->id,
                )->get()->first();
                $sheet->setCellValue('F' . $fila, $solicitudDetalle->nombre_producto ?? '');
                $sheet->setCellValue('G' . $fila, $solicitudDetalle->detalle_producto ?? '');
                $sheet->setCellValue('H' . $fila, $solicitud_producto->estado_solicitud);
                $sheet->setCellValue('I' . $fila, $solicitud_producto->precio_compra ?? '');
                $sheet->setCellValue('J' . $fila, $solicitud_producto->margen_ganancia ?? '');
                $sheet->setCellValue('K' . $fila, $solicitud_producto->observacion ?? '');
                $sheet->setCellValue('L' . $fila, $solicitud_producto->estado_seguimiento ?? '');
                $sheet->setCellValue('M' . $fila, $solicitud_producto->fecha_solicitud_t);
                $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->bodyTabla);
                $fila++;
                $links = str_replace(["<br />", "<br/>", "<br>", "<BR/>", "<BR />", "<BR>"], "\n", $solicitudDetalle->links_referencia);
                $sheet->setCellValue('A' . $fila, $links);
                $sheet->mergeCells("A" . $fila . ":M" . $fila);  //COMBINAR CELDAS
                $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->bodyTabla);

                $fila++;
            }


            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(13);
            $sheet->getColumnDimension('D')->setWidth(18);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(10);
            $sheet->getColumnDimension('G')->setWidth(26);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(15);
            $sheet->getColumnDimension('J')->setWidth(15);
            $sheet->getColumnDimension('K')->setWidth(15);
            $sheet->getColumnDimension('L')->setWidth(15);
            $sheet->getColumnDimension('M')->setWidth(15);

            foreach (range('A', 'M') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:M');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            // DESCARGA DEL ARCHIVO
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="solicitud_productos_' . time() . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }
    }

    //SEGUIMIENTO DE SOLICITUD PRODUCTOS
    public function seguimiento_solicituds()
    {
        return Inertia::render("Admin/Reportes/SeguimientoSolicituds");
    }

    public function r_seguimiento_solicituds(Request $request)
    {
        $formato =  $request->formato;
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;
        $estado =  $request->estado;
        $solicitud_productos = SolicitudProducto::select("solicitud_productos.*");

        if ($estado != 'todos') {
            $solicitud_productos->where("estado_seguimiento", $estado);
        }

        if ($fecha_ini && $fecha_fin) {
            $solicitud_productos->whereBetween("fecha_solicitud", [$fecha_ini, $fecha_fin]);
        }

        // Filtro por usuario
        $user = Auth::user();
        if ($user->sedes_todo != 1) {
            $sedes_id = $this->sedeUserService->getArraySedesIdUser();
            $solicitud_productos->whereIn("solicitud_productos.sede_id", $sedes_id);
        }

        $solicitud_productos = $solicitud_productos->where("status", 1)->orderBy("id", "asc")->get();
        if ($formato == "pdf") {
            $pdf = PDF::loadView('reportes.seguimiento_solicituds', compact('solicitud_productos', 'fecha_ini', 'fecha_fin'))->setPaper('letter', 'landscape');

            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('solicitud_productos.pdf');
        } else {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()
                ->setCreator("ADMIN")
                ->setLastModifiedBy('Administración')
                ->setTitle('Registros')
                ->setSubject('Registros')
                ->setDescription('Registros')
                ->setKeywords('PHPSpreadsheet')
                ->setCategory('Listado');

            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');

            $fila = 1;
            if (file_exists(public_path() . '/imgs/' . $this->configuracion->logo)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('logo');
                $drawing->setDescription('logo');
                $drawing->setPath(public_path() . '/imgs/' . $this->configuracion->logo); // put your path and image here
                $drawing->setCoordinates('A' . $fila);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(0);
                $drawing->setHeight(60);
                $drawing->setWorksheet($sheet);
            }

            $fila = 2;
            $sheet->setCellValue('A' . $fila, $this->configuracion->nombre_sistema);
            $sheet->mergeCells("A" . $fila . ":M" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':M' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "LISTA DE SEGUIMIENTO DE SOLICITUD DE COMPRA DE PRODUCTOS");
            $sheet->mergeCells("A" . $fila . ":M" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':M' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $fila++;

            $sheet->setCellValue('A' . $fila, 'CÓDIGO');
            $sheet->setCellValue('B' . $fila, 'CLIENTE');
            $sheet->setCellValue('C' . $fila, 'CELULAR');
            $sheet->setCellValue('D' . $fila, 'CORREO');
            $sheet->setCellValue('E' . $fila, 'SEDE');
            $sheet->setCellValue('F' . $fila, 'PRODUCTO');
            $sheet->setCellValue('G' . $fila, 'DETALLE');
            $sheet->setCellValue('H' . $fila, 'ESTADO DE SOLICITUD');
            $sheet->setCellValue('I' . $fila, "PRECIO COMPRA \n" . ($this->configuracion->conf_moneda ? $this->configuracion->conf_moneda["abrev"] : ''));
            $sheet->setCellValue('J' . $fila, "MARGEN GANANCIA \n" . ($this->configuracion->conf_moneda ? $this->configuracion->conf_moneda["abrev"] : ''));
            $sheet->setCellValue('K' . $fila, 'OBSERVACIÓN');
            $sheet->setCellValue('L' . $fila, 'SEGUIMIENTO');
            $sheet->setCellValue('M' . $fila, 'FECHA DE SOLICITUD');
            $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->headerTabla);
            $fila++;
            $cont = 1;

            foreach ($solicitud_productos as $solicitud_producto) {
                $sheet->setCellValue('A' . $fila, $solicitud_producto->codigo_solicitud);
                $sheet->setCellValue('B' . $fila, $solicitud_producto->cliente->full_name);
                $sheet->setCellValue('C' . $fila, $solicitud_producto->cliente->cel);
                $sheet->setCellValue('D' . $fila, $solicitud_producto->cliente->correo);
                $sheet->setCellValue('E' . $fila, $solicitud_producto->sede->nombre);
                $solicitudDetalle = SolicitudDetalle::where(
                    'solicitud_producto_id',
                    $solicitud_producto->id,
                )->get()->first();
                $sheet->setCellValue('F' . $fila, $solicitudDetalle->nombre_producto ?? '');
                $sheet->setCellValue('G' . $fila, $solicitudDetalle->detalle_producto ?? '');
                $sheet->setCellValue('H' . $fila, $solicitud_producto->estado_solicitud);
                $sheet->setCellValue('I' . $fila, $solicitud_producto->precio_compra ?? '');
                $sheet->setCellValue('J' . $fila, $solicitud_producto->margen_ganancia ?? '');
                $sheet->setCellValue('K' . $fila, $solicitud_producto->observacion ?? '');
                $sheet->setCellValue('L' . $fila, $solicitud_producto->estado_seguimiento ?? '');
                $sheet->setCellValue('M' . $fila, $solicitud_producto->fecha_solicitud_t);
                $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->bodyTabla);
                $fila++;
                $links = str_replace(["<br />", "<br/>", "<br>", "<BR/>", "<BR />", "<BR>"], "\n", $solicitudDetalle->links_referencia);
                $sheet->setCellValue('A' . $fila, $links);
                $sheet->mergeCells("A" . $fila . ":M" . $fila);  //COMBINAR CELDAS
                $sheet->getStyle('A' . $fila . ':M' . $fila)->applyFromArray($this->bodyTabla);
                $fila++;
            }


            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(13);
            $sheet->getColumnDimension('D')->setWidth(18);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(10);
            $sheet->getColumnDimension('G')->setWidth(26);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(15);
            $sheet->getColumnDimension('J')->setWidth(15);
            $sheet->getColumnDimension('K')->setWidth(15);
            $sheet->getColumnDimension('L')->setWidth(15);
            $sheet->getColumnDimension('M')->setWidth(15);

            foreach (range('A', 'M') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:M');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            // DESCARGA DEL ARCHIVO
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="seguimiento_solicituds' . time() . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }
    }

    // GRAFÍCO ORDENES DE VENTAS
    public function g_orden_ventas()
    {
        return Inertia::render("Admin/Reportes/GOrdenVentas");
    }

    public function r_g_orden_ventas(Request $request)
    {
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $estado = $request->estado;

        $orden_ventas = OrdenVenta::select("orden_ventas.*");

        $categories = ["PENDIENTE", "RECHAZADO", "CONFIRMADO"];

        if ($estado != 'todos') {
            $orden_ventas->where("estado_orden", $estado);
            $categories = [$estado];
        }

        $orden_ventas = $orden_ventas->where("status", 1)->get();
        $data = [];
        foreach ($categories as $value) {
            $total = DetalleVenta::join("orden_ventas", "orden_ventas.id", "=", "detalle_ventas.orden_venta_id")
                ->where("orden_ventas.status", 1)
                ->where("orden_ventas.estado_orden", $value);
            $ordenVentas = OrdenVenta::with(["cliente", "detalleVenta.producto"])->where("estado_orden", $value)
                ->where("status", 1);

            if ($fecha_ini && $fecha_fin) {
                $ordenVentas->whereBetween("orden_ventas.fecha_orden", [$fecha_ini, $fecha_fin]);
                $total->whereBetween("orden_ventas.fecha_orden", [$fecha_ini, $fecha_fin]);
            }
            $total = $total->sum("detalle_ventas.cantidad");
            $ordenVentas = $ordenVentas->orderBy("id", "asc")->get();

            $data[] = [
                "y" => (int)$total,
                "ordenVentas" => $ordenVentas
            ];
        }

        return response()->JSON([
            "categories" => $categories,
            "data" => $data
        ]);
    }

    // GRAFICO SOLICITUD DE PRODUCTOS
    public function g_solicitud_productos()
    {
        return Inertia::render("Admin/Reportes/GSolicitudProductos");
    }

    public function r_g_solicitud_productos(Request $request)
    {
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $estado = $request->estado;

        $solicitud_productos = SolicitudProducto::select("solicitud_productos.*");

        $categories = ["PENDIENTE", "RECHAZADO", "APROBADO"];

        if ($estado != 'todos') {
            $solicitud_productos->where("estado_solicitud", $estado);
            $categories = [$estado];
        }

        $solicitud_productos = $solicitud_productos->where("status", 1)->get();
        $data = [];
        foreach ($categories as $value) {
            $total = SolicitudDetalle::join("solicitud_productos", "solicitud_productos.id", "=", "solicitud_detalles.solicitud_producto_id")
                ->where("solicitud_productos.status", 1)
                ->where("solicitud_productos.estado_solicitud", $value);
            $solicitudProductos = SolicitudProducto::with(["cliente", "solicitudDetalles"])->where("estado_solicitud", $value)
                ->where("status", 1);

            if ($fecha_ini && $fecha_fin) {
                $solicitudProductos->whereBetween("solicitud_productos.fecha_solicitud", [$fecha_ini, $fecha_fin]);
                $total->whereBetween("solicitud_productos.fecha_solicitud", [$fecha_ini, $fecha_fin]);
            }
            // $total = $total->sum("solicitud_detalles.cantidad");
            $total = $total->count();
            $solicitudProductos = $solicitudProductos->orderBy("id", "asc")->get();

            $data[] = [
                "y" => (int)$total,
                "solicitudProductos" => $solicitudProductos
            ];
        }

        return response()->JSON([
            "categories" => $categories,
            "data" => $data
        ]);
    }



    // GRAFICO DE SEGUIMIENTO DE SOLICITUD DE PRODUCTOS
    public function g_seguimiento_productos()
    {
        return Inertia::render("Admin/Reportes/GSeguimientoProductos");
    }

    public function r_g_seguimiento_productos(Request $request)
    {
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $estado = $request->estado;

        $solicitud_productos = SolicitudProducto::select("solicitud_productos.*");

        $categories = ["PENDIENTE", "EN PROCESO", "EN ALMACÉN", "ENTREGADO"];

        if ($estado != 'todos') {
            $solicitud_productos->where("estado_seguimiento", $estado);
            $categories = [$estado];
        }

        $solicitud_productos = $solicitud_productos->where("status", 1)->get();
        $data = [];
        foreach ($categories as $value) {
            $total = SolicitudDetalle::join("solicitud_productos", "solicitud_productos.id", "=", "solicitud_detalles.solicitud_producto_id")
                ->where("solicitud_productos.status", 1)
                ->where("solicitud_productos.estado_seguimiento", $value);
            $solicitudProductos = SolicitudProducto::with(["cliente", "solicitudDetalles"])->where("estado_seguimiento", $value)
                ->where("status", 1);

            if ($fecha_ini && $fecha_fin) {
                $solicitudProductos->whereBetween("solicitud_productos.fecha_solicitud", [$fecha_ini, $fecha_fin]);
                $total->whereBetween("solicitud_productos.fecha_solicitud", [$fecha_ini, $fecha_fin]);
            }
            // $total = $total->sum("solicitud_detalles.cantidad");
            $total = $total->count();
            $solicitudProductos = $solicitudProductos->orderBy("id", "asc")->get();

            $data[] = [
                "y" => (int)$total,
                "solicitudProductos" => $solicitudProductos
            ];
        }

        return response()->JSON([
            "categories" => $categories,
            "data" => $data
        ]);
    }
}
