<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\ProductoImagen;
use Illuminate\Http\UploadedFile;

class ProductoImagenService
{
    private $pathImages = "";
    public function __construct(private  CargarArchivoService $cargarArchivoService)
    {
        $this->pathImages = public_path("imgs/productos");
    }

    /**
     * Cargar imagen
     *
     * @param Producto $producto
     * @param UploadedFile $foto
     * @return ProductoImagen
     */
    public function guardarImagenProducto(Producto $producto, UploadedFile $imagen, int $index = -1): ProductoImagen
    {
        $nombre = ($index != -1 ? $index : 0) . $producto->id . time();
        return $producto->imagens()->create([
            "imagen" => $this->cargarArchivoService->cargarArchivo($imagen, $this->pathImages, $nombre)
        ]);
    }

    /**
     * Eliminacion fisica de imagen producto
     *
     * @param ProductoImagen $productoImagen
     * @return void
     */
    public function eliminarImagenProducto(ProductoImagen $productoImagen): void
    {
        $imagen = $productoImagen->imagen;
        if ($productoImagen->delete()) {
            \File::delete($this->pathImages . "/" . $imagen);
        }
        $productoImagen->delete();
    }

    /**
     * Obtener extension del nombre de la imagen
     * ejem: image.png -> png
     * 
     * @param string $imagen
     * @return string
     */
    public static function getExtNomImagen(string $imagen): string
    {
        $array = explode(".", $imagen);
        return $array[count($array) - 1];
    }
}
