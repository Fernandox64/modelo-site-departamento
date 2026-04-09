<?php
require __DIR__ . '/../includes/config.php';
$page = page_data('pesquisa');
page_header('Pesquisa');
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
                <h2 class="h5 mb-1">Laboratorios de Pesquisa</h2>
                <p class="text-muted mb-0">Veja todos os laboratorios, suas areas de trabalho e links oficiais.</p>
            </div>
            <a class="btn btn-primary" href="/pesquisa/labs.php">Ver laboratorios</a>
        </div>
    </div>

    <div class="card news-card mt-4">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="h5 mb-1">Iniciacao Cientifica</h2>
                <p class="text-muted mb-0">Consulte orientacoes, oportunidades e informacoes sobre bolsas e projetos.</p>
            </div>
            <a class="btn btn-primary" href="/pesquisa/iniciacao-cientifica.php">Ver iniciacao cientifica</a>
        </div>
    </div>

    <div class="card news-card mt-4">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="h5 mb-1">Projetos de Pesquisa e Extensao</h2>
                <p class="text-muted mb-0">Acesse os projetos institucionais e suas frentes de desenvolvimento.</p>
            </div>
            <a class="btn btn-primary" href="/pesquisa/projetos.php">Ver projetos</a>
        </div>
    </div>
</div>
<?php page_footer(); ?>
