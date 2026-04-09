<?php
require __DIR__ . '/../includes/config.php';
$data = decom_chefia_get();
page_header('Chefia - DECOM');
?>
<div class="container py-4">
    <h1 class="section-title h3 mb-3"><?= e((string)$data['department_title']) ?></h1>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p class="mb-0"><?= e((string)$data['department_intro']) ?></p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <?php foreach (($data['positions'] ?? []) as $p): ?>
            <?php
                $role = trim((string)($p['role'] ?? ''));
                $name = trim((string)($p['name'] ?? ''));
                $email = trim((string)($p['email'] ?? ''));
                $mandate = trim((string)($p['mandate'] ?? ''));
                $secName = trim((string)($p['secretary_name'] ?? ''));
                $secEmail = trim((string)($p['secretary_email'] ?? ''));
                $secPhone = trim((string)($p['secretary_phone'] ?? ''));
                $hasContent = ($name !== '' || $email !== '' || $mandate !== '' || $secName !== '' || $secEmail !== '' || $secPhone !== '');
            ?>
            <?php if ($role !== '' && $hasContent): ?>
                <div class="col-md-6">
                    <div class="card h-100 news-card">
                        <div class="card-body">
                            <h2 class="h5 mb-2"><?= e($role) ?></h2>
                            <?php if ($name !== ''): ?><p class="mb-1 fw-semibold"><?= e($name) ?></p><?php endif; ?>
                            <?php if ($email !== ''): ?><p class="mb-1">Email: <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></p><?php endif; ?>
                            <?php if ($mandate !== ''): ?><p class="mb-2 text-muted">Mandato: <?= e($mandate) ?></p><?php endif; ?>
                            <?php if ($secName !== '' || $secEmail !== '' || $secPhone !== ''): ?>
                                <hr>
                                <p class="mb-1 fw-semibold">Secretaria</p>
                                <?php if ($secName !== ''): ?><p class="mb-1"><?= e($secName) ?></p><?php endif; ?>
                                <?php if ($secEmail !== ''): ?><p class="mb-1">Email: <a href="mailto:<?= e($secEmail) ?>"><?= e($secEmail) ?></a></p><?php endif; ?>
                                <?php if ($secPhone !== ''): ?><p class="mb-0">Tel: <?= e($secPhone) ?></p><?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5 mb-2">Endereco</h2>
            <p class="mb-2"><?= nl2br(e((string)$data['address_block'])) ?></p>
            <p class="mb-1">Telefone: <?= e((string)$data['phone']) ?></p>
            <p class="mb-0">E-mail: <a href="mailto:<?= e((string)$data['email']) ?>"><?= e((string)$data['email']) ?></a></p>
        </div>
    </div>
</div>
<?php page_footer(); ?>
