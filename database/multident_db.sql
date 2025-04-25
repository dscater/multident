-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 25-04-2025 a las 00:21:37
-- Versión del servidor: 8.0.30
-- Versión de PHP: 8.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `multident_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` bigint UNSIGNED NOT NULL,
  `nombres` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ci` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` varchar(800) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` date NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombres`, `apellidos`, `ci`, `cel`, `descripcion`, `fecha_registro`, `status`, `created_at`, `updated_at`) VALUES
(1, 'MARIA', 'GONZALES MAMANI', '3222323', '767766767', 'DESCRIPCION CLIENTE 1', '2025-04-19', 1, '2025-04-19 16:32:37', '2025-04-19 16:32:37'),
(2, 'PEDRO', 'RAMIRES', '0', '', '', '2025-04-19', 1, '2025-04-19 16:34:02', '2025-04-19 16:35:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracions`
--

CREATE TABLE `configuracions` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre_sistema` varchar(600) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razon_social` varchar(600) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(600) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracions`
--

INSERT INTO `configuracions` (`id`, `nombre_sistema`, `alias`, `razon_social`, `logo`, `created_at`, `updated_at`) VALUES
(1, 'MULTIDENT', 'MD', 'MULTIDENT S.A.', 'lg1744657735.png', '2025-04-14 01:39:41', '2025-04-14 19:09:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ordens`
--

CREATE TABLE `detalle_ordens` (
  `id` bigint UNSIGNED NOT NULL,
  `orden_venta_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `promocion_id` bigint UNSIGNED DEFAULT NULL,
  `promocion_descuento` double(8,2) DEFAULT '0.00',
  `cantidad` double NOT NULL,
  `precio_reg` decimal(24,2) NOT NULL,
  `precio` decimal(24,2) NOT NULL,
  `subtotal` decimal(24,2) NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_ordens`
--

INSERT INTO `detalle_ordens` (`id`, `orden_venta_id`, `producto_id`, `promocion_id`, `promocion_descuento`, `cantidad`, `precio_reg`, `precio`, `subtotal`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 15, 2, 10.00, 10, 180.00, 180.00, 1800.00, 1, '2025-04-24 22:56:30', '2025-04-24 22:56:30'),
(2, 2, 15, 2, 10.00, 20, 180.00, 180.00, 3600.00, 0, '2025-04-24 22:56:54', '2025-04-24 22:57:07'),
(3, 2, 18, NULL, NULL, 3, 100.50, 100.50, 301.50, 1, '2025-04-24 22:56:54', '2025-04-24 22:56:54'),
(4, 2, 15, 2, 10.00, 12, 180.00, 180.00, 2160.00, 0, '2025-04-24 23:07:01', '2025-04-24 23:07:37'),
(6, 4, 15, 2, 10.00, 20, 180.00, 180.00, 3600.00, 1, '2025-04-25 00:16:58', '2025-04-25 00:16:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_proformas`
--

CREATE TABLE `detalle_proformas` (
  `id` bigint UNSIGNED NOT NULL,
  `proforma_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `promocion_id` bigint UNSIGNED DEFAULT NULL,
  `promocion_descuento` double(8,2) DEFAULT '0.00',
  `cantidad` double NOT NULL,
  `precio_reg` decimal(24,2) NOT NULL,
  `precio` decimal(24,2) NOT NULL,
  `subtotal` decimal(24,2) NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_usos`
--

CREATE TABLE `detalle_usos` (
  `id` bigint UNSIGNED NOT NULL,
  `orden_venta_id` bigint UNSIGNED NOT NULL,
  `detalle_orden_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `ingreso_detalle_id` bigint UNSIGNED NOT NULL,
  `cantidad` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_usos`
--

INSERT INTO `detalle_usos` (`id`, `orden_venta_id`, `detalle_orden_id`, `producto_id`, `ingreso_detalle_id`, `cantidad`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 15, 1, 10, '2025-04-24 22:56:30', '2025-04-24 22:56:30'),
(2, 2, 2, 15, 1, 10, '2025-04-24 22:56:54', '2025-04-24 22:56:54'),
(3, 2, 2, 15, 4, 10, '2025-04-24 22:56:54', '2025-04-24 22:56:54'),
(8, 2, 3, 18, 3, 3, '2025-04-24 23:07:14', '2025-04-24 23:07:14'),
(9, 2, 4, 15, 1, 10, '2025-04-24 23:07:14', '2025-04-24 23:07:14'),
(10, 2, 4, 15, 4, 2, '2025-04-24 23:07:14', '2025-04-24 23:07:14'),
(13, 4, 6, 15, 1, 10, '2025-04-25 00:16:58', '2025-04-25 00:16:58'),
(14, 4, 6, 15, 4, 10, '2025-04-25 00:16:58', '2025-04-25 00:16:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devolucions`
--

CREATE TABLE `devolucions` (
  `id` bigint UNSIGNED NOT NULL,
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `orden_venta_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `detalle_orden_id` bigint UNSIGNED NOT NULL,
  `razon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_registro` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `devolucions`
--

INSERT INTO `devolucions` (`id`, `sucursal_id`, `orden_venta_id`, `producto_id`, `detalle_orden_id`, `razon`, `descripcion`, `fecha_registro`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 15, 4, 'INCORRECTO', 'DESC', '2025-04-24', '2025-04-24 23:07:37', '2025-04-24 23:07:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_accions`
--

CREATE TABLE `historial_accions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `accion` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `datos_original` json DEFAULT NULL,
  `datos_nuevo` json DEFAULT NULL,
  `modulo` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `historial_accions`
--

INSERT INTO `historial_accions` (`id`, `user_id`, `accion`, `descripcion`, `datos_original`, `datos_nuevo`, `modulo`, `fecha`, `hora`, `created_at`, `updated_at`) VALUES
(1, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN INGRESO DE PRODUCTOS', '{\"id\": 1, \"created_at\": \"2025-04-24T22:55:46.000000Z\", \"updated_at\": \"2025-04-24T22:55:46.000000Z\", \"descripcion\": \"\", \"sucursal_id\": \"1\", \"fecha_registro\": \"2025-04-24\", \"ingreso_detalles\": [{\"id\": 1, \"status\": 1, \"cantidad\": 20, \"created_at\": \"2025-04-24T22:55:46.000000Z\", \"disponible\": 20, \"updated_at\": \"2025-04-24T22:55:46.000000Z\", \"descripcion\": \"DESC\", \"producto_id\": 15, \"fecha_registro\": \"2025-04-24\", \"fecha_registro_t\": \"24/04/2025\", \"fecha_vencimiento\": \"2026-01-01\", \"fecha_vencimiento_t\": \"01/01/2026\", \"ingreso_producto_id\": 1, \"ubicacion_producto_id\": 1}, {\"id\": 2, \"status\": 1, \"cantidad\": 20, \"created_at\": \"2025-04-24T22:55:46.000000Z\", \"disponible\": 20, \"updated_at\": \"2025-04-24T22:55:46.000000Z\", \"descripcion\": \"\", \"producto_id\": 16, \"fecha_registro\": \"2025-04-24\", \"fecha_registro_t\": \"24/04/2025\", \"fecha_vencimiento\": \"2026-02-02\", \"fecha_vencimiento_t\": \"02/02/2026\", \"ingreso_producto_id\": 1, \"ubicacion_producto_id\": 1}, {\"id\": 3, \"status\": 1, \"cantidad\": 30, \"created_at\": \"2025-04-24T22:55:46.000000Z\", \"disponible\": 30, \"updated_at\": \"2025-04-24T22:55:46.000000Z\", \"descripcion\": \"\", \"producto_id\": 18, \"fecha_registro\": \"2025-04-24\", \"fecha_registro_t\": \"24/04/2025\", \"fecha_vencimiento\": null, \"fecha_vencimiento_t\": \"\", \"ingreso_producto_id\": 1, \"ubicacion_producto_id\": 2}]}', NULL, 'INGRESO DE PRODUCTOS', '2025-04-24', '18:55:46', '2025-04-24 22:55:46', '2025-04-24 22:55:46'),
(2, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN INGRESO DE PRODUCTOS', '{\"id\": 2, \"created_at\": \"2025-04-24T22:56:09.000000Z\", \"updated_at\": \"2025-04-24T22:56:09.000000Z\", \"descripcion\": \"\", \"sucursal_id\": \"1\", \"fecha_registro\": \"2025-04-24\", \"ingreso_detalles\": [{\"id\": 4, \"status\": 1, \"cantidad\": 30, \"created_at\": \"2025-04-24T22:56:09.000000Z\", \"disponible\": 30, \"updated_at\": \"2025-04-24T22:56:09.000000Z\", \"descripcion\": \"\", \"producto_id\": 15, \"fecha_registro\": \"2025-04-24\", \"fecha_registro_t\": \"24/04/2025\", \"fecha_vencimiento\": \"2026-01-01\", \"fecha_vencimiento_t\": \"01/01/2026\", \"ingreso_producto_id\": 2, \"ubicacion_producto_id\": 1}]}', NULL, 'INGRESO DE PRODUCTOS', '2025-04-24', '18:56:09', '2025-04-24 22:56:09', '2025-04-24 22:56:09'),
(3, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA ORDEN DE VENTA', '{\"id\": 1, \"nro\": 1, \"nit_ci\": \"3222323\", \"factura\": \"NO\", \"user_id\": 1, \"tipo_pago\": \"EFECTIVO\", \"cliente_id\": 1, \"created_at\": \"2025-04-24T22:56:30.000000Z\", \"updated_at\": \"2025-04-24T22:56:30.000000Z\", \"sucursal_id\": \"1\", \"detalle_ordens\": [{\"id\": 1, \"precio\": \"180.00\", \"status\": 1, \"cantidad\": 10, \"subtotal\": \"1800.00\", \"created_at\": \"2025-04-24T22:56:30.000000Z\", \"precio_reg\": \"180.00\", \"updated_at\": \"2025-04-24T22:56:30.000000Z\", \"producto_id\": 15, \"promocion_id\": 2, \"orden_venta_id\": 1, \"promocion_descuento\": 10}], \"fecha_registro\": \"2025-04-24\"}', NULL, 'ORDENES DE VENTA', '2025-04-24', '18:56:30', '2025-04-24 22:56:30', '2025-04-24 22:56:30'),
(4, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA ORDEN DE VENTA', '{\"id\": 2, \"nro\": 2, \"nit_ci\": \"0\", \"factura\": \"NO\", \"user_id\": 1, \"tipo_pago\": \"EFECTIVO\", \"cliente_id\": 2, \"created_at\": \"2025-04-24T22:56:54.000000Z\", \"updated_at\": \"2025-04-24T22:56:54.000000Z\", \"sucursal_id\": \"1\", \"detalle_ordens\": [{\"id\": 2, \"precio\": \"180.00\", \"status\": 1, \"cantidad\": 20, \"subtotal\": \"3600.00\", \"created_at\": \"2025-04-24T22:56:54.000000Z\", \"precio_reg\": \"180.00\", \"updated_at\": \"2025-04-24T22:56:54.000000Z\", \"producto_id\": 15, \"promocion_id\": 2, \"orden_venta_id\": 2, \"promocion_descuento\": 10}, {\"id\": 3, \"precio\": \"100.50\", \"status\": 1, \"cantidad\": 3, \"subtotal\": \"301.50\", \"created_at\": \"2025-04-24T22:56:54.000000Z\", \"precio_reg\": \"100.50\", \"updated_at\": \"2025-04-24T22:56:54.000000Z\", \"producto_id\": 18, \"promocion_id\": null, \"orden_venta_id\": 2, \"promocion_descuento\": null}], \"fecha_registro\": \"2025-04-24\"}', NULL, 'ORDENES DE VENTA', '2025-04-24', '18:56:54', '2025-04-24 22:56:55', '2025-04-24 22:56:55'),
(5, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UNA ORDEN DE VENTA', '{\"id\": 2, \"nro\": 2, \"nit_ci\": \"0\", \"status\": 1, \"factura\": \"NO\", \"user_id\": 1, \"tipo_pago\": \"EFECTIVO\", \"cliente_id\": 2, \"created_at\": \"2025-04-24T22:56:54.000000Z\", \"updated_at\": \"2025-04-24T22:56:54.000000Z\", \"sucursal_id\": 1, \"detalle_ordens\": [{\"id\": 2, \"precio\": \"180.00\", \"status\": 1, \"cantidad\": 20, \"subtotal\": \"3600.00\", \"created_at\": \"2025-04-24T22:56:54.000000Z\", \"precio_reg\": \"180.00\", \"updated_at\": \"2025-04-24T22:56:54.000000Z\", \"producto_id\": 15, \"promocion_id\": 2, \"orden_venta_id\": 2, \"promocion_descuento\": 10}, {\"id\": 3, \"precio\": \"100.50\", \"status\": 1, \"cantidad\": 3, \"subtotal\": \"301.50\", \"created_at\": \"2025-04-24T22:56:54.000000Z\", \"precio_reg\": \"100.50\", \"updated_at\": \"2025-04-24T22:56:54.000000Z\", \"producto_id\": 18, \"promocion_id\": null, \"orden_venta_id\": 2, \"promocion_descuento\": null}], \"fecha_registro\": \"2025-04-24\"}', '{\"id\": 2, \"nro\": 2, \"nit_ci\": \"0\", \"status\": 1, \"factura\": \"NO\", \"user_id\": 1, \"tipo_pago\": \"EFECTIVO\", \"cliente_id\": 2, \"created_at\": \"2025-04-24T22:56:54.000000Z\", \"updated_at\": \"2025-04-24T22:56:54.000000Z\", \"sucursal_id\": 1, \"detalle_ordens\": [{\"id\": 3, \"precio\": \"100.50\", \"status\": 1, \"cantidad\": 3, \"subtotal\": \"301.50\", \"created_at\": \"2025-04-24T22:56:54.000000Z\", \"precio_reg\": \"100.50\", \"updated_at\": \"2025-04-24T22:56:54.000000Z\", \"producto_id\": 18, \"promocion_id\": null, \"orden_venta_id\": 2, \"promocion_descuento\": null}], \"fecha_registro\": \"2025-04-24\"}', 'ORDENES DE VENTA', '2025-04-24', '18:57:07', '2025-04-24 22:57:07', '2025-04-24 22:57:07'),
(6, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UNA ORDEN DE VENTA', '{\"id\": 2, \"nro\": 2, \"nit_ci\": \"0\", \"status\": 1, \"factura\": \"NO\", \"user_id\": 1, \"tipo_pago\": \"EFECTIVO\", \"cliente_id\": 2, \"created_at\": \"2025-04-24T22:56:54.000000Z\", \"updated_at\": \"2025-04-24T22:56:54.000000Z\", \"sucursal_id\": 1, \"detalle_ordens\": [{\"id\": 3, \"precio\": \"100.50\", \"status\": 1, \"cantidad\": 3, \"subtotal\": \"301.50\", \"created_at\": \"2025-04-24T22:56:54.000000Z\", \"precio_reg\": \"100.50\", \"updated_at\": \"2025-04-24T22:56:54.000000Z\", \"producto_id\": 18, \"promocion_id\": null, \"orden_venta_id\": 2, \"promocion_descuento\": null}], \"fecha_registro\": \"2025-04-24\"}', '{\"id\": 2, \"nro\": 2, \"nit_ci\": \"0\", \"status\": 1, \"factura\": \"NO\", \"user_id\": 1, \"tipo_pago\": \"EFECTIVO\", \"cliente_id\": 2, \"created_at\": \"2025-04-24T22:56:54.000000Z\", \"updated_at\": \"2025-04-24T22:56:54.000000Z\", \"sucursal_id\": 1, \"detalle_ordens\": [{\"id\": 3, \"precio\": \"100.50\", \"status\": 1, \"cantidad\": 3, \"subtotal\": \"301.50\", \"created_at\": \"2025-04-24T22:56:54.000000Z\", \"precio_reg\": \"100.50\", \"updated_at\": \"2025-04-24T22:56:54.000000Z\", \"producto_id\": 18, \"promocion_id\": null, \"orden_venta_id\": 2, \"promocion_descuento\": null}, {\"id\": 4, \"precio\": \"180.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"180.00\", \"created_at\": \"2025-04-24T23:07:01.000000Z\", \"precio_reg\": \"180.00\", \"updated_at\": \"2025-04-24T23:07:01.000000Z\", \"producto_id\": 15, \"promocion_id\": 2, \"orden_venta_id\": 2, \"promocion_descuento\": 10}], \"fecha_registro\": \"2025-04-24\"}', 'ORDENES DE VENTA', '2025-04-24', '19:07:01', '2025-04-24 23:07:01', '2025-04-24 23:07:01'),
(7, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UNA ORDEN DE VENTA', '{\"id\": 2, \"nro\": 2, \"nit_ci\": \"0\", \"status\": 1, \"factura\": \"NO\", \"user_id\": 1, \"tipo_pago\": \"EFECTIVO\", \"cliente_id\": 2, \"created_at\": \"2025-04-24T22:56:54.000000Z\", \"updated_at\": \"2025-04-24T22:56:54.000000Z\", \"sucursal_id\": 1, \"detalle_ordens\": [{\"id\": 3, \"precio\": \"100.50\", \"status\": 1, \"cantidad\": 3, \"subtotal\": \"301.50\", \"created_at\": \"2025-04-24T22:56:54.000000Z\", \"precio_reg\": \"100.50\", \"updated_at\": \"2025-04-24T22:56:54.000000Z\", \"producto_id\": 18, \"promocion_id\": null, \"orden_venta_id\": 2, \"promocion_descuento\": null}, {\"id\": 4, \"precio\": \"180.00\", \"status\": 1, \"cantidad\": 1, \"subtotal\": \"180.00\", \"created_at\": \"2025-04-24T23:07:01.000000Z\", \"precio_reg\": \"180.00\", \"updated_at\": \"2025-04-24T23:07:01.000000Z\", \"producto_id\": 15, \"promocion_id\": 2, \"orden_venta_id\": 2, \"promocion_descuento\": 10}], \"fecha_registro\": \"2025-04-24\"}', '{\"id\": 2, \"nro\": 2, \"nit_ci\": \"0\", \"status\": 1, \"factura\": \"NO\", \"user_id\": 1, \"tipo_pago\": \"EFECTIVO\", \"cliente_id\": 2, \"created_at\": \"2025-04-24T22:56:54.000000Z\", \"updated_at\": \"2025-04-24T22:56:54.000000Z\", \"sucursal_id\": 1, \"detalle_ordens\": [{\"id\": 3, \"precio\": \"100.50\", \"status\": 1, \"cantidad\": 3, \"subtotal\": \"301.50\", \"created_at\": \"2025-04-24T22:56:54.000000Z\", \"precio_reg\": \"100.50\", \"updated_at\": \"2025-04-24T22:56:54.000000Z\", \"producto_id\": 18, \"promocion_id\": null, \"orden_venta_id\": 2, \"promocion_descuento\": null}, {\"id\": 4, \"precio\": \"180.00\", \"status\": 1, \"cantidad\": 12, \"subtotal\": \"2160.00\", \"created_at\": \"2025-04-24T23:07:01.000000Z\", \"precio_reg\": \"180.00\", \"updated_at\": \"2025-04-24T23:07:14.000000Z\", \"producto_id\": 15, \"promocion_id\": 2, \"orden_venta_id\": 2, \"promocion_descuento\": 10}], \"fecha_registro\": \"2025-04-24\"}', 'ORDENES DE VENTA', '2025-04-24', '19:07:14', '2025-04-24 23:07:14', '2025-04-24 23:07:14'),
(8, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA DEVOLUCIÓN', '{\"id\": 1, \"razon\": \"INCORRECTO\", \"created_at\": \"2025-04-24T23:07:37.000000Z\", \"updated_at\": \"2025-04-24T23:07:37.000000Z\", \"descripcion\": \"DESC\", \"producto_id\": 15, \"sucursal_id\": \"1\", \"fecha_registro\": \"2025-04-24\", \"orden_venta_id\": \"2\", \"detalle_orden_id\": 4}', NULL, 'DEVOLUCIONES', '2025-04-24', '19:07:37', '2025-04-24 23:07:37', '2025-04-24 23:07:37'),
(9, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA ORDEN DE VENTA', '{\"id\": 4, \"nro\": 3, \"nit_ci\": \"3222323\", \"factura\": \"NO\", \"user_id\": 1, \"tipo_pago\": \"EFECTIVO\", \"cliente_id\": 1, \"created_at\": \"2025-04-25T00:16:58.000000Z\", \"updated_at\": \"2025-04-25T00:16:58.000000Z\", \"sucursal_id\": \"1\", \"detalle_ordens\": [{\"id\": 6, \"precio\": \"180.00\", \"status\": 1, \"cantidad\": 20, \"subtotal\": \"3600.00\", \"created_at\": \"2025-04-25T00:16:58.000000Z\", \"precio_reg\": \"180.00\", \"updated_at\": \"2025-04-25T00:16:58.000000Z\", \"producto_id\": 15, \"promocion_id\": 2, \"orden_venta_id\": 4, \"promocion_descuento\": 10}], \"fecha_registro\": \"2025-04-24\"}', NULL, 'ORDENES DE VENTA', '2025-04-24', '20:16:58', '2025-04-25 00:16:58', '2025-04-25 00:16:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso_detalles`
--

CREATE TABLE `ingreso_detalles` (
  `id` bigint UNSIGNED NOT NULL,
  `ingreso_producto_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `cantidad` double NOT NULL,
  `disponible` double NOT NULL,
  `ubicacion_producto_id` bigint UNSIGNED NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_registro` date DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ingreso_detalles`
--

INSERT INTO `ingreso_detalles` (`id`, `ingreso_producto_id`, `producto_id`, `cantidad`, `disponible`, `ubicacion_producto_id`, `fecha_vencimiento`, `descripcion`, `fecha_registro`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 15, 20, 0, 1, '2026-01-01', 'DESC', '2025-04-24', 1, '2025-04-24 22:55:46', '2025-04-25 00:16:58'),
(2, 1, 16, 20, 20, 1, '2026-02-02', '', '2025-04-24', 1, '2025-04-24 22:55:46', '2025-04-24 22:55:46'),
(3, 1, 18, 30, 27, 2, NULL, '', '2025-04-24', 1, '2025-04-24 22:55:46', '2025-04-24 23:07:14'),
(4, 2, 15, 30, 20, 1, '2026-01-01', '', '2025-04-24', 1, '2025-04-24 22:56:09', '2025-04-25 00:16:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso_productos`
--

CREATE TABLE `ingreso_productos` (
  `id` bigint UNSIGNED NOT NULL,
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `fecha_registro` date DEFAULT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ingreso_productos`
--

INSERT INTO `ingreso_productos` (`id`, `sucursal_id`, `fecha_registro`, `descripcion`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-04-24', '', 1, '2025-04-24 22:55:46', '2025-04-24 22:55:46'),
(2, 1, '2025-04-24', '', 1, '2025-04-24 22:56:09', '2025-04-24 22:56:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kardex_productos`
--

CREATE TABLE `kardex_productos` (
  `id` bigint UNSIGNED NOT NULL,
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `tipo_registro` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registro_id` bigint UNSIGNED DEFAULT NULL,
  `modulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `detalle` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(24,2) DEFAULT NULL,
  `tipo_is` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad_ingreso` double DEFAULT NULL,
  `cantidad_salida` double DEFAULT NULL,
  `cantidad_saldo` double NOT NULL,
  `cu` decimal(24,2) NOT NULL,
  `monto_ingreso` decimal(24,2) DEFAULT NULL,
  `monto_salida` decimal(24,2) DEFAULT NULL,
  `monto_saldo` decimal(24,2) NOT NULL,
  `fecha` date NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `kardex_productos`
--

INSERT INTO `kardex_productos` (`id`, `sucursal_id`, `tipo_registro`, `registro_id`, `modulo`, `producto_id`, `detalle`, `precio`, `tipo_is`, `cantidad_ingreso`, `cantidad_salida`, `cantidad_saldo`, `cu`, `monto_ingreso`, `monto_salida`, `monto_saldo`, `fecha`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'INGRESO DE PRODUCTO', 1, 'IngresoDetalle', 15, 'VALOR INICIAL', 200.00, 'INGRESO', 20, NULL, 20, 200.00, 4000.00, NULL, 4000.00, '2025-04-24', 1, '2025-04-24 22:55:46', '2025-04-24 22:55:46'),
(2, 1, 'INGRESO DE PRODUCTO', 2, 'IngresoDetalle', 16, 'VALOR INICIAL', 200.00, 'INGRESO', 20, NULL, 20, 200.00, 4000.00, NULL, 4000.00, '2025-04-24', 1, '2025-04-24 22:55:46', '2025-04-24 22:55:46'),
(3, 1, 'INGRESO DE PRODUCTO', 3, 'IngresoDetalle', 18, 'VALOR INICIAL', 100.50, 'INGRESO', 30, NULL, 30, 100.50, 3015.00, NULL, 3015.00, '2025-04-24', 1, '2025-04-24 22:55:46', '2025-04-24 22:55:46'),
(4, 1, 'INGRESO DE PRODUCTO', 4, 'IngresoDetalle', 15, 'INGRESO DE PRODUCTO', 200.00, 'INGRESO', 30, NULL, 50, 200.00, 6000.00, NULL, 10000.00, '2025-04-24', 1, '2025-04-24 22:56:09', '2025-04-24 22:56:09'),
(5, 1, 'ORDEN DE VENTA', 1, 'DetalleOrden', 15, 'VENTA DE PRODUCTO', 200.00, 'EGRESO', NULL, 10, 40, 200.00, NULL, 2000.00, 8000.00, '2025-04-24', 1, '2025-04-24 22:56:30', '2025-04-24 22:56:30'),
(6, 1, 'ORDEN DE VENTA', 2, 'DetalleOrden', 15, 'VENTA DE PRODUCTO', 200.00, 'EGRESO', NULL, 20, 20, 200.00, NULL, 4000.00, 4000.00, '2025-04-24', 1, '2025-04-24 22:56:54', '2025-04-24 22:56:54'),
(7, 1, 'ORDEN DE VENTA', 3, 'DetalleOrden', 18, 'VENTA DE PRODUCTO', 100.50, 'EGRESO', NULL, 3, 27, 100.50, NULL, 301.50, 2713.50, '2025-04-24', 1, '2025-04-24 22:56:54', '2025-04-24 22:56:54'),
(8, 1, 'ORDEN DE VENTA', 3, 'DetalleOrden', 18, 'POR MODIFICACIÓN DE ORDEN DE VENTA', 100.50, 'INGRESO', 3, NULL, 30, 100.50, 301.50, NULL, 3015.00, '2025-04-24', 1, '2025-04-24 22:57:07', '2025-04-24 22:57:07'),
(9, 1, 'ORDEN DE VENTA', 3, 'DetalleOrden', 18, 'VENTA DE PRODUCTO (MODIFICACIÓN)', 100.50, 'EGRESO', NULL, 3, 27, 100.50, NULL, 301.50, 2713.50, '2025-04-24', 1, '2025-04-24 22:57:07', '2025-04-24 22:57:07'),
(10, 1, 'ORDEN DE VENTA', 2, 'DetalleOrden', 15, 'ELIMINACIÓN DE PRODUCTO DETALLE DE ORDEN', 200.00, 'INGRESO', 20, NULL, 40, 200.00, 4000.00, NULL, 8000.00, '2025-04-24', 1, '2025-04-24 22:57:07', '2025-04-24 22:57:07'),
(11, 1, 'ORDEN DE VENTA', 3, 'DetalleOrden', 18, 'POR MODIFICACIÓN DE ORDEN DE VENTA', 100.50, 'INGRESO', 3, NULL, 30, 100.50, 301.50, NULL, 3015.00, '2025-04-24', 1, '2025-04-24 23:07:01', '2025-04-24 23:07:01'),
(12, 1, 'ORDEN DE VENTA', 3, 'DetalleOrden', 18, 'VENTA DE PRODUCTO (MODIFICACIÓN)', 100.50, 'EGRESO', NULL, 3, 27, 100.50, NULL, 301.50, 2713.50, '2025-04-24', 1, '2025-04-24 23:07:01', '2025-04-24 23:07:01'),
(13, 1, 'ORDEN DE VENTA', 4, 'DetalleOrden', 15, 'VENTA DE PRODUCTO', 200.00, 'EGRESO', NULL, 1, 39, 200.00, NULL, 200.00, 7800.00, '2025-04-24', 1, '2025-04-24 23:07:01', '2025-04-24 23:07:01'),
(14, 1, 'ORDEN DE VENTA', 3, 'DetalleOrden', 18, 'POR MODIFICACIÓN DE ORDEN DE VENTA', 100.50, 'INGRESO', 3, NULL, 30, 100.50, 301.50, NULL, 3015.00, '2025-04-24', 1, '2025-04-24 23:07:14', '2025-04-24 23:07:14'),
(15, 1, 'ORDEN DE VENTA', 3, 'DetalleOrden', 18, 'VENTA DE PRODUCTO (MODIFICACIÓN)', 100.50, 'EGRESO', NULL, 3, 27, 100.50, NULL, 301.50, 2713.50, '2025-04-24', 1, '2025-04-24 23:07:14', '2025-04-24 23:07:14'),
(16, 1, 'ORDEN DE VENTA', 4, 'DetalleOrden', 15, 'POR MODIFICACIÓN DE ORDEN DE VENTA', 200.00, 'INGRESO', 1, NULL, 40, 200.00, 200.00, NULL, 8000.00, '2025-04-24', 1, '2025-04-24 23:07:14', '2025-04-24 23:07:14'),
(17, 1, 'ORDEN DE VENTA', 4, 'DetalleOrden', 15, 'VENTA DE PRODUCTO (MODIFICACIÓN)', 200.00, 'EGRESO', NULL, 12, 28, 200.00, NULL, 2400.00, 5600.00, '2025-04-24', 1, '2025-04-24 23:07:14', '2025-04-24 23:07:14'),
(18, 1, 'DEVOLUCIÓN', 1, 'Devolucion', 15, 'INGRESO POR DEVOLUCIÓN DE PRODUCTO POR INCORRECTO', 200.00, 'INGRESO', 12, NULL, 40, 200.00, 2400.00, NULL, 8000.00, '2025-04-24', 1, '2025-04-24 23:07:37', '2025-04-24 23:07:37'),
(20, 1, 'ORDEN DE VENTA', 6, 'DetalleOrden', 15, 'VENTA DE PRODUCTO', 200.00, 'EGRESO', NULL, 20, 20, 200.00, NULL, 4000.00, 4000.00, '2025-04-24', 1, '2025-04-25 00:16:58', '2025-04-25 00:16:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_01_31_165641_create_configuracions_table', 1),
(2, '2024_11_02_153309_create_roles_table', 1),
(3, '2024_11_02_153315_create_modulos_table', 1),
(4, '2024_11_02_153316_create_permisos_table', 1),
(5, '2024_11_02_153317_create_users_table', 1),
(6, '2024_11_02_153318_create_historial_accions_table', 1),
(7, '2024_11_02_153319_create_sucursal_table', 1),
(8, '2024_11_02_153834_create_clientes_table', 1),
(9, '2025_04_14_142025_create_productos_table', 2),
(10, '2025_04_14_142047_create_producto_relacions_table', 2),
(11, '2025_04_14_142059_create_ubicacion_productos_table', 2),
(12, '2025_04_14_142106_create_ingreso_productos_table', 2),
(13, '2025_04_14_142426_create_salida_productos_table', 2),
(14, '2025_04_14_142453_create_orden_ventas_table', 2),
(15, '2025_04_14_142509_create_detalle_ordens_table', 2),
(16, '2025_04_14_142536_create_notificacions_table', 2),
(17, '2025_04_14_142547_create_notificacion_users_table', 2),
(18, '2025_04_14_142609_create_devolucions_table', 2),
(19, '2025_04_14_142637_create_proformas_table', 2),
(20, '2025_04_14_142644_create_detalle_proformas_table', 2),
(21, '2025_04_14_142651_create_promocions_table', 2),
(22, '2025_04_14_143203_create_ingreso_detalles_table', 2),
(23, '2025_04_14_143810_create_producto_sucursals_table', 2),
(24, '2025_04_18_101317_create_kardex_productos_table', 3),
(25, '2025_04_24_180539_create_detalle_usos_table', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id` bigint UNSIGNED NOT NULL,
  `modulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `accion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id`, `modulo`, `nombre`, `accion`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Gestión de usuarios', 'usuarios.index', 'VER', 'VER LA LISTA DE USUARIOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(2, 'Gestión de usuarios', 'usuarios.create', 'CREAR', 'CREAR USUARIOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(3, 'Gestión de usuarios', 'usuarios.edit', 'EDITAR', 'EDITAR USUARIOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(4, 'Gestión de usuarios', 'usuarios.destroy', 'ELIMINAR', 'ELIMINAR USUARIOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(5, 'Roles y Permisos', 'roles.index', 'VER', 'VER LA LISTA DE ROLES Y PERMISOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(6, 'Roles y Permisos', 'roles.create', 'CREAR', 'CREAR ROLES Y PERMISOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(7, 'Roles y Permisos', 'roles.edit', 'EDITAR', 'EDITAR ROLES Y PERMISOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(8, 'Roles y Permisos', 'roles.destroy', 'ELIMINAR', 'ELIMINAR ROLES Y PERMISOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(9, 'Configuración', 'configuracions.index', 'VER', 'VER INFORMACIÓN DE LA CONFIGURACIÓN DEL SISTEMA', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(10, 'Configuración', 'configuracions.edit', 'EDITAR', 'EDITAR LA CONFIGURACIÓN DEL SISTEMA', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(11, 'Sucursales', 'sucursals.index', 'VER', 'VER LA LISTA DE SUCURSALES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(12, 'Sucursales', 'sucursals.create', 'CREAR', 'CREAR SUCURSALES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(13, 'Sucursales', 'sucursals.edit', 'EDITAR', 'EDITAR SUCURSALES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(14, 'Sucursales', 'sucursals.destroy', 'ELIMINAR', 'ELIMINAR SUCURSALES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(15, 'Productos', 'productos.index', 'VER', 'VER LA LISTA DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(16, 'Productos', 'productos.create', 'CREAR', 'CREAR PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(17, 'Productos', 'productos.edit', 'EDITAR', 'EDITAR PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(18, 'Productos', 'productos.relacion', 'RELACIÓN PRODUCTOS', 'RELACIONAR CON OTROS PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(19, 'Productos', 'productos.destroy', 'ELIMINAR', 'ELIMINAR PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(20, 'Ubicación de Productos', 'ubicacion_productos.index', 'VER', 'VER LA LISTA DE UBICACIÓN DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(21, 'Ubicación de Productos', 'ubicacion_productos.create', 'CREAR', 'CREAR UBICACIÓN DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(22, 'Ubicación de Productos', 'ubicacion_productos.edit', 'EDITAR', 'EDITAR UBICACIÓN DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(23, 'Ubicación de Productos', 'ubicacion_productos.destroy', 'ELIMINAR', 'ELIMINAR UBICACIÓN DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(24, 'Ingreso de Productos', 'ingreso_productos.index', 'VER', 'VER LA LISTA DE INGRESO DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(25, 'Ingreso de Productos', 'ingreso_productos.create', 'CREAR', 'CREAR INGRESO DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(26, 'Ingreso de Productos', 'ingreso_productos.edit', 'EDITAR', 'EDITAR INGRESO DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(27, 'Ingreso de Productos', 'ingreso_productos.destroy', 'ELIMINAR', 'ELIMINAR INGRESO DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(28, 'Salida de Productos', 'salida_productos.index', 'VER', 'VER LA LISTA DE SALIDA DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(29, 'Salida de Productos', 'salida_productos.create', 'CREAR', 'CREAR SALIDA DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(30, 'Salida de Productos', 'salida_productos.edit', 'EDITAR', 'EDITAR SALIDA DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(31, 'Salida de Productos', 'salida_productos.destroy', 'ELIMINAR', 'ELIMINAR SALIDA DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(32, 'Stock de Productos', 'producto_sucursals.index', 'VER', 'VER LA LISTA DE STOCK DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(33, 'Clientes', 'clientes.index', 'VER', 'VER LA LISTA DE CLIENTES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(34, 'Clientes', 'clientes.create', 'CREAR', 'CREAR CLIENTES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(35, 'Clientes', 'clientes.edit', 'EDITAR', 'EDITAR CLIENTES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(36, 'Clientes', 'clientes.destroy', 'ELIMINAR', 'ELIMINAR CLIENTES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(37, 'Orden de Ventas', 'orden_ventas.index', 'VER', 'VER LA LISTA DE ORDENES DE VENTA', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(38, 'Orden de Ventas', 'orden_ventas.create', 'CREAR', 'CREAR ORDENES DE VENTA', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(39, 'Orden de Ventas', 'orden_ventas.edit', 'EDITAR', 'EDITAR ORDENES DE VENTA', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(40, 'Orden de Ventas', 'orden_ventas.destroy', 'ELIMINAR', 'ELIMINAR ORDENES DE VENTA', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(41, 'Notificaciones', 'notificacions.index', 'VER', 'RECIBIR NOTIFICACIONES DE STOCK DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(42, 'Devoluciones', 'devolucions.index', 'VER', 'VER LA LISTA DE DEVOLUCIONES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(43, 'Devoluciones', 'devolucions.create', 'CREAR', 'CREAR DEVOLUCIONES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(44, 'Devoluciones', 'devolucions.edit', 'EDITAR', 'EDITAR DEVOLUCIONES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(45, 'Devoluciones', 'devolucions.destroy', 'ELIMINAR', 'ELIMINAR DEVOLUCIONES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(46, 'Proformas', 'proformas.index', 'VER', 'VER LA LISTA DE PROFORMAS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(47, 'Proformas', 'proformas.create', 'CREAR', 'CREAR PROFORMAS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(48, 'Proformas', 'proformas.edit', 'EDITAR', 'EDITAR PROFORMAS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(49, 'Proformas', 'proformas.destroy', 'ELIMINAR', 'ELIMINAR PROFORMAS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(50, 'Promociones', 'promocions.index', 'VER', 'VER LA LISTA DE PROMOCIONES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(51, 'Promociones', 'promocions.create', 'CREAR', 'CREAR PROMOCIONES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(52, 'Promociones', 'promocions.edit', 'EDITAR', 'EDITAR PROMOCIONES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(53, 'Promociones', 'promocions.destroy', 'ELIMINAR', 'ELIMINAR PROMOCIONES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(54, 'Reportes', 'reportes.usuarios', 'REPORTE LISTA DE USUARIOS', 'GENERAR REPORTES DE LISTA DE USUARIOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(55, 'Reportes', 'reportes.kardex_productos', 'REPORTE KARDEX DE PRODUCTOS', 'GENERAR REPORTES DE KARDEX DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(56, 'Reportes', 'reportes.orden_ventas', 'REPORTE ORDENES DE VENTAS', 'GENERAR REPORTES DE ORDENES DE VENTAS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(57, 'Reportes', 'reportes.stock_productos', 'REPORTE DE STOCK DE PRODUCTOS', 'GENERAR REPORTES DE STOCK DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(58, 'Reportes', 'reportes.ingreso_productos', 'REPORTE DE INGRESO DE PRODUCTOS', 'GENERAR REPORTES DE INGRESO DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(59, 'Reportes', 'reportes.salida_productos', 'REPORTE DE SALIDA DE PRODUCTOS', 'GENERAR REPORTES DE SALIDA DE PRODUCTOS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(60, 'Reportes', 'reportes.devolucions', 'REPORTE DE DEVOLUCIONES', 'GENERAR REPORTES DE DEVOLUCIONES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(61, 'Reportes', 'reportes.proformas', 'REPORTE DE PROFORMAS', 'GENERAR REPORTES DE PROFORMAS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(62, 'Reportes', 'reportes.clientes', 'REPORTE DE CLIENTES', 'GENERAR REPORTES DE CLIENTES', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(63, 'Reportes', 'reportes.g_cantidad_orden_ventas', 'REPORTE GRÁFICO DE CANTIDAD DE ORDENES DE VENTAS', 'GENERAR REPORTE GRÁFICO DE CANTIDAD DE ORDENES DE VENTAS', '2025-04-17 19:50:14', '2025-04-17 19:50:14'),
(64, 'Reportes', 'reportes.g_ingresos_orden_ventas', 'REPORTE GRÁFICO DE INGRESOS POR ORDENES DE VENTAS', 'GENERAR REPORTE GRÁFICO DE INGRESOS POR ORDENES DE VENTAS', '2025-04-17 19:50:14', '2025-04-17 19:50:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacions`
--

CREATE TABLE `notificacions` (
  `id` bigint UNSIGNED NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `tipo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `modulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registro_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificacions`
--

INSERT INTO `notificacions` (`id`, `descripcion`, `fecha`, `hora`, `tipo`, `sucursal_id`, `modulo`, `registro_id`, `created_at`, `updated_at`) VALUES
(1, 'STOCK DEL PRODUCTO PRODUCTO A1 ESTA POR DEBAJO DEL 50% DEL STOCK MAXIMO', '2025-04-24', '19:40:08', 'STOCK INTERMEDIO', 1, 'ProductoSucursal', 2, '2025-04-24 23:40:08', '2025-04-24 23:40:08'),
(2, 'STOCK DEL PRODUCTO PRODUCTO A ESTA POR DEBAJO DEL 50% DEL STOCK MAXIMO', '2025-04-24', '20:16:58', 'STOCK INTERMEDIO', 1, 'ProductoSucursal', 1, '2025-04-25 00:16:58', '2025-04-25 00:16:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion_users`
--

CREATE TABLE `notificacion_users` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `notificacion_id` bigint UNSIGNED NOT NULL,
  `visto` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificacion_users`
--

INSERT INTO `notificacion_users` (`id`, `user_id`, `notificacion_id`, `visto`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2025-04-24 23:40:08', '2025-04-25 00:06:25'),
(2, 3, 1, 0, '2025-04-24 23:40:08', '2025-04-24 23:40:08'),
(3, 4, 1, 0, '2025-04-24 23:40:08', '2025-04-24 23:40:08'),
(4, 1, 2, 1, '2025-04-25 00:16:58', '2025-04-25 00:17:04'),
(5, 4, 2, 0, '2025-04-25 00:16:58', '2025-04-25 00:16:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_ventas`
--

CREATE TABLE `orden_ventas` (
  `id` bigint UNSIGNED NOT NULL,
  `nro` bigint NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `nit_ci` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `factura` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_pago` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` date DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `orden_ventas`
--

INSERT INTO `orden_ventas` (`id`, `nro`, `user_id`, `sucursal_id`, `cliente_id`, `nit_ci`, `factura`, `tipo_pago`, `fecha_registro`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, '3222323', 'NO', 'EFECTIVO', '2025-04-24', 1, '2025-04-24 22:56:30', '2025-04-24 22:56:30'),
(2, 2, 1, 1, 2, '0', 'NO', 'EFECTIVO', '2025-04-24', 1, '2025-04-24 22:56:54', '2025-04-24 22:56:54'),
(4, 3, 1, 1, 1, '3222323', 'NO', 'EFECTIVO', '2025-04-24', 1, '2025-04-25 00:16:58', '2025-04-25 00:16:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `modulo_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `role_id`, `modulo_id`, `created_at`, `updated_at`) VALUES
(1, 2, 15, '2025-04-17 21:01:18', '2025-04-17 21:01:18'),
(2, 2, 16, '2025-04-17 21:01:20', '2025-04-17 21:01:20'),
(3, 2, 17, '2025-04-17 21:01:21', '2025-04-17 21:01:21'),
(4, 3, 15, '2025-04-17 21:01:42', '2025-04-17 21:01:42'),
(5, 3, 16, '2025-04-17 21:01:45', '2025-04-17 21:01:45'),
(6, 3, 24, '2025-04-19 14:40:24', '2025-04-19 14:40:24'),
(7, 3, 25, '2025-04-19 14:40:25', '2025-04-19 14:40:25'),
(8, 3, 28, '2025-04-19 16:06:26', '2025-04-19 16:06:26'),
(9, 3, 29, '2025-04-19 16:10:01', '2025-04-19 16:10:01'),
(10, 3, 37, '2025-04-23 15:01:14', '2025-04-23 15:01:14'),
(11, 3, 38, '2025-04-23 15:01:14', '2025-04-23 15:01:14'),
(12, 3, 18, '2025-04-23 15:11:24', '2025-04-23 15:11:24'),
(13, 3, 42, '2025-04-24 19:50:29', '2025-04-24 19:50:29'),
(14, 3, 43, '2025-04-24 19:50:30', '2025-04-24 19:50:30'),
(15, 3, 41, '2025-04-24 23:51:16', '2025-04-24 23:51:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(900) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio_pred` decimal(24,2) NOT NULL,
  `precio_min` decimal(24,2) NOT NULL,
  `precio_fac` decimal(24,2) NOT NULL,
  `precio_sf` decimal(24,2) NOT NULL,
  `stock_maximo` double NOT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio_pred`, `precio_min`, `precio_fac`, `precio_sf`, `stock_maximo`, `foto`, `fecha_registro`, `status`, `created_at`, `updated_at`) VALUES
(15, 'PRODUCTO A', 'DESCRIPCION PRODUCTO', 200.00, 160.00, 10.00, 0.00, 50, '151744662823.png', '2025-04-14', 1, '2025-04-14 20:26:05', '2025-04-22 21:28:09'),
(16, 'PRODUCTO A1', 'PARECIDO AL PROD A', 200.00, 170.00, 5.00, 0.00, 50, NULL, '2025-04-17', 1, '2025-04-17 20:07:08', '2025-04-17 20:07:08'),
(17, 'PRODUCTO A2', 'PARECIDO A PROD A Y A1', 220.00, 200.00, 6.00, 0.00, 60, NULL, '2025-04-17', 1, '2025-04-17 20:07:30', '2025-04-17 20:07:30'),
(18, 'PRODUCTO B', 'DESC PROD B', 100.50, 90.00, 10.00, 0.00, 40, NULL, '2025-04-17', 1, '2025-04-17 20:07:53', '2025-04-17 20:07:53'),
(19, 'PRODUCTO C', 'DESC PROD C', 50.90, 48.00, 10.00, 0.00, 30, NULL, '2025-04-17', 1, '2025-04-17 20:08:15', '2025-04-17 20:08:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_relacions`
--

CREATE TABLE `producto_relacions` (
  `id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `producto_relacion` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto_relacions`
--

INSERT INTO `producto_relacions` (`id`, `producto_id`, `producto_relacion`, `created_at`, `updated_at`) VALUES
(9, 15, 16, '2025-04-17 21:40:31', '2025-04-17 21:40:31'),
(10, 15, 17, '2025-04-17 21:40:34', '2025-04-17 21:40:34'),
(11, 16, 15, '2025-04-19 14:31:00', '2025-04-19 14:31:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_sucursals`
--

CREATE TABLE `producto_sucursals` (
  `id` bigint UNSIGNED NOT NULL,
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `stock_actual` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto_sucursals`
--

INSERT INTO `producto_sucursals` (`id`, `sucursal_id`, `producto_id`, `stock_actual`, `created_at`, `updated_at`) VALUES
(1, 1, 15, 20, '2025-04-24 22:55:46', '2025-04-25 00:16:58'),
(2, 1, 16, 20, '2025-04-24 22:55:46', '2025-04-24 22:55:46'),
(3, 1, 18, 27, '2025-04-24 22:55:46', '2025-04-24 23:07:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proformas`
--

CREATE TABLE `proformas` (
  `id` bigint UNSIGNED NOT NULL,
  `nro` bigint NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `nit_ci` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `factura` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_validez` date NOT NULL,
  `fecha_registro` date DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promocions`
--

CREATE TABLE `promocions` (
  `id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `porcentaje` double NOT NULL,
  `fecha_ini` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_registro` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `promocions`
--

INSERT INTO `promocions` (`id`, `producto_id`, `porcentaje`, `fecha_ini`, `fecha_fin`, `descripcion`, `fecha_registro`, `created_at`, `updated_at`) VALUES
(2, 15, 10, '2025-04-22', '2025-04-30', '', '2025-04-22', '2025-04-22 19:57:35', '2025-04-22 19:57:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `permisos` int NOT NULL DEFAULT '0',
  `usuarios` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `permisos`, `usuarios`, `created_at`, `updated_at`) VALUES
(1, 'SUPER USUARIO', 1, 0, NULL, NULL),
(2, 'ADMINISTRADOR', 0, 1, NULL, NULL),
(3, 'AUXILIAR', 0, 1, '2025-04-14 19:51:25', '2025-04-14 19:51:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salida_productos`
--

CREATE TABLE `salida_productos` (
  `id` bigint UNSIGNED NOT NULL,
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `cantidad` double NOT NULL,
  `descripcion` varchar(800) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` date NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursals`
--

CREATE TABLE `sucursals` (
  `id` bigint UNSIGNED NOT NULL,
  `codigo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(600) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fonos` varchar(600) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `fecha_registro` date NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sucursals`
--

INSERT INTO `sucursals` (`id`, `codigo`, `nombre`, `direccion`, `fonos`, `user_id`, `fecha_registro`, `status`, `created_at`, `updated_at`) VALUES
(1, 'S001', 'SUCURSAL A', 'LOS OLIVOS', '777777 - 67676767', 3, '2025-04-14', 1, '2025-04-14 19:54:06', '2025-04-14 19:58:15'),
(2, 'S002', 'SUCURSAL B', '', '5656565656', 3, '2025-04-14', 1, '2025-04-14 19:58:29', '2025-04-14 19:58:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacion_productos`
--

CREATE TABLE `ubicacion_productos` (
  `id` bigint UNSIGNED NOT NULL,
  `lugar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_filas` int NOT NULL,
  `fecha_registro` date NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ubicacion_productos`
--

INSERT INTO `ubicacion_productos` (`id`, `lugar`, `numero_filas`, `fecha_registro`, `status`, `created_at`, `updated_at`) VALUES
(1, 'LUGAR A', 1, '2025-04-17', 1, '2025-04-17 22:10:25', '2025-04-17 22:10:25'),
(2, 'LUGAR B', 1, '2025-04-17', 1, '2025-04-17 22:11:05', '2025-04-17 22:11:11'),
(3, 'LUGAR A', 2, '2025-04-17', 1, '2025-04-17 22:11:29', '2025-04-17 22:11:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `usuario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombres` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `paterno` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `materno` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ci` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ci_exp` varchar(155) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `sucursals_todo` int NOT NULL DEFAULT '0',
  `sucursal_id` int UNSIGNED DEFAULT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` date NOT NULL,
  `acceso` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `usuario`, `nombres`, `paterno`, `materno`, `ci`, `ci_exp`, `correo`, `password`, `role_id`, `sucursals_todo`, `sucursal_id`, `foto`, `fecha_registro`, `acceso`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', 'admin', NULL, '0', '', 'admin@admin.com', '$2y$12$65d4fgZsvBV5Lc/AxNKh4eoUdbGyaczQ4sSco20feSQANshNLuxSC', 1, 1, NULL, NULL, '2025-04-13', 1, 1, NULL, NULL),
(3, 'JPERES', 'JUAN', 'PERES', 'MAMANI', '444444', 'LP', 'juan@gmail.com', '$2y$12$b4EOhFH4jgoCw9XCTTsbdehEOeVBFNysg7RG2Kqe9r/0Is4./A6sK', 2, 1, NULL, '31744662841.jpg', '2025-04-14', 1, 1, '2025-04-14 19:39:54', '2025-04-14 20:34:01'),
(4, 'MCONDORI', 'MARCOS', 'CONDORI', '', '555555', 'LP', NULL, '$2y$12$s7fcK8m0793KQtkTF51EZuoGHJgn0/6N6OykmnjDTrUeS14mihzQC', 3, 0, 1, '41744662862.jpg', '2025-04-14', 1, 1, '2025-04-14 19:52:56', '2025-04-14 20:34:22');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configuracions`
--
ALTER TABLE `configuracions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_ordens`
--
ALTER TABLE `detalle_ordens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detalle_ordens_orden_venta_id_foreign` (`orden_venta_id`),
  ADD KEY `detalle_ordens_producto_id_foreign` (`producto_id`);

--
-- Indices de la tabla `detalle_proformas`
--
ALTER TABLE `detalle_proformas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detalle_proformas_proforma_id_foreign` (`proforma_id`),
  ADD KEY `detalle_proformas_producto_id_foreign` (`producto_id`);

--
-- Indices de la tabla `detalle_usos`
--
ALTER TABLE `detalle_usos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detalle_usos_orden_venta_id_foreign` (`orden_venta_id`),
  ADD KEY `detalle_usos_detalle_orden_id_foreign` (`detalle_orden_id`),
  ADD KEY `detalle_usos_producto_id_foreign` (`producto_id`),
  ADD KEY `detalle_usos_ingreso_detalle_id_foreign` (`ingreso_detalle_id`);

--
-- Indices de la tabla `devolucions`
--
ALTER TABLE `devolucions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `devolucions_sucursal_id_foreign` (`sucursal_id`),
  ADD KEY `devolucions_orden_venta_id_foreign` (`orden_venta_id`),
  ADD KEY `devolucions_producto_id_foreign` (`producto_id`),
  ADD KEY `devolucions_detalle_orden_id_foreign` (`detalle_orden_id`);

--
-- Indices de la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `historial_accions_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `ingreso_detalles`
--
ALTER TABLE `ingreso_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ingreso_detalles_ingreso_producto_id_foreign` (`ingreso_producto_id`),
  ADD KEY `ingreso_detalles_producto_id_foreign` (`producto_id`),
  ADD KEY `ingreso_detalles_ubicacion_producto_id_foreign` (`ubicacion_producto_id`);

--
-- Indices de la tabla `ingreso_productos`
--
ALTER TABLE `ingreso_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ingreso_productos_sucursal_id_foreign` (`sucursal_id`);

--
-- Indices de la tabla `kardex_productos`
--
ALTER TABLE `kardex_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kardex_productos_producto_id_foreign` (`producto_id`),
  ADD KEY `kardex_productos_sucursal_id_foreign` (`sucursal_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notificacions`
--
ALTER TABLE `notificacions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notificacion_users`
--
ALTER TABLE `notificacion_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notificacion_users_user_id_foreign` (`user_id`),
  ADD KEY `notificacion_users_notificacion_id_foreign` (`notificacion_id`);

--
-- Indices de la tabla `orden_ventas`
--
ALTER TABLE `orden_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orden_ventas_sucursal_id_foreign` (`sucursal_id`),
  ADD KEY `orden_ventas_cliente_id_foreign` (`cliente_id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permisos_role_id_foreign` (`role_id`),
  ADD KEY `permisos_modulo_id_foreign` (`modulo_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `producto_relacions`
--
ALTER TABLE `producto_relacions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_relacions_producto_id_foreign` (`producto_id`),
  ADD KEY `producto_relacions_producto_relacion_foreign` (`producto_relacion`);

--
-- Indices de la tabla `producto_sucursals`
--
ALTER TABLE `producto_sucursals`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `proformas`
--
ALTER TABLE `proformas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `promocions`
--
ALTER TABLE `promocions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promocions_producto_id_foreign` (`producto_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `salida_productos`
--
ALTER TABLE `salida_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salida_productos_sucursal_id_foreign` (`sucursal_id`),
  ADD KEY `salida_productos_producto_id_foreign` (`producto_id`);

--
-- Indices de la tabla `sucursals`
--
ALTER TABLE `sucursals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sucursals_codigo_unique` (`codigo`),
  ADD KEY `sucursals_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `ubicacion_productos`
--
ALTER TABLE `ubicacion_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_usuario_unique` (`usuario`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `configuracions`
--
ALTER TABLE `configuracions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_ordens`
--
ALTER TABLE `detalle_ordens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `detalle_proformas`
--
ALTER TABLE `detalle_proformas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_usos`
--
ALTER TABLE `detalle_usos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `devolucions`
--
ALTER TABLE `devolucions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `ingreso_detalles`
--
ALTER TABLE `ingreso_detalles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `ingreso_productos`
--
ALTER TABLE `ingreso_productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `kardex_productos`
--
ALTER TABLE `kardex_productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `notificacions`
--
ALTER TABLE `notificacions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `notificacion_users`
--
ALTER TABLE `notificacion_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `orden_ventas`
--
ALTER TABLE `orden_ventas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `producto_relacions`
--
ALTER TABLE `producto_relacions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `producto_sucursals`
--
ALTER TABLE `producto_sucursals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `proformas`
--
ALTER TABLE `proformas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `promocions`
--
ALTER TABLE `promocions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `salida_productos`
--
ALTER TABLE `salida_productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sucursals`
--
ALTER TABLE `sucursals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ubicacion_productos`
--
ALTER TABLE `ubicacion_productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_ordens`
--
ALTER TABLE `detalle_ordens`
  ADD CONSTRAINT `detalle_ordens_orden_venta_id_foreign` FOREIGN KEY (`orden_venta_id`) REFERENCES `orden_ventas` (`id`),
  ADD CONSTRAINT `detalle_ordens_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `detalle_proformas`
--
ALTER TABLE `detalle_proformas`
  ADD CONSTRAINT `detalle_proformas_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `detalle_proformas_proforma_id_foreign` FOREIGN KEY (`proforma_id`) REFERENCES `proformas` (`id`);

--
-- Filtros para la tabla `detalle_usos`
--
ALTER TABLE `detalle_usos`
  ADD CONSTRAINT `detalle_usos_detalle_orden_id_foreign` FOREIGN KEY (`detalle_orden_id`) REFERENCES `detalle_ordens` (`id`),
  ADD CONSTRAINT `detalle_usos_ingreso_detalle_id_foreign` FOREIGN KEY (`ingreso_detalle_id`) REFERENCES `ingreso_detalles` (`id`),
  ADD CONSTRAINT `detalle_usos_orden_venta_id_foreign` FOREIGN KEY (`orden_venta_id`) REFERENCES `orden_ventas` (`id`),
  ADD CONSTRAINT `detalle_usos_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `devolucions`
--
ALTER TABLE `devolucions`
  ADD CONSTRAINT `devolucions_detalle_orden_id_foreign` FOREIGN KEY (`detalle_orden_id`) REFERENCES `detalle_ordens` (`id`),
  ADD CONSTRAINT `devolucions_orden_venta_id_foreign` FOREIGN KEY (`orden_venta_id`) REFERENCES `orden_ventas` (`id`),
  ADD CONSTRAINT `devolucions_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `devolucions_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursals` (`id`);

--
-- Filtros para la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  ADD CONSTRAINT `historial_accions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `ingreso_detalles`
--
ALTER TABLE `ingreso_detalles`
  ADD CONSTRAINT `ingreso_detalles_ingreso_producto_id_foreign` FOREIGN KEY (`ingreso_producto_id`) REFERENCES `ingreso_productos` (`id`),
  ADD CONSTRAINT `ingreso_detalles_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `ingreso_detalles_ubicacion_producto_id_foreign` FOREIGN KEY (`ubicacion_producto_id`) REFERENCES `ubicacion_productos` (`id`);

--
-- Filtros para la tabla `ingreso_productos`
--
ALTER TABLE `ingreso_productos`
  ADD CONSTRAINT `ingreso_productos_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursals` (`id`);

--
-- Filtros para la tabla `kardex_productos`
--
ALTER TABLE `kardex_productos`
  ADD CONSTRAINT `kardex_productos_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `kardex_productos_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursals` (`id`);

--
-- Filtros para la tabla `notificacion_users`
--
ALTER TABLE `notificacion_users`
  ADD CONSTRAINT `notificacion_users_notificacion_id_foreign` FOREIGN KEY (`notificacion_id`) REFERENCES `notificacions` (`id`),
  ADD CONSTRAINT `notificacion_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `orden_ventas`
--
ALTER TABLE `orden_ventas`
  ADD CONSTRAINT `orden_ventas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `orden_ventas_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursals` (`id`);

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `permisos_modulo_id_foreign` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`),
  ADD CONSTRAINT `permisos_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `producto_relacions`
--
ALTER TABLE `producto_relacions`
  ADD CONSTRAINT `producto_relacions_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `producto_relacions_producto_relacion_foreign` FOREIGN KEY (`producto_relacion`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `promocions`
--
ALTER TABLE `promocions`
  ADD CONSTRAINT `promocions_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `salida_productos`
--
ALTER TABLE `salida_productos`
  ADD CONSTRAINT `salida_productos_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `salida_productos_sucursal_id_foreign` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursals` (`id`);

--
-- Filtros para la tabla `sucursals`
--
ALTER TABLE `sucursals`
  ADD CONSTRAINT `sucursals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
