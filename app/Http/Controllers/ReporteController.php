<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\DetalleOrden;
use App\Models\DetalleVenta;
use App\Models\Devolucion;
use App\Models\HistorialOferta;
use App\Models\IngresoDetalle;
use App\Models\KardexProducto;
use App\Models\OrdenVenta;
use App\Models\Producto;
use App\Models\Proforma;
use App\Models\Publicacion;
use App\Models\PublicacionDetalle;
use App\Models\SalidaProducto;
use App\Models\SolicitudDetalle;
use App\Models\SolicitudProducto;
use App\Models\SubastaCliente;
use App\Models\Sucursal;
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
use Illuminate\Support\Facades\DB;
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

    public $textRight = [
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
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
            $usuarios->where('role_id', $role_id);
        }

        $usuarios = $usuarios->where("status", 1)->orderBy("paterno", "ASC")->get();

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
            $sheet->mergeCells("A" . $fila . ":I" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':I' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "LISTA DE USUARIOS");
            $sheet->mergeCells("A" . $fila . ":I" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':I' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $fila++;
            $sheet->setCellValue('A' . $fila, 'N°');
            $sheet->setCellValue('B' . $fila, 'APELLIDOS');
            $sheet->setCellValue('C' . $fila, 'NOMBRES');
            $sheet->setCellValue('D' . $fila, 'C.I.');
            $sheet->setCellValue('E' . $fila, 'CORREO');
            $sheet->setCellValue('F' . $fila, 'SUCURSAL');
            $sheet->setCellValue('G' . $fila, 'ROLE');
            $sheet->setCellValue('H' . $fila, 'ACCESO');
            $sheet->setCellValue('I' . $fila, 'FECHA DE REGISTRO');
            $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->headerTabla);
            $fila++;

            foreach ($usuarios as $key => $user) {
                $sheet->setCellValue('A' . $fila, $key + 1);
                $sheet->setCellValue('B' . $fila, $user->paterno . ' ' . $user->materno);
                $sheet->setCellValue('C' . $fila, $user->nombres);
                $sheet->setCellValue('D' . $fila, $user->full_ci);
                $sheet->setCellValue('E' . $fila, $user->correo);
                $sheet->setCellValue('F' . $fila, $user->sucursals_todo == 0 ? $user->sucursal->nombre : 'TODOS');
                $sheet->setCellValue('G' . $fila, $user->role->nombre);
                $sheet->setCellValue('H' . $fila, $user->acceso == 1 ? 'HABILITADO' : 'DENEGADO');
                $sheet->setCellValue('I' . $fila, $user->fecha_registro_t);
                $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->bodyTabla);
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
            $sheet->getPageSetup()->setPrintArea('A:I');
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

    // REPORTE KARDEX PRODUCTOS
    public function kardex_productos()
    {
        return Inertia::render("Admin/Reportes/KardexProductos");
    }

    public function r_kardex_productos(Request $request)
    {
        $producto_id = $request->producto_id;
        $sucursal_id = $request->sucursal_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $formato = $request->formato;

        $productos = Producto::select("productos.*");
        if ($producto_id != 'todos') {
            $productos->where("id", $producto_id);
        }
        $productos = $productos->get();

        $sucursals = Sucursal::select("sucursals.*");
        if ($sucursal_id != 'todos') {
            $sucursals->where("id", $sucursal_id);
        }
        $sucursals = $sucursals->get();

        $kardex_sucursals = [];
        foreach ($sucursals as $sucursal) {
            $kardex_sucursals[$sucursal->id] = [
                "array_kardex" => [],
                "array_saldo_anterior" => [],
            ];
            $array_kardex = [];
            $array_saldo_anterior = [];
            foreach ($productos as $registro) {
                $kardex = KardexProducto::where('producto_id', $registro->id)
                    ->where("sucursal_id", $sucursal->id)->get();
                $array_saldo_anterior[$registro->id] = [
                    'sw' => false,
                    'saldo_anterior' => []
                ];
                if ($fecha_ini && $fecha_fin) {
                    $kardex = KardexProducto::where('producto_id', $registro->id)
                        ->where("sucursal_id", $sucursal->id)
                        ->whereBetween('fecha', [$fecha_ini, $fecha_fin])->get();
                    // buscar saldo anterior si existe
                    $saldo_anterior = KardexProducto::where('producto_id', $registro->id)
                        ->where("sucursal_id", $sucursal->id)
                        ->where('fecha', '<', $fecha_ini)
                        ->orderBy('created_at', 'asc')->get()->last();
                    if ($saldo_anterior) {
                        $cantidad_saldo = $saldo_anterior->cantidad_saldo;
                        $monto_saldo = $saldo_anterior->monto_saldo;
                        $array_saldo_anterior[$registro->id] = [
                            'sw' => true,
                            'saldo_anterior' => [
                                'cantidad_saldo' => $cantidad_saldo,
                                'monto_saldo' => $monto_saldo,
                            ]
                        ];
                    }
                }

                $array_kardex[$registro->id] = $kardex;
            }
            $kardex_sucursals[$sucursal->id]["array_kardex"] = $array_kardex;
            $kardex_sucursals[$sucursal->id]["array_saldo_anterior"] = $array_saldo_anterior;
        }

        if ($formato == 'pdf') {
            $pdf = PDF::loadView('reportes.kardex_productos', compact('productos', 'sucursals', "kardex_sucursals"))->setPaper('letter', 'portrait');

            // ENUMERAR LAS PÁGINAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('kardex.pdf');
        } else {
            $array_dias = [
                '0' => 'Domingo',
                '1' => 'Lunes',
                '2' => 'Martes',
                '3' => 'Miércoles',
                '4' => 'Jueves',
                '5' => 'Viernes',
                '6' => 'Sábado',
            ];
            $array_meses = [
                '01' => 'enero',
                '02' => 'febrero',
                '03' => 'marzo',
                '04' => 'abril',
                '05' => 'mayo',
                '06' => 'junio',
                '07' => 'julio',
                '08' => 'agosto',
                '09' => 'septiembre',
                '10' => 'octubre',
                '11' => 'noviembre',
                '12' => 'diciembre',
            ];

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

            foreach ($sucursals as $sucursal) {
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
                $fila++;

                $sheet->setCellValue('A' . $fila, $this->configuracion->nombre_sistema);
                $sheet->mergeCells("A" . $fila . ":I" . $fila);  //COMBINAR CELDAS
                $sheet->getStyle('A' . $fila . ':I' . $fila)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->titulo);
                $fila++;
                $sheet->setCellValue('A' . $fila, "KARDEX DE PRODUCTOS");
                $sheet->mergeCells("A" . $fila . ":I" . $fila);  //COMBINAR CELDAS
                $sheet->getStyle('A' . $fila . ':I' . $fila)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->titulo);
                $fila++;
                $sheet->setCellValue('A' . $fila, $sucursal->nombre);
                $sheet->mergeCells("A" . $fila . ":I" . $fila);  //COMBINAR CELDAS
                $sheet->getStyle('A' . $fila . ':I' . $fila)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->titulo);
                $fila++;
                $sheet->setCellValue('A' . $fila, $array_dias[date('w')] . ', ' . date('d') . ' de ' . $array_meses[date('m')] . ' de ' . date('Y'));
                $sheet->mergeCells("A" . $fila . ":I" . $fila);  //COMBINAR CELDAS
                $sheet->getStyle('A' . $fila . ':I' . $fila)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->titulo);
                $fila++;
                $sheet->setCellValue('A' . $fila, '(Expresado en bolivianos)');
                $sheet->mergeCells("A" . $fila . ":I" . $fila);  //COMBINAR CELDAS
                $sheet->getStyle('A' . $fila . ':I' . $fila)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->titulo);
                $fila++;
                $fila++;

                foreach ($productos as $registro) {
                    $sheet->setCellValue('A' . $fila, $registro->nombre);
                    $sheet->mergeCells("A" . $fila . ":I" . $fila);  //COMBINAR CELDAS
                    $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->headerTabla);
                    $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->textCenter);
                    $fila++;
                    $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->textCenter);
                    $sheet->setCellValue('A' . $fila, 'FECHA');
                    $sheet->mergeCells("A" . $fila . ":A" . ($fila + 1));  //COMBINAR CELDAS
                    $sheet->setCellValue('B' . $fila, 'DETALLE');
                    $sheet->mergeCells("B" . $fila . ":B" . ($fila + 1));  //COMBINAR CELDAS
                    $sheet->setCellValue('C' . $fila, 'CANTIDADES');
                    $sheet->mergeCells("C" . $fila . ":E" . $fila);  //COMBINAR CELDAS
                    $sheet->setCellValue('F' . $fila, 'P/U');
                    $sheet->mergeCells("F" . $fila . ":F" . ($fila + 1));  //COMBINAR CELDAS
                    $sheet->setCellValue('G' . $fila, 'BOLIVIANOS');
                    $sheet->mergeCells("G" . $fila . ":I" . $fila);  //COMBINAR CELDAS
                    $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->headerTabla);
                    $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->textCenter);
                    $fila++;
                    $sheet->setCellValue('C' . $fila, 'ENTRADA');
                    $sheet->setCellValue('D' . $fila, 'SALIDA');
                    $sheet->setCellValue('E' . $fila, 'SALDO');

                    $sheet->setCellValue('G' . $fila, 'ENTRADA');
                    $sheet->setCellValue('H' . $fila, 'SALIDA');
                    $sheet->setCellValue('I' . $fila, 'SALDO');

                    $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->headerTabla);
                    $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->textCenter);
                    $fila++;
                    if (
                        count($kardex_sucursals[$sucursal->id]['array_kardex'][$registro->id]) > 0 ||
                        $kardex_sucursals[$sucursal->id]['array_saldo_anterior'][$registro->id]['sw']
                    ) {
                        if ($kardex_sucursals[$sucursal->id]['array_saldo_anterior'][$registro->id]['sw']) {
                            $sheet->setCellValue('B' . $fila, 'SALDO ANTERIOR');
                            $sheet->setCellValue('E' . $fila, $kardex_sucursals[$sucursal->id]['array_saldo_anterior'][$registro->id]['saldo_anterior']['cantidad_saldo']);
                            $sheet->setCellValue('F' . $fila,  $registro->precio);
                            $sheet->setCellValue('I' . $fila,  number_format($kardex_sucursals[$sucursal->id]['array_saldo_anterior'][$registro->id]['saldo_anterior']['monto_saldo'], 2, '.', ','));
                            $fila++;
                        }

                        foreach ($kardex_sucursals[$sucursal->id]['array_kardex'][$registro->id] as $key => $value) {
                            $sheet->setCellValue('A' . $fila,  date('d-m-Y', strtotime($value['fecha'])));
                            $sheet->setCellValue('B' . $fila, $value['detalle']);
                            $sheet->setCellValue('C' . $fila,  $value['cantidad_ingreso']);
                            $sheet->setCellValue('D' . $fila, $value['cantidad_salida']);
                            $sheet->setCellValue('E' . $fila, $value['cantidad_saldo']);
                            $sheet->setCellValue('F' . $fila, number_format($value['cu'], 2, '.', ','));
                            $sheet->setCellValue('G' . $fila,  $value['monto_ingreso']);
                            $sheet->setCellValue('H' . $fila,  $value['monto_salida']);
                            $sheet->setCellValue('I' . $fila, number_format($value['monto_saldo'], 2, '.', ','));
                            $sheet->getStyle('A' . $fila . ':I' . $fila)->applyFromArray($this->bodyTabla);
                            $fila++;
                        }
                    } else {
                        $sheet->setCellValue('A' . $fila, 'NO SE ENCONTRARÓN REGISTROS');
                        $sheet->mergeCells("A" . $fila . ":I" . $fila);  //COMBINAR CELDAS
                    }
                    $fila += 3;
                }
                $fila += 3;
            }


            $sheet->getColumnDimension('A')->setWidth(10);
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
            $sheet->getPageSetup()->setPrintArea('A:I');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            // DESCARGA DEL ARCHIVO
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="kardex_productos' . time() . '.xlsx"');
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
        $producto_id =  $request->producto_id;
        $sucursal_id =  $request->sucursal_id;
        $factura =  $request->factura;
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;
        $orden_ventas = OrdenVenta::select("orden_ventas.*")
            ->join("detalle_ordens", "detalle_ordens.orden_venta_id", "=", "orden_ventas.id")
            ->groupBy("orden_ventas.id");

        if ($producto_id != 'todos') {
            $orden_ventas->where("detalle_ordens.producto_id", $producto_id);
        }

        if ($sucursal_id != 'todos') {
            $orden_ventas->where("orden_ventas.sucursal_id", $sucursal_id);
        }

        if ($factura != 'todos') {
            $orden_ventas->where("orden_ventas.factura", $factura);
        }

        if ($fecha_ini && $fecha_fin) {
            $orden_ventas->whereBetween("fecha_registro", [$fecha_ini, $fecha_fin]);
        }

        $orden_ventas = $orden_ventas->where("orden_ventas.status", 1)->orderBy("id", "asc")->get();
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
            $sheet->mergeCells("A" . $fila . ":O" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':O' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':O' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "LISTA DE ORDENDES DE VENTAS");
            $sheet->mergeCells("A" . $fila . ":O" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':O' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':O' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $fila++;

            $sheet->setCellValue('A' . $fila, 'NRO');
            $sheet->setCellValue('B' . $fila, 'SUCURSAL');
            $sheet->setCellValue('C' . $fila, 'USUARIO');
            $sheet->setCellValue('D' . $fila, 'CLIENTE');
            $sheet->setCellValue('E' . $fila, 'NIT/C.I.');
            $sheet->setCellValue('F' . $fila, 'DESCRIPCIÓN');
            $sheet->setCellValue('G' . $fila, 'PRODUCTO');
            $sheet->setCellValue('H' . $fila, 'CANTIDAD');
            $sheet->setCellValue('I' . $fila, 'P/U');
            $sheet->setCellValue('J' . $fila, 'DESC. PROMOCIÓN %');
            $sheet->setCellValue('K' . $fila, 'SUBTOTAL');
            $sheet->setCellValue('L' . $fila, 'IMPORTE TOTAL');
            $sheet->setCellValue('M' . $fila, 'TIPO DE PAGO');
            $sheet->setCellValue('N' . $fila, 'FACTURA');
            $sheet->setCellValue('O' . $fila, 'FECHA');
            $sheet->getStyle('A' . $fila . ':O' . $fila)->applyFromArray($this->headerTabla);
            $fila++;

            $fila_comb = $fila;
            $suma_total = 0;
            foreach ($orden_ventas as $orden_venta) {
                $fila_comb = $fila;
                $sheet->setCellValue('A' . $fila, $orden_venta->nro);
                $sheet->setCellValue('B' . $fila, $orden_venta->sucursal->nombre);
                $sheet->setCellValue('C' . $fila, $orden_venta->user->usuario);
                $sheet->setCellValue('D' . $fila, $orden_venta->cliente->full_name);
                $sheet->setCellValue('E' . $fila, $orden_venta->nit_ci);
                $sheet->setCellValue('F' . $fila, $orden_venta->descripcion);
                $sheet->setCellValue('O' . $fila, $orden_venta->fecha_registro_t);
                foreach ($orden_venta->detalle_ordens as $key_det => $det) {
                    $sheet->setCellValue('G' . $fila, $det->producto->nombre . ' - ' . $fila);
                    $sheet->setCellValue('H' . $fila, $det->cantidad);
                    $sheet->setCellValue('I' . $fila, number_format($det->precio, 2, ".", ","));
                    $sheet->setCellValue('J' . $fila, $det->promocion_descuento . '%');
                    $sheet->setCellValue('K' . $fila, number_format($det->subtotal, 2, ".", ","));
                    if ($key_det == count($orden_venta->detalle_ordens) - 1) {
                        $sheet->setCellValue('L' . $fila, number_format($orden_venta->total, 2, ".", ","));
                        $sheet->setCellValue('M' . $fila, $orden_venta->tipo_pago);
                        $sheet->setCellValue('N' . $fila, $orden_venta->factura);
                    }
                    $sheet->getStyle('A' . $fila . ':O' . $fila)->applyFromArray($this->bodyTabla);
                    $sheet->getStyle('K' . $fila . ':L' . $fila)->applyFromArray($this->textRight);
                    $fila++;
                }
                $sheet->mergeCells("A" . $fila_comb . ":A" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("B" . $fila_comb . ":B" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("C" . $fila_comb . ":C" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("D" . $fila_comb . ":D" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("E" . $fila_comb . ":E" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("F" . $fila_comb . ":F" . $fila - 1);  //COMBINAR CELDAS
                // $sheet->mergeCells("M" . $fila_comb . ":M" . $fila - 1);  //COMBINAR CELDAS
                // $sheet->mergeCells("N" . $fila_comb . ":N" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("O" . $fila_comb . ":O" . $fila - 1);  //COMBINAR CELDAS
                // $fila++;
                $suma_total += (float) $orden_venta->total;
            }
            // $fila++;
            $sheet->setCellValue('A' . $fila, "TOTAL");
            $sheet->mergeCells("A" . $fila . ":K" . $fila);  //COMBINAR CELDAS
            $sheet->setCellValue('L' . $fila, number_format($suma_total, 2, ".", ","));
            $sheet->getStyle("A" . $fila . ":L" . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('A' . $fila . ':O' . $fila)->applyFromArray($this->headerTabla);

            $sheet->getColumnDimension('A')->setWidth(8);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(13);
            $sheet->getColumnDimension('D')->setWidth(18);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(26);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(15);
            $sheet->getColumnDimension('J')->setWidth(15);
            $sheet->getColumnDimension('K')->setWidth(15);
            $sheet->getColumnDimension('L')->setWidth(15);
            $sheet->getColumnDimension('M')->setWidth(15);
            $sheet->getColumnDimension('N')->setWidth(15);
            $sheet->getColumnDimension('O')->setWidth(15);

            foreach (range('A', 'O') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:O');
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

    //STOCK DE PRODUCTOS
    public function stock_productos()
    {
        return Inertia::render("Admin/Reportes/StockProductos");
    }

    public function r_stock_productos(Request $request)
    {
        $formato =  $request->formato;
        $estado =  $request->estado;

        $sucursals = Sucursal::where("status", 1)->get();

        if ($formato == "pdf") {
            $pdf = PDF::loadView('reportes.stock_productos', compact('sucursals'))->setPaper('letter', 'portrait');

            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('stock_productos.pdf');
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

            foreach ($sucursals as $sucursal) {
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

                $fila++;
                $sheet->setCellValue('A' . $fila, $this->configuracion->nombre_sistema);
                $sheet->mergeCells("A" . $fila . ":C" . $fila);  //COMBINAR CELDAS
                $sheet->getStyle('A' . $fila . ':C' . $fila)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A' . $fila . ':C' . $fila)->applyFromArray($this->titulo);
                $fila++;
                $sheet->setCellValue('A' . $fila, "STOCK DE PRODUCTOS");
                $sheet->mergeCells("A" . $fila . ":C" . $fila);  //COMBINAR CELDAS
                $sheet->getStyle('A' . $fila . ':C' . $fila)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A' . $fila . ':C' . $fila)->applyFromArray($this->titulo);
                $fila++;
                $sheet->setCellValue('A' . $fila,  $sucursal->nombre);
                $sheet->mergeCells("A" . $fila . ":C" . $fila);  //COMBINAR CELDAS
                $sheet->getStyle('A' . $fila . ':C' . $fila)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A' . $fila . ':C' . $fila)->applyFromArray($this->titulo);
                $fila++;
                $fila++;

                $sheet->setCellValue('A' . $fila, 'PRODUCTO');
                $sheet->setCellValue('B' . $fila, 'STOCK ACTUAL');
                $sheet->setCellValue('C' . $fila, 'STOCK MÁXIMO');
                $sheet->getStyle('A' . $fila . ':C' . $fila)->applyFromArray($this->headerTabla);
                $fila++;

                $producto_sucursals = Producto::select(
                    'productos.*',
                    DB::raw(
                        '(
                        SELECT COALESCE(stock_actual, 0)
                            FROM producto_sucursals
                            WHERE producto_sucursals.producto_id = productos.id
                            AND producto_sucursals.sucursal_id = ' .
                            $sucursal->id .
                            '
                            LIMIT 1
                        ) as stock_actual',
                    ),
                )
                    ->where('status', 1)
                    ->get();
                foreach ($producto_sucursals as $producto_sucursal) {
                    $sheet->setCellValue('A' . $fila, $producto_sucursal->nombre);
                    $sheet->setCellValue('B' . $fila, $producto_sucursal->stock_actual ?? 0);
                    $sheet->setCellValue('C' . $fila, $producto_sucursal->stock_maximo);
                    $sheet->getStyle('A' . $fila . ':C' . $fila)->applyFromArray($this->bodyTabla);
                    $fila++;
                }
                $fila += 3;
            }

            $sheet->getColumnDimension('A')->setWidth(40);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);

            foreach (range('A', 'C') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:C');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            // DESCARGA DEL ARCHIVO
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="stock_productos_' . time() . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }
    }

    // INGRESO DE PRODUCTOS
    public function ingreso_productos()
    {
        return Inertia::render("Admin/Reportes/IngresoProductos");
    }

    public function r_ingreso_productos(Request $request)
    {
        $producto_id = $request->producto_id;
        $sucursal_id = $request->sucursal_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $formato = $request->formato;

        $ingreso_detalles = IngresoDetalle::select("ingreso_detalles.*")
            ->join("ingreso_productos", "ingreso_productos.id", "=", "ingreso_detalles.ingreso_producto_id");

        if ($producto_id != 'todos') {
            $ingreso_detalles->where('ingreso_detalles.producto_id', $producto_id);
        }
        if ($sucursal_id != 'todos') {
            $ingreso_detalles->where('ingreso_productos.sucursal_id', $sucursal_id);
        }

        if ($fecha_ini && $fecha_fin) {
            $ingreso_detalles->whereBetween('ingreso_productos.fecha_registro', [$fecha_ini, $fecha_fin]);
        }

        $ingreso_detalles = $ingreso_detalles->where("ingreso_productos.status", 1)->get();

        if ($formato === 'pdf') {
            $pdf = PDF::loadView('reportes.ingreso_productos', compact('ingreso_detalles'))->setPaper('legal', 'portrait');

            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('ingreso_productos.pdf');
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
            $sheet->mergeCells("A" . $fila . ":H" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':H' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "INGRESOS DE PRODUCTOS");
            $sheet->mergeCells("A" . $fila . ":H" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':H' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $fila++;
            $sheet->setCellValue('A' . $fila, 'N°');
            $sheet->setCellValue('B' . $fila, 'SUCURSAL');
            $sheet->setCellValue('C' . $fila, 'PRODUCTO');
            $sheet->setCellValue('D' . $fila, 'CANTIDAD');
            $sheet->setCellValue('E' . $fila, 'UBICACIÓN');
            $sheet->setCellValue('F' . $fila, 'FECHA DE VENCIMIENTO');
            $sheet->setCellValue('G' . $fila, 'DESCRIPCIÓN');
            $sheet->setCellValue('H' . $fila, 'FECHA DE REGISTRO');
            $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->headerTabla);
            $fila++;

            foreach ($ingreso_detalles as $key => $ingreso_detalle) {
                $sheet->setCellValue('A' . $fila, $key + 1);
                $sheet->setCellValue('B' . $fila, $ingreso_detalle->ingreso_producto->sucursal->nombre);
                $sheet->setCellValue('C' . $fila, $ingreso_detalle->producto->nombre);
                $sheet->setCellValue('D' . $fila, $ingreso_detalle->cantidad);
                $sheet->setCellValue('E' . $fila, $ingreso_detalle->ubicacion_producto->lugar . ' - ' . $ingreso_detalle->ubicacion_producto->numero_filas);
                $sheet->setCellValue('F' . $fila, $ingreso_detalle->fecha_vencimiento_t);
                $sheet->setCellValue('G' . $fila, $ingreso_detalle->ingreso_producto->descripcion);
                $sheet->setCellValue('H' . $fila, $ingreso_detalle->ingreso_producto->fecha_registro_t);
                $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->bodyTabla);
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

            foreach (range('A', 'H') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:H');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            // DESCARGA DEL ARCHIVO
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="ingreso_productos' . time() . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }
    }

    // SALIDAS DE PRODUCTOS
    public function salida_productos()
    {
        return Inertia::render("Admin/Reportes/SalidaProductos");
    }

    public function r_salida_productos(Request $request)
    {
        $producto_id = $request->producto_id;
        $sucursal_id = $request->sucursal_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $formato = $request->formato;

        $salida_productos = SalidaProducto::select("salida_productos.*");

        if ($producto_id != 'todos') {
            $salida_productos->where('salida_productos.producto_id', $producto_id);
        }
        if ($sucursal_id != 'todos') {
            $salida_productos->where('salida_productos.sucursal_id', $sucursal_id);
        }

        if ($fecha_ini && $fecha_fin) {
            $salida_productos->whereBetween('salida_productos.fecha_registro', [$fecha_ini, $fecha_fin]);
        }

        $salida_productos = $salida_productos->where("salida_productos.status", 1)->get();

        if ($formato === 'pdf') {
            $pdf = PDF::loadView('reportes.salida_productos', compact('salida_productos'))->setPaper('legal', 'portrait');

            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('salida_productos.pdf');
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
            $sheet->mergeCells("A" . $fila . ":F" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':F' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "SALIDAS DE PRODUCTOS");
            $sheet->mergeCells("A" . $fila . ":F" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':F' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $fila++;
            $sheet->setCellValue('A' . $fila, 'N°');
            $sheet->setCellValue('B' . $fila, 'SUCURSAL');
            $sheet->setCellValue('C' . $fila, 'PRODUCTO');
            $sheet->setCellValue('D' . $fila, 'CANTIDAD');
            $sheet->setCellValue('E' . $fila, 'DESCRIPCIÓN');
            $sheet->setCellValue('F' . $fila, 'FECHA DE REGISTRO');
            $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($this->headerTabla);
            $fila++;

            foreach ($salida_productos as $key => $salida_producto) {
                $sheet->setCellValue('A' . $fila, $key + 1);
                $sheet->setCellValue('B' . $fila, $salida_producto->sucursal->nombre);
                $sheet->setCellValue('C' . $fila, $salida_producto->producto->nombre);
                $sheet->setCellValue('D' . $fila, $salida_producto->cantidad);
                $sheet->setCellValue('E' . $fila, $salida_producto->descripcion);
                $sheet->setCellValue('F' . $fila, $salida_producto->fecha_registro_t);
                $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($this->bodyTabla);
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

            foreach (range('A', 'H') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:F');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            // DESCARGA DEL ARCHIVO
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="salida_productos' . time() . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }
    }

    // DEVOLUCIONES
    public function devolucions()
    {
        return Inertia::render("Admin/Reportes/Devolucions");
    }

    public function r_devolucions(Request $request)
    {
        $sucursal_id = $request->sucursal_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $formato = $request->formato;

        $devolucions = Devolucion::select("devolucions.*");

        if ($sucursal_id != 'todos') {
            $devolucions->where('devolucions.sucursal_id', $sucursal_id);
        }

        if ($fecha_ini && $fecha_fin) {
            $devolucions->whereBetween('devolucions.fecha_registro', [$fecha_ini, $fecha_fin]);
        }

        $devolucions = $devolucions->get();

        if ($formato === 'pdf') {
            $pdf = PDF::loadView('reportes.devolucions', compact('devolucions'))->setPaper('legal', 'portrait');

            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('devolucions.pdf');
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
            $sheet->mergeCells("A" . $fila . ":H" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':H' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "SALIDAS DE PRODUCTOS");
            $sheet->mergeCells("A" . $fila . ":H" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':H' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $fila++;
            $sheet->setCellValue('A' . $fila, 'N°');
            $sheet->setCellValue('B' . $fila, 'SUCURSAL');
            $sheet->setCellValue('C' . $fila, 'NRO. ORDEN DE VENTA');
            $sheet->setCellValue('D' . $fila, 'PRODUCTO');
            $sheet->setCellValue('E' . $fila, 'CANTIDAD');
            $sheet->setCellValue('F' . $fila, 'RAZÓN DE DEVOLUCIÓN');
            $sheet->setCellValue('G' . $fila, 'DESCRIPCIÓN');
            $sheet->setCellValue('H' . $fila, 'FECHA DE REGISTRO');
            $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->headerTabla);
            $fila++;

            foreach ($devolucions as $key => $devolucion) {
                $sheet->setCellValue('A' . $fila, $key + 1);
                $sheet->setCellValue('B' . $fila, $devolucion->sucursal->nombre);
                $sheet->setCellValue('C' . $fila, $devolucion->orden_venta->nro);
                $sheet->setCellValue('D' . $fila, $devolucion->producto->nombre);
                $sheet->setCellValue('E' . $fila, $devolucion->detalle_orden->cantidad);
                $sheet->setCellValue('F' . $fila, $devolucion->razon);
                $sheet->setCellValue('G' . $fila, $devolucion->descripcion);
                $sheet->setCellValue('H' . $fila, $devolucion->fecha_registro_t);
                $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->bodyTabla);
                $fila++;
            }

            $sheet->getColumnDimension('A')->setWidth(6);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(10);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(15);

            foreach (range('A', 'H') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:H');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            // DESCARGA DEL ARCHIVO
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="devolucions' . time() . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }
    }


    // PROFORMAS
    public function proformas()
    {
        return Inertia::render("Admin/Reportes/Proformas");
    }

    public function r_proformas(Request $request)
    {
        $sucursal_id = $request->sucursal_id;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $formato = $request->formato;

        $proformas = Proforma::select("proformas.*");

        if ($sucursal_id != 'todos') {
            $proformas->where('proformas.sucursal_id', $sucursal_id);
        }

        if ($fecha_ini && $fecha_fin) {
            $proformas->whereBetween('proformas.fecha_registro', [$fecha_ini, $fecha_fin]);
        }

        $proformas = $proformas->where("status", 1)->get();

        if ($formato === 'pdf') {
            $pdf = PDF::loadView('reportes.proformas', compact('proformas'))->setPaper('legal', 'landscape');

            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('proformas.pdf');
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
            $sheet->mergeCells("A" . $fila . ":O" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':O' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':O' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "PROFORMAS");
            $sheet->mergeCells("A" . $fila . ":O" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':O' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':O' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $fila++;

            $sheet->setCellValue('A' . $fila, 'NRO');
            $sheet->setCellValue('B' . $fila, 'SUCURSAL');
            $sheet->setCellValue('C' . $fila, 'USUARIO');
            $sheet->setCellValue('D' . $fila, 'CLIENTE');
            $sheet->setCellValue('E' . $fila, 'NIT/C.I.');
            $sheet->setCellValue('F' . $fila, 'DESCRIPCIÓN');
            $sheet->setCellValue('G' . $fila, 'PRODUCTO');
            $sheet->setCellValue('H' . $fila, 'CANTIDAD');
            $sheet->setCellValue('I' . $fila, 'P/U');
            $sheet->setCellValue('J' . $fila, 'DESC. PROMOCIÓN %');
            $sheet->setCellValue('K' . $fila, 'SUBTOTAL');
            $sheet->setCellValue('L' . $fila, 'IMPORTE TOTAL');
            $sheet->setCellValue('M' . $fila, 'FECHA DE VALIDEZ HASTA');
            $sheet->setCellValue('N' . $fila, 'FACTURA');
            $sheet->setCellValue('O' . $fila, 'FECHA');
            $sheet->getStyle('A' . $fila . ':O' . $fila)->applyFromArray($this->headerTabla);
            $fila++;

            $fila_comb = $fila;
            $suma_total = 0;
            foreach ($proformas as $proforma) {
                $fila_comb = $fila;
                $sheet->setCellValue('A' . $fila, $proforma->nro);
                $sheet->setCellValue('B' . $fila, $proforma->sucursal->nombre);
                $sheet->setCellValue('C' . $fila, $proforma->user->usuario);
                $sheet->setCellValue('D' . $fila, $proforma->cliente->full_name);
                $sheet->setCellValue('E' . $fila, $proforma->nit_ci);
                $sheet->setCellValue('F' . $fila, $proforma->descripcion);
                $sheet->setCellValue('O' . $fila, $proforma->fecha_registro_t);
                foreach ($proforma->detalle_proformas as $key_det => $det) {
                    $sheet->setCellValue('G' . $fila, $det->producto->nombre . ' - ' . $fila);
                    $sheet->setCellValue('H' . $fila, $det->cantidad);
                    $sheet->setCellValue('I' . $fila, number_format($det->precio, 2, ".", ","));
                    $sheet->setCellValue('J' . $fila, $det->promocion_descuento . '%');
                    $sheet->setCellValue('K' . $fila, number_format($det->subtotal, 2, ".", ","));
                    if ($key_det == count($proforma->detalle_proformas) - 1) {
                        $sheet->setCellValue('L' . $fila, number_format($proforma->total, 2, ".", ","));
                        $sheet->setCellValue('M' . $fila, $proforma->fecha_validez_t);
                        $sheet->setCellValue('N' . $fila, $proforma->factura);
                    }
                    $sheet->getStyle('A' . $fila . ':O' . $fila)->applyFromArray($this->bodyTabla);
                    $sheet->getStyle('K' . $fila . ':L' . $fila)->applyFromArray($this->textRight);
                    $fila++;
                }
                $sheet->mergeCells("A" . $fila_comb . ":A" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("B" . $fila_comb . ":B" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("C" . $fila_comb . ":C" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("D" . $fila_comb . ":D" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("E" . $fila_comb . ":E" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("F" . $fila_comb . ":F" . $fila - 1);  //COMBINAR CELDAS
                // $sheet->mergeCells("M" . $fila_comb . ":M" . $fila - 1);  //COMBINAR CELDAS
                // $sheet->mergeCells("N" . $fila_comb . ":N" . $fila - 1);  //COMBINAR CELDAS
                $sheet->mergeCells("O" . $fila_comb . ":O" . $fila - 1);  //COMBINAR CELDAS
                // $fila++;
                $suma_total += (float) $proforma->total;
            }
            // $fila++;
            $sheet->setCellValue('A' . $fila, "TOTAL");
            $sheet->mergeCells("A" . $fila . ":K" . $fila);  //COMBINAR CELDAS
            $sheet->setCellValue('L' . $fila, number_format($suma_total, 2, ".", ","));
            $sheet->getStyle("A" . $fila . ":L" . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('A' . $fila . ':O' . $fila)->applyFromArray($this->headerTabla);

            $sheet->getColumnDimension('A')->setWidth(8);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(13);
            $sheet->getColumnDimension('D')->setWidth(18);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(26);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(15);
            $sheet->getColumnDimension('J')->setWidth(15);
            $sheet->getColumnDimension('K')->setWidth(15);
            $sheet->getColumnDimension('L')->setWidth(15);
            $sheet->getColumnDimension('M')->setWidth(15);
            $sheet->getColumnDimension('N')->setWidth(15);
            $sheet->getColumnDimension('O')->setWidth(15);

            foreach (range('A', 'O') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:O');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            // DESCARGA DEL ARCHIVO
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="proformas_' . time() . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }
    }

    // CLIENTES
    public function clientes()
    {
        return Inertia::render("Admin/Reportes/Clientes");
    }

    public function r_clientes(Request $request)
    {
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $formato = $request->formato;

        $clientes = Cliente::select("clientes.*");

        if ($fecha_ini && $fecha_fin) {
            $clientes->whereBetween('clientes.fecha_registro', [$fecha_ini, $fecha_fin]);
        }

        $clientes = $clientes->where("clientes.status", 1)->get();

        if ($formato === 'pdf') {
            $pdf = PDF::loadView('reportes.clientes', compact('clientes'))->setPaper('legal', 'portrait');

            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('clientes.pdf');
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
            $sheet->mergeCells("A" . $fila . ":F" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':F' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "LISTA DE CLIENTES");
            $sheet->mergeCells("A" . $fila . ":F" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':F' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $fila++;
            $sheet->setCellValue('A' . $fila, 'N°');
            $sheet->setCellValue('B' . $fila, 'NOMBRE CLIENTE');
            $sheet->setCellValue('C' . $fila, 'NIT/C.I.');
            $sheet->setCellValue('D' . $fila, 'CELULAR');
            $sheet->setCellValue('E' . $fila, 'DESCRIPCIÓN');
            $sheet->setCellValue('F' . $fila, 'FECHA DE REGISTRO');
            $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($this->headerTabla);
            $fila++;

            foreach ($clientes as $key => $cliente) {
                $sheet->setCellValue('A' . $fila, $key + 1);
                $sheet->setCellValue('B' . $fila, $cliente->full_name);
                $sheet->setCellValue('C' . $fila, $cliente->ci);
                $sheet->setCellValue('D' . $fila, $cliente->cel);
                $sheet->setCellValue('E' . $fila, $cliente->descripcion);
                $sheet->setCellValue('F' . $fila, $cliente->fecha_registro_t);
                $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($this->bodyTabla);
                $fila++;
            }

            $sheet->getColumnDimension('A')->setWidth(6);
            $sheet->getColumnDimension('B')->setWidth(35);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(10);
            $sheet->getColumnDimension('E')->setWidth(35);
            $sheet->getColumnDimension('F')->setWidth(12);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(15);

            foreach (range('A', 'H') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:F');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            // DESCARGA DEL ARCHIVO
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="clientes' . time() . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }
    }

    // GRAFÍCO CANTIDAD DE ORDENES DE VENTAS
    public function g_cantidad_orden_ventas()
    {
        return Inertia::render("Admin/Reportes/GOrdenVentas");
    }

    public function r_g_cantidad_orden_ventas(Request $request)
    {
        $producto_id =  $request->producto_id;
        $sucursal_id =  $request->sucursal_id;
        $factura =  $request->factura;
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;

        $productos = Producto::where("status", 1);

        if ($producto_id != 'todos') {
            $productos->where("id", $producto_id);
        }
        $productos = $productos->get();

        $data = [];
        $categories = [];
        foreach ($productos as $producto) {
            $categories[] = $producto->nombre;
            $cantidad = DetalleOrden::join("orden_ventas", "orden_ventas.id", "=", "detalle_ordens.orden_venta_id")
                ->where("orden_ventas.status", 1)
                ->where("producto_id", $producto->id);
            if ($fecha_ini && $fecha_fin) {
                $cantidad->whereBetween("orden_ventas.fecha_registro", [$fecha_ini, $fecha_fin]);
            }

            if ($sucursal_id != 'todos') {
                $cantidad->where("orden_ventas.sucursal_id", $sucursal_id);
            }
            if ($factura != 'todos') {
                $cantidad->where("orden_ventas.factura", $factura);
            }

            $cantidad = $cantidad->where("detalle_ordens.status", 1)->orderBy("id", "asc")->sum("detalle_ordens.cantidad");

            $data[] = [
                "y" => (int)$cantidad,
            ];
        }

        return response()->JSON([
            "categories" => $categories,
            "data" => $data
        ]);
    }

    // GRAFÍCO ORDENES DE VENTAS
    public function g_ingresos_orden_ventas()
    {
        return Inertia::render("Admin/Reportes/GIngresoOrdenVentas");
    }

    public function r_g_ingresos_orden_ventas(Request $request)
    {
        $producto_id =  $request->producto_id;
        $sucursal_id =  $request->sucursal_id;
        $factura =  $request->factura;
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;

        $productos = Producto::where("status", 1);

        if ($producto_id != 'todos') {
            $productos->where("id", $producto_id);
        }
        $productos = $productos->get();

        $data = [];
        $categories = [];
        foreach ($productos as $producto) {
            $categories[] = $producto->nombre;
            $total = DetalleOrden::join("orden_ventas", "orden_ventas.id", "=", "detalle_ordens.orden_venta_id")
                ->where("orden_ventas.status", 1)
                ->where("producto_id", $producto->id);
            if ($fecha_ini && $fecha_fin) {
                $total->whereBetween("orden_ventas.fecha_registro", [$fecha_ini, $fecha_fin]);
            }

            if ($sucursal_id != 'todos') {
                $total->where("orden_ventas.sucursal_id", $sucursal_id);
            }
            if ($factura != 'todos') {
                $total->where("orden_ventas.factura", $factura);
            }

            $total = $total->where("detalle_ordens.status", 1)->orderBy("id", "asc")->sum("detalle_ordens.subtotal");

            $data[] = [
                "y" => (int)$total,
            ];
        }

        return response()->JSON([
            "categories" => $categories,
            "data" => $data
        ]);
    }
}
