<?php
include_once __DIR__ . "/../../src/Templates/header.php";

use App\Core\SessionHandler;

$is_logged_in = SessionHandler::estaAutenticado();
$is_admin = SessionHandler::esAdmin();
$is_personal = SessionHandler::esPersonal();
$is_cliente = SessionHandler::esCliente();

$sections = [];
$sections['Publico'] = [
    ['Catalogo', BASE_URL . 'catalogos/index.php'],
    ['Novedades', BASE_URL . 'novedades/index.php'],
    ['Contacto', BASE_URL . 'site/contacto.php'],
    ['Quienes somos', BASE_URL . 'site/quienes_somos.php'],
];

if (!$is_logged_in) {
    $sections['Autenticacion'] = [
        ['Iniciar sesion', BASE_URL . 'auth/login.php'],
        ['Recuperar contrasena', BASE_URL . 'auth/forgot_password.php'],
    ];
} else {
    $sections['Cuenta'] = [
        ['Mi perfil', BASE_URL . 'perfil/index.php'],
        ['Cerrar sesion', BASE_URL . 'auth/logout.php'],
    ];
}

if ($is_cliente) {
    $sections['Cliente'] = [
        ['Mis mascotas', BASE_URL . 'mascotas/mis_mascotas.php'],
    ];
}

if ($is_personal) {
    $sections['Personal'] = [
        ['Atenciones', BASE_URL . 'atenciones/index.php'],
        ['Mascotas', BASE_URL . 'mascotas/index.php'],
        ['Catalogos', BASE_URL . 'catalogos/index.php'],
        ['Novedades', BASE_URL . 'novedades/index.php'],
    ];
}

if ($is_admin) {
    $sections['Personal/Admin'] = [
        ['Usuarios', BASE_URL . 'usuarios/index.php'],
        ['Servicios', BASE_URL . 'servicios/index.php'],
        ['Atenciones', BASE_URL . 'atenciones/index.php'],
        ['Mascotas', BASE_URL . 'mascotas/index.php'],
        ['Catalogos', BASE_URL . 'catalogos/index.php'],
        ['Novedades', BASE_URL . 'novedades/index.php'],
    ];
    unset($sections['Personal']);
}
?>

<main class="py-4">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h1 class="h3 mb-0">Mapa del sitio</h1>
            <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-outline-secondary btn-sm">Volver al inicio</a>
        </div>
        <p class="text-muted small">Solo se muestran accesos disponibles segun tu rol actual.</p>

        <div class="row g-4">
            <?php foreach ($sections as $title => $links): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-white">
                            <h2 class="h6 mb-0 fw-semibold"><?php echo htmlspecialchars($title); ?></h2>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled small mb-0">
                                <?php foreach ($links as $link): ?>
                                    <li class="mb-2">
                                        <a class="text-decoration-none" href="<?php echo $link[1]; ?>">
                                            <?php echo htmlspecialchars($link[0]); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php
include_once __DIR__ . "/../../src/Templates/footer.php";
?>
