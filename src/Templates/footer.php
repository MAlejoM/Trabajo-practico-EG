<?php
?>
    <footer class="border-top py-4 mt-auto">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="text-body-secondary small">© <?php echo date('Y'); ?> Veterinaria San Antón</span>
            <div class="d-flex gap-2">
                <a href="<?php echo BASE_URL; ?>contacto.php" class="btn btn-outline-success btn-sm">Contacto</a>
                <a href="<?php echo BASE_URL; ?>quienes_somos.php" class="btn btn-success btn-sm">¿Quiénes somos?</a>
            </div>
        </div>
    </footer>

    <!-- Modal de Confirmación Global -->
    <div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfirmacionTitulo">Confirmar acción</h5>
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