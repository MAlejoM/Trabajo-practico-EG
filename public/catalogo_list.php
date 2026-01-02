<?php
include_once __DIR__ . "/../src/includes/header.php";
include_once __DIR__ . "/../src/logic/catalogos.logic.php";

// Verificar si el usuario es admin (para mostrar botones de administración)
$esAdmin = isset($_SESSION['usuarioId']) && isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';

// Obtener parámetros de filtrado y búsqueda
$categoriaFiltro = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

// Obtener productos del catálogo
$productos = obtenerCatalogos($categoriaFiltro ?: null, $busqueda ?: null);
$categorias = obtenerCategorias();

// Mensajes de éxito/error
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : null;
$tipoMensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : null;
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

// Preprocesar productos para base64 si tienen imagen (para que el JSON sea válido y manejable en JS)
$productos_js = array_map(function ($p) {
    if (!empty($p['imagen'])) {
        $p['imagen'] = base64_encode($p['imagen']);
    }
    return $p;
}, $productos);
?>

<div class="container py-4">
    <div class="row g-4">
        <aside class="col-md-4 col-lg-3 d-none d-md-block">
            <div class="card sticky-top" style="top: 1rem;">
                <div class="card-header fw-semibold">Menú principal</div>
                <div class="card-body d-grid gap-2">
                    <?php include_once __DIR__ . "/../src/includes/menu_lateral.php"; ?>
                </div>
            </div>
        </aside>

        <div class="col-12 col-md-8 col-lg-9">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h1 class="h4 mb-0">Catálogo de Productos</h1>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <?php if ($esAdmin): ?>
                                <a href="catalogos/crear.php" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus me-1"></i> Nuevo Producto
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros y búsqueda -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-5">
                            <input type="text"
                                class="form-control form-control-sm"
                                name="busqueda"
                                placeholder="Buscar por nombre o descripción..."
                                value="<?php echo htmlspecialchars($busqueda); ?>">
                        </div>

                        <div class="col-md-4">
                            <select class="form-select form-select-sm" name="categoria">
                                <option value="">Todas las categorías</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>"
                                        <?php echo ($categoriaFiltro === $cat) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <div class="btn-group btn-group-sm w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Filtrar
                                </button>
                                <?php if ($categoriaFiltro || $busqueda): ?>
                                    <a href="catalogo_list.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>

                    <?php if ($mensaje): ?>
                        <div class="alert alert-<?php echo $tipoMensaje === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($mensaje); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($productos)): ?>
                        <div class="alert alert-info">
                            No se encontraron productos<?php echo ($categoriaFiltro || $busqueda) ? ' con los filtros aplicados' : ''; ?>.
                        </div>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach ($productos as $producto): ?>
                                <div class="col-sm-6 col-xl-4">
                                    <div class="card h-100">
                                        <?php if ($producto['imagen']): ?>
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['imagen']); ?>"
                                                class="card-img-top"
                                                alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                                style="height: 200px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                                                <i class="fas fa-box fa-3x opacity-50"></i>
                                            </div>
                                        <?php endif; ?>

                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>

                                            <div class="mb-2">
                                                <?php if ($producto['categoria']): ?>
                                                    <span class="badge bg-info text-dark">
                                                        <?php echo htmlspecialchars($producto['categoria']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($producto['precio']): ?>
                                                <p class="h5 text-success mb-2">
                                                    $<?php echo number_format($producto['precio'], 2, ',', '.'); ?>
                                                </p>
                                            <?php endif; ?>

                                            <p class="text-muted small mb-3">
                                                <i class="fas fa-warehouse me-1"></i>
                                                Stock: <?php echo $producto['stock']; ?> unidades
                                                <?php if ($producto['stock'] < 5 && $producto['stock'] > 0): ?>
                                                    <span class="badge bg-warning text-dark ms-1">Pocas unidades</span>
                                                <?php elseif ($producto['stock'] === 0 || $producto['stock'] == '0'): ?>
                                                    <span class="badge bg-danger ms-1">Sin stock</span>
                                                <?php endif; ?>
                                            </p>

                                            <div class="btn-group btn-group-sm mt-auto">
                                                <button class="btn btn-outline-primary" onclick="verDetalle(<?php echo $producto['id']; ?>)">
                                                    <i class="fas fa-eye me-1"></i>Ver detalles
                                                </button>

                                                <?php if ($esAdmin): ?>
                                                    <a href="catalogos/editar.php?id=<?php echo $producto['id']; ?>" class="btn btn-outline-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-outline-danger" onclick="confirmarEliminar(<?php echo $producto['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalle -->
<div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detalleModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalContenido"></div>
        </div>
    </div>
</div>

<script>
    const productos = <?php echo json_encode($productos_js); ?>;

    function verDetalle(id) {
        const producto = productos.find(p => p.id == id);
        if (!producto) return;

        document.getElementById('detalleModalLabel').textContent = producto.nombre;

        let contenidoHTML = '';

        if (producto.imagen) {
            contenidoHTML += `<img src="data:image/jpeg;base64,${producto.imagen}" alt="${producto.nombre}" class="img-fluid rounded mb-3 w-100">`;
        }

        contenidoHTML += `<div class="row g-3">`;

        if (producto.categoria) {
            contenidoHTML += `
        <div class="col-12">
          <strong><i class="fas fa-tag me-2"></i>Categoría:</strong>
          <span class="badge bg-info text-dark ms-2">${producto.categoria}</span>
        </div>`;
        }

        if (producto.precio) {
            contenidoHTML += `
        <div class="col-md-6">
          <strong><i class="fas fa-dollar-sign me-2"></i>Precio:</strong>
          <span class="h5 text-success ms-2">$${Number(producto.precio).toFixed(2).replace('.', ',')}</span>
        </div>`;
        }

        contenidoHTML += `
      <div class="col-md-6">
        <strong><i class="fas fa-warehouse me-2"></i>Stock:</strong>
        <span class="ms-2">${producto.stock} unidades</span>
      </div>`;

        if (producto.descripcion) {
            contenidoHTML += `
        <div class="col-12">
          <strong><i class="fas fa-info-circle me-2"></i>Descripción:</strong>
          <p class="mt-2" style="white-space: pre-wrap;">${producto.descripcion}</p>
        </div>`;
        }

        contenidoHTML += `</div>`;

        document.getElementById('modalContenido').innerHTML = contenidoHTML;

        const modal = new bootstrap.Modal(document.getElementById('detalleModal'));
        modal.show();
    }

    function confirmarEliminar(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.')) {
            window.location.href = 'catalogos/eliminar.php?id=' + id;
        }
    }
</script>

<?php
include_once __DIR__ . "/../src/includes/footer.php";
?>