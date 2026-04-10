<?php
header('Content-Type: text/html; charset=UTF-8');
$logo = site_logo_settings_get();
$logoUrl = (string)($logo['url'] ?? '');
$logoHeight = (int)($logo['height'] ?? 32);
if ($logoHeight < 20) {
    $logoHeight = 20;
}
if ($logoHeight > 80) {
    $logoHeight = 80;
}
$renderLogoHeight = min(80, $logoHeight + 8);
$ufopLogoUrl = '/assets/images/logo-ufop.png';
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? SITE_NAME) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="/assets/css/theme.css" rel="stylesheet">
</head>
<body>
<div class="topbar py-2">
    <div class="container d-flex flex-wrap justify-content-between gap-2">
        <div><?= e(SITE_UNIVERSITY) ?> &middot; <?= e(SITE_SIGLA) ?></div>
        <div><?= e(SITE_PHONE) ?> &middot; <?= e(SITE_EMAIL) ?></div>
    </div>
</div>

<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom sticky-top py-1">
    <div class="container nav-main-container align-items-center">
        <a class="navbar-brand d-flex align-items-center gap-2 my-0 py-0 me-3" href="/" title="DECOM UFOP" style="height:<?= e((string)$renderLogoHeight) ?>px;overflow:hidden;">
            <img
                src="<?= e($ufopLogoUrl) ?>"
                alt="Logo UFOP"
                style="height:<?= e((string)$renderLogoHeight) ?>px;width:auto;display:block"
            >
            <img
                src="<?= e($logoUrl) ?>"
                alt="Logo DECOM UFOP"
                style="height:<?= e((string)$renderLogoHeight) ?>px;width:auto;display:block"
            >
        </a>
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarMain"
            aria-controls="navbarMain"
            aria-expanded="false"
            aria-label="Alternar navegacao">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav main-nav-list me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="/">Inicio</a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">DECOM</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/decom/quem-somos.php">Quem somos</a></li>
                        <li><a class="dropdown-item" href="/decom/chefia.php">Chefia</a></li>
                        <li><a class="dropdown-item" href="/decom/comunicacao-logo.php">Comunicacao e logo</a></li>
                        <li><a class="dropdown-item" href="/decom/localizacao.php">Localizacao</a></li>
                        <li><a class="dropdown-item" href="/decom/mapa-campus.php">Mapa do campus</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Noticias e Eventos</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/noticias/index.php">Noticias</a></li>
                        <li><a class="dropdown-item" href="/noticias/editais.php">Editais</a></li>
                        <li><a class="dropdown-item" href="/noticias/defesas.php">Defesas</a></li>
                        <li><a class="dropdown-item" href="/noticias/estagios-empregos.php">Estagios e Empregos</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Pessoal</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/pessoal/docentes.php">Docentes</a></li>
                        <li><a class="dropdown-item" href="/pessoal/funcionarios.php">Funcionarios</a></li>
                        <li><a class="dropdown-item" href="/pessoal/atendimento-docentes.php">Atendimento Docentes</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Ensino</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/ensino/ciencia-computacao.php">Ciencia da Computacao</a></li>
                        <li><a class="dropdown-item" href="/ensino/inteligencia-artificial.php">Inteligencia Artificial</a></li>
                        <li><a class="dropdown-item" href="/ensino/horarios-de-aula.php">Horarios de Aula</a></li>
                        <li><a class="dropdown-item" href="/ensino/informacoes-uteis.php">Informacoes Uteis</a></li>
                        <li><a class="dropdown-item" href="/ensino/monografias.php">Monografias</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Pesquisa</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/pesquisa/index.php">Pesquisa</a></li>
                        <li><a class="dropdown-item" href="/pesquisa/labs.php">Laboratorios</a></li>
                        <li><a class="dropdown-item" href="/pesquisa/iniciacao-cientifica.php">Iniciacao Cientifica</a></li>
                        <li><a class="dropdown-item" href="/pesquisa/projetos.php">Projetos de Pesquisa/Extensao</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Extensao</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/extensao/index.php">Extensao</a></li>
                        <li><a class="dropdown-item" href="/pesquisa/projetos.php">Projetos de Pesquisa/Extensao</a></li>
                    </ul>
                </li>

                <li class="nav-item"><a class="nav-link" href="/contato/index.php">Contato</a></li>
                <li class="nav-item d-flex align-items-center ms-lg-1">
                    <a class="btn btn-primary btn-sm nav-admin-btn" href="/admin/login.php" aria-label="Area administrativa" title="Area administrativa">
                        <i class="bi bi-person-workspace"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
