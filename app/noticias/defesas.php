<?php
require __DIR__ . '/../includes/config.php';

$perPage = 9;
$currentPage = max(1, (int)($_GET['pagina'] ?? 1));
$selectedYear = isset($_GET['ano']) ? (int)$_GET['ano'] : 0;
$items = [];
$years = [];
$totalItems = 0;
$totalPages = 1;

try {
    $pdo = db();

    $yearsStmt = $pdo->query("SELECT DISTINCT YEAR(published_at) AS y FROM defesa_items ORDER BY y DESC");
    $years = array_values(array_filter(array_map(static fn(array $r): int => (int)$r['y'], $yearsStmt->fetchAll()), static fn(int $y): bool => $y > 0));

    if ($selectedYear <= 0 && !empty($years)) {
        $selectedYear = $years[0];
    }

    if ($selectedYear > 0) {
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM defesa_items WHERE YEAR(published_at) = :year");
        $countStmt->execute([':year' => $selectedYear]);
        $totalItems = (int)$countStmt->fetchColumn();

        $totalPages = max(1, (int)ceil($totalItems / $perPage));
        $currentPage = min($currentPage, $totalPages);
        $offset = ($currentPage - 1) * $perPage;

        $stmt = $pdo->prepare(
            "SELECT slug, title, summary, category, content, image
             FROM defesa_items
             WHERE YEAR(published_at) = :year
             ORDER BY published_at DESC, id DESC
             LIMIT :limite OFFSET :offset"
        );
        $stmt->bindValue(':year', $selectedYear, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();
    }
} catch (Throwable $e) {
    $items = demo_defesas();
    $totalItems = count($items);
    $totalPages = 1;
    $currentPage = 1;
}

function build_defesas_url(int $year, int $page): string {
    return '/noticias/defesas.php?ano=' . urlencode((string)$year) . '&pagina=' . urlencode((string)$page);
}

page_header('Defesas');
?>
<div class="container py-4">
    <h1 class="section-title h3 mb-4">Defesas</h1>

    <?php if (!empty($years)): ?>
        <div class="d-flex flex-wrap gap-2 mb-4">
            <?php foreach ($years as $year): ?>
                <a class="btn btn-sm <?= $year === $selectedYear ? 'btn-primary' : 'btn-outline-primary' ?>" href="<?= e(build_defesas_url($year, 1)) ?>">
                    <?= e((string)$year) ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php foreach ($items as $item): ?>
            <div class="col-md-6 col-xl-4">
                <a class="card card-link news-card h-100" href="/noticias/ver.php?slug=<?= urlencode($item['slug']) ?>">
                    <img class="news-card-cover" src="<?= e(content_image($item)) ?>" alt="<?= e($item['title']) ?>">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge text-bg-info"><?= e($item['category']) ?></span>
                        </div>
                        <h2 class="h5 mb-2"><?= e($item['title']) ?></h2>
                        <p class="news-summary mb-3"><?= e($item['summary']) ?></p>
                        <span class="news-cta mt-auto">Ver defesa</span>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($items)): ?>
        <div class="alert alert-warning mt-4 mb-0">Nenhuma defesa encontrada para o ano selecionado.</div>
    <?php endif; ?>

    <?php if ($totalPages > 1 && $selectedYear > 0): ?>
        <nav class="mt-4" aria-label="Paginacao de defesas">
            <ul class="pagination">
                <li class="page-item<?= $currentPage <= 1 ? ' disabled' : '' ?>">
                    <a class="page-link" href="<?= e(build_defesas_url($selectedYear, max(1, $currentPage - 1))) ?>">Anterior</a>
                </li>
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <li class="page-item<?= $p === $currentPage ? ' active' : '' ?>">
                        <a class="page-link" href="<?= e(build_defesas_url($selectedYear, $p)) ?>"><?= e((string)$p) ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item<?= $currentPage >= $totalPages ? ' disabled' : '' ?>">
                    <a class="page-link" href="<?= e(build_defesas_url($selectedYear, min($totalPages, $currentPage + 1))) ?>">Proxima</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
<?php page_footer(); ?>
