<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';

require_admin_permission('view_dashboard');

function admin_count_table(string $table): int {
    try {
        return (int)db()->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

$newsCount = admin_count_table('news_items');
$editaisCount = admin_count_table('edital_items');
$defesasCount = admin_count_table('defesa_items');
$jobsCount = admin_count_table('job_items');
$peopleCount = admin_count_table('people_items');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
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
                <li class="nav-item d-none d-md-block"><a href="/" class="nav-link">Site</a></li>
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
                    <li class="nav-item menu-open">
                        <a href="#" class="nav-link">
                            <p>Site da Graduacao</p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="/admin/site-graduacao.php" class="nav-link"><p>Painel da Graduacao</p></a></li>
                            <li class="nav-item"><a href="/admin/content.php?type=noticias" class="nav-link"><p>Noticias</p></a></li>
                            <li class="nav-item"><a href="/admin/content.php?type=editais" class="nav-link"><p>Editais</p></a></li>
                            <li class="nav-item"><a href="/admin/content.php?type=defesas" class="nav-link"><p>Defesas</p></a></li>
                            <li class="nav-item"><a href="/admin/content.php?type=estagios" class="nav-link"><p>Estagios e Empregos</p></a></li>
                            <li class="nav-item"><a href="/admin/pessoal.php" class="nav-link"><p>Pessoal</p></a></li>
                            <li class="nav-item"><a href="/admin/atendimento-docentes.php" class="nav-link"><p>Atendimento Docentes</p></a></li>
                    <li class="nav-item"><a href="/admin/logo.php" class="nav-link"><p>Logo do Site</p></a></li>
                            <li class="nav-item"><a href="/admin/decom-chefia.php" class="nav-link"><p>Chefia DECOM</p></a></li>
                            <li class="nav-item"><a href="/admin/contato.php" class="nav-link"><p>Contato</p></a></li>
                            <li class="nav-item"><a href="/admin/carousel.php" class="nav-link"><p>Carrousel de Imagens Home</p></a></li>
                            <li class="nav-item"><a href="/admin/horarios.php" class="nav-link"><p>Horarios de Aula</p></a></li>
                    <li class="nav-item"><a href="/admin/pesquisa.php" class="nav-link"><p>Pesquisa</p></a></li>
                    <li class="nav-item"><a href="/admin/extensao.php" class="nav-link"><p>Extensao</p></a></li>
                    <li class="nav-item"><a href="/admin/projetos.php" class="nav-link"><p>Projetos</p></a></li>
                    <li class="nav-item"><a href="/admin/pesquisa-iniciacao-cientifica.php" class="nav-link"><p>Iniciacao Cientifica</p></a></li>
                        </ul>
                    </li>
                    <?php if (admin_can('manage_users')): ?><li class="nav-item"><a href="/admin/users.php" class="nav-link"><p>Usuarios e Permissoes</p></a></li><?php endif; ?>
                </ul>
            </nav>
        </div>
    </aside>

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6"><h3 class="mb-0">Painel Administrativo</h3></div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-header"><h3 class="card-title">Noticias</h3></div>
                            <div class="card-body">
                                <p class="display-6 mb-2"><?= e((string)$newsCount) ?></p>
                                <p class="text-secondary">Registros cadastrados.</p>
                                <a class="btn btn-primary" href="/admin/content.php?type=noticias">Gerenciar Noticias</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-secondary card-outline">
                            <div class="card-header"><h3 class="card-title">Editais</h3></div>
                            <div class="card-body">
                                <p class="display-6 mb-2"><?= e((string)$editaisCount) ?></p>
                                <p class="text-secondary">Registros cadastrados.</p>
                                <a class="btn btn-secondary" href="/admin/content.php?type=editais">Gerenciar Editais</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-header"><h3 class="card-title">Chefia DECOM</h3></div>
                            <div class="card-body">
                                <p class="mb-2">Edite os dados de chefia, secretarias e endereco institucional.</p>
                                <a class="btn btn-primary" href="/admin/decom-chefia.php">Gerenciar Chefia</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-header"><h3 class="card-title">Contato</h3></div>
                            <div class="card-body">
                                <p class="mb-2">Configure dados publicos e email que recebe o formulario de contato.</p>
                                <a class="btn btn-primary" href="/admin/contato.php">Gerenciar Contato</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-header"><h3 class="card-title">Horarios de Aula</h3></div>
                            <div class="card-body">
                                <p class="mb-2">Edite e importe os horarios de alunos pela pagina oficial antiga.</p>
                                <a class="btn btn-primary" href="/admin/horarios.php">Gerenciar Horarios</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-info card-outline">
                            <div class="card-header"><h3 class="card-title">Defesas</h3></div>
                            <div class="card-body">
                                <p class="display-6 mb-2"><?= e((string)$defesasCount) ?></p>
                                <p class="text-secondary">Registros cadastrados.</p>
                                <a class="btn btn-info text-white" href="/admin/content.php?type=defesas">Gerenciar Defesas</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-dark card-outline">
                            <div class="card-header"><h3 class="card-title">Estagios e Empregos</h3></div>
                            <div class="card-body">
                                <p class="display-6 mb-2"><?= e((string)$jobsCount) ?></p>
                                <p class="text-secondary">Registros cadastrados.</p>
                                <a class="btn btn-dark" href="/admin/content.php?type=estagios">Gerenciar Estagios e Empregos</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-warning card-outline">
                            <div class="card-header"><h3 class="card-title">Pessoal</h3></div>
                            <div class="card-body">
                                <p class="display-6 mb-2"><?= e((string)$peopleCount) ?></p>
                                <p class="text-secondary">Docentes e funcionarios cadastrados.</p>
                                <a class="btn btn-warning" href="/admin/pessoal.php">Gerenciar Pessoal</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-header"><h3 class="card-title">Logo do Site</h3></div>
                            <div class="card-body">
                                <p class="mb-2">Ajuste a imagem e o tamanho da logo exibida no cabecalho.</p>
                                <a class="btn btn-primary" href="/admin/logo.php">Gerenciar Logo</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-secondary card-outline">
                            <div class="card-header"><h3 class="card-title">Iniciacao Cientifica</h3></div>
                            <div class="card-body">
                                <p class="mb-2">Edite a pagina publica de Iniciacao Cientifica dentro da aba Pesquisa.</p>
                                <a class="btn btn-secondary" href="/admin/pesquisa-iniciacao-cientifica.php">Gerenciar Iniciacao Cientifica</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc3/dist/js/adminlte.min.js"></script>
</body>
</html>




