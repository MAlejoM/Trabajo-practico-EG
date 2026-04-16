<?php
ob_start();

use App\Core\SessionHandler;

// Cargar autoload y variables de entorno PRIMERO
require_once __DIR__ . '/../autoload.php';

// LUEGO incluir el manejador de errores (ahora $_ENV está disponible)
require_once __DIR__ . '/../Core/error_handler.php';
require_once __DIR__ . '/../config.php';

SessionHandler::iniciar();

if (!defined('BASE_URL')) {
    $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $protocol = $isHttps ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    define('BASE_URL', $protocol . $host . "/");
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Veterinaria San Antón</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>uploads/Logo.jpeg" type="image/jpeg">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos propios -->
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>css/style.css?v=<?php echo time(); ?>">
</head>

<body>
    <!-- Navbar -->
    <header>
        <?php
        $systemError = SessionHandler::getError();
        if ($systemError): ?>
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2 fs-4"></i>
                        <div>
                            <strong>¡Ops! Algo salió mal</strong><br>
                            <?php echo htmlspecialchars($systemError); ?>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
        <?php
        $flash = SessionHandler::getMensaje();
        if ($flash): ?>
            <div class="container mt-3">
                <div class="alert alert-<?php echo htmlspecialchars($flash['tipo']); ?> alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-<?php echo $flash['tipo'] === 'danger' ? 'exclamation-circle' : 'check-circle'; ?> me-2"></i>
                    <?php echo htmlspecialchars($flash['texto']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
        <nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="<?php echo BASE_URL; ?>index.php">
                    <img src="<?php echo BASE_URL; ?>uploads/Logo.jpeg" alt="Logo Veterinaria San Anton" width="40" height="40" class="me-2 rounded-circle object-fit-cover">
                    <span class="brand-title">Veterinaria San Antón</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                        <?php
                        $_nav_logged_in  = SessionHandler::estaAutenticado();
                        $_nav_role       = SessionHandler::getRol();
                        $_nav_is_personal = SessionHandler::esPersonal();
                        $_nav_is_cliente  = SessionHandler::esCliente();
                        ?>
                        <?php if ($_nav_logged_in && $_nav_is_personal && $_nav_role === 'admin'): ?>
                            <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>catalogos/index.php">Administrar Catálogo</a></li>
                            <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>novedades/index.php">Administrar Novedades</a></li>
                            <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>usuarios/index.php">Usuarios</a></li>
                            <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>servicios/index.php">Servicios</a></li>
                            <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>mascotas/index.php">Mascotas</a></li>
                            <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>atenciones/index.php">Atenciones</a></li>
                        <?php elseif ($_nav_logged_in && $_nav_is_personal): ?>
                            <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>catalogos/index.php">Catálogo</a></li>
                            <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>novedades/index.php">Novedades</a></li>
                            <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>mascotas/index.php">Mascotas</a></li>
                            <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>atenciones/index.php">Atenciones</a></li>
                        <?php elseif ($_nav_logged_in && $_nav_is_cliente): ?>
                            <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>catalogos/index.php">Catálogo</a></li>
                            <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>novedades/index.php">Novedades</a></li>
                            <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>mascotas/mis_mascotas.php">Mis Mascotas</a></li>
                        <?php else: ?>
                            <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>catalogos/index.php">Catálogo</a></li>
                            <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>novedades/index.php">Novedades</a></li>
                        <?php endif; ?>

                        <?php if (SessionHandler::estaAutenticado()): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?php echo BASE_URL; ?>uploads/Perfil.jpeg" alt="Foto de perfil" width="32" height="32" class="rounded-circle me-2 object-fit-cover">
                                    <span>Mi cuenta</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>perfil/index.php">Mi perfil</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>auth/logout.php">Cerrar sesión</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a href="<?php echo BASE_URL; ?>auth/login.php" class="btn btn-success ms-lg-2">Iniciar sesión</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>