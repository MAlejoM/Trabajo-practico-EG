<?php

namespace App\Core;

/**
 * Maneja todo lo relacionado a la sesión del usuario.
 * La idea es que nadie más toque $_SESSION directamente, así los cambios
 * en las claves de sesión se hacen en un solo lugar.
 *
 * Excepción: error_handler.php escribe $_SESSION['system_error'] directo
 * porque se carga antes del autoload y no puede llegar a esta clase.
 */
class SessionHandler
{
    // Claves de sesión en un solo lugar para no andar hardcodeando strings

    private const KEY_USUARIO_ID  = 'usuarioId';
    private const KEY_NOMBRE      = 'nombre';
    private const KEY_APELLIDO    = 'apellido';
    private const KEY_ROL         = 'rol';
    private const KEY_PERSONAL_ID = 'personal_id';
    private const KEY_CLIENTE_ID  = 'cliente_id';
    private const KEY_SYSTEM_ERR  = 'system_error';

    // --- Ciclo de vida ---

    // Arranca la sesión si todavía no está activa
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Escribe los datos del usuario en sesión después de un login exitoso.
     * Espera la fila de la BD tal como la devuelve AuthService::login().
     */
    public static function poblar(array $user): void
    {
        self::iniciar();

        $_SESSION[self::KEY_USUARIO_ID] = $user['id'];
        $_SESSION[self::KEY_NOMBRE]     = $user['nombre'];
        $_SESSION[self::KEY_APELLIDO]   = $user['apellido'];

        if (!empty($user['personal_id'])) {
            $_SESSION[self::KEY_PERSONAL_ID] = $user['personal_id'];
            $_SESSION[self::KEY_ROL]         = $user['rol_nombre'];
        }

        if (!empty($user['cliente_id'])) {
            $_SESSION[self::KEY_CLIENTE_ID] = $user['cliente_id'];
        }
    }

    // Cierra y destruye la sesión activa
    public static function destruir(): void
    {
        self::iniciar();
        session_destroy();
    }

    // Sincroniza nombre/apellido en sesión después de que el usuario edite su perfil
    public static function actualizarPerfil(string $nombre, string $apellido): void
    {
        self::iniciar();
        $_SESSION[self::KEY_NOMBRE]   = $nombre;
        $_SESSION[self::KEY_APELLIDO] = $apellido;
    }

    // --- Getters ---


    public static function getId(): ?int
    {
        self::iniciar();
        $val = $_SESSION[self::KEY_USUARIO_ID] ?? null;
        return $val !== null ? (int) $val : null;
    }

    public static function getNombre(): string
    {
        self::iniciar();
        return $_SESSION[self::KEY_NOMBRE] ?? '';
    }

    public static function getApellido(): string
    {
        self::iniciar();
        return $_SESSION[self::KEY_APELLIDO] ?? '';
    }

    // Solo tiene valor si el usuario es personal; clientes no tienen rol
    public static function getRol(): ?string
    {
        self::iniciar();
        return $_SESSION[self::KEY_ROL] ?? null;
    }


    public static function getPersonalId(): ?int
    {
        self::iniciar();
        $val = $_SESSION[self::KEY_PERSONAL_ID] ?? null;
        return $val !== null ? (int) $val : null;
    }


    public static function getClienteId(): ?int
    {
        self::iniciar();
        $val = $_SESSION[self::KEY_CLIENTE_ID] ?? null;
        return $val !== null ? (int) $val : null;
    }

    // --- Verificadores de rol ---


    public static function estaAutenticado(): bool
    {
        self::iniciar();
        return isset($_SESSION[self::KEY_USUARIO_ID]);
    }


    public static function esAdmin(): bool
    {
        self::iniciar();
        return isset($_SESSION[self::KEY_ROL]) && $_SESSION[self::KEY_ROL] === 'admin';
    }


    public static function esPersonal(): bool
    {
        self::iniciar();
        return isset($_SESSION[self::KEY_PERSONAL_ID]);
    }


    public static function esCliente(): bool
    {
        self::iniciar();
        return isset($_SESSION[self::KEY_CLIENTE_ID]);
    }

    // --- Guards (redirigen si no se cumple la condición) ---


    public static function requiereAutenticacion(string $url = '../auth/login.php'): void
    {
        if (!self::estaAutenticado()) {
            header("Location: $url");
            exit;
        }
    }


    public static function requiereAdmin(string $url = '../index.php'): void
    {
        self::requiereAutenticacion();
        if (!self::esAdmin()) {
            header("Location: $url");
            exit;
        }
    }

    // Admin también pasa este guard (tiene personal_id en sesión)
    public static function requierePersonal(string $url = '../index.php'): void
    {
        self::requiereAutenticacion();
        if (!self::esPersonal()) {
            header("Location: $url");
            exit;
        }
    }

    // --- Flash de errores de sistema ---

    // Guarda el error para mostrarlo en el próximo request (lo consume header.php)
    public static function setError(string $mensaje): void
    {
        self::iniciar();
        $_SESSION[self::KEY_SYSTEM_ERR] = $mensaje;
    }

    // Lee y borra el error de la sesión (patrón flash)
    public static function getError(): ?string
    {
        self::iniciar();
        if (!isset($_SESSION[self::KEY_SYSTEM_ERR])) {
            return null;
        }
        $msg = $_SESSION[self::KEY_SYSTEM_ERR];
        unset($_SESSION[self::KEY_SYSTEM_ERR]);
        return $msg;
    }
}
