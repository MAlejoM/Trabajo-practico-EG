<?php
include_once __DIR__ . "/../../src/Templates/header.php";


use App\Modules\Catalogos\CatalogoService;

// Verificar si el usuario es admin
$esAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';

// Filtros
$categoriaFiltro = $_GET['categoria'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';

$productos = CatalogoService::getAll($categoriaFiltro ?: null, $busqueda ?: null);
$categorias = CatalogoService::getCategorias();

// Preprocesar para JS modal
$productos_js = array_map(function ($p) {
    if (!empty($p['imagen'])) {
        $p['imagen_base64'] = base64_encode($p['imagen']);
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
                    <?php include_once __DIR__ . "/../../src/Templates/menu_lateral.php"; ?>
                </div>
            </div>
        </aside>

        <div class="col-12 col-md-8 col-lg-9">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h1 class="h4 mb-0">Catálogo de Productos</h1>
                        <?php if ($esAdmin): ?>
                            <a href="crear.php" class="btn btn-success btn-sm">
                                <i class="fas fa-plus me-1"></i> Nuevo Producto
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-5">
                            <input type="text" class="form-control form-control-sm" name="busqueda" placeholder="Buscar..." value="<?php echo htmlspecialchars($busqueda); ?>">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select form-select-sm" name="categoria">
                                <option value="">Todas las categorías</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($categoriaFiltro === $cat) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="btn-group btn-group-sm w-100">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                                <?php if ($categoriaFiltro || $busqueda): ?>
                                    <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>

                    <?php if (empty($productos)): ?>
                        <div class="alert alert-info">No se encontraron productos.</div>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach ($productos as $producto): ?>
                                <div class="col-sm-6 col-xl-4">
                                    <div class="card h-100 shadow-sm border-0">
                                        <?php if ($producto['imagen']): ?>
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['imagen']); ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light text-muted d-flex align-items-center justify-content-center" style="height: 180px;">
                                                <i class="fas fa-box fa-3x opacity-25"></i>
                                            </div>
                                        <?php endif; ?>

                                        <div class="card-body d-flex flex-column text-dark">
                                            <h5 class="card-title h6 fw-bold mb-1"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                                            <div class="mb-2">
                                                <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($producto['categoria'] ?: 'Sin categoría'); ?></span>
                                            </div>
                                            <p class="h5 text-primary mb-2">$<?php echo number_format($producto['precio'], 2, ',', '.'); ?></p>
                                            <p class="small text-muted mb-3">Stock: <?php echo $producto['stock']; ?></p>

                                            <div class="btn-group btn-group-sm mt-auto">
                                                <button class="btn btn-outline-primary" onclick="verDetalle(<?php echo $producto['id']; ?>)">Ver</button>
                                                <?php if ($esAdmin): ?>
                                                    <a href="editar.php?id=<?php echo $producto['id']; ?>" class="btn btn-outline-secondary"><i class="fas fa-edit"></i></a>
                                                    <button class="btn btn-outline-danger" onclick="confirmarEliminar(<?php echo $producto['id']; ?>)"><i class="fas fa-trash"></i></button>
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

<!-- Modal Detalle -->
<div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitulo"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-dark" id="modalContenido"></div>
        </div>
    </div>
</div>

<script>
    const productos = <?php echo json_encode($productos_js); ?>;

    function verDetalle(id) {
        const p = productos.find(x => x.id == id);
        if (!p) return;
        document.getElementById('modalTitulo').innerText = p.nombre;
        let html = p.imagen_base64 ? `<img src="data:image/jpeg;base64,${p.imagen_base64}" class="img-fluid rounded mb-3 w-100" style="max-height: 400px; object-fit: contain;">` : '';
        html += `<p><strong>Categoría:</strong> ${p.categoria || '-'}</p>`;
        html += `<p class="h4 text-success">$${Number(p.precio).toLocaleString('es-AR', {minimumFractionDigits:2})}</p>`;
        html += `<p><strong>Stock:</strong> ${p.stock}</p>`;
        html += `<p><strong>Descripción:</strong><br>${p.descripcion || '-'}</p>`;
        document.getElementById('modalContenido').innerHTML = html;
        new bootstrap.Modal(document.getElementById('detalleModal')).show();
    }

    function confirmarEliminar(id) {
        if (confirm('¿Eliminar producto?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'index.php';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'eliminar_id';
            input.value = id;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id']) && $esAdmin) {
    CatalogoService::delete($_POST['eliminar_id']);
    echo "<script>window.location.href='index.php';</script>";
}
include_once __DIR__ . "/../../src/Templates/footer.php";
?>