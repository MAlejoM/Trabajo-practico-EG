-- Migración para el nuevo sistema de recuperación de contraseña con tokens firmados
ALTER TABLE Usuarios ADD COLUMN token_recuperacion TEXT DEFAULT NULL;

