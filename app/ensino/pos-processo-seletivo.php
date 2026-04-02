<?php
require __DIR__ . '/../includes/config.php';

ensure_ppgcc_tables();
$groups = ppgcc_selection_items_grouped();

page_header('Processo Seletivo - Pos-graduacao');
?>
<div class="container py-4">
    <h1 class="section-title h3 mb-3">Processo Seletivo do PPGCC</h1>
    <p class="text-muted mb-4">
        Esta pagina consolida os dados da pagina oficial de processos seletivos do PPGCC/UFOP
        (editais, formularios, resultados, comissoes e documentos relacionados).
    </p>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5 mb-2">Fonte oficial</h2>
            <p class="mb-2">
                Os itens abaixo sao importados da pagina oficial:
                <a target="_blank" rel="noopener" href="https://www3.decom.ufop.br/pos/processoseletivo/">
                    https://www3.decom.ufop.br/pos/processoseletivo/
                </a>
            </p>
            <p class="text-muted mb-0">
                Caso um novo edital nao apareca imediatamente, a secretaria pode atualizar a importacao no painel admin.
            </p>
        </div>
    </div>

    <?php if (empty($groups)): ?>
        <div class="alert alert-warning">Nenhum item importado ate o momento.</div>
    <?php else: ?>
        <?php foreach ($groups as $groupTitle => $items): ?>
            <div class="card news-card mb-3">
                <div class="card-body">
                    <h2 class="h5 mb-3"><?= e((string)$groupTitle) ?></h2>
                    <div class="row g-2">
                        <?php foreach ($items as $it): ?>
                            <div class="col-md-6">
                                <a class="d-block border rounded p-2 text-decoration-none" target="_blank" rel="noopener" href="<?= e((string)$it['item_url']) ?>">
                                    <?= e((string)$it['item_title']) ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php page_footer(); ?>
