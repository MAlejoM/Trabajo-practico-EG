<?php

/**
 * CONFIGURACIÓN DE MODO - Cambiar a false en producción
 */
define('DEV_MODE', true);

/**
 * Manejador global de excepciones
 */
function global_exception_handler($exception)
{
    // Log completo del error
    $errorMessage = sprintf(
        "[%s] Exception: %s in %s:%d\nStack trace:\n%s\n",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    );

    // Guardar en log de PHP
    error_log($errorMessage);

    // En desarrollo, también guardar en archivo específico de la app
    if (DEV_MODE) {
        $logDir = __DIR__ . '/../../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        file_put_contents(
            $logDir . '/app_errors.log',
            $errorMessage . "\n" . str_repeat("=", 80) . "\n\n",
            FILE_APPEND
        );
    }

    // Si es una petición AJAX, devolver JSON
    if (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    ) {
        header('Content-Type: application/json');

        if (DEV_MODE) {
            echo json_encode([
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => explode("\n", $exception->getTraceAsString())
            ]);
        } else {
            echo json_encode(['error' => 'Ha ocurrido un error inesperado en el servidor.']);
        }
        exit;
    }

    // En desarrollo, mostrar error detallado
    if (DEV_MODE) {
        http_response_code(500);
?>
        <!DOCTYPE html>
        <html>

        <head>
            <title>Error de Aplicación</title>
            <style>
                body {
                    font-family: 'Segoe UI', Arial, sans-serif;
                    padding: 20px;
                    background: #f5f5f5;
                }

                .error-container {
                    max-width: 1200px;
                    margin: 0 auto;
                    background: white;
                    padding: 30px;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                }

                h1 {
                    color: #d32f2f;
                    margin: 0 0 20px 0;
                }

                .error-message {
                    background: #ffebee;
                    padding: 15px;
                    border-left: 4px solid #d32f2f;
                    margin: 20px 0;
                }

                .error-location {
                    background: #e3f2fd;
                    padding: 15px;
                    border-left: 4px solid #1976d2;
                    margin: 20px 0;
                }

                .stack-trace {
                    background: #f5f5f5;
                    padding: 15px;
                    border: 1px solid #ddd;
                    overflow-x: auto;
                }

                pre {
                    margin: 0;
                    white-space: pre-wrap;
                    word-wrap: break-word;
                    font-size: 13px;
                    line-height: 1.5;
                }

                .label {
                    font-weight: bold;
                    color: #666;
                }

                .back-link {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 10px 20px;
                    background: #1976d2;
                    color: white;
                    text-decoration: none;
                    border-radius: 4px;
                }

                .back-link:hover {
                    background: #1565c0;
                }
            </style>
        </head>

        <body>
            <div class="error-container">
                <h1>⚠️ Error de Aplicación</h1>

                <div class="error-message">
                    <p class="label">Mensaje de Error:</p>
                    <p><?php echo htmlspecialchars($exception->getMessage()); ?></p>
                </div>

                <div class="error-location">
                    <p class="label">Ubicación:</p>
                    <p><strong>Archivo:</strong> <?php echo htmlspecialchars($exception->getFile()); ?></p>
                    <p><strong>Línea:</strong> <?php echo $exception->getLine(); ?></p>
                </div>

                <div class="stack-trace">
                    <p class="label">Stack Trace:</p>
                    <pre><?php echo htmlspecialchars($exception->getTraceAsString()); ?></pre>
                </div>

                <a href="javascript:history.back()" class="back-link">← Volver</a>
                <a href="/" class="back-link">🏠 Inicio</a>
            </div>
        </body>

        </html>
<?php
        exit;
    }

    // En producción, redirigir con mensaje genérico
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['system_error'] = "Lo sentimos, ocurrió un error inesperado. Por favor, intenta de nuevo más tarde.";

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

    // En modo desarrollo, registrar warnings y notices también
    if (DEV_MODE && ($errno === E_WARNING || $errno === E_NOTICE)) {
        $logMessage = sprintf(
            "[%s] %s: %s in %s:%d\n",
            date('Y-m-d H:i:s'),
            $errno === E_WARNING ? 'WARNING' : 'NOTICE',
            $errstr,
            $errfile,
            $errline
        );
        error_log($logMessage);

        $logDir = __DIR__ . '/../../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        file_put_contents(
            $logDir . '/app_errors.log',
            $logMessage,
            FILE_APPEND
        );
    }

    // Convertir errores fatales o importantes en excepciones
    if ($errno === E_ERROR || $errno === E_USER_ERROR || $errno === E_RECOVERABLE_ERROR) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    // Para otros (warnings/notices), simplemente dejamos que PHP siga
    return false;
}

// Registrar los manejadores
set_exception_handler('global_exception_handler');
set_error_handler('global_error_handler');

// En modo desarrollo, mostrar todos los errores
if (DEV_MODE) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}
