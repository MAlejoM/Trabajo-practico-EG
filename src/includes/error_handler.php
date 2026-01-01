<?php

/**
 * Manejador global de excepciones
 */
function global_exception_handler($exception)
{
    // Loguear el error (opcional, aquí podrías guardar en un archivo .log)
    error_log($exception->getMessage());

    // Si es una petición AJAX, devolver JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Ha ocurrido un error inesperado en el servidor.']);
        exit;
    }

    // Iniciar sesión si no está activa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Guardar mensaje amigable para el usuario
    $_SESSION['system_error'] = "Lo sentimos, ocurrió un error inesperado. Por favor, intenta de nuevo más tarde.";

    // Redirigir a la página principal
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . $host . "/";

    header("Location: " . $baseUrl . "public/index.php");
    exit;
}

/**
 * Manejador global de errores de PHP
 */
function global_error_handler($errno, $errstr, $errfile, $errline)
{
    // Solo manejar errores que no sean silenciados con @
    if (!(error_reporting() & $errno)) {
        return false;
    }

    // Convertir errores fatales o importantes en excepciones
    if ($errno === E_ERROR || $errno === E_USER_ERROR || $errno === E_RECOVERABLE_ERROR) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    // Para otros (warnings/notices), simplemente dejamos que PHP siga o logueamos
    return false;
}

// Registrar los manejadores
set_exception_handler('global_exception_handler');
set_error_handler('global_error_handler');
