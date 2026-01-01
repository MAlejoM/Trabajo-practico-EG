<?php
include_once __DIR__ . "/../src/lib/funciones.php";

$cliente_id = $_SESSION['cliente_id'];
$mascotas = get_mascotas_by_cliente($cliente_id);

if (empty($mascotas)) {
    echo '<div class="alert alert-info">No tienes mascotas registradas.</div>';
} else {
    echo '<div class="row g-3">';
    foreach ($mascotas as $mascota) {
        echo '<div class="col-12 col-sm-6 col-lg-4">';
        echo '<div class="card h-100">';

        // Mostrar foto de la mascota si existe
        if (!empty($mascota['foto'])) {
            echo '<img src="data:image/jpeg;base64,' . base64_encode($mascota['foto']) . '" class="card-img-top" style="height: 200px; object-fit: cover;" alt="' . $mascota['nombre'] . '" />';
        } else {
            // Imagen placeholder si no hay foto
            echo '<div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">';
            echo '<i class="fas fa-paw fa-4x text-white"></i>';
            echo '</div>';
        }

        echo '<div class="card-body d-flex flex-column">';
        echo '<h5 class="card-title">' . $mascota['nombre'] . '</h5>';

        // Información de la mascota
        echo '<ul class="list-unstyled mb-3">';
        if (!empty($mascota['raza'])) {
            echo '<li><strong>Raza:</strong> ' . $mascota['raza'] . '</li>';
        }
        if (!empty($mascota['color'])) {
            echo '<li><strong>Color:</strong> ' . $mascota['color'] . '</li>';
        }
        if (!empty($mascota['fechaDeNac'])) {
            $fecha = date('d/m/Y', strtotime($mascota['fechaDeNac']));
            echo '<li><strong>Nacimiento:</strong> ' . $fecha . '</li>';
        }
        echo '</ul>';

        // Botones de acción
        echo '<div class="mt-auto d-grid gap-2">';
        echo '<a href="' . BASE_URL . 'public/atenciones/atencion_list_by_mascota.php?id=' . $mascota['id'] . '" class="btn btn-sm btn-primary">';
        echo '<i class="fas fa-calendar-check me-1"></i>Ver Atenciones</a>';
        echo '</div>';

        echo '</div>'; // card-body
        echo '</div>'; // card
        echo '</div>'; // col
    }
    echo '</div>'; // row
}
