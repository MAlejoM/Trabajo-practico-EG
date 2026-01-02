-- Script para agregar columnas faltantes a la tabla productos
-- Ejecutar este script en phpMyAdmin o mediante línea de comandos

ALTER TABLE `productos` 
ADD COLUMN `descripcion` TEXT DEFAULT NULL AFTER `nombre`,
ADD COLUMN `imagen` VARCHAR(255) DEFAULT NULL AFTER `precio`,
ADD COLUMN `categoria` VARCHAR(100) DEFAULT NULL AFTER `imagen`,
ADD COLUMN `stock` INT DEFAULT 0 AFTER `categoria`,
ADD COLUMN `usuarioId` INT NOT NULL AFTER `stock`,
ADD FOREIGN KEY (`usuarioId`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE;

-- Verificar la estructura actualizada
DESCRIBE `productos`;
