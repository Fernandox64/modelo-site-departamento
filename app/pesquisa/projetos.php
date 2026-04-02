<?php
require __DIR__ . '/../includes/config.php';

$projects = research_projects_data();
page_header('Projetos de Pesquisa e Extensao');
?>
<div class="container py-4">
    <h1 class="section-title h3 mb-4">Projetos de Pesquisa e Extensao</h1>
    <p class="text-muted mb-4">Conheca os projetos ativos do departamento e suas linhas de atuacao.</p>

    <div class="row g-4">
        <?php foreach ($projects as $project): ?>
            <?php $isExt = (($project['project_type'] ?? '') === 'extensao'); ?>
            <div class="col-md-6 col-xl-4">
                <div class="card news-card h-100">
                    <div class="card-body d-flex flex-column">
                        <span class="badge <?= $isExt ? 'text-bg-secondary' : 'text-bg-primary' ?> mb-2">
                            <?= $isExt ? 'Extensao' : 'Pesquisa' ?>
                        </span>
                        <h2 class="h5 mb-2"><?= e((string)$project['title']) ?></h2>
                        <p class="news-summary mb-3"><?= e((string)$project['summary']) ?></p>
                        <?php if (!empty($project['coordinator'])): ?>
                            <p class="mb-3"><strong>Coordenacao:</strong> <?= e((string)$project['coordinator']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($project['site_url'])): ?>
                            <a class="btn btn-outline-primary btn-sm mt-auto" href="<?= e((string)$project['site_url']) ?>" target="_blank" rel="noopener">Ver projeto</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php page_footer(); ?>
