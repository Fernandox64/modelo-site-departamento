<?php
require __DIR__ . '/../includes/config.php';

$item = isset($_GET['slug']) ? find_demo_item((string)$_GET['slug']) : null;

if (!$item) {
    http_response_code(404);
    page_header('Conteudo nao encontrado');
    echo '<div class="container py-4"><div class="alert alert-danger">Conteudo nao encontrado.</div></div>';
    page_footer();
    exit;
}

page_header((string)$item['title']);
?>
<div class="container py-4">
    <div class="card shadow-sm overflow-hidden">
        <img
            class="news-card-cover"
            style="height:280px;border-radius:0"
            src="<?= e(content_image($item)) ?>"
            alt="<?= e($item['title']) ?>"
        >
        <div class="card-body p-4">
            <div class="d-flex gap-2 flex-wrap mb-3">
                <span class="badge text-bg-primary"><?= e($item['category']) ?></span>
            </div>
            <h1 class="h2"><?= e($item['title']) ?></h1>
            <p class="lead text-secondary"><?= e($item['summary']) ?></p>
            <div><?= render_rich_text((string)$item['content']) ?></div>
        </div>
    </div>
</div>
<?php page_footer(); ?>
