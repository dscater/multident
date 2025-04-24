<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\IngresoProductoController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\OrdenVentaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProductoRelacionController;
use App\Http\Controllers\ProductoSucursalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProformaController;
use App\Http\Controllers\PromocionController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalidaProductoController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UbicacionProductoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsuarioController;
use App\Models\ProductoSucursal;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthenticatedSessionController::class, 'create']);

Route::get('/clear-cache', function () {
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    Artisan::call('optimize');
    return 'Cache eliminado <a href="/">Ir al inicio</a>';
})->name('clear.cache');


Route::get("configuracions/getConfiguracion", [ConfiguracionController::class, 'getConfiguracion'])->name("configuracions.getConfiguracion");

// SEDES
Route::get("sedes/listado", [SedeController::class, 'listado'])->name("sedes.listado");

// ADMINISTRACION
Route::middleware(['auth', 'permisoUsuario'])->prefix("admin")->group(function () {
    // INICIO
    Route::get('/inicio', [InicioController::class, 'inicio'])->name('inicio');

    // CONFIGURACION
    Route::put("configuracions/update", [ConfiguracionController::class, 'update'])->name("configuracions.update");
    Route::resource("configuracions", ConfiguracionController::class)->only(
        ["index", "show"]
    );

    // USUARIO
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('profile/update_foto', [ProfileController::class, 'update_foto'])->name('profile.update_foto');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get("getUser", [UserController::class, 'getUser'])->name('users.getUser');
    Route::get("permisosUsuario", [UserController::class, 'permisosUsuario']);

    // USUARIOS
    Route::get("usuarios/clientes", [UsuarioController::class, 'clientes'])->name("usuarios.clientes");
    Route::put("usuarios/password/{user}", [UsuarioController::class, 'actualizaPassword'])->name("usuarios.password");
    Route::get("usuarios/api", [UsuarioController::class, 'api'])->name("usuarios.api");
    Route::get("usuarios/paginado", [UsuarioController::class, 'paginado'])->name("usuarios.paginado");
    Route::get("usuarios/listado", [UsuarioController::class, 'listado'])->name("usuarios.listado");
    Route::get("usuarios/listado/byTipo", [UsuarioController::class, 'byTipo'])->name("usuarios.byTipo");
    Route::get("usuarios/show/{user}", [UsuarioController::class, 'show'])->name("usuarios.show");
    Route::put("usuarios/update/{user}", [UsuarioController::class, 'update'])->name("usuarios.update");
    Route::delete("usuarios/{user}", [UsuarioController::class, 'destroy'])->name("usuarios.destroy");
    Route::resource("usuarios", UsuarioController::class)->only(
        ["index", "store"]
    );

    // CLIENTES
    Route::get("clientes/api", [ClienteController::class, 'api'])->name("clientes.api");
    Route::get("clientes/paginado", [ClienteController::class, 'paginado'])->name("clientes.paginado");
    Route::get("clientes/listado", [ClienteController::class, 'listado'])->name("clientes.listado");
    Route::resource("clientes", ClienteController::class)->only(
        ["index", "store", "edit", "show", "update", "destroy"]
    );

    // ROLES
    Route::get("roles/api", [RoleController::class, 'api'])->name("roles.api");
    Route::get("roles/paginado", [RoleController::class, 'paginado'])->name("roles.paginado");
    Route::get("roles/listado", [RoleController::class, 'listado'])->name("roles.listado");
    Route::post("roles/actualizaPermiso/{role}", [RoleController::class, 'actualizaPermiso'])->name("roles.actualizaPermiso");
    Route::resource("roles", RoleController::class)->only(
        ["index", "store", "edit", "show", "update", "destroy"]
    );

    // SUCURSALES
    Route::get("sucursals/api", [SucursalController::class, 'api'])->name("sucursals.api");
    Route::get("sucursals/paginado", [SucursalController::class, 'paginado'])->name("sucursals.paginado");
    Route::get("sucursals/listado", [SucursalController::class, 'listado'])->name("sucursals.listado");
    Route::resource("sucursals", SucursalController::class)->only(
        ["index", "store", "edit", "show", "update", "destroy"]
    );

    // PRODUCTOS
    Route::get("productos/api", [ProductoController::class, 'api'])->name("productos.api");
    Route::get("productos/paginado", [ProductoController::class, 'paginado'])->name("productos.paginado");
    Route::get("productos/listadoSinProducto", [ProductoController::class, 'listadoSinProducto'])->name("productos.listadoSinProducto");
    Route::get("productos/listado", [ProductoController::class, 'listado'])->name("productos.listado");
    Route::resource("productos", ProductoController::class)->only(
        ["index", "store", "show", "update", "destroy"]
    );

    // PRODUCTO SUCURSAL
    Route::get("producto_sucursals/getProductoSucursal", [ProductoSucursalController::class, 'getProductoSucursal'])->name("producto_sucursals.getProductoSucursal");
    Route::get("producto_sucursals/getProductoSucursales", [ProductoSucursalController::class, 'getProductoSucursales'])->name("producto_sucursals.getProductoSucursales");
    
    // RELACION PRODUCTOS
    Route::get("producto_relacions/listadoPorProducto/{producto}", [ProductoRelacionController::class, 'listadoPorProducto'])->name("producto_relacions.listadoPorProducto");
    Route::post("producto_relacions/store/{producto}", [ProductoRelacionController::class, 'store'])->name("productos.relacion");
    Route::delete("producto_relacions/destroy/{producto_relacion}", [ProductoRelacionController::class, 'destroy'])->name("productos_relacion.destroy");

    // UBICACION PRODUCTOS
    Route::get("ubicacion_productos/api", [UbicacionProductoController::class, 'api'])->name("ubicacion_productos.api");
    Route::get("ubicacion_productos/paginado", [UbicacionProductoController::class, 'paginado'])->name("ubicacion_productos.paginado");
    Route::get("ubicacion_productos/listado", [UbicacionProductoController::class, 'listado'])->name("ubicacion_productos.listado");
    Route::resource("ubicacion_productos", UbicacionProductoController::class)->only(
        ["index", "store", "show", "update", "destroy"]
    );

    // INGRESO DE PRODUCTOS
    Route::get("ingreso_productos/api", [IngresoProductoController::class, 'api'])->name("ingreso_productos.api");
    Route::get("ingreso_productos/paginado", [IngresoProductoController::class, 'paginado'])->name("ingreso_productos.paginado");
    Route::get("ingreso_productos/listado", [IngresoProductoController::class, 'listado'])->name("ingreso_productos.listado");
    Route::resource("ingreso_productos", IngresoProductoController::class)->only(
        ["index", "create", "edit", "store", "show", "update", "destroy"]
    );

    // SALIDA DE PRODUCTOS
    Route::get("salida_productos/api", [SalidaProductoController::class, 'api'])->name("salida_productos.api");
    Route::get("salida_productos/paginado", [SalidaProductoController::class, 'paginado'])->name("salida_productos.paginado");
    Route::get("salida_productos/listado", [SalidaProductoController::class, 'listado'])->name("salida_productos.listado");
    Route::resource("salida_productos", SalidaProductoController::class)->only(
        ["index", "create", "store", "edit", "show", "update", "destroy"]
    );

    // ORDENES DE VENTA
    Route::get("orden_ventas/api", [OrdenVentaController::class, 'api'])->name("orden_ventas.api");
    Route::get("orden_ventas/generarPdf/{orden_venta}", [OrdenVentaController::class, 'generarPdf'])->name("orden_ventas.generarPdf");
    Route::get("orden_ventas/paginado", [OrdenVentaController::class, 'paginado'])->name("orden_ventas.paginado");
    Route::get("orden_ventas/listado", [OrdenVentaController::class, 'listado'])->name("orden_ventas.listado");
    Route::resource("orden_ventas", OrdenVentaController::class)->only(
        ["index", "create", "edit", "store", "show", "update", "destroy"]
    );

    // DEVOLUCIONES
    Route::get("devolucions/api", [DevolucionController::class, 'api'])->name("devolucions.api");
    Route::get("devolucions/paginado", [DevolucionController::class, 'paginado'])->name("devolucions.paginado");
    Route::get("devolucions/listado", [DevolucionController::class, 'listado'])->name("devolucions.listado");
    Route::resource("devolucions", DevolucionController::class)->only(
        ["index", "store", "show", "update", "destroy"]
    );

    // PROFORMAS
    Route::get("proformas/api", [ProformaController::class, 'api'])->name("proformas.api");
    Route::get("proformas/paginado", [ProformaController::class, 'paginado'])->name("proformas.paginado");
    Route::get("proformas/listado", [ProformaController::class, 'listado'])->name("proformas.listado");
    Route::resource("proformas", ProformaController::class)->only(
        ["index", "store", "show", "update", "destroy"]
    );

    // PROMOCIONES
    Route::get("promocions/api", [PromocionController::class, 'api'])->name("promocions.api");
    Route::get("promocions/paginado", [PromocionController::class, 'paginado'])->name("promocions.paginado");
    Route::get("promocions/listado", [PromocionController::class, 'listado'])->name("promocions.listado");
    Route::resource("promocions", PromocionController::class)->only(
        ["index", "store", "show", "update", "destroy"]
    );

    // PRODUCTO SUCURSAL
    Route::get("producto_sucursals/api", [ProductoSucursal::class, 'api'])->name("producto_sucursals.api");
    Route::get("producto_sucursals/paginado", [ProductoSucursal::class, 'paginado'])->name("producto_sucursals.paginado");
    Route::get("producto_sucursals/listado", [ProductoSucursal::class, 'listado'])->name("producto_sucursals.listado");
    Route::resource("producto_sucursals", ProductoSucursal::class)->only(
        ["index"]
    );

    // NOTIFICACIONS
    Route::get("notificacions/listadoPorUsuario", [NotificacionController::class, "listadoPorUsuario"])->name("notificacions.listadoPorUsuario");

    // REPORTES
    Route::get('reportes/usuarios', [ReporteController::class, 'usuarios'])->name("reportes.usuarios");
    Route::get('reportes/r_usuarios', [ReporteController::class, 'r_usuarios'])->name("reportes.r_usuarios");

    Route::get('reportes/productos', [ReporteController::class, 'productos'])->name("reportes.productos");
    Route::get('reportes/r_productos', [ReporteController::class, 'r_productos'])->name("reportes.r_productos");
});
require __DIR__ . '/auth.php';
