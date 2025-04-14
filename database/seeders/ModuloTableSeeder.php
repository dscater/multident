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

        // CONFIGURACION DE PAGOS
        Modulo::create([
            "modulo" => "Configuración de pagos",
            "nombre" => "configuracion_pagos.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE CONFIGURACIÓN DE PAGOS"
        ]);

        Modulo::create([
            "modulo" => "Configuración de pagos",
            "nombre" => "configuracion_pagos.create",
            "accion" => "CREAR",
            "descripcion" => "CREAR CONFIGURACIÓN DE PAGOS"
        ]);

        Modulo::create([
            "modulo" => "Configuración de pagos",
            "nombre" => "configuracion_pagos.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR CONFIGURACIÓN DE PAGOS"
        ]);

        Modulo::create([
            "modulo" => "Configuración de pagos",
            "nombre" => "configuracion_pagos.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR CONFIGURACIÓN DE PAGOS"
        ]);

        // CATEGORIAS
        Modulo::create([
            "modulo" => "Categorías",
            "nombre" => "categorias.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE CATEGORIAS"
        ]);

        Modulo::create([
            "modulo" => "Categorías",
            "nombre" => "categorias.create",
            "accion" => "CREAR",
            "descripcion" => "CREAR CATEGORIAS"
        ]);

        Modulo::create([
            "modulo" => "Categorías",
            "nombre" => "categorias.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR CATEGORIAS"
        ]);

        Modulo::create([
            "modulo" => "Categorías",
            "nombre" => "categorias.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR CATEGORIAS"
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
            "nombre" => "productos.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR PRODUCTOS"
        ]);

        // ORDEN DE VENTA
        Modulo::create([
            "modulo" => "Orden de venta",
            "nombre" => "orden_ventas.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE ORDENES DE VENTA"
        ]);

        Modulo::create([
            "modulo" => "Orden de venta",
            "nombre" => "orden_ventas.todos",
            "accion" => "TODAS LAS ORDENES DE VENTA",
            "descripcion" => "VER TODAS LAS ORDENES DE VENTA"
        ]);

        Modulo::create([
            "modulo" => "Orden de venta",
            "nombre" => "orden_ventas.confirmar",
            "accion" => "CONFIRMAR",
            "descripcion" => "CONFIRMAR ORDENES DE VENTA"
        ]);

        Modulo::create([
            "modulo" => "Orden de venta",
            "nombre" => "orden_ventas.create",
            "accion" => "CREAR",
            "descripcion" => "CREAR ORDEN DE VENTA"
        ]);

        Modulo::create([
            "modulo" => "Orden de venta",
            "nombre" => "orden_ventas.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR ORDEN DE VENTA"
        ]);

        Modulo::create([
            "modulo" => "Orden de venta",
            "nombre" => "orden_ventas.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR ORDEN DE VENTA"
        ]);

        // SOLICITUD PRODUCTOS
        Modulo::create([
            "modulo" => "Solicitud de productos",
            "nombre" => "solicitud_productos.index",
            "accion" => "VER",
            "descripcion" => "VER LA LISTA DE SOLICITUDES DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Solicitud de productos",
            "nombre" => "solicitud_productos.todos",
            "accion" => "TODAS LAS SOLICITUDES DE PRODUCTOS",
            "descripcion" => "VER TODAS LAS SOLICITUDES DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Solicitud de productos",
            "nombre" => "solicitud_productos.confirmar",
            "accion" => "VERIFICAR",
            "descripcion" => "VERIFICAR SOLICITUDES DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Solicitud de productos",
            "nombre" => "solicitud_productos.seguimiento",
            "accion" => "SEGUIMIENTO",
            "descripcion" => "SEGUIMIENTO DE SOLICITUDES DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Solicitud de productos",
            "nombre" => "solicitud_productos.create",
            "accion" => "CREAR",
            "descripcion" => "CREAR SOLICITUD DE PRODUCTO"
        ]);

        Modulo::create([
            "modulo" => "Solicitud de productos",
            "nombre" => "solicitud_productos.edit",
            "accion" => "EDITAR",
            "descripcion" => "EDITAR SOLICITUD DE PRODUCTO"
        ]);

        Modulo::create([
            "modulo" => "Solicitud de productos",
            "nombre" => "solicitud_productos.destroy",
            "accion" => "ELIMINAR",
            "descripcion" => "ELIMINAR SOLICITUD DE PRODUCTO"
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
            "nombre" => "reportes.productos",
            "accion" => "REPORTE PRODUCTOS",
            "descripcion" => "GENERAR REPORTES DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.orden_ventas",
            "accion" => "REPORTE ORDENES DE VENTAS",
            "descripcion" => "GENERAR REPORTES DE ORDENES DE VENTAS"
        ]);

        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.solicitud_productos",
            "accion" => "REPORTE DE SOLICITUD DE PRODUCTOS",
            "descripcion" => "GENERAR REPORTES DE SOLICITUD DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.seguimiento_solicituds",
            "accion" => "REPORTE DE SEGUIMIENTO DE SOLICITUD DE PRODUCTOS",
            "descripcion" => "GENERAR REPORTES DE SEGUIMIENTO DE SOLICITUD DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.g_orden_ventas",
            "accion" => "REPORTE GRÁFICO DE ORDENES DE VENTAS",
            "descripcion" => "GENERAR REPORTE GRÁFICO DE ORDENES DE VENTAS"
        ]);

        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.g_solicitud_productos",
            "accion" => "REPORTE GRÁFICO DE SOLICITUD DE PRODUCTOS",
            "descripcion" => "GENERAR REPORTE GRÁFICO DE SOLICITUD DE PRODUCTOS"
        ]);

        Modulo::create([
            "modulo" => "Reportes",
            "nombre" => "reportes.g_seguimiento_productos",
            "accion" => "REPORTE GRÁFICO DE SEGUIMIENTO DE SOLICITUD DE PRODUCTOS",
            "descripcion" => "GENERAR REPORTE GRÁFICO DE SEGUIMIENTO DE SOLICITUD DE PRODUCTOS"
        ]);
    }
}
