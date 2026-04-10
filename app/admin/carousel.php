<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';

require_admin_permission('manage_carousel');

function carousel_empty_slide(): array {
    return ['image' => '', 'badge' => '', 'title' => '', 'text' => ''];
}

function carousel_store_uploaded_image(array $file, int $slide): string {
    $errorCode = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($errorCode !== UPLOAD_ERR_OK) {
        throw new RuntimeException("Falha no upload da imagem do slide {$slide}.");
    }

    $tmp = (string)($file['tmp_name'] ?? '');
    $size = (int)($file['size'] ?? 0);
    if ($tmp === '' || !is_uploaded_file($tmp) || $size <= 0 || $size > 8 * 1024 * 1024) {
        throw new RuntimeException("Arquivo invalido no slide {$slide}. Use imagens ate 8MB.");
    }

    $imageInfo = @getimagesize($tmp);
    if ($imageInfo === false) {
        throw new RuntimeException("O arquivo do slide {$slide} nao e uma imagem valida.");
    }

    $mime = strtolower((string)($imageInfo['mime'] ?? ''));
    $extMap = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];
    if (!isset($extMap[$mime])) {
        throw new RuntimeException("Formato nao suportado no slide {$slide}. Use JPG, PNG, GIF ou WEBP.");
    }

    $relativeDir = '/assets/images/carousel/uploads';
    $absoluteDir = __DIR__ . '/../assets/images/carousel/uploads';
    if (!is_dir($absoluteDir) && !mkdir($absoluteDir, 0775, true) && !is_dir($absoluteDir)) {
        throw new RuntimeException('Nao foi possivel criar a pasta de upload do carrossel.');
    }

    $filename = 'slide_' . $slide . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extMap[$mime];
    $destination = $absoluteDir . '/' . $filename;
    if (!move_uploaded_file($tmp, $destination)) {
        throw new RuntimeException("Nao foi possivel salvar a imagem do slide {$slide}.");
    }

    return $relativeDir . '/' . $filename;
}

$error = null;
$success = null;
$slides = hero_carousel_get();
$editingIndex = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 0],
]);
if (!is_int($editingIndex) || !isset($slides[$editingIndex])) {
    $editingIndex = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Token CSRF invalido.';
    } else {
        try {
            $action = (string)($_POST['action'] ?? 'save');
            $slides = hero_carousel_get();

            if ($action === 'delete') {
                $deleteIndex = (int)($_POST['delete_index'] ?? -1);
                if (!isset($slides[$deleteIndex])) {
                    throw new RuntimeException('Slide nao encontrado para exclusao.');
                }
                if (count($slides) <= 1) {
                    throw new RuntimeException('O carrossel precisa ter pelo menos um slide.');
                }

                $removed = $slides[$deleteIndex];
                unset($slides[$deleteIndex]);
                $slides = array_values($slides);
                hero_carousel_save($slides);
                admin_audit_log('carousel_delete_slide', [
                    'deleted_index' => $deleteIndex,
                    'title' => (string)($removed['title'] ?? ''),
                ], 'site_settings');
                $success = 'Slide removido com sucesso.';
                $editingIndex = null;
            } else {
                $postedIndex = (int)($_POST['slide_index'] ?? -1);
                $isEditing = $postedIndex >= 0 && isset($slides[$postedIndex]);
                $baseSlide = $isEditing ? $slides[$postedIndex] : carousel_empty_slide();
                $image = trim((string)($baseSlide['image'] ?? ''));

                $didUpload = false;
                if (isset($_FILES['slide_upload']) && is_array($_FILES['slide_upload'])) {
                    $uploadError = (int)($_FILES['slide_upload']['error'] ?? UPLOAD_ERR_NO_FILE);
                    if ($uploadError !== UPLOAD_ERR_NO_FILE) {
                        $slideNumber = $isEditing ? ($postedIndex + 1) : (count($slides) + 1);
                        $image = carousel_store_uploaded_image($_FILES['slide_upload'], $slideNumber);
                        $didUpload = true;
                    }
                }

                $imageFromInput = trim((string)($_POST['image'] ?? ''));
                if (!$didUpload && $imageFromInput !== '') {
                    $image = $imageFromInput;
                }

                $badge = trim((string)($_POST['badge'] ?? ''));
                $title = trim((string)($_POST['title'] ?? ''));
                $text = trim((string)($_POST['text'] ?? ''));

                if ($title === '') {
                    throw new RuntimeException('O titulo do slide e obrigatorio.');
                }
                if ($image === '') {
                    throw new RuntimeException('Envie uma imagem ou informe um caminho valido.');
                }

                $slideData = [
                    'image' => $image,
                    'badge' => $badge,
                    'title' => $title,
                    'text' => $text,
                ];

                if ($isEditing) {
                    $slides[$postedIndex] = $slideData;
                    $editingIndex = $postedIndex;
                    $success = 'Slide atualizado com sucesso.';
                    admin_audit_log('carousel_update_slide', [
                        'slide_index' => $postedIndex,
                        'image' => $image,
                        'title' => $title,
                    ], 'site_settings');
                } else {
                    $slides[] = $slideData;
                    $editingIndex = count($slides) - 1;
                    $success = 'Novo slide adicionado com sucesso.';
                    admin_audit_log('carousel_add_slide', [
                        'slide_index' => $editingIndex,
                        'image' => $image,
                        'title' => $title,
                    ], 'site_settings');
                }

                hero_carousel_save($slides);
            }

            $slides = hero_carousel_get();
            if (!is_int($editingIndex) || !isset($slides[$editingIndex])) {
                $editingIndex = null;
            }
        } catch (Throwable $e) {
            $error = 'Falha ao salvar configuracao do carrossel: ' . $e->getMessage();
            error_log('Admin carousel error: ' . $e->getMessage());
        }
    }
}

$editingSlide = is_int($editingIndex) && isset($slides[$editingIndex])
    ? $slides[$editingIndex]
    : carousel_empty_slide();
$previewImage = (string)($editingSlide['image'] ?? '');
if ($previewImage === '') {
    $previewImage = '/assets/images/carousel/tech-circuit.jpg';
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Carrossel da Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc3/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <style>
        .carousel-upload-preview {
            width: 100%;
            max-height: 260px;
            object-fit: cover;
            border-radius: .5rem;
            border: 1px solid #ced4da;
            background: #f8f9fa;
        }
        .slide-thumb {
            width: 140px;
            height: 78px;
            object-fit: cover;
            border-radius: .5rem;
            border: 1px solid #dee2e6;
            background: #f8f9fa;
        }
    </style>
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
                    <li class="nav-item"><a href="/admin/decom-chefia.php" class="nav-link"><p>Chefia DECOM</p></a></li>
                    <li class="nav-item"><a href="/admin/carousel.php" class="nav-link active"><p>Carrousel de Imagens Home</p></a></li>
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
                    <h3 class="mb-0">Gerenciar Carrossel da Home</h3>
                    <a class="btn btn-dark btn-sm" href="/" target="_blank" rel="noopener">Ver Home</a>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header"><h3 class="card-title">Instrucoes</h3></div>
                    <div class="card-body">
                        <p class="mb-2"><i class="bi bi-upload me-1"></i>Use esta tela para adicionar, editar e remover slides do carrossel.</p>
                        <p class="mb-2 text-muted">Formatos aceitos: JPG, PNG, GIF e WEBP. Tamanho maximo: 8MB por imagem.</p>
                        <p class="mb-0 text-muted">Dica: para editar um item existente, clique em "Editar" na lista abaixo.</p>
                    </div>
                </div>

                <form method="post" enctype="multipart/form-data" class="card mb-4">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="slide_index" value="<?= e(is_int($editingIndex) ? (string)$editingIndex : '-1') ?>">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0"><?= is_int($editingIndex) ? 'Editar slide' : 'Adicionar novo slide' ?></h3>
                        <?php if (is_int($editingIndex)): ?>
                            <a href="/admin/carousel.php" class="btn btn-outline-secondary btn-sm">Novo slide</a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Preview</label>
                                <img
                                    id="slide-preview-main"
                                    class="carousel-upload-preview"
                                    src="<?= e($previewImage) ?>"
                                    alt="Preview do slide"
                                >
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Upload de imagem</label>
                                <input
                                    class="form-control js-slide-upload"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.gif,.webp,image/*"
                                    name="slide_upload"
                                    data-preview-id="slide-preview-main"
                                >
                                <small class="text-muted">Opcional no modo edicao: se nao enviar novo arquivo, a imagem atual sera mantida.</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Caminho da imagem (opcional)</label>
                                <input
                                    class="form-control"
                                    name="image"
                                    placeholder="/assets/images/carousel/exemplo.jpg"
                                    value="<?= e((string)($editingSlide['image'] ?? '')) ?>"
                                >
                                <small class="text-muted">Se preenchido, esse caminho tem prioridade sobre o upload.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Subtitulo</label>
                                <input class="form-control" name="badge" value="<?= e((string)($editingSlide['badge'] ?? '')) ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Titulo</label>
                                <input class="form-control" name="title" required value="<?= e((string)($editingSlide['title'] ?? '')) ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Texto</label>
                                <textarea class="form-control" rows="3" name="text"><?= e((string)($editingSlide['text'] ?? '')) ?></textarea>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary" type="submit"><?= is_int($editingIndex) ? 'Salvar alteracoes' : 'Adicionar slide' ?></button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="card mb-4">
                    <div class="card-header"><h3 class="card-title">Slides cadastrados</h3></div>
                    <div class="card-body p-0">
                        <?php if ($slides === []): ?>
                            <div class="p-3 text-muted">Nenhum slide cadastrado no momento.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th style="width: 90px;">Ordem</th>
                                        <th style="width: 170px;">Preview</th>
                                        <th>Titulo e conteudo</th>
                                        <th style="width: 210px;">Acoes</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($slides as $idx => $slide): ?>
                                        <tr>
                                            <td>#<?= e((string)($idx + 1)) ?></td>
                                            <td>
                                                <img
                                                    class="slide-thumb"
                                                    src="<?= e((string)($slide['image'] ?? '/assets/images/carousel/tech-circuit.jpg')) ?>"
                                                    alt="Slide <?= e((string)($idx + 1)) ?>"
                                                >
                                            </td>
                                            <td>
                                                <div class="fw-semibold"><?= e((string)($slide['title'] ?? 'Sem titulo')) ?></div>
                                                <div class="text-muted small mb-1"><?= e((string)($slide['badge'] ?? '')) ?></div>
                                                <div class="small text-body-secondary"><?= e((string)($slide['text'] ?? '')) ?></div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a class="btn btn-outline-primary btn-sm" href="/admin/carousel.php?edit=<?= e((string)$idx) ?>">Editar</a>
                                                    <form method="post" onsubmit="return confirm('Confirma a exclusao deste slide?');">
                                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="delete_index" value="<?= e((string)$idx) ?>">
                                                        <button class="btn btn-outline-danger btn-sm" type="submit">Apagar</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc3/dist/js/adminlte.min.js"></script>
<script>
    document.querySelectorAll('.js-slide-upload').forEach(function (input) {
        input.addEventListener('change', function () {
            var previewId = input.getAttribute('data-preview-id');
            var preview = document.getElementById(previewId);
            if (!preview || !input.files || !input.files[0]) {
                return;
            }
            var file = input.files[0];
            var url = URL.createObjectURL(file);
            preview.src = url;
            preview.onload = function () { URL.revokeObjectURL(url); };
        });
    });
</script>
</body>
</html>




