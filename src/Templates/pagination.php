<?php

/**
 * Renders a Bootstrap pagination component.
 *
 * @param int   $currentPage  The current page number (1-based).
 * @param int   $totalPages   Total number of pages.
 * @param array $queryParams  Current GET parameters to preserve in all page links (should NOT include 'page').
 */
function renderPagination(int $currentPage, int $totalPages, array $queryParams = []): void
{
    if ($totalPages <= 1) {
        return;
    }

    unset($queryParams['page']);
    $baseQuery = '?' . (!empty($queryParams) ? http_build_query($queryParams) . '&' : '') . 'page=';

    $window = 2; // pages shown on each side of current
    $start  = max(1, $currentPage - $window);
    $end    = min($totalPages, $currentPage + $window);
    ?>
    <nav aria-label="Paginación" class="mt-3">
        <ul class="pagination pagination-sm justify-content-center mb-0">

            <!-- Previous -->
            <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo $baseQuery . ($currentPage - 1); ?>" aria-label="Anterior">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>

            <!-- First page + ellipsis -->
            <?php if ($start > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo $baseQuery . 1; ?>">1</a>
                </li>
                <?php if ($start > 2): ?>
                    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Page window -->
            <?php for ($i = $start; $i <= $end; $i++): ?>
                <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo $baseQuery . $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <!-- Ellipsis + last page -->
            <?php if ($end < $totalPages): ?>
                <?php if ($end < $totalPages - 1): ?>
                    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                <?php endif; ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo $baseQuery . $totalPages; ?>"><?php echo $totalPages; ?></a>
                </li>
            <?php endif; ?>

            <!-- Next -->
            <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo $baseQuery . ($currentPage + 1); ?>" aria-label="Siguiente">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>

        </ul>
    </nav>
    <?php
}
