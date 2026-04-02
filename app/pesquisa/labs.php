<?php
require __DIR__ . '/../includes/config.php';

$labs = research_labs_data();
page_header('Laboratorios de Pesquisa');
?>
<div class="container py-4">
    <h1 class="section-title h3 mb-4">Laboratorios de Pesquisa</h1>
    <p class="text-muted mb-4">Conheca os laboratorios e linhas de trabalho desenvolvidas no DECOM.</p>

    <div class="row g-4">
        <?php foreach ($labs as $lab): ?>
            <div class="col-md-6 col-xl-4">
                <div class="card news-card h-100">
                    <div class="card-body d-flex flex-column">
                        <span class="badge text-bg-primary mb-2">Laboratorio</span>
                        <h2 class="h5 mb-2"><?= e((string)$lab['name']) ?></h2>
                        <p class="news-summary mb-3"><?= e((string)$lab['summary']) ?></p>
                        <?php if (!empty($lab['site_url'])): ?>
                            <a class="btn btn-outline-primary btn-sm mt-auto" href="<?= e((string)$lab['site_url']) ?>" target="_blank" rel="noopener">Visitar site</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php page_footer(); ?>
