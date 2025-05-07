<?php

namespace App\Services;

use App\Models\IngresoDetalle;
use App\Models\Notificacion;
use App\Models\OrdenVenta;
use App\Models\ProductoSucursal;
use App\Models\Role;
use App\Models\SolicitudProducto;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotificacionService
{

    public function __construct(private SedeUserService $sedeUserService) {}

    public function verificaNotificacionProducto(int $producto_id, int $sucursal_id)
    {
        $fecha_actual = date("Y-m-d");
        $hora = date("H:i:s");
        $producto_sucursal = ProductoSucursal::where("producto_id", $producto_id)
            ->where("sucursal_id", $sucursal_id)
            ->get()->first();

        if ($producto_sucursal) {
            $producto = $producto_sucursal->producto;
            $mitad = (float)$producto->stock_maximo * 0.5;
            $cuarta = (float)$producto->stock_maximo * 0.25;
            $diez = (float)$producto->stock_maximo * 0.1;

            $existe = Notificacion::where("fecha", $fecha_actual)
                // ->where("tipo", "STOCK INTERMEDIO")
                ->where("sucursal_id", $producto_sucursal->sucursal_id)
                ->where("registro_id", $producto_sucursal->id)
                ->where("modulo", "ProductoSucursal")
                ->get()->first();

            if (!$existe && $producto_sucursal->stock_actual <= $mitad) {

                $descripcion = "STOCK DEL PRODUCTO " . $producto_sucursal->producto->nombre;
                $tipo = "STOCK BAJO";
                if ($producto_sucursal->stock_actual <= $diez) {
                    $descripcion .= " ESTA POR DEBAJO DEL 10% DEL STOCK MAXIMO";
                    $tipo = "STOCK BAJO";
                } elseif ($producto_sucursal->stock_actual <= $cuarta) {
                    $descripcion .= " ESTA POR DEBAJO DEL 25% DEL STOCK MAXIMO";
                    $tipo = "STOCK CUARTA PARTE";
                } elseif ($producto_sucursal->stock_actual <= $mitad) {
                    $descripcion .= " ESTA POR DEBAJO DEL 50% DEL STOCK MAXIMO";
                    $tipo = "STOCK INTERMEDIO";
                }

                //Notificacion
                $notificacion = Notificacion::create([
                    "descripcion" => $descripcion,
                    "fecha" => $fecha_actual,
                    "hora" => $hora,
                    "tipo" => $tipo,
                    "sucursal_id" => $producto_sucursal->sucursal_id,
                    "modulo" => "ProductoSucursal",
                    "registro_id" => $producto_sucursal->id,
                ]);

                // usuarios sucursal todo
                $users = $this->getUsuariosNotificacionesSucursalTodo("notificacions.index");
                foreach ($users as $user) {
                    $user->notificacion_users()->create([
                        "notificacion_id" => $notificacion->id,
                    ]);
                }
                // usuarios pertenecen sucursal
                $users = $this->getUsuariosNotificacionesSucursales("notificacions.index", (int)$producto_sucursal->sucursal_id);
                foreach ($users as $user) {
                    $user->notificacion_users()->create([
                        "notificacion_id" => $notificacion->id,
                    ]);
                }
            }
        }
    }

    public function notificacion1()
    {
        $fecha_actual = date("Y-m-d");
        $hora = date("H:i:s");

        $producto_sucursals = ProductoSucursal::with(["producto", "sucursal"])
            ->select("producto_sucursals.*")
            ->join("productos", "productos.id", "=", "producto_sucursals.producto_id")
            ->whereRaw("producto_sucursals.stock_actual <= productos.stock_maximo * 0.5");

        if (Auth::user()->sucursals_todo == 0) {
            $producto_sucursals->where("sucursal_id", Auth::user()->sucursal_id);
        }
        $producto_sucursals = $producto_sucursals->get();

        foreach ($producto_sucursals as $producto_sucursal) {
            $existe = Notificacion::where("fecha", $fecha_actual)
                // ->where("tipo", "STOCK INTERMEDIO")
                ->where("sucursal_id", $producto_sucursal->sucursal_id)
                ->where("registro_id", $producto_sucursal->id)
                ->where("modulo", "ProductoSucursal")
                ->where("fecha", $fecha_actual)
                ->get()->first();

            if (!$existe) {
                //Notificacion
                $notificacion = Notificacion::create([
                    "descripcion" => "STOCK DEL PRODUCTO " . $producto_sucursal->producto->nombre . " ESTA POR DEBAJO DEL 50% DEL STOCK MAXIMO",
                    "fecha" => $fecha_actual,
                    "hora" => $hora,
                    "tipo" => "STOCK INTERMEDIO",
                    "sucursal_id" => $producto_sucursal->sucursal_id,
                    "modulo" => "ProductoSucursal",
                    "registro_id" => $producto_sucursal->id,
                ]);

                // usuarios sucursal todo
                $users = $this->getUsuariosNotificacionesSucursalTodo("notificacions.index");
                foreach ($users as $user) {
                    $user->notificacion_users()->create([
                        "notificacion_id" => $notificacion->id,
                    ]);
                }
                // usuarios pertenecen sucursal
                $users = $this->getUsuariosNotificacionesSucursales("notificacions.index", (int)$producto_sucursal->sucursal_id);
                foreach ($users as $user) {
                    $user->notificacion_users()->create([
                        "notificacion_id" => $notificacion->id,
                    ]);
                }
            }
        }
    }

    public function notificacion2()
    {
        $fecha_actual = date("Y-m-d");
        $hora = date("H:i:s");

        $producto_sucursals = ProductoSucursal::with(["producto", "sucursal"])
            ->select("producto_sucursals.*")
            ->join("productos", "productos.id", "=", "producto_sucursals.producto_id")
            ->whereRaw("producto_sucursals.stock_actual <= productos.stock_maximo * 0.25");

        if (Auth::user()->sucursals_todo == 0) {
            $producto_sucursals->where("sucursal_id", Auth::user()->sucursal_id);
        }
        $producto_sucursals = $producto_sucursals->get();

        foreach ($producto_sucursals as $producto_sucursal) {
            $existe = Notificacion::where("fecha", $fecha_actual)
                // ->where("tipo", "STOCK CUARTA PARTE")
                ->where("sucursal_id", $producto_sucursal->sucursal_id)
                ->where("registro_id", $producto_sucursal->id)
                ->where("modulo", "ProductoSucursal")
                ->where("fecha", $fecha_actual)
                ->get()->first();

            if (!$existe) {
                //Notificacion
                $notificacion = Notificacion::create([
                    "descripcion" => "STOCK DEL PRODUCTO " . $producto_sucursal->producto->nombre . " ESTA POR DEBAJO DEL 25% DEL STOCK MAXIMO",
                    "fecha" => $fecha_actual,
                    "hora" => $hora,
                    "tipo" => "STOCK CUARTA PARTE",
                    "sucursal_id" => $producto_sucursal->sucursal_id,
                    "modulo" => "ProductoSucursal",
                    "registro_id" => $producto_sucursal->id,
                ]);

                // usuarios sucursal todo
                $users = $this->getUsuariosNotificacionesSucursalTodo("productos.index");
                foreach ($users as $user) {
                    $user->notificacion_users()->create([
                        "notificacion_id" => $notificacion->id,
                    ]);
                }
                // usuarios pertenecen sucursal
                $users = $this->getUsuariosNotificacionesSucursales("productos.index", (int)$producto_sucursal->sucursal_id);
                foreach ($users as $user) {
                    $user->notificacion_users()->create([
                        "notificacion_id" => $notificacion->id,
                    ]);
                }
            }
        }
    }
    public function notificacion3()
    {
        $fecha_actual = date("Y-m-d");
        $hora = date("H:i:s");

        $producto_sucursals = ProductoSucursal::with(["producto", "sucursal"])
            ->select("producto_sucursals.*")
            ->join("productos", "productos.id", "=", "producto_sucursals.producto_id")
            ->whereRaw("producto_sucursals.stock_actual <= productos.stock_maximo * 0.1");

        if (Auth::user()->sucursals_todo == 0) {
            $producto_sucursals->where("sucursal_id", Auth::user()->sucursal_id);
        }
        $producto_sucursals = $producto_sucursals->get();

        foreach ($producto_sucursals as $producto_sucursal) {
            $existe = Notificacion::where("fecha", $fecha_actual)
                // ->where("tipo", "STOCK BAJO")
                ->where("sucursal_id", $producto_sucursal->sucursal_id)
                ->where("registro_id", $producto_sucursal->id)
                ->where("modulo", "ProductoSucursal")
                ->where("fecha", $fecha_actual)
                ->get()->first();

            if (!$existe) {
                //Notificacion
                $notificacion = Notificacion::create([
                    "descripcion" => "STOCK DEL PRODUCTO " . $producto_sucursal->producto->nombre . " ESTA POR DEBAJO DEL 10% DEL STOCK MAXIMO",
                    "fecha" => $fecha_actual,
                    "hora" => $hora,
                    "tipo" => "STOCK BAJO",
                    "sucursal_id" => $producto_sucursal->sucursal_id,
                    "modulo" => "ProductoSucursal",
                    "registro_id" => $producto_sucursal->id,
                ]);

                // usuarios sucursal todo
                $users = $this->getUsuariosNotificacionesSucursalTodo("productos.index");
                foreach ($users as $user) {
                    $user->notificacion_users()->create([
                        "notificacion_id" => $notificacion->id,
                    ]);
                }
                // usuarios pertenecen sucursal
                $users = $this->getUsuariosNotificacionesSucursales("productos.index", (int)$producto_sucursal->sucursal_id);
                foreach ($users as $user) {
                    $user->notificacion_users()->create([
                        "notificacion_id" => $notificacion->id,
                    ]);
                }
            }
        }
    }

    /**
     * Notificar Productos por vencer
     *
     * @return void
     */
    public function notificacion4()
    {
        $fecha_actual = date("Y-m-d");
        $hora = date("H:i:s");
        $hoy = Carbon::now();
        $fechaLimite = Carbon::now()->addYear(); // hoy + 12 meses

        $ingreso_detalles = IngresoDetalle::select("ingreso_detalles.*")
            ->join("ingreso_productos", "ingreso_productos.id", "=", "ingreso_detalles.ingreso_producto_id")
            ->whereBetween('fecha_vencimiento', [$hoy, $fechaLimite])
            ->get();

        foreach ($ingreso_detalles as $detalle) {
            if ($detalle->fecha_vencimiento) {
                $fechaVencimiento = Carbon::parse($detalle->fecha_vencimiento);
                $mesesRestantes = (int)Carbon::now()->startOfDay()->diffInMonths($fechaVencimiento->startOfDay(), false);

                if ($mesesRestantes <= 12) {
                    // Aquí notificarías
                    $existe = Notificacion::where("fecha", $fecha_actual)
                        // ->where("tipo", "12 MESES")
                        ->where("sucursal_id", $detalle->ingreso_producto->sucursal_id)
                        ->where("registro_id", $detalle->id)
                        ->where("modulo", "IngresoDetalle")
                        ->where("fecha", $fecha_actual)
                        ->get()->first();

                    if (!$existe) {
                        $crear = false;
                        Log::debug($mesesRestantes);
                        if ((int)$mesesRestantes === 3) {
                            $crear = true;
                            $tipo = "3 MESES";
                        } elseif ((int)$mesesRestantes === 6) {
                            $crear = true;
                            $tipo = "6 MESES";
                        } elseif ((int)$mesesRestantes === 9) {
                            $crear = true;
                            $tipo = "9 MESES";
                        } elseif ((int)$mesesRestantes === 12) {
                            $crear = true;
                            $tipo = "12 MESES";
                        }
                        if ($crear) {
                            // Log::debug("CREARA " . $mesesRestantes);
                            //Notificacion
                            $notificacion = Notificacion::create([
                                "descripcion" => "EL PRODUCTO " . $detalle->producto->nombre . " ESTA A " . $mesesRestantes . " MESES DE SU FECHA DE VENCIMIENTO",
                                "fecha" => $fecha_actual,
                                "hora" => $hora,
                                "tipo" => $tipo,
                                "sucursal_id" =>  $detalle->ingreso_producto->sucursal_id,
                                "modulo" => "IngresoDetalle",
                                "registro_id" => $detalle->id,
                            ]);

                            // usuarios sucursal todo
                            $users = $this->getUsuariosNotificacionesSucursalTodo("productos.index");
                            foreach ($users as $user) {
                                $user->notificacion_users()->create([
                                    "notificacion_id" => $notificacion->id,
                                ]);
                            }
                            // usuarios pertenecen sucursal
                            $users = $this->getUsuariosNotificacionesSucursales("productos.index", (int)$detalle->ingreso_producto->sucursal_id);
                            foreach ($users as $user) {
                                $user->notificacion_users()->create([
                                    "notificacion_id" => $notificacion->id,
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
    private function generarUsuariosNotificacion(string $modulo): void
    {
        $roles = Role::join("permisos", "permisos.role_id", "=", "roles.id")
            ->join("modulos", "modulos.id", "=", "permisos.modulo_id")
            ->where("modulos.nombre", $modulo)
            ->distinct()
            ->pluck("roles.id")
            ->toArray();
        $roles[] = 1;

        $this->usersNotificados = User::whereIn("role_id", $roles)->where("status", 1)->get();
    }

    private function getUsuariosNotificacionesSucursalTodo(string $modulo): Collection
    {
        $roles = Role::join("permisos", "permisos.role_id", "=", "roles.id")
            ->join("modulos", "modulos.id", "=", "permisos.modulo_id")
            ->where("modulos.nombre", $modulo)
            ->distinct()
            ->pluck("roles.id")
            ->toArray();
        $roles[] = 1;

        return User::whereIn("role_id", $roles)
            ->where("sucursals_todo", 1)
            ->where("status", 1)
            ->get();
    }

    private function getUsuariosNotificacionesSucursales(string $modulo, int $sucursal_id): Collection
    {
        $roles = Role::join("permisos", "permisos.role_id", "=", "roles.id")
            ->join("modulos", "modulos.id", "=", "permisos.modulo_id")
            ->where("modulos.nombre", $modulo)
            ->distinct()
            ->pluck("roles.id")
            ->toArray();
        $roles[] = 1;

        return User::whereIn("role_id", $roles)->where("status", 1)->where("sucursal_id", $sucursal_id)->get();
    }
}
