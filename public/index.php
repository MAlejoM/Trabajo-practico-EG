<?php

include_once __DIR__ . "/../src/includes/header.php";

?>
<main>
  <?php if (isset($_SESSION['usuarioId']) && isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
    <!-- Panel de administración para Administradores -->
    <section class="py-5">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <h1 class="display-5 fw-semibold mb-3">Panel de Administrador</h1>
            <p class="lead mb-4">Bienvenido/a <?php echo htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']); ?></p>
          </div>
        </div>
        
        <!-- Menú de opciones para Administrador -->
        <div class="row g-4">
          <div class="col-sm-6 col-lg-4 col-xl-2">
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                  <div class="flex-shrink-0">
                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                      <i class="fas fa-shopping-cart"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h5 class="card-title mb-0">Administrar Catálogo</h5>
                  </div>
                </div>
                <p class="card-text flex-grow-1">Gestión completa de productos y servicios del catálogo.</p>
                <div class="mt-auto">
                  <a href="<?php echo BASE_URL; ?>public/admin/catalogo.php" class="btn btn-info">Acceder</a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-lg-4 col-xl-2">
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                  <div class="flex-shrink-0">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                      <i class="fas fa-newspaper"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h5 class="card-title mb-0">Administrar Novedades</h5>
                  </div>
                </div>
                <p class="card-text flex-grow-1">Control de noticias y actualizaciones del sitio web.</p>
                <div class="mt-auto">
                  <a href="<?php echo BASE_URL; ?>public/admin/novedades.php" class="btn btn-warning">Acceder</a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-lg-4 col-xl-2">
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                  <div class="flex-shrink-0">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                      <i class="fas fa-users"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h5 class="card-title mb-0">Usuarios</h5>
                  </div>
                </div>
                <p class="card-text flex-grow-1">Administración de usuarios, roles y permisos del sistema.</p>
                <div class="mt-auto">
                  <a href="<?php echo BASE_URL; ?>public/admin/usuarios.php" class="btn btn-danger">Acceder</a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-lg-4 col-xl-2">
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                  <div class="flex-shrink-0">
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                      <i class="fas fa-cogs"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h5 class="card-title mb-0">Servicios</h5>
                  </div>
                </div>
                <p class="card-text flex-grow-1">Configuración y gestión de servicios disponibles.</p>
                <div class="mt-auto">
                  <a href="<?php echo BASE_URL; ?>public/servicios.php" class="btn btn-secondary">Acceder</a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-lg-4 col-xl-2">
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                  <div class="flex-shrink-0">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                      <i class="fas fa-paw"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h5 class="card-title mb-0">Mascotas</h5>
                  </div>
                </div>
                <p class="card-text flex-grow-1">Administración de fichas de mascotas y historial médico.</p>
                <div class="mt-auto">
                  <a href="<?php echo BASE_URL; ?>public/mascota_list.php" class="btn btn-success">Acceder</a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-lg-4 col-xl-2">
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                  <div class="flex-shrink-0">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                      <i class="fas fa-stethoscope"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h5 class="card-title mb-0">Atenciones</h5>
                  </div>
                </div>
                <p class="card-text flex-grow-1">Gestión de atenciones médicas y consultas.</p>
                <div class="mt-auto">
                  <a href="<?php echo BASE_URL; ?>public/atencion_list.php" class="btn btn-primary">Acceder</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  <?php elseif (isset($_SESSION['usuarioId']) && isset($_SESSION['personal_id'])): ?>
    <!-- Panel para Personal (veterinarios, peluqueros, etc.) -->
    <section class="py-5">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <h1 class="display-5 fw-semibold mb-3">Panel de Personal</h1>
            <p class="lead mb-4">Bienvenido/a <?php echo htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']); ?></p>
          </div>
        </div>
        
        <!-- Menú de opciones para Personal -->
        <div class="row g-4">
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                  <div class="flex-shrink-0">
                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                      <i class="fas fa-shopping-cart"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h5 class="card-title mb-0">Catálogo</h5>
                  </div>
                </div>
                <p class="card-text flex-grow-1">Ver productos y servicios disponibles en la veterinaria.</p>
                <div class="mt-auto">
                  <a href="<?php echo BASE_URL; ?>public/catalogo.php" class="btn btn-info">Acceder</a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                  <div class="flex-shrink-0">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                      <i class="fas fa-newspaper"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h5 class="card-title mb-0">Novedades</h5>
                  </div>
                </div>
                <p class="card-text flex-grow-1">Últimas noticias y actualizaciones de la veterinaria.</p>
                <div class="mt-auto">
                  <a href="<?php echo BASE_URL; ?>public/novedades.php" class="btn btn-warning">Acceder</a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                  <div class="flex-shrink-0">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                      <i class="fas fa-paw"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h5 class="card-title mb-0">Mascotas</h5>
                  </div>
                </div>
                <p class="card-text flex-grow-1">Administración de fichas de mascotas, historial médico y datos de contacto.</p>
                <div class="mt-auto">
                  <a href="<?php echo BASE_URL; ?>public/mascota_list.php" class="btn btn-success">Acceder</a>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center mb-3">
                  <div class="flex-shrink-0">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                      <i class="fas fa-stethoscope"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h5 class="card-title mb-0">Atenciones</h5>
                  </div>
                </div>
                <p class="card-text flex-grow-1">Gestión de atenciones médicas, consultas y seguimientos de pacientes.</p>
                <div class="mt-auto">
                  <a href="<?php echo BASE_URL; ?>public/atenciones.php" class="btn btn-primary">Acceder</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  <?php else: ?>
    <!-- Contenido público para usuarios no autenticados o con otros roles -->
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
  <?php endif; ?>
</main>

<?php
include_once __DIR__ . "/../src/includes/footer.php"; ?>