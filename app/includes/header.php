<?php
header('Content-Type: text/html; charset=UTF-8');
$menuGraduacao = primary_menu_item('graduacao');
$menuPosGraduacao = primary_menu_item('pos_graduacao');
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? SITE_NAME) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/theme.css" rel="stylesheet">
</head>
<body>
<div class="topbar py-2">
    <div class="container d-flex flex-wrap justify-content-between gap-2">
        <div><?= e(SITE_UNIVERSITY) ?> · <?= e(SITE_SIGLA) ?></div>
        <div><?= e(SITE_PHONE) ?> · <?= e(SITE_EMAIL) ?></div>
    </div>
</div>

<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/" title="DECOM UFOP" style="height:40px;padding-top:0;padding-bottom:0;overflow:visible;">
            <img
                src="http://www.decom.ufop.br/decom/site_media/img/decom_logo.png"
                alt="Logo DECOM UFOP"
                style="height:48px;width:auto;display:block"
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
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="/">Inicio</a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">DECOM</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/decom/quem-somos.php">Quem somos</a></li>
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
                        <li><a class="dropdown-item" href="/pos/inicio.php">Pos-graduacao em Computacao (Subsite)</a></li>
                        <li><a class="dropdown-item" href="/pos/noticias.php">Noticias da Pos</a></li>
                        <li><a class="dropdown-item" href="/pos/editais.php">Editais da Pos</a></li>
                        <li><a class="dropdown-item" href="/pos/processo-seletivo.php">Processo Seletivo (PPGCC)</a></li>
                        <li><a class="dropdown-item" href="/ensino/inteligencia-artificial.php">Inteligencia Artificial</a></li>
                        <li><a class="dropdown-item" href="/ensino/horarios-de-aula.php">Horarios de Aula</a></li>
                        <li><a class="dropdown-item" href="/ensino/informacoes-uteis.php">Informacoes Uteis</a></li>
                        <li><a class="dropdown-item" href="/ensino/monografias.php">Monografias</a></li>
                    </ul>
                </li>

                <li class="nav-item"><a class="nav-link" href="<?= e((string)$menuGraduacao['url']) ?>"><?= e((string)$menuGraduacao['label']) ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= e((string)$menuPosGraduacao['url']) ?>"><?= e((string)$menuPosGraduacao['label']) ?></a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Pesquisa</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/pesquisa/index.php">Pesquisa</a></li>
                        <li><a class="dropdown-item" href="/pesquisa/labs.php">Laboratorios</a></li>
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

                <li class="nav-item"><a class="nav-link" href="/pessoal/atendimento-docentes.php">Atendimento</a></li>
                <li class="nav-item"><a class="nav-link" href="/ensino/horarios-de-aula.php">Horario</a></li>
                <li class="nav-item"><a class="nav-link" href="/contato/index.php">Contato</a></li>
            </ul>

            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary btn-sm" href="/health.php">Health</a>
                <a class="btn btn-primary btn-sm" href="/admin/login.php">Admin</a>
            </div>
        </div>
    </div>
</nav>
