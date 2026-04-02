<?php
header('Content-Type: text/html; charset=UTF-8');
$menuGraduacao = primary_menu_item('graduacao');
$menuPosGraduacao = primary_menu_item('pos_graduacao');
?>
<!doctype html><html lang="pt-BR"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? SITE_NAME) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f5f7fb}.topbar{font-size:.92rem;background:#102a43;color:#fff}.hero{background:linear-gradient(135deg,#0d3b66,#1d70b8);color:#fff}.hero .card{background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.15);color:#fff}.card-link{text-decoration:none;color:inherit}.section-title{border-left:5px solid #0d6efd;padding-left:.75rem}
.news-card{border:0;border-radius:14px;box-shadow:0 10px 24px rgba(16,42,67,.08);transition:transform .2s ease,box-shadow .2s ease;background:#fff}
.news-card:hover{transform:translateY(-4px);box-shadow:0 14px 30px rgba(16,42,67,.16)}
.news-card-cover{width:100%;height:180px;object-fit:cover;border-radius:14px 14px 0 0;background:#dbe7f3}
.news-card .card-body{padding:1.25rem}
.news-card .news-summary{color:#52606d;line-height:1.5;min-height:48px}
.news-card .news-cta{font-weight:600;color:#0d6efd}
</style></head><body>
<div class="topbar py-2"><div class="container d-flex flex-wrap justify-content-between gap-2"><div><?= e(SITE_UNIVERSITY) ?> · <?= e(SITE_SIGLA) ?></div><div><?= e(SITE_PHONE) ?> · <?= e(SITE_EMAIL) ?></div></div></div>
<nav class="navbar navbar-expand-xl navbar-dark bg-dark shadow-sm sticky-top"><div class="container">
<a class="navbar-brand fw-bold" href="/">DECOM</a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuMain"><span class="navbar-toggler-icon"></span></button>
<div id="menuMain" class="collapse navbar-collapse"><ul class="navbar-nav me-auto">
<li class="nav-item"><a class="nav-link" href="/">Inicio</a></li>
<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">DECOM</a><ul class="dropdown-menu">
<li><a class="dropdown-item" href="/decom/quem-somos.php">Quem somos</a></li>
<li><a class="dropdown-item" href="/decom/comunicacao-logo.php">Comunicacao e logo</a></li>
<li><a class="dropdown-item" href="/decom/localizacao.php">Localizacao</a></li>
<li><a class="dropdown-item" href="/decom/mapa-campus.php">Mapa do campus</a></li></ul></li>
<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Noticias e Eventos</a><ul class="dropdown-menu">
<li><a class="dropdown-item" href="/noticias/index.php">Noticias</a></li>
<li><a class="dropdown-item" href="/noticias/editais.php">Editais</a></li>
<li><a class="dropdown-item" href="/noticias/defesas.php">Defesas</a></li>
<li><a class="dropdown-item" href="/noticias/estagios-empregos.php">Estagios e Empregos</a></li></ul></li>
<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Pessoal</a><ul class="dropdown-menu">
<li><a class="dropdown-item" href="/pessoal/docentes.php">Docentes</a></li>
<li><a class="dropdown-item" href="/pessoal/funcionarios.php">Funcionarios</a></li></ul></li>
<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Ensino</a><ul class="dropdown-menu">
<li><a class="dropdown-item" href="/ensino/ciencia-computacao.php">Ciencia da Computacao</a></li>
<li><a class="dropdown-item" href="/ensino/pos-graduacao.php">Pos-graduacao em Computacao</a></li>
<li><a class="dropdown-item" href="/ensino/pos-processo-seletivo.php">Processo Seletivo (PPGCC)</a></li>
<li><a class="dropdown-item" href="/ensino/inteligencia-artificial.php">Inteligencia Artificial</a></li>
<li><a class="dropdown-item" href="/ensino/horarios-de-aula.php">Horarios de Aula</a></li>
<li><a class="dropdown-item" href="/ensino/informacoes-uteis.php">Informacoes Uteis</a></li>
<li><a class="dropdown-item" href="/ensino/monografias.php">Monografias</a></li></ul></li>
<li class="nav-item"><a class="nav-link" href="<?= e((string)$menuGraduacao['url']) ?>"><?= e((string)$menuGraduacao['label']) ?></a></li>
<li class="nav-item"><a class="nav-link" href="<?= e((string)$menuPosGraduacao['url']) ?>"><?= e((string)$menuPosGraduacao['label']) ?></a></li>
<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Pesquisa</a><ul class="dropdown-menu">
<li><a class="dropdown-item" href="/pesquisa/index.php">Pesquisa</a></li>
<li><a class="dropdown-item" href="/pesquisa/labs.php">Laboratorios</a></li>
<li><a class="dropdown-item" href="/pesquisa/projetos.php">Projetos de Pesquisa/Extensao</a></li></ul></li>
<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Extensao</a><ul class="dropdown-menu">
<li><a class="dropdown-item" href="/extensao/index.php">Extensao</a></li>
<li><a class="dropdown-item" href="/pesquisa/projetos.php">Projetos de Pesquisa/Extensao</a></li></ul></li>
<li class="nav-item"><a class="nav-link" href="/contato/index.php">Contato</a></li>
</ul><div class="d-flex gap-2"><a class="btn btn-outline-light btn-sm" href="/health.php">Health</a><a class="btn btn-primary btn-sm" href="/admin/login.php">Admin</a></div></div></div></nav>
