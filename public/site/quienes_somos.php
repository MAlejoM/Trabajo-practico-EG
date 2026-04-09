<?php
include_once __DIR__ . "/../../src/Templates/header.php";
?>

<main>
    <section class="py-3 bg-light border-bottom">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="display-5 fw-semibold mb-3">¿Quiénes somos?</h1>
                    <p class="lead mb-4">Somos una veterinaria comprometida con el cuidado y bienestar de tus mascotas, brindando atención profesional con amor y dedicación.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-3">
        <div class="container">
            <div class="row g-3 justify-content-center">

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h2 class="h5 card-title mb-0">Nuestra Misión</h2>
                                </div>
                            </div>
                            <p class="card-text text-muted flex-grow-1">Brindar atención veterinaria de calidad, con calidez humana y compromiso profesional, garantizando el bienestar de cada animal que llega a nuestra clínica.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h2 class="h5 card-title mb-0">Nuestra Visión</h2>
                                </div>
                            </div>
                            <p class="card-text text-muted flex-grow-1">Ser la veterinaria de referencia en la comunidad, reconocida por la excelencia en el trato, la tecnología aplicada al cuidado animal y el vínculo de confianza con nuestros clientes.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h2 class="h5 card-title mb-0">Nuestros Valores</h2>
                                </div>
                            </div>
                            <p class="card-text text-muted flex-grow-1">Actuamos con responsabilidad, empatía y transparencia. Creemos en el respeto hacia cada animal y en la importancia del vínculo entre las mascotas y sus familias.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="py-3 bg-light border-top">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="fw-semibold mb-2">Un equipo apasionado por los animales</h2>
                    <p class="text-muted mb-3">Contamos con profesionales veterinarios capacitados y en constante actualización, listos para atender a tu mascota con el mejor cuidado posible. Nuestras instalaciones están equipadas para ofrecer consultas, cirugías, vacunaciones, grooming y más.</p>
                    <a href="<?php echo BASE_URL; ?>site/contacto.php" class="btn btn-primary">Contáctanos</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include_once __DIR__ . "/../../src/Templates/footer.php";
?>
