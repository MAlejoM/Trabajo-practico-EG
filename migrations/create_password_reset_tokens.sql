-- ============================================
-- Migración: Tabla para Tokens de Recuperación
-- ============================================

CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    token_hash VARCHAR(255) NOT NULL,
    expira_en DATETIME NOT NULL,
    usado TINYINT(1) DEFAULT 0,
    ip_solicitud VARCHAR(45),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expiracion (expira_en),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Descripción de campos:
-- - id: Identificador único del token
-- - usuario_id: ID del usuario que solicitó el reset
-- - token: Token aleatorio de 64 caracteres (enviado por email)
-- - token_hash: Hash SHA-256 del token (seguridad adicional)
-- - expira_en: Timestamp cuando expira el token (1 hora)
-- - usado: Flag para marcar tokens ya utilizados (un solo uso)
-- - ip_solicitud: IP desde donde se solicitó (auditoría)
-- - creado_en: Timestamp de creación
-- ============================================
