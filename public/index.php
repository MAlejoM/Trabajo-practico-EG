<?php

include_once __DIR__ . "/../src/includes/header.php";

?>
<main>
  <section class="py-5 bg-light border-bottom">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-12 col-lg-6">
          <h1 class="display-5 fw-semibold mb-3">Bienvenido a Veterinaria San Antón</h1>
          <p class="lead mb-4">Cuidamos a tus mascotas con amor y profesionalismo. Conocé nuestros servicios, novedades y catálogo.</p>
          <div class="d-flex gap-2 flex-wrap">
            <a class="btn btn-success" href="<?php echo BASE_URL; ?>public/catalogo.php">Ver catálogo</a>
            <a class="btn btn-outline-success" href="<?php echo BASE_URL; ?>public/novedades.php">Últimas novedades</a>
          </div>
        </div>
        <div class="col-12 col-lg-6 text-center">
          <img class="img-fluid rounded shadow-sm hero-image" src="<?php echo BASE_URL; ?>public/uploads/bienvenida.png" alt="Imagen de bienvenida" />
        </div>
      </div>
    </div>
  </section>
</main>

<?php
include_once __DIR__ . "/../src/includes/footer.php"; ?>