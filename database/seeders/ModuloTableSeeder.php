<?php

namespace Database\Seeders;

use App\Models\Modulo;
use Illuminate\Database\Seeder;

class ModuloTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // GESTIÓN DE USUARIOS
        Modulo::create([
            "modulo" => "Gestión de usuarios",
            "nombre" => "usuarios.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE USUARIOS"
        ]);

        Modulo::create([
            "modulo" => "Gestión de usuarios",
            "nombre" => "usuarios.create",
            "accion" => "CREAR",
            "descripcion" => "CREAR USUARIOS"
        ]);

        Modulo::create([
            "modulo" => "Gestión de usuarios",
            "nombre" => "usuarios.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR USUARIOS"
        ]);

        Modulo::create([
            "modulo" => "Gestión de usuarios",
            "nombre" => "usuarios.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR USUARIOS"
        ]);

        // ROLES Y PERMISOS
        Modulo::create([
            "modulo" => "Roles y Permisos",
            "nombre" => "roles.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE ROLES Y PERMISOS"
        ]);

        Modulo::create([
            "modulo" => "Roles y Permisos",
            "nombre" => "roles.create",
            "accion" => "CREAR",
            "descripcion" => "CREAR ROLES Y PERMISOS"
        ]);

        Modulo::create([
            "modulo" => "Roles y Permisos",
            "nombre" => "roles.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR ROLES Y PERMISOS"
        ]);

        Modulo::create([
            "modulo" => "Roles y Permisos",
            "nombre" => "roles.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR ROLES Y PERMISOS"
        ]);

        // CONFIGURACIÓN DEL SISTEMA
        Modulo::create([
            "modulo" => "Configuración",
            "nombre" => "configuracions.index",
            "accion" => "VER",
            "descripcion" => "VER INFORMACIÓN DE LA CONFIGURACIÓN DEL SISTEMA"
        ]);

        Modulo::create([
            "modulo" => "Configuración",
            "nombre" => "configuracions.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR LA CONFIGURACIÓN DEL SISTEMA"
        ]);

        // SUCURSALES
        Modulo::create([
            "modulo" => "Sucursales",
            "nombre" => "sucursals.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE SUCURSALES"
        ]);

        Modulo::create([
            "modulo" => "Sucursales",
            "nombre" => "sucursals.create",
            "accion" => "CREAR",
            "descripcion" => "CREAR SUCURSALES"
        ]);

        Modulo::create([
            "modulo" => "Sucursales",
            "nombre" => "sucursals.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR SUCURSALES"
        ]);

        Modulo::create([
            "modulo" => "Sucursales",
            "nombre" => "sucursals.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR SUCURSALES"
        ]);

        // PRODUCTOS
        Modulo::create([
            "modulo" => "Productos",
            "nombre" => "productos.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Productos",
            "nombre" => "productos.create",
            "accion" => "CREAR",
            "descripcion" => "CREAR PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Productos",
            "nombre" => "productos.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Productos",
            "nombre" => "productos.relacion",
            "accion" => "RELACIÓN PRODUCTOS",
            "descripcion" => "RELACIONAR CON OTROS PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Productos",
            "nombre" => "productos.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR PRODUCTOS"
        ]);

        // UBICACIÓN PRODUCTOS
        Modulo::create([
            "modulo" => "Ubicación de Productos",
            "nombre" => "ubicacion_productos.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE UBICACIÓN DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Ubicación de Productos",
            "nombre" => "ubicacion_productos.create",
            "accion" => "CREAR",
            "descripcion" => "CREAR UBICACIÓN DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Ubicación de Productos",
            "nombre" => "ubicacion_productos.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR UBICACIÓN DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Ubicación de Productos",
            "nombre" => "ubicacion_productos.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR UBICACIÓN DE PRODUCTOS"
        ]);

        // INGRESO DE PRODUCTOS
        Modulo::create([
            "modulo" => "Ingreso de Productos",
            "nombre" => "ingreso_productos.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE INGRESO DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Ingreso de Productos",
            "nombre" => "ingreso_productos.create",
            "accion" => "CREAR",
            "descripcion" => "CREAR INGRESO DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Ingreso de Productos",
            "nombre" => "ingreso_productos.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR INGRESO DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Ingreso de Productos",
            "nombre" => "ingreso_productos.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR INGRESO DE PRODUCTOS"
        ]);

        // SALIDA DE PRODUCTOS
        Modulo::create([
            "modulo" => "Salida de Productos",
            "nombre" => "salida_productos.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE SALIDA DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Salida de Productos",
            "nombre" => "salida_productos.create",
            "accion" => "CREAR",
            "descripcion" => "CREAR SALIDA DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Salida de Productos",
            "nombre" => "salida_productos.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR SALIDA DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Salida de Productos",
            "nombre" => "salida_productos.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR SALIDA DE PRODUCTOS"
        ]);

        // STOCK DE PRODUCTOS
        Modulo::create([
            "modulo" => "Stock de Productos",
            "nombre" => "producto_sucursals.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE STOCK DE PRODUCTOS"
        ]);

        // CLIENTES
        Modulo::create([
            "modulo" => "Clientes",
            "nombre" => "clientes.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE CLIENTES"
        ]);

        Modulo::create([
            "modulo" => "Clientes",
            "nombre" => "clientes.create",
            "accion" => "CREAR",
            "descripcion" => "CREAR CLIENTES"
        ]);

        Modulo::create([
            "modulo" => "Clientes",
            "nombre" => "clientes.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR CLIENTES"
        ]);

        Modulo::create([
            "modulo" => "Clientes",
            "nombre" => "clientes.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR CLIENTES"
        ]);

        // ORDEN DE VENTAS
        Modulo::create([
            "modulo" => "Orden de Ventas",
            "nombre" => "orden_ventas.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE ORDENES DE VENTA"
        ]);

        Modulo::create([
            "modulo" => "Orden de Ventas",
            "nombre" => "orden_ventas.create",
            "accion" => "CREAR",
            "descripcion" => "CREAR ORDENES DE VENTA"
        ]);

        Modulo::create([
            "modulo" => "Orden de Ventas",
            "nombre" => "orden_ventas.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR ORDENES DE VENTA"
        ]);

        Modulo::create([
            "modulo" => "Orden de Ventas",
            "nombre" => "orden_ventas.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR ORDENES DE VENTA"
        ]);

        // NOTIFICACIONES
        Modulo::create([
            "modulo" => "Notificaciones",
            "nombre" => "notificacions.index",
            "accion" => "VER",
            "descripcion" => "RECIBIR NOTIFICACIONES DE STOCK DE PRODUCTOS"
        ]);

        // DEVOLUCIONES
        Modulo::create([
            "modulo" => "Devoluciones",
            "nombre" => "devolucions.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE DEVOLUCIONES"
        ]);

        Modulo::create([
            "modulo" => "Devoluciones",
            "nombre" => "devolucions.create",
            "accion" => "CREAR",
            "descripcion" => "CREAR DEVOLUCIONES"
        ]);

        Modulo::create([
            "modulo" => "Devoluciones",
            "nombre" => "devolucions.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR DEVOLUCIONES"
        ]);

        Modulo::create([
            "modulo" => "Devoluciones",
            "nombre" => "devolucions.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR DEVOLUCIONES"
        ]);

        // PROFORMAS
        Modulo::create([
            "modulo" => "Proformas",
            "nombre" => "proformas.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE PROFORMAS"
        ]);

        Modulo::create([
            "modulo" => "Proformas",
            "nombre" => "proformas.create",
            "accion" => "CREAR",
            "descripcion" => "CREAR PROFORMAS"
        ]);

        Modulo::create([
            "modulo" => "Proformas",
            "nombre" => "proformas.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR PROFORMAS"
        ]);

        Modulo::create([
            "modulo" => "Proformas",
            "nombre" => "proformas.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR PROFORMAS"
        ]);

        // PROMOCIONES
        Modulo::create([
            "modulo" => "Promociones",
            "nombre" => "promocions.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE PROMOCIONES"
        ]);

        Modulo::create([
            "modulo" => "Promociones",
            "nombre" => "promocions.create",
            "accion" => "CREAR",
            "descripcion" => "CREAR PROMOCIONES"
        ]);

        Modulo::create([
            "modulo" => "Promociones",
            "nombre" => "promocions.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR PROMOCIONES"
        ]);

        Modulo::create([
            "modulo" => "Promociones",
            "nombre" => "promocions.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR PROMOCIONES"
        ]);

        // REPORTES
        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.usuarios",
            "accion" => "REPORTE LISTA DE USUARIOS",
            "descripcion" => "GENERAR REPORTES DE LISTA DE USUARIOS"
        ]);

        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.kardex_productos",
            "accion" => "REPORTE KARDEX DE PRODUCTOS",
            "descripcion" => "GENERAR REPORTES DE KARDEX DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.orden_ventas",
            "accion" => "REPORTE ORDENES DE VENTAS",
            "descripcion" => "GENERAR REPORTES DE ORDENES DE VENTAS"
        ]);

        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.stock_productos",
            "accion" => "REPORTE DE STOCK DE PRODUCTOS",
            "descripcion" => "GENERAR REPORTES DE STOCK DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.ingreso_productos",
            "accion" => "REPORTE DE INGRESO DE PRODUCTOS",
            "descripcion" => "GENERAR REPORTES DE INGRESO DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.salida_productos",
            "accion" => "REPORTE DE SALIDA DE PRODUCTOS",
            "descripcion" => "GENERAR REPORTES DE SALIDA DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.devolucions",
            "accion" => "REPORTE DE DEVOLUCIONES",
            "descripcion" => "GENERAR REPORTES DE DEVOLUCIONES"
        ]);

        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.proformas",
            "accion" => "REPORTE DE PROFORMAS",
            "descripcion" => "GENERAR REPORTES DE PROFORMAS"
        ]);

        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.clientes",
            "accion" => "REPORTE DE CLIENTES",
            "descripcion" => "GENERAR REPORTES DE CLIENTES"
        ]);

        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.g_cantidad_orden_ventas",
            "accion" => "REPORTE GRÁFICO DE CANTIDAD DE ORDENES DE VENTAS",
            "descripcion" => "GENERAR REPORTE GRÁFICO DE CANTIDAD DE ORDENES DE VENTAS"
        ]);

        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.g_ingresos_orden_ventas",
            "accion" => "REPORTE GRÁFICO DE INGRESOS POR ORDENES DE VENTAS",
            "descripcion" => "GENERAR REPORTE GRÁFICO DE INGRESOS POR ORDENES DE VENTAS"
        ]);
    }
}
