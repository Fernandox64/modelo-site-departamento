<?php
declare(strict_types=1);

require __DIR__ . '/../includes/config.php';

$page = page_data('pesquisa');
$projects = research_projects_data('pesquisa');
$slug = trim((string)($_GET['slug'] ?? ''));
$selected = null;
if ($slug !== '') {
    foreach ($projects as $item) {
        if ((string)$item['slug'] === $slug) {
            $selected = $item;
            break;
        }
    }
}

page_header($selected ? (string)$selected['title'] : 'Pesquisa');
?>
<div class="container py-4">
    <?php if ($selected): ?>
        <?php
            $selectedId = (int)($selected['id'] ?? 0);
            $gallery = $selectedId > 0 ? research_project_images_by_project_id($selectedId) : [];
            if (empty($gallery)) {
                $fallback = trim((string)($selected['image_url'] ?? ''));
                $gallery[] = ['image_url' => $fallback !== '' ? $fallback : '/assets/images/carousel/tech-circuit.jpg', 'caption' => ''];
            }
        ?>
        <a class="btn btn-outline-secondary btn-sm mb-3" href="/pesquisa/index.php">Voltar para pesquisa</a>
        <div class="card shadow-sm">
            <div id="pesquisaCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php foreach ($gallery as $idx => $photo): ?>
                        <button type="button" data-bs-target="#pesquisaCarousel" data-bs-slide-to="<?= e((string)$idx) ?>" class="<?= $idx === 0 ? 'active' : '' ?>" <?= $idx === 0 ? 'aria-current="true"' : '' ?> aria-label="Foto <?= e((string)($idx + 1)) ?>"></button>
                    <?php endforeach; ?>
                </div>
                <div class="carousel-inner rounded-top overflow-hidden">
                    <?php foreach ($gallery as $idx => $photo): ?>
                        <div class="carousel-item<?= $idx === 0 ? ' active' : '' ?>">
                            <img src="<?= e((string)$photo['image_url']) ?>" alt="<?= e((string)$selected['title']) ?>" class="d-block w-100" style="height:min(62vh,560px);object-fit:cover;">
                            <?php if (!empty($photo['caption'])): ?>
                                <div class="carousel-caption d-none d-md-block"><p class="mb-0"><?= e((string)$photo['caption']) ?></p></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($gallery) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#pesquisaCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Anterior</span></button>
                    <button class="carousel-control-next" type="button" data-bs-target="#pesquisaCarousel" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Proxima</span></button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <span class="badge text-bg-primary mb-2">Pesquisa</span>
                <h1 class="h3 mb-3"><?= e((string)$selected['title']) ?></h1>
                <p class="lead"><?= e((string)$selected['summary']) ?></p>
                <p><?= e((string)($selected['description'] ?? $selected['summary'] ?? '')) ?></p>
                <?php if (!empty($selected['coordinator'])): ?><p><strong>Coordenacao/Responsavel:</strong> <?= e((string)$selected['coordinator']) ?></p><?php endif; ?>
                <?php if (!empty($selected['site_url'])): ?><a class="btn btn-primary btn-sm" href="<?= e((string)$selected['site_url']) ?>" target="_blank" rel="noopener">Site do projeto</a><?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <h1 class="section-title h3 mb-2"><?= e((string)$page['title']) ?></h1>
        <p class="text-muted mb-3"><?= e((string)$page['summary']) ?></p>
        <div class="card shadow-sm mb-4"><div class="card-body"><?= render_rich_text((string)$page['content']) ?></div></div>
        <div class="row g-4">
            <?php foreach ($projects as $project): ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card news-card card-clickable h-100 shadow-sm position-relative">
                        <img src="<?= e((string)($project['image_url'] ?? '/assets/images/carousel/tech-circuit.jpg')) ?>" alt="<?= e((string)$project['title']) ?>" class="card-img-top" style="height:180px;object-fit:cover;">
                        <div class="card-body d-flex flex-column">
                            <a class="stretched-link" href="/pesquisa/index.php?slug=<?= e((string)$project['slug']) ?>"><span class="visually-hidden">Abrir projeto <?= e((string)$project['title']) ?></span></a>
                            <span class="badge text-bg-primary mb-2">Pesquisa</span>
                            <h2 class="h5 mb-2"><?= e((string)$project['title']) ?></h2>
                            <p class="news-summary mb-3"><?= e((string)$project['summary']) ?></p>
                            <?php if (!empty($project['coordinator'])): ?><p class="mb-3"><strong>Coordenacao:</strong> <?= e((string)$project['coordinator']) ?></p><?php endif; ?>
                            <div class="mt-auto d-flex flex-wrap gap-2 position-relative" style="z-index:2;">
                                <a class="btn btn-outline-primary btn-sm" href="/pesquisa/index.php?slug=<?= e((string)$project['slug']) ?>">Ver detalhes</a>
                                <?php if (!empty($project['site_url'])): ?><a class="btn btn-outline-dark btn-sm" href="<?= e((string)$project['site_url']) ?>" target="_blank" rel="noopener">Site</a><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (empty($projects)): ?><div class="alert alert-warning mt-4 mb-0">Nenhum item de pesquisa cadastrado.</div><?php endif; ?>
    <?php endif; ?>
</div>
<?php page_footer(); ?>
