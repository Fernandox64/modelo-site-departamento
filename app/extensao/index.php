<?php
require __DIR__ . '/../includes/config.php';
$page = page_data('extensao');
page_header('Extensao');
?>
<div class="container py-4">
    <h1 class="section-title h3 mb-4"><?= e($page['title']) ?></h1>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p class="lead"><?= e($page['summary']) ?></p>
            <div><?= nl2br(e($page['content'])) ?></div>
        </div>
    </div>

    <div class="card news-card">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="h5 mb-1">Projetos de Pesquisa e Extensao</h2>
                <p class="text-muted mb-0">Confira os projetos de extensao em andamento no departamento.</p>
            </div>
            <a class="btn btn-primary" href="/pesquisa/projetos.php">Ver projetos</a>
        </div>
    </div>
</div>
<?php page_footer(); ?>
