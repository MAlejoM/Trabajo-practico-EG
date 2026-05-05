<?php
use App\Core\SessionHandler;

$is_logged_in = SessionHandler::estaAutenticado();
$is_admin = SessionHandler::esAdmin();
$is_personal = SessionHandler::esPersonal();
$is_cliente = SessionHandler::esCliente();
?>
    <footer class="border-top mt-auto bg-light">
        <div class="container py-4">
            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <h2 class="h6 text-uppercase fw-semibold">Veterinaria San Antón</h2>
                    <p class="small text-muted mb-2">
                        Cuidamos a tus mascotas con atencion profesional y calidez humana.
                    </p>
                    <div class="small text-muted">
                        <i class="fas fa-map-marker-alt me-2" aria-hidden="true"></i>Av. Pellegrini 1234, Rosario, Santa Fe
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <h2 class="h6 text-uppercase fw-semibold">Contacto</h2>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2"><i class="fas fa-phone me-2" aria-hidden="true"></i>+54 11 1234-5678</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2" aria-hidden="true"></i>contacto@veterinariasananton.com</li>
                        <li><i class="fas fa-clock me-2" aria-hidden="true"></i>Lun - Vie: 9:00 - 18:00 hs | Sab: 9:00 - 13:00 hs</li>
                    </ul>
                </div>
            </div>
                <button class="btn btn-link text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#footerMore" aria-expanded="false" aria-controls="footerMore">
                    Mapa de Sitio <i class="fas fa-chevron-down ms-1" aria-hidden="true"></i>
                </button>
                <div class="collapse" id="footerMore">
                    <div class="row g-4 mt-2">
                        <div class="col-6 col-lg-3">
                            <h3 class="h6 text-uppercase fw-semibold">Publico</h3>
                            <ul class="list-unstyled small mb-0">
                                <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>catalogos/index.php">Catalogo</a></li>
                                <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>novedades/index.php">Novedades</a></li>
                                <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>site/contacto.php">Contacto</a></li>
                                <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>site/quienes_somos.php">Quienes somos</a></li>
                            </ul>
                        </div>

                        <?php if (!$is_logged_in): ?>
                            <div class="col-6 col-lg-3">
                                <h3 class="h6 text-uppercase fw-semibold">Autenticacion</h3>
                                <ul class="list-unstyled small mb-0">
                                    <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>auth/login.php">Iniciar sesion</a></li>
                                    <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>auth/forgot_password.php">Recuperar contrasena</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div class="col-6 col-lg-3">
                                <h3 class="h6 text-uppercase fw-semibold">Cuenta</h3>
                                <ul class="list-unstyled small mb-0">
                                    <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>perfil/index.php">Mi perfil</a></li>
                                    <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>auth/logout.php">Cerrar sesion</a></li>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if ($is_cliente): ?>
                            <div class="col-6 col-lg-3">
                                <h3 class="h6 text-uppercase fw-semibold">Cliente</h3>
                                <ul class="list-unstyled small mb-0">
                                    <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>mascotas/mis_mascotas.php">Mis mascotas</a></li>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if ($is_personal && !$is_admin): ?>
                            <div class="col-6 col-lg-3">
                                <h3 class="h6 text-uppercase fw-semibold">
                                    <button class="btn btn-link text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#personalAtenciones">
                                        Atenciones <i class="fas fa-chevron-down ms-1" aria-hidden="true"></i>
                                    </button>
                                </h3>
                                <div class="collapse" id="personalAtenciones">
                                    <ul class="list-unstyled small mb-0">
                                        <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>atenciones/index.php">Ver atenciones</a></li>
                                        <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>atenciones/crear.php">Crear atencion</a></li>
                                    </ul>
                                </div>
                                <h3 class="h6 text-uppercase fw-semibold mt-2">
                                    <button class="btn btn-link text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#personalMascotas">
                                        Mascotas <i class="fas fa-chevron-down ms-1" aria-hidden="true"></i>
                                    </button>
                                </h3>
                                <div class="collapse" id="personalMascotas">
                                    <ul class="list-unstyled small mb-0">
                                        <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>mascotas/index.php">Ver mascotas</a></li>
                                        <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>mascotas/crear.php">Crear mascota</a></li>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($is_admin): ?>
                            <div class="col-6 col-lg-3">
                                <h3 class="h6 text-uppercase fw-semibold">
                                    <button class="btn btn-link text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminUsuarios">
                                        Usuarios <i class="fas fa-chevron-down ms-1" aria-hidden="true"></i>
                                    </button>
                                </h3>
                                <div class="collapse" id="adminUsuarios">
                                    <ul class="list-unstyled small mb-0">
                                        <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>usuarios/index.php">Ver usuarios</a></li>
                                        <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>usuarios/crear.php">Crear usuario</a></li>
                                    </ul>
                                </div>
                                <h3 class="h6 text-uppercase fw-semibold mt-2">
                                    <button class="btn btn-link text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminServicios">
                                        Servicios <i class="fas fa-chevron-down ms-1" aria-hidden="true"></i>
                                    </button>
                                </h3>
                                <div class="collapse" id="adminServicios">
                                    <ul class="list-unstyled small mb-0">
                                        <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>servicios/index.php">Ver servicios</a></li>
                                        <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>servicios/crear.php">Crear servicio</a></li>
                                    </ul>
                                </div>
                                <h3 class="h6 text-uppercase fw-semibold mt-2">
                                    <button class="btn btn-link text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminAtenciones">
                                        Atenciones <i class="fas fa-chevron-down ms-1"></i>
                                    </button>
                                </h3>
                                <div class="collapse" id="adminAtenciones">
                                    <ul class="list-unstyled small mb-0">
                                        <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>atenciones/index.php">Ver atenciones</a></li>
                                        <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>atenciones/crear.php">Crear atencion</a></li>
                                    </ul>
                                </div>
                                <h3 class="h6 text-uppercase fw-semibold mt-2">
                                    <button class="btn btn-link text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminMascotas">
                                        Mascotas <i class="fas fa-chevron-down ms-1"></i>
                                    </button>
                                </h3>
                                <div class="collapse" id="adminMascotas">
                                    <ul class="list-unstyled small mb-0">
                                        <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>mascotas/index.php">Ver mascotas</a></li>
                                        <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>mascotas/crear.php">Crear mascota</a></li>
                                    </ul>
                                </div>
                                <h3 class="h6 text-uppercase fw-semibold mt-2">
                                    <button class="btn btn-link text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminCatalogos">
                                        Catalogos <i class="fas fa-chevron-down ms-1" aria-hidden="true"></i>
                                    </button>
                                </h3>
                                <div class="collapse" id="adminCatalogos">
                                    <ul class="list-unstyled small mb-0">
                                        <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>catalogos/index.php">Ver catalogos</a></li>
                                        <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>catalogos/crear.php">Crear catalogo</a></li>
                                    </ul>
                                </div>
                                <h3 class="h6 text-uppercase fw-semibold mt-2">
                                    <button class="btn btn-link text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNovedades">
                                        Novedades <i class="fas fa-chevron-down ms-1" aria-hidden="true"></i>
                                    </button>
                                </h3>
                                <div class="collapse" id="adminNovedades">
                                    <ul class="list-unstyled small mb-0">
                                        <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>novedades/index.php">Ver novedades</a></li>
                                        <li class="mb-1"><a class="text-decoration-none" href="<?php echo BASE_URL; ?>novedades/crear.php">Crear novedad</a></li>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="border-top pt-3 mt-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span class="text-body-secondary small">© <?php echo date('Y'); ?> Veterinaria San Antón</span>
            </div>
        </div>
    </footer>

    <!-- Modal de Confirmación Global -->
    <div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="h5 modal-title" id="modalConfirmacionTitulo">Confirmar acción</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-dark" id="modalConfirmacionMensaje">
                    ¿Está seguro de realizar esta acción?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="modalConfirmacionBtn">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        function confirmarAccion(mensaje, callback, opciones = {}) {
            const titulo = opciones.titulo || 'Confirmar acción';
            const btnTexto = opciones.btnTexto || 'Confirmar';
            const btnClase = opciones.btnClase || 'btn-danger';

            document.getElementById('modalConfirmacionTitulo').textContent = titulo;
            document.getElementById('modalConfirmacionMensaje').textContent = mensaje;

            const btn = document.getElementById('modalConfirmacionBtn');
            btn.textContent = btnTexto;
            btn.className = 'btn ' + btnClase;

            // Limpiar listeners anteriores clonando el botón
            const nuevoBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(nuevoBtn, btn);
            nuevoBtn.id = 'modalConfirmacionBtn';

            nuevoBtn.addEventListener('click', function () {
                bootstrap.Modal.getInstance(document.getElementById('modalConfirmacion')).hide();
                callback();
            });

            new bootstrap.Modal(document.getElementById('modalConfirmacion')).show();
        }
    </script>
</body>
</html>