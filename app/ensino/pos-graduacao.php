<?php
require __DIR__ . '/../includes/config.php';

ensure_ppgcc_tables();
$content = ppgcc_content_get();
$notices = ppgcc_notices(12, true);
$yearStats = ppgcc_graduate_years();
$selectedYear = isset($_GET['ano']) ? (int)$_GET['ano'] : 0;
if ($selectedYear === 0 && !empty($yearStats)) {
    $selectedYear = (int)$yearStats[0]['graduate_year'];
}
$graduates = $selectedYear > 0 ? ppgcc_graduates_by_year($selectedYear) : [];

page_header((string)($content['title'] ?? 'Pos-graduacao em Computacao'));
?>
<div class="container py-4">
    <h1 class="section-title h3 mb-3"><?= e((string)($content['title'] ?? 'Pos-graduacao em Computacao')) ?></h1>
    <div class="card news-card mb-4"><div class="card-body"><?= render_rich_text((string)($content['intro_html'] ?? '')) ?></div></div>

    <div class="row g-4">
        <div class="col-lg-6"><div class="card h-100 shadow-sm"><div class="card-body"><h2 class="h5">Criterios de ingresso</h2><?= render_rich_text((string)($content['ingresso_html'] ?? '')) ?></div></div></div>
        <div class="col-lg-6"><div class="card h-100 shadow-sm"><div class="card-body"><h2 class="h5">Editais e selecoes</h2><?= render_rich_text((string)($content['editais_html'] ?? '')) ?></div></div></div>
        <div class="col-lg-6"><div class="card h-100 shadow-sm"><div class="card-body"><h2 class="h5">Grade e carga horaria</h2><?= render_rich_text((string)($content['grade_html'] ?? '')) ?></div></div></div>
        <div class="col-lg-6"><div class="card h-100 shadow-sm"><div class="card-body"><h2 class="h5">Estagio em docencia</h2><?= render_rich_text((string)($content['docencia_html'] ?? '')) ?></div></div></div>
        <div class="col-lg-6"><div class="card h-100 shadow-sm"><div class="card-body"><h2 class="h5">Bolsas e auxilios</h2><?= render_rich_text((string)($content['bolsas_html'] ?? '')) ?></div></div></div>
        <div class="col-lg-6"><div class="card h-100 shadow-sm"><div class="card-body"><h2 class="h5">Facilidades para graduacao</h2><?= render_rich_text((string)($content['graduacao_html'] ?? '')) ?></div></div></div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h2 class="h4 mb-3">Editais e informacoes de selecao</h2>
            <p class="mb-3">
                Para a listagem completa de documentos de cada edital (formularios, resultados e anexos),
                acesse a pagina consolidada de processo seletivo:
                <a href="/ensino/pos-processo-seletivo.php">/ensino/pos-processo-seletivo.php</a>.
            </p>
            <?php if (!empty($notices)): ?>
                <div class="row g-3">
                    <?php foreach ($notices as $n): ?>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <span class="badge <?= (string)$n['notice_type'] === 'edital' ? 'text-bg-danger' : 'text-bg-primary' ?>">
                                    <?= (string)$n['notice_type'] === 'edital' ? 'Edital' : 'Informacao' ?>
                                </span>
                                <h3 class="h6 mt-2 mb-1"><?= e((string)$n['title']) ?></h3>
                                <p class="text-muted mb-2"><?= e((string)$n['summary']) ?></p>
                                <small class="d-block text-muted mb-2">Publicado em: <?= e(date('d/m/Y', strtotime((string)$n['published_at']))) ?></small>
                                <?php if (!empty($n['notice_url'])): ?>
                                    <a href="<?= e((string)$n['notice_url']) ?>" target="_blank" rel="noopener">Acessar</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">Nenhum edital ou informe publicado no momento.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h2 class="h4 mb-3">Formandos / Egressos do PPGCC (por ano)</h2>
            <?php if (!empty($yearStats)): ?>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php foreach ($yearStats as $ys): ?>
                        <?php $year = (int)$ys['graduate_year']; ?>
                        <a class="btn btn-sm <?= $year === $selectedYear ? 'btn-dark' : 'btn-outline-dark' ?>" href="/ensino/pos-graduacao.php?ano=<?= e((string)$year) ?>">
                            <?= e((string)$year) ?> (<?= e((string)$ys['total']) ?>)
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($graduates)): ?>
                    <div class="row g-2">
                        <?php foreach ($graduates as $g): ?>
                            <div class="col-md-6 col-xl-4">
                                <div class="border rounded p-2 h-100">
                                    <strong><?= e((string)$g['student_name']) ?></strong>
                                    <?php if (!empty($g['source_url'])): ?>
                                        <div><a href="<?= e((string)$g['source_url']) ?>" target="_blank" rel="noopener">fonte</a></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Nao ha egressos cadastrados para este ano.</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-muted mb-0">Base de egressos ainda nao preenchida.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php page_footer(); ?>
