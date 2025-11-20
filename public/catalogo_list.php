<?php
include_once __DIR__ . "/../src/lib/funciones.php";

$result = get_all_catalogo();

if (empty($result)) {
    echo '<div class="alert alert-info">No hay productos disponibles en el catálogo.</div>';
} else {
    echo '<div class="row g-3">';
    foreach ($result as $row) {
        echo '<div class="col-12 col-sm-6 col-lg-4">';
        echo '<div class="card h-100">';
        if (!empty($row['imagen'])) {
            echo '<img src="data:image/jpeg;base64,'.base64_encode($row['imagen']).'" class="card-img-top" style="height: 200px; object-fit: cover;" alt="'.$row['nombre'].'" />';
        }
        echo '<div class="card-body d-flex flex-column">';
        echo '<h5 class="card-title">'.$row['nombre'].'</h5>';
        echo '<p class="card-text">'.$row['descripcion'].'</p>';
        echo '<div class="mt-auto">';
        echo '<span class="h5 text-success">$'.$row['precio'].'</span>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
}
?>