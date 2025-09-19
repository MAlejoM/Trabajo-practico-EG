<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    // Ajustar el subdirectorio si cambia el nombre del repo o carpeta de despliegue
    define('BASE_URL', $protocol . $host. "/");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Veterinaria San Antón</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Estilos propios -->
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>public/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <!-- Navbar -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="<?php echo BASE_URL; ?>public/index.php">
                    <img src="<?php echo BASE_URL; ?>public/uploads/Logo.jpeg" alt="Logo Veterinaria San Anton" width="40" height="40" class="me-2 rounded-circle object-fit-cover">
                    <span class="brand-title">Veterinaria San Antón</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                        <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>public/catalogo.php">Catálogo</a></li>
                        <li class="nav-item d-lg-none"><a class="nav-link" href="<?php echo BASE_URL; ?>public/novedades.php">Novedades</a></li>
                        <?php if (isset($_SESSION['usuarioId'])): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?php echo BASE_URL; ?>public/uploads/Perfil.jpeg" alt="Foto de perfil" width="32" height="32" class="rounded-circle me-2 object-fit-cover">
                                    <span>Mi cuenta</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>public/mi_perfil.php">Mi perfil</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>public/logout.php">Cerrar sesión</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a href="<?php echo BASE_URL; ?>public/login.php" class="btn btn-success ms-lg-2">Iniciar sesión</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>