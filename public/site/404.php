<?php
include_once __DIR__ . "/../../src/Templates/header.php";
?>

<main class="container py-5">
    <div class="row justify-content-center" style="flex: 1;">
        <div class="col-md-8 text-center mt-5">
            <div class="mb-4">
                <i class="fas fa-paw text-success mb-3" style="font-size: 5rem;"></i>
                <h1 class="display-1 fw-bold text-dark">404</h1>
                <h2 class="h3 mb-4 text-muted">¡Oops! Página no encontrada</h2>
            </div>

            <p class="lead mb-5 text-secondary">
                Parece que el rastro que buscabas se ha perdido.
                Tal vez la mascota se llevó la página a dar un paseo.
            </p>

            <div class="d-flex justify-content-center gap-3">
                <a href="<?php echo BASE_URL; ?>public/index.php" class="btn btn-success btn-lg px-4 shadow-sm">
                    <i class="fas fa-home me-2"></i>Volver al inicio
                </a>
                <button onclick="window.history.back()" class="btn btn-outline-secondary btn-lg px-4 shadow-sm">
                    <i class="fas fa-arrow-left me-2"></i>Regresar
                </button>
            </div>
        </div>
    </div>
</main>

<style>
    main {
        min-height: 70vh;
        display: flex;
        align-items: center;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 1rem;
        margin-top: 2rem;
    }

    .display-1 {
        color: #198754;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    i.fas.fa-paw {
        animation: rotate-paw 3s infinite ease-in-out;
    }

    @keyframes rotate-paw {

        0%,
        100% {
            transform: rotate(-10deg);
        }

        50% {
            transform: rotate(10deg);
        }
    }
</style>

<?php
include_once __DIR__ . "/../../src/Templates/footer.php";
?>
