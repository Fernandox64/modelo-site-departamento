<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';

require_admin_permission('manage_content');

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Token CSRF invalido.';
    } else {
        try {
            $positions = [];
            for ($i = 0; $i < 10; $i++) {
                $positions[$i] = [
                    'role' => (string)($_POST["positions_{$i}_role"] ?? ''),
                    'name' => (string)($_POST["positions_{$i}_name"] ?? ''),
                    'email' => (string)($_POST["positions_{$i}_email"] ?? ''),
                    'mandate' => (string)($_POST["positions_{$i}_mandate"] ?? ''),
                    'secretary_name' => (string)($_POST["positions_{$i}_secretary_name"] ?? ''),
                    'secretary_email' => (string)($_POST["positions_{$i}_secretary_email"] ?? ''),
                    'secretary_phone' => (string)($_POST["positions_{$i}_secretary_phone"] ?? ''),
                ];
            }

            decom_chefia_save([
                'department_title' => (string)($_POST['department_title'] ?? ''),
                'department_intro' => (string)($_POST['department_intro'] ?? ''),
                'positions' => $positions,
                'address_block' => (string)($_POST['address_block'] ?? ''),
                'phone' => (string)($_POST['phone'] ?? ''),
                'email' => (string)($_POST['email'] ?? ''),
            ]);
            admin_audit_log('decom_chefia_update', ['updated' => true], 'site_settings');
            $success = 'Pagina de chefia atualizada com sucesso.';
        } catch (Throwable $e) {
            $error = 'Falha ao salvar dados da chefia.';
            error_log('Admin decom-chefia error: ' . $e->getMessage());
        }
    }
}

$data = decom_chefia_get();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Chefia DECOM</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc3/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">
    <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">Menu</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <form method="post" action="/admin/logout.php" class="m-0">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <button type="submit" class="btn btn-outline-danger btn-sm">Sair</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <div class="sidebar-brand">
            <a href="/admin/dashboard.php" class="brand-link text-decoration-none"><span class="brand-text fw-light">DECOM Admin</span></a>
        </div>
        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                    <li class="nav-item"><a href="/admin/site-graduacao.php" class="nav-link"><p>Site da Graduacao</p></a></li>
                    <li class="nav-item"><a href="/admin/content.php?type=noticias" class="nav-link"><p>Noticias</p></a></li>
                    <li class="nav-item"><a href="/admin/content.php?type=editais" class="nav-link"><p>Editais</p></a></li>
                    <li class="nav-item"><a href="/admin/content.php?type=defesas" class="nav-link"><p>Defesas</p></a></li>
                    <li class="nav-item"><a href="/admin/content.php?type=estagios" class="nav-link"><p>Estagios e Empregos</p></a></li>
                    <li class="nav-item"><a href="/admin/pessoal.php" class="nav-link"><p>Pessoal</p></a></li>
                    <li class="nav-item"><a href="/admin/atendimento-docentes.php" class="nav-link"><p>Atendimento Docentes</p></a></li>
                    <li class="nav-item"><a href="/admin/logo.php" class="nav-link"><p>Logo do Site</p></a></li>
                    <li class="nav-item"><a href="/admin/decom-chefia.php" class="nav-link active"><p>Chefia DECOM</p></a></li>
                    <li class="nav-item"><a href="/admin/carousel.php" class="nav-link"><p>Carrousel de Imagens Home</p></a></li>
                    <li class="nav-item"><a href="/admin/horarios.php" class="nav-link"><p>Horarios de Aula</p></a></li>
                    <li class="nav-item"><a href="/admin/pesquisa.php" class="nav-link"><p>Pesquisa</p></a></li>
                    <li class="nav-item"><a href="/admin/extensao.php" class="nav-link"><p>Extensao</p></a></li>
                    <li class="nav-item"><a href="/admin/projetos.php" class="nav-link"><p>Projetos</p></a></li>
                    <li class="nav-item"><a href="/admin/pesquisa-iniciacao-cientifica.php" class="nav-link"><p>Iniciacao Cientifica</p></a></li>
                    <?php if (admin_can('manage_users')): ?><li class="nav-item"><a href="/admin/users.php" class="nav-link"><p>Usuarios e Permissoes</p></a></li><?php endif; ?>
                </ul>
            </nav>
        </div>
    </aside>

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Chefia do DECOM</h3>
                    <a class="btn btn-dark btn-sm" href="/decom/chefia.php" target="_blank" rel="noopener">Ver pagina publica</a>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

                <form method="post" class="card">
                    <div class="card-body">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                        <div class="mb-3">
                            <label class="form-label">Titulo do Departamento</label>
                            <input class="form-control" name="department_title" value="<?= e((string)$data['department_title']) ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Texto institucional</label>
                            <textarea class="form-control" rows="4" name="department_intro"><?= e((string)$data['department_intro']) ?></textarea>
                        </div>

                        <?php for ($i = 0; $i < 10; $i++): ?>
                            <?php $p = $data['positions'][$i] ?? ['role' => '', 'name' => '', 'email' => '', 'mandate' => '', 'secretary_name' => '', 'secretary_email' => '', 'secretary_phone' => '']; ?>
                            <div class="border rounded p-3 mb-3">
                                <h4 class="h6 text-uppercase text-muted mb-3">Cargo <?= e((string)($i + 1)) ?><?= $i >= 4 ? ' (Opcional)' : '' ?></h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Cargo</label>
                                        <input class="form-control" name="positions_<?= e((string)$i) ?>_role" value="<?= e((string)$p['role']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Responsavel</label>
                                        <input class="form-control" name="positions_<?= e((string)$i) ?>_name" value="<?= e((string)$p['name']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input class="form-control" name="positions_<?= e((string)$i) ?>_email" value="<?= e((string)$p['email']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Mandato</label>
                                        <input class="form-control" name="positions_<?= e((string)$i) ?>_mandate" value="<?= e((string)$p['mandate']) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Secretaria - Nome</label>
                                        <input class="form-control" name="positions_<?= e((string)$i) ?>_secretary_name" value="<?= e((string)$p['secretary_name']) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Secretaria - Email</label>
                                        <input class="form-control" name="positions_<?= e((string)$i) ?>_secretary_email" value="<?= e((string)$p['secretary_email']) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Secretaria - Telefone</label>
                                        <input class="form-control" name="positions_<?= e((string)$i) ?>_secretary_phone" value="<?= e((string)$p['secretary_phone']) ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>

                        <div class="mb-3">
                            <label class="form-label">Endereco</label>
                            <textarea class="form-control" rows="5" name="address_block"><?= e((string)$data['address_block']) ?></textarea>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Telefone geral</label>
                                <input class="form-control" name="phone" value="<?= e((string)$data['phone']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email geral</label>
                                <input class="form-control" name="email" value="<?= e((string)$data['email']) ?>">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Salvar chefia</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc3/dist/js/adminlte.min.js"></script>
</body>
</html>




