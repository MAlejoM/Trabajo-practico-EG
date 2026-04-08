-- ============================================================
-- init.sql - Veterinaria San Anton
-- Generado: 2026-03-14
-- Inicializa la base de datos completa desde cero.
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ============================================================
-- TABLA: roles
-- Roles del personal (Veterinario, Recepcionista, etc.)
-- ============================================================
CREATE TABLE `roles` (
  `id`     INT(11)      NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'admin'),
(2, 'Veterinario');

-- ============================================================
-- TABLA: usuarios
-- Usuarios del sistema (Personal y Clientes comparten esta tabla)
-- ============================================================
CREATE TABLE `usuarios` (
  `id`                   INT(11)      NOT NULL AUTO_INCREMENT,
  `email`                VARCHAR(191) NOT NULL UNIQUE,
  `nombre`               VARCHAR(100) NOT NULL,
  `apellido`             VARCHAR(100) NOT NULL,
  `clave`                VARCHAR(300) NOT NULL,
  `activo`               TINYINT(1)   NOT NULL DEFAULT 1,
  `token_recuperacion`   TEXT         DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Usuarios iniciales — contraseña para todos: "123123"
INSERT INTO `usuarios` (`id`, `email`, `nombre`, `apellido`, `clave`, `activo`) VALUES
(1, 'admin@gmail.com',       'Admin',  'Sistema', '$2y$10$ovLEXZUeufcjpT8UsS2lze0yCtMRCRoHAzcVnMjC8O74IUmf6DuvC', 1),
(2, 'veterinario@gmail.com', 'Carlos', 'Lopez',   '$2y$10$ovLEXZUeufcjpT8UsS2lze0yCtMRCRoHAzcVnMjC8O74IUmf6DuvC', 1),
(3, 'cliente@gmail.com',     'Maria',  'Garcia',  '$2y$10$ovLEXZUeufcjpT8UsS2lze0yCtMRCRoHAzcVnMjC8O74IUmf6DuvC', 1);

-- ============================================================
-- TABLA: personal
-- Personal de la clínica, vinculados a un usuario y un rol
-- ============================================================
CREATE TABLE `personal` (
  `id`        INT(11) NOT NULL AUTO_INCREMENT,
  `usuarioId` INT(11) NOT NULL,
  `rolId`     INT(11) NOT NULL,
  `activo`    TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_personal_usuario` FOREIGN KEY (`usuarioId`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_personal_rol`     FOREIGN KEY (`rolId`)     REFERENCES `roles`    (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- admin → rol admin (1), veterinario → rol Veterinario (2)
INSERT INTO `personal` (`id`, `usuarioId`, `rolId`, `activo`) VALUES
(1, 1, 1, 1),
(2, 2, 2, 1);

-- ============================================================
-- TABLA: clientes
-- Clientes de la clínica, vinculados a un usuario
-- ============================================================
CREATE TABLE `clientes` (
  `id`        INT(11)      NOT NULL AUTO_INCREMENT,
  `usuarioId` INT(11)      NOT NULL,
  `ciudad`    VARCHAR(100) DEFAULT NULL,
  `telefono`  VARCHAR(20)  DEFAULT NULL,
  `direccion` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_clientes_usuario` FOREIGN KEY (`usuarioId`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- cliente@gmail.com es cliente (usuarioId = 3)
INSERT INTO `clientes` (`id`, `usuarioId`, `ciudad`, `telefono`, `direccion`) VALUES
(1, 3, 'Buenos Aires', '1122334455', 'Av. Siempreviva 742');

-- ============================================================
-- TABLA: mascotas
-- Mascotas de los clientes
-- ============================================================
CREATE TABLE `mascotas` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `clienteId`  INT(11)      NOT NULL,
  `nombre`     VARCHAR(100) NOT NULL,
  `foto`       LONGBLOB     DEFAULT NULL,
  `raza`       VARCHAR(100) DEFAULT NULL,
  `color`      VARCHAR(50)  DEFAULT NULL,
  `fechaDeNac` DATE         DEFAULT NULL,
  `fechaMuerte` DATE        DEFAULT NULL,
  `activo`     TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_mascotas_cliente` FOREIGN KEY (`clienteId`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLA: servicios
-- Servicios que ofrece la clínica
-- ============================================================
CREATE TABLE `servicios` (
  `id`     INT(11)        NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150)   NOT NULL,
  `precio` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `activo` TINYINT(1)     NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `servicios` (`id`, `nombre`, `precio`, `activo`) VALUES
(1, 'Consulta General',     500.00, 1),
(2, 'Vacunación',           800.00, 1),
(3, 'Cirugía',             3000.00, 1),
(4, 'Higiene y Estética',   700.00, 1);

-- ============================================================
-- TABLA: rolesServicios
-- Relación N:M entre roles del personal y servicios que pueden realizar
-- ============================================================
CREATE TABLE `rolesservicios` (
  `id`         INT(11) NOT NULL AUTO_INCREMENT,
  `rolId`      INT(11) NOT NULL,
  `servicioId` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rol_servicio` (`rolId`, `servicioId`),
  CONSTRAINT `fk_rs_rol`      FOREIGN KEY (`rolId`)      REFERENCES `roles`    (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rs_servicio` FOREIGN KEY (`servicioId`) REFERENCES `servicios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- El Administrador puede hacer todo; el Veterinario todo excepto Estética
INSERT INTO `rolesservicios` (`rolId`, `servicioId`) VALUES
(1, 1),(1, 2),(1, 3),(1, 4),
(2, 1),(2, 2),(2, 3);

-- ============================================================
-- TABLA: atenciones
-- Registro de atenciones/citas de mascotas
-- ============================================================
CREATE TABLE `atenciones` (
  `id`          INT(11)      NOT NULL AUTO_INCREMENT,
  `clienteId`   INT(11)      NOT NULL,
  `mascotaId`   INT(11)      NOT NULL,
  `personalId`  INT(11)      NOT NULL,
  `fechaHora`   DATETIME     NOT NULL,
  `titulo`      VARCHAR(200) NOT NULL,
  `servicioId`  INT(11)      DEFAULT NULL,
  `descripcion` TEXT         DEFAULT NULL,
  `activo`      TINYINT(1)   NOT NULL DEFAULT 1,
  `estado`      VARCHAR(50)  NOT NULL DEFAULT 'pendiente',
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_aten_cliente`  FOREIGN KEY (`clienteId`)  REFERENCES `clientes`  (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_aten_mascota`  FOREIGN KEY (`mascotaId`)  REFERENCES `mascotas`  (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_aten_personal` FOREIGN KEY (`personalId`) REFERENCES `personal`  (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_aten_servicio` FOREIGN KEY (`servicioId`) REFERENCES `servicios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLA: novedades
-- Publicaciones/noticias de la clínica
-- ============================================================
CREATE TABLE `novedades` (
  `id`               INT(11)      NOT NULL AUTO_INCREMENT,
  `titulo`           VARCHAR(255) NOT NULL,
  `contenido`        TEXT         NOT NULL,
  `imagen`           LONGBLOB     DEFAULT NULL,
  `usuarioId`        INT(11)      NOT NULL,
  `fechaPublicacion` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_novedades_usuario` FOREIGN KEY (`usuarioId`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLA: productos
-- Catálogo de productos para venta
-- ============================================================
CREATE TABLE `productos` (
  `id`          INT(11)        NOT NULL AUTO_INCREMENT,
  `nombre`      VARCHAR(200)   NOT NULL,
  `descripcion` TEXT           DEFAULT NULL,
  `precio`      DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `imagen`      LONGBLOB  DEFAULT NULL,
  `categoria`   VARCHAR(100)   DEFAULT NULL,
  `stock`       INT(11)        NOT NULL DEFAULT 0,
  `usuarioId`   INT(11)        NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_productos_usuario` FOREIGN KEY (`usuarioId`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- TABLA: password_reset_tokens
-- Tokens para la recuperación de contraseña
-- ============================================================
CREATE TABLE `password_reset_tokens` (
  `id`            INT(11)      NOT NULL AUTO_INCREMENT,
  `usuario_id`    INT(11)      NOT NULL,
  `token`         VARCHAR(64)  NOT NULL UNIQUE,
  `token_hash`    VARCHAR(64)  NOT NULL,
  `expira_en`     DATETIME     NOT NULL,
  `usado`         TINYINT(1)   NOT NULL DEFAULT 0,
  `ip_solicitud`  VARCHAR(45)  DEFAULT NULL,
  `creado_en`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_prt_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;
