-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 19-04-2025 a las 17:01:22
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
  `cantidad` double NOT NULL,
  `precio` decimal(24,2) NOT NULL,
  `subtotal` decimal(24,2) NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_proformas`
--

CREATE TABLE `detalle_proformas` (
  `id` bigint UNSIGNED NOT NULL,
  `proforma_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `cantidad` double NOT NULL,
  `precio` decimal(24,2) NOT NULL,
  `subtotal` decimal(24,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN USUARIO', '{\"ci\": \"444444\", \"id\": 2, \"foto\": \"21744659510.jpg\", \"acceso\": \"1\", \"ci_exp\": \"LP\", \"correo\": \"juan@gmail.com\", \"materno\": \"MAMANI\", \"nombres\": \"JUAN\", \"paterno\": \"PERES\", \"role_id\": \"2\", \"usuario\": \"juan@gmail.com\", \"created_at\": \"2025-04-14T19:38:30.000000Z\", \"updated_at\": \"2025-04-14T19:38:30.000000Z\", \"sucursal_id\": null, \"sucurals_todo\": 1, \"fecha_registro\": \"2025-04-14\"}', NULL, 'USUARIOS', '2025-04-14', '15:38:30', '2025-04-14 19:38:30', '2025-04-14 19:38:30'),
(2, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN USUARIO', '{\"ci\": \"444444\", \"id\": 3, \"foto\": \"31744659594.jpg\", \"acceso\": \"1\", \"ci_exp\": \"LP\", \"correo\": \"juan@gmail.com\", \"materno\": \"MAMANI\", \"nombres\": \"JUAN\", \"paterno\": \"PERES\", \"role_id\": \"2\", \"usuario\": \"JPERES\", \"created_at\": \"2025-04-14T19:39:54.000000Z\", \"updated_at\": \"2025-04-14T19:39:54.000000Z\", \"sucursal_id\": null, \"sucurals_todo\": 1, \"fecha_registro\": \"2025-04-14\"}', NULL, 'USUARIOS', '2025-04-14', '15:39:54', '2025-04-14 19:39:54', '2025-04-14 19:39:54'),
(3, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN USUARIO', '{\"ci\": \"444444\", \"id\": 3, \"foto\": \"31744659594.jpg\", \"acceso\": 1, \"ci_exp\": \"LP\", \"correo\": \"juan@gmail.com\", \"status\": 1, \"materno\": \"MAMANI\", \"nombres\": \"JUAN\", \"paterno\": \"PERES\", \"role_id\": 2, \"usuario\": \"JPERES\", \"created_at\": \"2025-04-14T19:39:54.000000Z\", \"updated_at\": \"2025-04-14T19:39:54.000000Z\", \"sucursal_id\": null, \"fecha_registro\": \"2025-04-14\", \"sucursals_todo\": 1}', '{\"ci\": \"444444\", \"id\": 3, \"foto\": \"31744659594.jpg\", \"acceso\": \"0\", \"ci_exp\": \"LP\", \"correo\": \"juan@gmail.com\", \"status\": 1, \"materno\": \"MAMANI\", \"nombres\": \"JUAN\", \"paterno\": \"PERESASD\", \"role_id\": \"2\", \"usuario\": \"juan@gmail.com\", \"created_at\": \"2025-04-14T19:39:54.000000Z\", \"updated_at\": \"2025-04-14T19:48:56.000000Z\", \"sucursal_id\": null, \"fecha_registro\": \"2025-04-14\", \"sucursals_todo\": 1}', 'USUARIOS', '2025-04-14', '15:48:56', '2025-04-14 19:48:56', '2025-04-14 19:48:56'),
(4, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN USUARIO', '{\"ci\": \"444444\", \"id\": 3, \"foto\": \"31744659594.jpg\", \"acceso\": 0, \"ci_exp\": \"LP\", \"correo\": \"juan@gmail.com\", \"status\": 1, \"materno\": \"MAMANI\", \"nombres\": \"JUAN\", \"paterno\": \"PERESASD\", \"role_id\": 2, \"usuario\": \"juan@gmail.com\", \"created_at\": \"2025-04-14T19:39:54.000000Z\", \"updated_at\": \"2025-04-14T19:48:56.000000Z\", \"sucursal_id\": null, \"fecha_registro\": \"2025-04-14\", \"sucursals_todo\": 1}', '{\"ci\": \"444444\", \"id\": 3, \"foto\": \"31744659594.jpg\", \"acceso\": \"0\", \"ci_exp\": \"LP\", \"correo\": \"juan@gmail.com\", \"status\": 1, \"materno\": \"MAMANI\", \"nombres\": \"JUAN\", \"paterno\": \"PERES\", \"role_id\": \"2\", \"usuario\": \"juan@gmail.com\", \"created_at\": \"2025-04-14T19:39:54.000000Z\", \"updated_at\": \"2025-04-14T19:49:00.000000Z\", \"sucursal_id\": null, \"fecha_registro\": \"2025-04-14\", \"sucursals_todo\": 1}', 'USUARIOS', '2025-04-14', '15:49:00', '2025-04-14 19:49:00', '2025-04-14 19:49:00'),
(5, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN USUARIO', '{\"ci\": \"444444\", \"id\": 3, \"foto\": \"31744659594.jpg\", \"acceso\": 0, \"ci_exp\": \"LP\", \"correo\": \"juan@gmail.com\", \"status\": 1, \"materno\": \"MAMANI\", \"nombres\": \"JUAN\", \"paterno\": \"PERES\", \"role_id\": 2, \"usuario\": \"juan@gmail.com\", \"created_at\": \"2025-04-14T19:39:54.000000Z\", \"updated_at\": \"2025-04-14T19:49:00.000000Z\", \"sucursal_id\": null, \"fecha_registro\": \"2025-04-14\", \"sucursals_todo\": 1}', '{\"ci\": \"444444\", \"id\": 3, \"foto\": \"31744659594.jpg\", \"acceso\": \"1\", \"ci_exp\": \"LP\", \"correo\": \"juan@gmail.com\", \"status\": 1, \"materno\": \"MAMANI\", \"nombres\": \"JUAN\", \"paterno\": \"PERES\", \"role_id\": \"2\", \"usuario\": \"juan@gmail.com\", \"created_at\": \"2025-04-14T19:39:54.000000Z\", \"updated_at\": \"2025-04-14T19:49:04.000000Z\", \"sucursal_id\": null, \"fecha_registro\": \"2025-04-14\", \"sucursals_todo\": 1}', 'USUARIOS', '2025-04-14', '15:49:04', '2025-04-14 19:49:04', '2025-04-14 19:49:04'),
(6, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN ROLE', '{\"id\": 3, \"nombre\": \"AUXILIAR\", \"created_at\": \"2025-04-14T19:51:25.000000Z\", \"updated_at\": \"2025-04-14T19:51:25.000000Z\"}', NULL, 'ROLES', '2025-04-14', '15:51:25', '2025-04-14 19:51:25', '2025-04-14 19:51:25'),
(7, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN USUARIO', '{\"ci\": \"555555\", \"id\": 4, \"foto\": \"41744660376.jpg\", \"acceso\": \"1\", \"ci_exp\": \"LP\", \"correo\": null, \"materno\": \"\", \"nombres\": \"MARCOS\", \"paterno\": \"CONDORI\", \"role_id\": \"3\", \"usuario\": \"MCONDORI\", \"created_at\": \"2025-04-14T19:52:56.000000Z\", \"updated_at\": \"2025-04-14T19:52:56.000000Z\", \"sucursal_id\": null, \"fecha_registro\": \"2025-04-14\", \"sucursals_todo\": 1}', NULL, 'USUARIOS', '2025-04-14', '15:52:56', '2025-04-14 19:52:56', '2025-04-14 19:52:56'),
(8, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA CATEGORÍA', '{\"id\": 1, \"fonos\": \"777777 - 67676767\", \"codigo\": \"S001\", \"nombre\": \"SUCURSAL A\", \"user_id\": \"3\", \"direccion\": \"LOS OLIVOS\", \"created_at\": \"2025-04-14T19:54:06.000000Z\", \"updated_at\": \"2025-04-14T19:54:06.000000Z\", \"fecha_registro\": \"2025-04-14\"}', NULL, 'SUCURSALES', '2025-04-14', '15:54:06', '2025-04-14 19:54:06', '2025-04-14 19:54:06'),
(9, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UNA CATEGORÍA', '{\"id\": 1, \"fonos\": \"777777 - 67676767\", \"codigo\": \"S001\", \"nombre\": \"SUCURSAL A\", \"status\": 1, \"user_id\": 3, \"direccion\": \"LOS OLIVOS\", \"created_at\": \"2025-04-14T19:54:06.000000Z\", \"updated_at\": \"2025-04-14T19:54:06.000000Z\", \"fecha_registro\": \"2025-04-14\"}', '{\"id\": 1, \"fonos\": \"777777 - 67676767\", \"codigo\": \"S001ASD\", \"nombre\": \"SUCURSAL A\", \"status\": 1, \"user_id\": \"3\", \"direccion\": \"LOS OLIVOS\", \"created_at\": \"2025-04-14T19:54:06.000000Z\", \"updated_at\": \"2025-04-14T19:55:50.000000Z\", \"fecha_registro\": \"2025-04-14\"}', 'SUCURSALES', '2025-04-14', '15:55:50', '2025-04-14 19:55:50', '2025-04-14 19:55:50'),
(10, 1, 'ELIMINACIÓN', 'EL USUARIO admin ELIMINÓ UNA SUCURSAL', '{\"id\": 1, \"fonos\": \"777777 - 67676767\", \"codigo\": \"S001ASD\", \"nombre\": \"SUCURSAL A\", \"status\": 1, \"user_id\": 3, \"direccion\": \"LOS OLIVOS\", \"created_at\": \"2025-04-14T19:54:06.000000Z\", \"updated_at\": \"2025-04-14T19:55:50.000000Z\", \"fecha_registro\": \"2025-04-14\"}', NULL, 'SUCURSALES', '2025-04-14', '15:58:01', '2025-04-14 19:58:01', '2025-04-14 19:58:01'),
(11, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UNA SUCURSAL', '{\"id\": 1, \"fonos\": \"777777 - 67676767\", \"codigo\": \"S001ASD\", \"nombre\": \"SUCURSAL A\", \"status\": 1, \"user_id\": 3, \"direccion\": \"LOS OLIVOS\", \"created_at\": \"2025-04-14T19:54:06.000000Z\", \"updated_at\": \"2025-04-14T19:58:01.000000Z\", \"fecha_registro\": \"2025-04-14\"}', '{\"id\": 1, \"fonos\": \"777777 - 67676767\", \"codigo\": \"S001\", \"nombre\": \"SUCURSAL A\", \"status\": 1, \"user_id\": \"3\", \"direccion\": \"LOS OLIVOS\", \"created_at\": \"2025-04-14T19:54:06.000000Z\", \"updated_at\": \"2025-04-14T19:58:15.000000Z\", \"fecha_registro\": \"2025-04-14\"}', 'SUCURSALES', '2025-04-14', '15:58:15', '2025-04-14 19:58:15', '2025-04-14 19:58:15'),
(12, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA SUCURSAL', '{\"id\": 2, \"fonos\": \"5656565656\", \"codigo\": \"S002\", \"nombre\": \"SUCURSAL B\", \"user_id\": \"3\", \"direccion\": \"\", \"created_at\": \"2025-04-14T19:58:29.000000Z\", \"updated_at\": \"2025-04-14T19:58:29.000000Z\", \"fecha_registro\": \"2025-04-14\"}', NULL, 'SUCURSALES', '2025-04-14', '15:58:29', '2025-04-14 19:58:29', '2025-04-14 19:58:29'),
(13, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN USUARIO', '{\"ci\": \"555555\", \"id\": 4, \"foto\": \"41744660376.jpg\", \"acceso\": 1, \"ci_exp\": \"LP\", \"correo\": null, \"status\": 1, \"materno\": \"\", \"nombres\": \"MARCOS\", \"paterno\": \"CONDORI\", \"role_id\": 3, \"usuario\": \"MCONDORI\", \"sucursal\": null, \"created_at\": \"2025-04-14T19:52:56.000000Z\", \"updated_at\": \"2025-04-14T19:52:56.000000Z\", \"sucursal_id\": null, \"fecha_registro\": \"2025-04-14\", \"sucursals_todo\": 1}', '{\"ci\": \"555555\", \"id\": 4, \"foto\": \"41744660376.jpg\", \"acceso\": \"1\", \"ci_exp\": \"LP\", \"correo\": null, \"status\": 1, \"materno\": \"\", \"nombres\": \"MARCOS\", \"paterno\": \"CONDORI\", \"role_id\": \"3\", \"usuario\": \"MCONDORI\", \"sucursal\": {\"id\": 1, \"fonos\": \"777777 - 67676767\", \"codigo\": \"S001\", \"nombre\": \"SUCURSAL A\", \"status\": 1, \"user_id\": 3, \"status_t\": \"HABILITADO\", \"direccion\": \"LOS OLIVOS\", \"created_at\": \"2025-04-14T19:54:06.000000Z\", \"updated_at\": \"2025-04-14T19:58:15.000000Z\", \"fecha_registro\": \"2025-04-14\", \"fecha_registro_t\": \"14/04/2025\"}, \"created_at\": \"2025-04-14T19:52:56.000000Z\", \"updated_at\": \"2025-04-14T20:05:02.000000Z\", \"sucursal_id\": \"1\", \"fecha_registro\": \"2025-04-14\", \"sucursals_todo\": 0}', 'USUARIOS', '2025-04-14', '16:05:02', '2025-04-14 20:05:02', '2025-04-14 20:05:02'),
(14, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PRODUCTO', '{\"id\": 15, \"foto\": \"151744662365.png\", \"nombre\": \"PRODUCTO A\", \"precio_sf\": \"0\", \"created_at\": \"2025-04-14T20:26:05.000000Z\", \"precio_fac\": \"10\", \"precio_min\": \"170\", \"updated_at\": \"2025-04-14T20:26:05.000000Z\", \"descripcion\": \"DESCRIPCION PRODUCTO\", \"precio_pred\": \"200\", \"stock_maximo\": \"50\", \"fecha_registro\": \"2025-04-14\"}', NULL, 'PRODUCTOS', '2025-04-14', '16:26:05', '2025-04-14 20:26:05', '2025-04-14 20:26:05'),
(15, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN PRODUCTO', '{\"id\": 15, \"foto\": \"151744662365.png\", \"nombre\": \"PRODUCTO A\", \"status\": 1, \"precio_sf\": \"0.00\", \"created_at\": \"2025-04-14T20:26:05.000000Z\", \"precio_fac\": \"10.00\", \"precio_min\": \"170.00\", \"updated_at\": \"2025-04-14T20:26:05.000000Z\", \"descripcion\": \"DESCRIPCION PRODUCTO\", \"precio_pred\": \"200.00\", \"stock_maximo\": 50, \"fecha_registro\": \"2025-04-14\"}', '{\"id\": 15, \"foto\": \"151744662823.png\", \"nombre\": \"PRODUCTO A\", \"status\": 1, \"precio_sf\": \"0.00\", \"created_at\": \"2025-04-14T20:26:05.000000Z\", \"precio_fac\": \"10.00\", \"precio_min\": \"170.00\", \"updated_at\": \"2025-04-14T20:33:43.000000Z\", \"descripcion\": \"DESCRIPCION PRODUCTO\", \"precio_pred\": \"200.00\", \"stock_maximo\": \"50\", \"fecha_registro\": \"2025-04-14\"}', 'PRODUCTOS', '2025-04-14', '16:33:43', '2025-04-14 20:33:43', '2025-04-14 20:33:43'),
(16, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN USUARIO', '{\"ci\": \"444444\", \"id\": 3, \"foto\": \"31744659594.jpg\", \"acceso\": 1, \"ci_exp\": \"LP\", \"correo\": \"juan@gmail.com\", \"status\": 1, \"materno\": \"MAMANI\", \"nombres\": \"JUAN\", \"paterno\": \"PERES\", \"role_id\": 2, \"usuario\": \"JPERES\", \"sucursal\": null, \"created_at\": \"2025-04-14T19:39:54.000000Z\", \"updated_at\": \"2025-04-14T19:49:04.000000Z\", \"sucursal_id\": null, \"fecha_registro\": \"2025-04-14\", \"sucursals_todo\": 1}', '{\"ci\": \"444444\", \"id\": 3, \"foto\": \"31744662841.jpg\", \"acceso\": \"1\", \"ci_exp\": \"LP\", \"correo\": \"juan@gmail.com\", \"status\": 1, \"materno\": \"MAMANI\", \"nombres\": \"JUAN\", \"paterno\": \"PERES\", \"role_id\": \"2\", \"usuario\": \"JPERES\", \"sucursal\": null, \"created_at\": \"2025-04-14T19:39:54.000000Z\", \"updated_at\": \"2025-04-14T20:34:01.000000Z\", \"sucursal_id\": null, \"fecha_registro\": \"2025-04-14\", \"sucursals_todo\": 1}', 'USUARIOS', '2025-04-14', '16:34:01', '2025-04-14 20:34:01', '2025-04-14 20:34:01'),
(17, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN USUARIO', '{\"ci\": \"555555\", \"id\": 4, \"foto\": \"41744660376.jpg\", \"acceso\": 1, \"ci_exp\": \"LP\", \"correo\": null, \"status\": 1, \"materno\": \"\", \"nombres\": \"MARCOS\", \"paterno\": \"CONDORI\", \"role_id\": 3, \"usuario\": \"MCONDORI\", \"sucursal\": {\"id\": 1, \"fonos\": \"777777 - 67676767\", \"codigo\": \"S001\", \"nombre\": \"SUCURSAL A\", \"status\": 1, \"user_id\": 3, \"status_t\": \"HABILITADO\", \"direccion\": \"LOS OLIVOS\", \"created_at\": \"2025-04-14T19:54:06.000000Z\", \"updated_at\": \"2025-04-14T19:58:15.000000Z\", \"fecha_registro\": \"2025-04-14\", \"fecha_registro_t\": \"14/04/2025\"}, \"created_at\": \"2025-04-14T19:52:56.000000Z\", \"updated_at\": \"2025-04-14T20:05:02.000000Z\", \"sucursal_id\": 1, \"fecha_registro\": \"2025-04-14\", \"sucursals_todo\": 0}', '{\"ci\": \"555555\", \"id\": 4, \"foto\": \"41744662862.jpg\", \"acceso\": \"1\", \"ci_exp\": \"LP\", \"correo\": null, \"status\": 1, \"materno\": \"\", \"nombres\": \"MARCOS\", \"paterno\": \"CONDORI\", \"role_id\": \"3\", \"usuario\": \"MCONDORI\", \"sucursal\": {\"id\": 1, \"fonos\": \"777777 - 67676767\", \"codigo\": \"S001\", \"nombre\": \"SUCURSAL A\", \"status\": 1, \"user_id\": 3, \"status_t\": \"HABILITADO\", \"direccion\": \"LOS OLIVOS\", \"created_at\": \"2025-04-14T19:54:06.000000Z\", \"updated_at\": \"2025-04-14T19:58:15.000000Z\", \"fecha_registro\": \"2025-04-14\", \"fecha_registro_t\": \"14/04/2025\"}, \"created_at\": \"2025-04-14T19:52:56.000000Z\", \"updated_at\": \"2025-04-14T20:34:22.000000Z\", \"sucursal_id\": \"1\", \"fecha_registro\": \"2025-04-14\", \"sucursals_todo\": 0}', 'USUARIOS', '2025-04-14', '16:34:22', '2025-04-14 20:34:22', '2025-04-14 20:34:22'),
(18, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PRODUCTO', '{\"id\": 16, \"nombre\": \"PRODUCTO A1\", \"precio_sf\": \"0\", \"created_at\": \"2025-04-17T20:07:08.000000Z\", \"precio_fac\": \"5\", \"precio_min\": \"170\", \"updated_at\": \"2025-04-17T20:07:08.000000Z\", \"descripcion\": \"PARECIDO AL PROD A\", \"precio_pred\": \"200\", \"stock_maximo\": \"50\", \"fecha_registro\": \"2025-04-17\"}', NULL, 'PRODUCTOS', '2025-04-17', '16:07:08', '2025-04-17 20:07:08', '2025-04-17 20:07:08'),
(19, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PRODUCTO', '{\"id\": 17, \"nombre\": \"PRODUCTO A2\", \"precio_sf\": \"0\", \"created_at\": \"2025-04-17T20:07:30.000000Z\", \"precio_fac\": \"6\", \"precio_min\": \"200\", \"updated_at\": \"2025-04-17T20:07:30.000000Z\", \"descripcion\": \"PARECIDO A PROD A Y A1\", \"precio_pred\": \"220\", \"stock_maximo\": \"60\", \"fecha_registro\": \"2025-04-17\"}', NULL, 'PRODUCTOS', '2025-04-17', '16:07:30', '2025-04-17 20:07:30', '2025-04-17 20:07:30'),
(20, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PRODUCTO', '{\"id\": 18, \"nombre\": \"PRODUCTO B\", \"precio_sf\": \"0\", \"created_at\": \"2025-04-17T20:07:53.000000Z\", \"precio_fac\": \"10\", \"precio_min\": \"90\", \"updated_at\": \"2025-04-17T20:07:53.000000Z\", \"descripcion\": \"DESC PROD B\", \"precio_pred\": \"100.5\", \"stock_maximo\": \"40\", \"fecha_registro\": \"2025-04-17\"}', NULL, 'PRODUCTOS', '2025-04-17', '16:07:53', '2025-04-17 20:07:53', '2025-04-17 20:07:53'),
(21, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN PRODUCTO', '{\"id\": 19, \"nombre\": \"PRODUCTO C\", \"precio_sf\": \"0\", \"created_at\": \"2025-04-17T20:08:15.000000Z\", \"precio_fac\": \"10\", \"precio_min\": \"48\", \"updated_at\": \"2025-04-17T20:08:15.000000Z\", \"descripcion\": \"DESC PROD C\", \"precio_pred\": \"50.9\", \"stock_maximo\": \"30\", \"fecha_registro\": \"2025-04-17\"}', NULL, 'PRODUCTOS', '2025-04-17', '16:08:15', '2025-04-17 20:08:15', '2025-04-17 20:08:15'),
(22, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO LA RELACIÓN DE UN PRODUCTO', '{\"id\": 2, \"created_at\": \"2025-04-17T20:56:15.000000Z\", \"updated_at\": \"2025-04-17T20:56:15.000000Z\", \"producto_id\": \"15\", \"producto_relacion\": \"16\"}', NULL, 'PRODUCTOS', '2025-04-17', '16:56:15', '2025-04-17 20:56:15', '2025-04-17 20:56:15'),
(23, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO LA RELACIÓN DE UN PRODUCTO', '{\"id\": 3, \"created_at\": \"2025-04-17T21:24:57.000000Z\", \"updated_at\": \"2025-04-17T21:24:57.000000Z\", \"producto_id\": \"15\", \"producto_relacion\": \"17\"}', NULL, 'PRODUCTOS', '2025-04-17', '17:24:57', '2025-04-17 21:24:57', '2025-04-17 21:24:57'),
(24, 1, 'ELIMINACIÓN', 'EL USUARIO admin ELIMINÓ UNA RELACIÓN DE PRODUCTO', '{\"id\": 3, \"created_at\": \"2025-04-17T21:24:57.000000Z\", \"updated_at\": \"2025-04-17T21:24:57.000000Z\", \"producto_id\": 15, \"producto_relacion\": 17}', NULL, 'PRODUCTOS', '2025-04-17', '17:36:37', '2025-04-17 21:36:37', '2025-04-17 21:36:37'),
(25, 1, 'ELIMINACIÓN', 'EL USUARIO admin ELIMINÓ UNA RELACIÓN DE PRODUCTO', '{\"id\": 2, \"created_at\": \"2025-04-17T20:56:15.000000Z\", \"updated_at\": \"2025-04-17T20:56:15.000000Z\", \"producto_id\": 15, \"producto_relacion\": 16}', NULL, 'PRODUCTOS', '2025-04-17', '17:38:25', '2025-04-17 21:38:25', '2025-04-17 21:38:25'),
(26, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO LA RELACIÓN DE UN PRODUCTO', '{\"id\": 4, \"created_at\": \"2025-04-17T21:39:14.000000Z\", \"updated_at\": \"2025-04-17T21:39:14.000000Z\", \"producto_id\": \"15\", \"producto_relacion\": \"16\"}', NULL, 'PRODUCTOS', '2025-04-17', '17:39:14', '2025-04-17 21:39:14', '2025-04-17 21:39:14'),
(27, 1, 'ELIMINACIÓN', 'EL USUARIO admin ELIMINÓ UNA RELACIÓN DE PRODUCTO', '{\"id\": 4, \"created_at\": \"2025-04-17T21:39:14.000000Z\", \"updated_at\": \"2025-04-17T21:39:14.000000Z\", \"producto_id\": 15, \"producto_relacion\": 16}', NULL, 'PRODUCTOS', '2025-04-17', '17:39:16', '2025-04-17 21:39:16', '2025-04-17 21:39:16'),
(28, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO LA RELACIÓN DE UN PRODUCTO', '{\"id\": 5, \"created_at\": \"2025-04-17T21:39:25.000000Z\", \"updated_at\": \"2025-04-17T21:39:25.000000Z\", \"producto_id\": \"15\", \"producto_relacion\": \"16\"}', NULL, 'PRODUCTOS', '2025-04-17', '17:39:25', '2025-04-17 21:39:25', '2025-04-17 21:39:25'),
(29, 1, 'ELIMINACIÓN', 'EL USUARIO admin ELIMINÓ UNA RELACIÓN DE PRODUCTO', '{\"id\": 5, \"created_at\": \"2025-04-17T21:39:25.000000Z\", \"updated_at\": \"2025-04-17T21:39:25.000000Z\", \"producto_id\": 15, \"producto_relacion\": 16}', NULL, 'PRODUCTOS', '2025-04-17', '17:39:30', '2025-04-17 21:39:30', '2025-04-17 21:39:30'),
(30, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO LA RELACIÓN DE UN PRODUCTO', '{\"id\": 6, \"created_at\": \"2025-04-17T21:39:58.000000Z\", \"updated_at\": \"2025-04-17T21:39:58.000000Z\", \"producto_id\": \"15\", \"producto_relacion\": \"16\"}', NULL, 'PRODUCTOS', '2025-04-17', '17:39:58', '2025-04-17 21:39:58', '2025-04-17 21:39:58'),
(31, 1, 'ELIMINACIÓN', 'EL USUARIO admin ELIMINÓ UNA RELACIÓN DE PRODUCTO', '{\"id\": 6, \"created_at\": \"2025-04-17T21:39:58.000000Z\", \"updated_at\": \"2025-04-17T21:39:58.000000Z\", \"producto_id\": 15, \"producto_relacion\": 16}', NULL, 'PRODUCTOS', '2025-04-17', '17:40:00', '2025-04-17 21:40:00', '2025-04-17 21:40:00'),
(32, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO LA RELACIÓN DE UN PRODUCTO', '{\"id\": 7, \"created_at\": \"2025-04-17T21:40:02.000000Z\", \"updated_at\": \"2025-04-17T21:40:02.000000Z\", \"producto_id\": \"15\", \"producto_relacion\": \"16\"}', NULL, 'PRODUCTOS', '2025-04-17', '17:40:02', '2025-04-17 21:40:02', '2025-04-17 21:40:02'),
(33, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO LA RELACIÓN DE UN PRODUCTO', '{\"id\": 8, \"created_at\": \"2025-04-17T21:40:07.000000Z\", \"updated_at\": \"2025-04-17T21:40:07.000000Z\", \"producto_id\": \"15\", \"producto_relacion\": \"17\"}', NULL, 'PRODUCTOS', '2025-04-17', '17:40:07', '2025-04-17 21:40:07', '2025-04-17 21:40:07'),
(34, 1, 'ELIMINACIÓN', 'EL USUARIO admin ELIMINÓ UNA RELACIÓN DE PRODUCTO', '{\"id\": 8, \"created_at\": \"2025-04-17T21:40:07.000000Z\", \"updated_at\": \"2025-04-17T21:40:07.000000Z\", \"producto_id\": 15, \"producto_relacion\": 17}', NULL, 'PRODUCTOS', '2025-04-17', '17:40:21', '2025-04-17 21:40:21', '2025-04-17 21:40:21'),
(35, 1, 'ELIMINACIÓN', 'EL USUARIO admin ELIMINÓ UNA RELACIÓN DE PRODUCTO', '{\"id\": 7, \"created_at\": \"2025-04-17T21:40:02.000000Z\", \"updated_at\": \"2025-04-17T21:40:02.000000Z\", \"producto_id\": 15, \"producto_relacion\": 16}', NULL, 'PRODUCTOS', '2025-04-17', '17:40:22', '2025-04-17 21:40:22', '2025-04-17 21:40:22'),
(36, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO LA RELACIÓN DE UN PRODUCTO', '{\"id\": 9, \"created_at\": \"2025-04-17T21:40:31.000000Z\", \"updated_at\": \"2025-04-17T21:40:31.000000Z\", \"producto_id\": \"15\", \"producto_relacion\": \"16\"}', NULL, 'PRODUCTOS', '2025-04-17', '17:40:31', '2025-04-17 21:40:31', '2025-04-17 21:40:31'),
(37, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO LA RELACIÓN DE UN PRODUCTO', '{\"id\": 10, \"created_at\": \"2025-04-17T21:40:34.000000Z\", \"updated_at\": \"2025-04-17T21:40:34.000000Z\", \"producto_id\": \"15\", \"producto_relacion\": \"17\"}', NULL, 'PRODUCTOS', '2025-04-17', '17:40:34', '2025-04-17 21:40:34', '2025-04-17 21:40:34'),
(38, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA UBICACIÓN DE PRODUCTO', '{\"id\": 1, \"lugar\": \"LUGAR A\", \"created_at\": \"2025-04-17T22:10:25.000000Z\", \"updated_at\": \"2025-04-17T22:10:25.000000Z\", \"numero_filas\": \"1\", \"fecha_registro\": \"2025-04-17\"}', NULL, 'UBICACIÓN DE PRODUCTOS', '2025-04-17', '18:10:25', '2025-04-17 22:10:25', '2025-04-17 22:10:25'),
(39, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA UBICACIÓN DE PRODUCTO', '{\"id\": 2, \"lugar\": \"LUGAR B\", \"created_at\": \"2025-04-17T22:11:05.000000Z\", \"updated_at\": \"2025-04-17T22:11:05.000000Z\", \"numero_filas\": \"2\", \"fecha_registro\": \"2025-04-17\"}', NULL, 'UBICACIÓN DE PRODUCTOS', '2025-04-17', '18:11:05', '2025-04-17 22:11:05', '2025-04-17 22:11:05'),
(40, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UNA UBICACIÓN DE PRODUCTO', '{\"id\": 2, \"lugar\": \"LUGAR B\", \"status\": 1, \"created_at\": \"2025-04-17T22:11:05.000000Z\", \"updated_at\": \"2025-04-17T22:11:05.000000Z\", \"numero_filas\": 2, \"fecha_registro\": \"2025-04-17\"}', '{\"id\": 2, \"lugar\": \"LUGAR B\", \"status\": 1, \"created_at\": \"2025-04-17T22:11:05.000000Z\", \"updated_at\": \"2025-04-17T22:11:11.000000Z\", \"numero_filas\": \"1\", \"fecha_registro\": \"2025-04-17\"}', 'UBICACIÓN DE PRODUCTOS', '2025-04-17', '18:11:11', '2025-04-17 22:11:11', '2025-04-17 22:11:11'),
(41, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA UBICACIÓN DE PRODUCTO', '{\"id\": 3, \"lugar\": \"LUGAR A\", \"created_at\": \"2025-04-17T22:11:29.000000Z\", \"updated_at\": \"2025-04-17T22:11:29.000000Z\", \"numero_filas\": \"2\", \"fecha_registro\": \"2025-04-17\"}', NULL, 'UBICACIÓN DE PRODUCTOS', '2025-04-17', '18:11:29', '2025-04-17 22:11:29', '2025-04-17 22:11:29'),
(42, 1, 'ELIMINACIÓN', 'EL USUARIO admin ELIMINÓ UNA UBICACIÓN DE PRODUCTO', '{\"id\": 3, \"lugar\": \"LUGAR A\", \"status\": 1, \"created_at\": \"2025-04-17T22:11:29.000000Z\", \"updated_at\": \"2025-04-17T22:11:29.000000Z\", \"numero_filas\": 2, \"fecha_registro\": \"2025-04-17\"}', NULL, 'UBICACIÓN DE PRODUCTOS', '2025-04-17', '18:11:53', '2025-04-17 22:11:53', '2025-04-17 22:11:53'),
(43, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN INGRESO DE PRODUCTOS', '{\"id\": 1, \"created_at\": \"2025-04-19T14:24:46.000000Z\", \"updated_at\": \"2025-04-19T14:24:46.000000Z\", \"descripcion\": \"\", \"sucursal_id\": \"1\", \"fecha_registro\": \"2025-04-19\", \"ingreso_detalles\": [{\"id\": 1, \"status\": 1, \"cantidad\": 10, \"created_at\": \"2025-04-19T14:24:46.000000Z\", \"updated_at\": \"2025-04-19T14:24:46.000000Z\", \"descripcion\": \"DESCRIPCION INGRESO PRODUCTO\", \"producto_id\": 15, \"fecha_registro\": \"2025-04-19\", \"fecha_registro_t\": \"19/04/2025\", \"fecha_vencimiento\": \"2027-01-01\", \"fecha_vencimiento_t\": \"01/01/2027\", \"ingreso_producto_id\": 1, \"ubicacion_producto_id\": 1}, {\"id\": 2, \"status\": 1, \"cantidad\": 20, \"created_at\": \"2025-04-19T14:24:46.000000Z\", \"updated_at\": \"2025-04-19T14:24:46.000000Z\", \"descripcion\": \"\", \"producto_id\": 16, \"fecha_registro\": \"2025-04-19\", \"fecha_registro_t\": \"19/04/2025\", \"fecha_vencimiento\": \"2027-01-01\", \"fecha_vencimiento_t\": \"01/01/2027\", \"ingreso_producto_id\": 1, \"ubicacion_producto_id\": 1}]}', NULL, 'INGRESO DE PRODUCTOS', '2025-04-19', '10:24:46', '2025-04-19 14:24:46', '2025-04-19 14:24:46'),
(44, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN INGRESO DE PRODUCTOS', '{\"id\": 1, \"status\": 1, \"created_at\": \"2025-04-19T14:24:46.000000Z\", \"updated_at\": \"2025-04-19T14:24:46.000000Z\", \"descripcion\": \"\", \"sucursal_id\": 1, \"fecha_registro\": \"2025-04-19\", \"ingreso_detalles\": [{\"id\": 1, \"status\": 1, \"cantidad\": 10, \"created_at\": \"2025-04-19T14:24:46.000000Z\", \"updated_at\": \"2025-04-19T14:24:46.000000Z\", \"descripcion\": \"DESCRIPCION INGRESO PRODUCTO\", \"producto_id\": 15, \"fecha_registro\": \"2025-04-19\", \"fecha_registro_t\": \"19/04/2025\", \"fecha_vencimiento\": \"2027-01-01\", \"fecha_vencimiento_t\": \"01/01/2027\", \"ingreso_producto_id\": 1, \"ubicacion_producto_id\": 1}, {\"id\": 2, \"status\": 1, \"cantidad\": 20, \"created_at\": \"2025-04-19T14:24:46.000000Z\", \"updated_at\": \"2025-04-19T14:24:46.000000Z\", \"descripcion\": \"\", \"producto_id\": 16, \"fecha_registro\": \"2025-04-19\", \"fecha_registro_t\": \"19/04/2025\", \"fecha_vencimiento\": \"2027-01-01\", \"fecha_vencimiento_t\": \"01/01/2027\", \"ingreso_producto_id\": 1, \"ubicacion_producto_id\": 1}]}', '{\"id\": 1, \"status\": 1, \"created_at\": \"2025-04-19T14:24:46.000000Z\", \"updated_at\": \"2025-04-19T14:24:46.000000Z\", \"descripcion\": \"\", \"sucursal_id\": \"1\", \"fecha_registro\": \"2025-04-19\", \"ingreso_detalles\": [{\"id\": 1, \"status\": 1, \"cantidad\": 13, \"created_at\": \"2025-04-19T14:24:46.000000Z\", \"updated_at\": \"2025-04-19T14:30:51.000000Z\", \"descripcion\": \"DESCRIPCION INGRESO PRODUCTO\", \"producto_id\": 15, \"fecha_registro\": \"2025-04-19\", \"fecha_registro_t\": \"19/04/2025\", \"fecha_vencimiento\": \"2027-01-01\", \"fecha_vencimiento_t\": \"01/01/2027\", \"ingreso_producto_id\": 1, \"ubicacion_producto_id\": 1}, {\"id\": 2, \"status\": 1, \"cantidad\": 20, \"created_at\": \"2025-04-19T14:24:46.000000Z\", \"updated_at\": \"2025-04-19T14:24:46.000000Z\", \"descripcion\": \"\", \"producto_id\": 16, \"fecha_registro\": \"2025-04-19\", \"fecha_registro_t\": \"19/04/2025\", \"fecha_vencimiento\": \"2027-01-01\", \"fecha_vencimiento_t\": \"01/01/2027\", \"ingreso_producto_id\": 1, \"ubicacion_producto_id\": 1}]}', 'INGRESO DE PRODUCTOS', '2025-04-19', '10:30:51', '2025-04-19 14:30:51', '2025-04-19 14:30:51'),
(45, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO LA RELACIÓN DE UN PRODUCTO', '{\"id\": 11, \"created_at\": \"2025-04-19T14:31:00.000000Z\", \"updated_at\": \"2025-04-19T14:31:00.000000Z\", \"producto_id\": \"16\", \"producto_relacion\": \"15\"}', NULL, 'PRODUCTOS', '2025-04-19', '10:31:00', '2025-04-19 14:31:00', '2025-04-19 14:31:00'),
(46, 4, 'CREACIÓN', 'EL USUARIO MCONDORI REGISTRO UN INGRESO DE PRODUCTOS', '{\"id\": 2, \"created_at\": \"2025-04-19T14:47:48.000000Z\", \"updated_at\": \"2025-04-19T14:47:48.000000Z\", \"descripcion\": \"\", \"sucursal_id\": \"1\", \"fecha_registro\": \"2025-04-19\", \"ingreso_detalles\": [{\"id\": 3, \"status\": 1, \"cantidad\": 30, \"created_at\": \"2025-04-19T14:47:48.000000Z\", \"updated_at\": \"2025-04-19T14:47:48.000000Z\", \"descripcion\": \"\", \"producto_id\": 17, \"fecha_registro\": \"2025-04-19\", \"fecha_registro_t\": \"19/04/2025\", \"fecha_vencimiento\": null, \"fecha_vencimiento_t\": \"\", \"ingreso_producto_id\": 2, \"ubicacion_producto_id\": 2}]}', NULL, 'INGRESO DE PRODUCTOS', '2025-04-19', '10:47:48', '2025-04-19 14:47:48', '2025-04-19 14:47:48'),
(47, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA SALIDA DE PRODUCTOS', '{\"id\": 1, \"cantidad\": \"1\", \"created_at\": \"2025-04-19T15:20:22.000000Z\", \"updated_at\": \"2025-04-19T15:20:22.000000Z\", \"descripcion\": \"\", \"producto_id\": \"15\", \"sucursal_id\": \"1\", \"fecha_registro\": \"2025-04-19\"}', NULL, 'SALIDA DE PRODUCTOS', '2025-04-19', '11:20:22', '2025-04-19 15:20:22', '2025-04-19 15:20:22'),
(48, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UNA SALIDA DE PRODUCTOS', '{\"id\": 1, \"status\": 1, \"cantidad\": 1, \"created_at\": \"2025-04-19T15:20:22.000000Z\", \"updated_at\": \"2025-04-19T15:20:22.000000Z\", \"descripcion\": \"\", \"producto_id\": 15, \"sucursal_id\": 1, \"fecha_registro\": \"2025-04-19\"}', '{\"id\": 1, \"status\": 1, \"cantidad\": \"1000\", \"created_at\": \"2025-04-19T15:20:22.000000Z\", \"updated_at\": \"2025-04-19T15:25:19.000000Z\", \"descripcion\": \"\", \"producto_id\": \"15\", \"sucursal_id\": \"1\", \"fecha_registro\": \"2025-04-19\"}', 'SALIDA DE PRODUCTOS', '2025-04-19', '11:25:19', '2025-04-19 15:25:19', '2025-04-19 15:25:19'),
(49, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UNA SALIDA DE PRODUCTOS', '{\"id\": 1, \"status\": 1, \"cantidad\": 1000, \"created_at\": \"2025-04-19T15:20:22.000000Z\", \"updated_at\": \"2025-04-19T15:25:19.000000Z\", \"descripcion\": \"\", \"producto_id\": 15, \"sucursal_id\": 1, \"fecha_registro\": \"2025-04-19\"}', '{\"id\": 1, \"status\": 1, \"cantidad\": \"1\", \"created_at\": \"2025-04-19T15:20:22.000000Z\", \"updated_at\": \"2025-04-19T15:26:46.000000Z\", \"descripcion\": \"\", \"producto_id\": \"15\", \"sucursal_id\": \"1\", \"fecha_registro\": \"2025-04-19\"}', 'SALIDA DE PRODUCTOS', '2025-04-19', '11:26:46', '2025-04-19 15:26:46', '2025-04-19 15:26:46'),
(50, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UNA SALIDA DE PRODUCTOS', '{\"id\": 1, \"status\": 1, \"cantidad\": 1, \"created_at\": \"2025-04-19T15:20:22.000000Z\", \"updated_at\": \"2025-04-19T15:26:46.000000Z\", \"descripcion\": \"\", \"producto_id\": 15, \"sucursal_id\": 1, \"fecha_registro\": \"2025-04-19\"}', '{\"id\": 1, \"status\": 1, \"cantidad\": \"2\", \"created_at\": \"2025-04-19T15:20:22.000000Z\", \"updated_at\": \"2025-04-19T15:27:48.000000Z\", \"descripcion\": \"\", \"producto_id\": \"15\", \"sucursal_id\": \"1\", \"fecha_registro\": \"2025-04-19\"}', 'SALIDA DE PRODUCTOS', '2025-04-19', '11:27:48', '2025-04-19 15:27:48', '2025-04-19 15:27:48'),
(51, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UNA SALIDA DE PRODUCTOS', '{\"id\": 1, \"status\": 1, \"cantidad\": 2, \"created_at\": \"2025-04-19T15:20:22.000000Z\", \"updated_at\": \"2025-04-19T15:27:48.000000Z\", \"descripcion\": \"\", \"producto_id\": 15, \"sucursal_id\": 1, \"fecha_registro\": \"2025-04-19\"}', '{\"id\": 1, \"status\": 1, \"cantidad\": \"1\", \"created_at\": \"2025-04-19T15:20:22.000000Z\", \"updated_at\": \"2025-04-19T15:37:26.000000Z\", \"descripcion\": \"\", \"producto_id\": \"15\", \"sucursal_id\": \"1\", \"fecha_registro\": \"2025-04-19\"}', 'SALIDA DE PRODUCTOS', '2025-04-19', '11:37:26', '2025-04-19 15:37:26', '2025-04-19 15:37:26'),
(52, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN INGRESO DE PRODUCTOS', '{\"id\": 4, \"created_at\": \"2025-04-19T15:39:44.000000Z\", \"updated_at\": \"2025-04-19T15:39:44.000000Z\", \"descripcion\": \"\", \"sucursal_id\": \"2\", \"fecha_registro\": \"2025-04-19\", \"ingreso_detalles\": [{\"id\": 5, \"status\": 1, \"cantidad\": 20, \"created_at\": \"2025-04-19T15:39:44.000000Z\", \"updated_at\": \"2025-04-19T15:39:44.000000Z\", \"descripcion\": \"INGRESO PRODUCTO A EN SUCURSAL B\", \"producto_id\": 15, \"fecha_registro\": \"2025-04-19\", \"fecha_registro_t\": \"19/04/2025\", \"fecha_vencimiento\": \"2027-01-01\", \"fecha_vencimiento_t\": \"01/01/2027\", \"ingreso_producto_id\": 4, \"ubicacion_producto_id\": 1}]}', NULL, 'INGRESO DE PRODUCTOS', '2025-04-19', '11:39:44', '2025-04-19 15:39:44', '2025-04-19 15:39:44'),
(53, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UNA SALIDA DE PRODUCTOS', '{\"id\": 1, \"status\": 1, \"cantidad\": 1, \"created_at\": \"2025-04-19T15:20:22.000000Z\", \"updated_at\": \"2025-04-19T15:37:26.000000Z\", \"descripcion\": \"\", \"producto_id\": 15, \"sucursal_id\": 1, \"fecha_registro\": \"2025-04-19\"}', '{\"id\": 1, \"status\": 1, \"cantidad\": \"1\", \"created_at\": \"2025-04-19T15:20:22.000000Z\", \"updated_at\": \"2025-04-19T15:39:54.000000Z\", \"descripcion\": \"\", \"producto_id\": \"15\", \"sucursal_id\": \"2\", \"fecha_registro\": \"2025-04-19\"}', 'SALIDA DE PRODUCTOS', '2025-04-19', '11:39:54', '2025-04-19 15:39:54', '2025-04-19 15:39:54'),
(54, 1, 'ELIMINACIÓN', 'EL USUARIO admin ELIMINÓ UNA SALIDA DE PRODUCTOS', '{\"id\": 1, \"status\": 1, \"cantidad\": 1, \"created_at\": \"2025-04-19T15:20:22.000000Z\", \"updated_at\": \"2025-04-19T15:39:54.000000Z\", \"descripcion\": \"\", \"producto_id\": 15, \"sucursal_id\": 2, \"fecha_registro\": \"2025-04-19\"}', '{\"id\": 1, \"status\": 0, \"cantidad\": 1, \"created_at\": \"2025-04-19T15:20:22.000000Z\", \"updated_at\": \"2025-04-19T15:40:20.000000Z\", \"descripcion\": \"\", \"producto_id\": 15, \"sucursal_id\": 2, \"fecha_registro\": \"2025-04-19\"}', 'SALIDA DE PRODUCTOS', '2025-04-19', '11:40:20', '2025-04-19 15:40:20', '2025-04-19 15:40:20');
INSERT INTO `historial_accions` (`id`, `user_id`, `accion`, `descripcion`, `datos_original`, `datos_nuevo`, `modulo`, `fecha`, `hora`, `created_at`, `updated_at`) VALUES
(55, 1, 'ELIMINACIÓN', 'EL USUARIO admin ELIMINÓ UNA SALIDA DE PRODUCTOS', '{\"id\": 1, \"status\": 1, \"cantidad\": 1, \"created_at\": \"2025-04-19T15:20:22.000000Z\", \"updated_at\": \"2025-04-19T15:40:20.000000Z\", \"descripcion\": \"\", \"producto_id\": 15, \"sucursal_id\": 2, \"fecha_registro\": \"2025-04-19\"}', '{\"id\": 1, \"status\": 0, \"cantidad\": 1, \"producto\": {\"id\": 15, \"foto\": \"151744662823.png\", \"nombre\": \"PRODUCTO A\", \"status\": 1, \"foto_b64\": \"data:image/png;base64,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\", \"monto_cf\": 220, \"monto_sf\": 200, \"url_foto\": \"http://multident.test/imgs/productos/151744662823.png\", \"precio_sf\": \"0.00\", \"created_at\": \"2025-04-14T20:26:05.000000Z\", \"precio_fac\": \"10.00\", \"precio_min\": \"170.00\", \"updated_at\": \"2025-04-14T20:33:43.000000Z\", \"descripcion\": \"DESCRIPCION PRODUCTO\", \"precio_pred\": \"200.00\", \"stock_maximo\": 50, \"fecha_registro\": \"2025-04-14\", \"fecha_registro_t\": \"14/04/2025\"}, \"created_at\": \"2025-04-19T15:20:22.000000Z\", \"updated_at\": \"2025-04-19T15:42:33.000000Z\", \"descripcion\": \"\", \"producto_id\": 15, \"sucursal_id\": 2, \"fecha_registro\": \"2025-04-19\"}', 'SALIDA DE PRODUCTOS', '2025-04-19', '11:42:33', '2025-04-19 15:42:33', '2025-04-19 15:42:33'),
(56, 4, 'CREACIÓN', 'EL USUARIO MCONDORI REGISTRO UNA SALIDA DE PRODUCTOS', '{\"id\": 2, \"cantidad\": \"1\", \"created_at\": \"2025-04-19T16:10:18.000000Z\", \"updated_at\": \"2025-04-19T16:10:18.000000Z\", \"descripcion\": \"\", \"producto_id\": \"15\", \"sucursal_id\": \"1\", \"fecha_registro\": \"2025-04-19\"}', NULL, 'SALIDA DE PRODUCTOS', '2025-04-19', '12:10:18', '2025-04-19 16:10:18', '2025-04-19 16:10:18'),
(57, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN CLIENTE', '{\"ci\": \"3222323\", \"id\": 1, \"cel\": \"767766767\", \"nombres\": \"MARIA\", \"apellidos\": \"GONZALES MAMANI\", \"created_at\": \"2025-04-19T16:32:37.000000Z\", \"updated_at\": \"2025-04-19T16:32:37.000000Z\", \"descripcion\": \"DESCRIPCION CLIENTE 1\", \"fecha_registro\": \"2025-04-19\"}', NULL, 'CLIENTES', '2025-04-19', '12:32:37', '2025-04-19 16:32:37', '2025-04-19 16:32:37'),
(58, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UN CLIENTE', '{\"ci\": \"\", \"id\": 2, \"cel\": \"\", \"nombres\": \"PEDRO\", \"apellidos\": \"\", \"created_at\": \"2025-04-19T16:34:02.000000Z\", \"updated_at\": \"2025-04-19T16:34:02.000000Z\", \"descripcion\": \"\", \"fecha_registro\": \"2025-04-19\"}', NULL, 'CLIENTES', '2025-04-19', '12:34:02', '2025-04-19 16:34:02', '2025-04-19 16:34:02'),
(59, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN CLIENTE', '{\"ci\": \"\", \"id\": 2, \"cel\": \"\", \"status\": 1, \"nombres\": \"PEDRO\", \"apellidos\": \"\", \"created_at\": \"2025-04-19T16:34:02.000000Z\", \"updated_at\": \"2025-04-19T16:34:02.000000Z\", \"descripcion\": \"\", \"fecha_registro\": \"2025-04-19\"}', '{\"ci\": \"BBB\", \"id\": 2, \"cel\": \"CC\", \"status\": 1, \"nombres\": \"PEDRO\", \"apellidos\": \"AAA\", \"created_at\": \"2025-04-19T16:34:02.000000Z\", \"updated_at\": \"2025-04-19T16:34:12.000000Z\", \"descripcion\": \"DD\", \"fecha_registro\": \"2025-04-19\"}', 'CLIENTES', '2025-04-19', '12:34:12', '2025-04-19 16:34:12', '2025-04-19 16:34:12'),
(60, 1, 'ELIMINACIÓN', 'EL USUARIO admin ELIMINÓ UN CLIENTE', '{\"ci\": \"BBB\", \"id\": 2, \"cel\": \"CC\", \"status\": 1, \"nombres\": \"PEDRO\", \"apellidos\": \"AAA\", \"created_at\": \"2025-04-19T16:34:02.000000Z\", \"updated_at\": \"2025-04-19T16:34:12.000000Z\", \"descripcion\": \"DD\", \"fecha_registro\": \"2025-04-19\"}', NULL, 'CLIENTES', '2025-04-19', '12:34:45', '2025-04-19 16:34:45', '2025-04-19 16:34:45'),
(61, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UN CLIENTE', '{\"ci\": \"BBB\", \"id\": 2, \"cel\": \"CC\", \"status\": 1, \"nombres\": \"PEDRO\", \"apellidos\": \"AAA\", \"created_at\": \"2025-04-19T16:34:02.000000Z\", \"updated_at\": \"2025-04-19T16:34:45.000000Z\", \"descripcion\": \"DD\", \"fecha_registro\": \"2025-04-19\"}', '{\"ci\": \"0\", \"id\": 2, \"cel\": \"\", \"status\": 1, \"nombres\": \"PEDRO\", \"apellidos\": \"RAMIRES\", \"created_at\": \"2025-04-19T16:34:02.000000Z\", \"updated_at\": \"2025-04-19T16:35:19.000000Z\", \"descripcion\": \"\", \"fecha_registro\": \"2025-04-19\"}', 'CLIENTES', '2025-04-19', '12:35:19', '2025-04-19 16:35:19', '2025-04-19 16:35:19'),
(62, 1, 'CREACIÓN', 'EL USUARIO admin REGISTRO UNA PROMOCIÓN', '{\"id\": 1, \"fecha_fin\": \"2025-04-30\", \"fecha_ini\": \"2025-04-22\", \"created_at\": \"2025-04-19T16:58:04.000000Z\", \"porcentaje\": \"10\", \"updated_at\": \"2025-04-19T16:58:04.000000Z\", \"descripcion\": \"PRIMERA PROMOCION\", \"producto_id\": \"16\", \"fecha_registro\": \"2025-04-19\"}', NULL, 'PROMOCIONES', '2025-04-19', '12:58:04', '2025-04-19 16:58:04', '2025-04-19 16:58:04'),
(63, 1, 'MODIFICACIÓN', 'EL USUARIO admin ACTUALIZÓ UNA PROMOCIÓN', '{\"id\": 1, \"fecha_fin\": \"2025-04-30\", \"fecha_ini\": \"2025-04-22\", \"created_at\": \"2025-04-19T16:58:04.000000Z\", \"porcentaje\": 10, \"updated_at\": \"2025-04-19T16:58:04.000000Z\", \"descripcion\": \"PRIMERA PROMOCION\", \"producto_id\": 16, \"fecha_registro\": \"2025-04-19\"}', '{\"id\": 1, \"fecha_fin\": \"2025-05-01\", \"fecha_ini\": \"2025-04-22\", \"created_at\": \"2025-04-19T16:58:04.000000Z\", \"porcentaje\": \"8\", \"updated_at\": \"2025-04-19T17:00:31.000000Z\", \"descripcion\": \"PRIMERA PROMOCION\", \"producto_id\": \"16\", \"fecha_registro\": \"2025-04-19\"}', 'PROMOCIONES', '2025-04-19', '13:00:31', '2025-04-19 17:00:31', '2025-04-19 17:00:31'),
(64, 1, 'ELIMINACIÓN', 'EL USUARIO admin ELIMINÓ UNA PROMOCIÓN', '{\"id\": 1, \"fecha_fin\": \"2025-05-01\", \"fecha_ini\": \"2025-04-22\", \"created_at\": \"2025-04-19T16:58:04.000000Z\", \"porcentaje\": 8, \"updated_at\": \"2025-04-19T17:00:31.000000Z\", \"descripcion\": \"PRIMERA PROMOCION\", \"producto_id\": 16, \"fecha_registro\": \"2025-04-19\"}', NULL, 'PROMOCIONES', '2025-04-19', '13:01:04', '2025-04-19 17:01:04', '2025-04-19 17:01:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso_detalles`
--

CREATE TABLE `ingreso_detalles` (
  `id` bigint UNSIGNED NOT NULL,
  `ingreso_producto_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `cantidad` double NOT NULL,
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

INSERT INTO `ingreso_detalles` (`id`, `ingreso_producto_id`, `producto_id`, `cantidad`, `ubicacion_producto_id`, `fecha_vencimiento`, `descripcion`, `fecha_registro`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 15, 13, 1, '2027-01-01', 'DESCRIPCION INGRESO PRODUCTO', '2025-04-19', 1, '2025-04-19 14:24:46', '2025-04-19 14:30:51'),
(2, 1, 16, 20, 1, '2027-01-01', '', '2025-04-19', 1, '2025-04-19 14:24:46', '2025-04-19 14:24:46'),
(3, 2, 17, 30, 2, NULL, '', '2025-04-19', 1, '2025-04-19 14:47:48', '2025-04-19 14:47:48'),
(5, 4, 15, 20, 1, '2027-01-01', 'INGRESO PRODUCTO A EN SUCURSAL B', '2025-04-19', 1, '2025-04-19 15:39:44', '2025-04-19 15:39:44');

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
(1, 1, '2025-04-19', '', 1, '2025-04-19 14:24:46', '2025-04-19 14:24:46'),
(2, 1, '2025-04-19', '', 1, '2025-04-19 14:47:48', '2025-04-19 14:47:48'),
(4, 2, '2025-04-19', '', 1, '2025-04-19 15:39:44', '2025-04-19 15:39:44');

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
(1, 1, 'INGRESO DE PRODUCTO', 1, 'IngresoDetalle', 15, 'VALOR INICIAL', 200.00, 'INGRESO', 13, NULL, 13, 200.00, 2600.00, NULL, 2600.00, '2025-04-19', 1, '2025-04-19 14:24:46', '2025-04-19 14:30:51'),
(2, 1, 'INGRESO DE PRODUCTO', 2, 'IngresoDetalle', 16, 'VALOR INICIAL', 200.00, 'INGRESO', 20, NULL, 20, 200.00, 4000.00, NULL, 4000.00, '2025-04-19', 1, '2025-04-19 14:24:46', '2025-04-19 14:30:51'),
(3, 1, 'INGRESO DE PRODUCTO', 3, 'IngresoDetalle', 17, 'VALOR INICIAL', 220.00, 'INGRESO', 30, NULL, 30, 220.00, 6600.00, NULL, 6600.00, '2025-04-19', 1, '2025-04-19 14:47:48', '2025-04-19 14:47:48'),
(4, 1, 'SALIDA DE PRODUCTO', 1, 'SalidaProducto', 15, 'SALIDA DE PRODUCTO', 200.00, 'EGRESO', NULL, 1, 12, 200.00, NULL, 200.00, 2400.00, '2025-04-19', 1, '2025-04-19 15:20:22', '2025-04-19 15:39:54'),
(6, 2, 'INGRESO DE PRODUCTO', 5, 'IngresoDetalle', 15, 'VALOR INICIAL', 200.00, 'INGRESO', 20, NULL, 20, 200.00, 4000.00, NULL, 4000.00, '2025-04-19', 1, '2025-04-19 15:39:44', '2025-04-19 15:39:54'),
(7, 2, 'SALIDA DE PRODUCTO', 1, 'SalidaProducto', 15, 'ELIMINACIÓN DE SALIDA DE PRODUCTO', 200.00, 'INGRESO', 1, NULL, 21, 200.00, 200.00, NULL, 4200.00, '2025-04-19', 1, '2025-04-19 15:42:33', '2025-04-19 15:42:33'),
(8, 1, 'SALIDA DE PRODUCTO', 2, 'SalidaProducto', 15, 'SALIDA DE PRODUCTO', 200.00, 'EGRESO', NULL, 1, 11, 200.00, NULL, 200.00, 2200.00, '2025-04-19', 1, '2025-04-19 16:10:18', '2025-04-19 16:10:18');

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
(24, '2025_04_18_101317_create_kardex_productos_table', 3);

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
  `hora` date NOT NULL,
  `tipo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `registro_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion_users`
--

CREATE TABLE `notificacion_users` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `notificacion_id` bigint UNSIGNED NOT NULL,
  `visto` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_ventas`
--

CREATE TABLE `orden_ventas` (
  `id` bigint UNSIGNED NOT NULL,
  `nro` bigint NOT NULL,
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `factura` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_pago` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` date DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(9, 3, 29, '2025-04-19 16:10:01', '2025-04-19 16:10:01');

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
(15, 'PRODUCTO A', 'DESCRIPCION PRODUCTO', 200.00, 170.00, 10.00, 0.00, 50, '151744662823.png', '2025-04-14', 1, '2025-04-14 20:26:05', '2025-04-14 20:33:43'),
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
(1, 1, 15, 12, '2025-04-19 14:24:46', '2025-04-19 16:10:18'),
(2, 1, 16, 20, '2025-04-19 14:24:46', '2025-04-19 14:30:51'),
(3, 1, 17, 30, '2025-04-19 14:47:48', '2025-04-19 14:47:48'),
(5, 2, 15, 20, '2025-04-19 15:39:44', '2025-04-19 15:42:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proformas`
--

CREATE TABLE `proformas` (
  `id` bigint UNSIGNED NOT NULL,
  `nro` bigint NOT NULL,
  `sucursal_id` bigint UNSIGNED NOT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
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

--
-- Volcado de datos para la tabla `salida_productos`
--

INSERT INTO `salida_productos` (`id`, `sucursal_id`, `producto_id`, `cantidad`, `descripcion`, `fecha_registro`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 15, 1, '', '2025-04-19', 0, '2025-04-19 15:20:22', '2025-04-19 15:42:33'),
(2, 1, 15, 1, '', '2025-04-19', 1, '2025-04-19 16:10:18', '2025-04-19 16:10:18');

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_proformas`
--
ALTER TABLE `detalle_proformas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `devolucions`
--
ALTER TABLE `devolucions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_accions`
--
ALTER TABLE `historial_accions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `ingreso_detalles`
--
ALTER TABLE `ingreso_detalles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ingreso_productos`
--
ALTER TABLE `ingreso_productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `kardex_productos`
--
ALTER TABLE `kardex_productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `notificacions`
--
ALTER TABLE `notificacions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificacion_users`
--
ALTER TABLE `notificacion_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `orden_ventas`
--
ALTER TABLE `orden_ventas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `proformas`
--
ALTER TABLE `proformas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `promocions`
--
ALTER TABLE `promocions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `salida_productos`
--
ALTER TABLE `salida_productos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
