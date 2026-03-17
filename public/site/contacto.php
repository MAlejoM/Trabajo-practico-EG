<?php
include_once __DIR__ . "/../../src/Templates/header.php";
?>

<main>
    <section class="py-5 bg-light border-bottom">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="display-5 fw-semibold mb-3">Contáctanos</h1>
                    <p class="lead mb-4">Si tienes alguna duda, sugerencia o comentario, no dudes en contactarnos. Estamos para servirte.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Información de contacto -->
                <div class="col-lg-6 col-md-8 mx-auto">
                    <div class="card shadow-sm border-0 bg-light">
                        <div class="card-body d-flex flex-column p-4">
                            <h3 class="fw-semibold mb-4 text-center">Información de Contacto</h3>
                            <div class="d-flex align-items-center mb-4">
                                <div class="flex-shrink-0">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-1">Dirección</h5>
                                    <p class="mb-0 text-muted">Calle Falsa 123, Ciudad</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-1">Teléfono</h5>
                                    <p class="mb-0 text-muted">+54 11 1234-5678</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-1">Correo Electrónico</h5>
                                    <p class="mb-0 text-muted">contacto@veterinariasananton.com</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mt-auto">
                                <div class="flex-shrink-0">
                                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-1">Horario de Atención</h5>
                                    <p class="mb-0 text-muted">Lun - Vie: 9:00 - 18:00 hs<br>Sab: 9:00 - 13:00 hs</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include_once __DIR__ . "/../../src/Templates/footer.php";
?>
