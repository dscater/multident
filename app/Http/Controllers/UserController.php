<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\OrdenVenta;
use App\Models\Producto;
use App\Models\SolicitudProducto;
use App\Models\User;
use App\Services\SedeUserService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    public function __construct(private SedeUserService $sedeUserService) {}

    public function permisosUsuario(Request $request)
    {
        return response()->JSON([
            "permisos" => Auth::user()->getPermisos()
        ]);
    }

    public function getUser()
    {
        return response()->JSON([
            "user" => Auth::user()
        ]);
    }

    public static function getInfoBoxUser(?SedeUserService $sedeUserService)
    {
        $permisos = [];
        $array_infos = [];
        if (Auth::check()) {
            $oUser = new User();
            $permisos = $oUser->getPermisos();
            if ($permisos == '*' || (is_array($permisos) && in_array('usuarios.index', $permisos))) {
                $array_infos[] = [
                    'label' => 'USUARIOS',
                    'cantidad' => User::where('id', '!=', 1)->count(),
                    'color' => 'bg-principal',
                    'icon' => "fa-users",
                    "url" => "usuarios.index"
                ];
            }

            if ($permisos == '*' || (is_array($permisos) && in_array('orden_ventas.index', $permisos))) {
                $orden_ventas = OrdenVenta::select("orden_ventas.id");
                $orden_ventas->where("status", 1);
                $orden_ventas = $orden_ventas->count();

                $array_infos[] = [
                    'label' => 'ORDENES DE VENTA',
                    'cantidad' => $orden_ventas,
                    'color' => 'bg-principal',
                    'icon' => "fa-list",
                    "url" => "orden_ventas.index"
                ];
            }

            if ($permisos == '*' || (is_array($permisos) && in_array('productos.index', $permisos))) {
                $productos = Producto::select("productos.id");
                $productos->where("status", 1);
                $productos = $productos->count();

                $array_infos[] = [
                    'label' => 'PRODUCTOS',
                    'cantidad' => $productos,
                    'color' => 'bg-principal',
                    'icon' => "fa-list",
                    "url" => "productos.index"
                ];
            }

            if ($permisos == '*' || (is_array($permisos) && in_array('clientes.index', $permisos))) {
                $clientes = Cliente::select("clientes.id");
                $clientes->where("status", 1);
                $clientes = $clientes->count();

                $array_infos[] = [
                    'label' => 'CLIENTES',
                    'cantidad' => $clientes,
                    'color' => 'bg-principal',
                    'icon' => "fa-list",
                    "url" => "clientes.index"
                ];
            }
        }


        return $array_infos;
    }
}
