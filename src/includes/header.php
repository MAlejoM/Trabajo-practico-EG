<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    define('BASE_URL', $protocol . $host . '/Trabajo-practico-EG/');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Veterinaria San Antón</title>
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>public/css/style.css?v=<?php echo time(); ?>">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="<?php echo BASE_URL; ?>public/index.php" class="brand">
                <img src="<?php echo BASE_URL; ?>public/uploads/Logo.jpeg" alt="Logo Veterinaria San Anton" width="50" height="50">
                <h1>Veterinaria San Anton</h1>
            </a>
                
                <div class="header-right">
                    <nav aria-label="Navegación principal">
                        <?php if (isset($_SESSION['dni'])): ?>
                            <div class="profile-section">
                                <span class="welcome-text" aria-label="Mensaje de bienvenida">Bienvenido</span>
                                <a href="<?php echo BASE_URL; ?>public/mi_perfil.php" class="profile-link" aria-label="Ver mi perfil">
                                    <img src="<?php echo BASE_URL; ?>public/uploads/Perfil.jpeg" alt="Foto de perfil" width="40" height="40">
                                </a>
                            </div>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>public/login.php" class="btn" role="button">
                                Iniciar sesión
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </div>
        </div>
    </header>