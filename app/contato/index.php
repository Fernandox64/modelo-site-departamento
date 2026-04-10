<?php
declare(strict_types=1);

require __DIR__ . '/../includes/config.php';

$settings = contact_settings_get();
$status = null;
$statusType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
        $status = 'Token CSRF invalido. Recarregue a pagina e tente novamente.';
        $statusType = 'danger';
    } else {
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $message = trim((string)($_POST['message'] ?? ''));

        if ($name === '' || $email === '' || $message === '') {
            $status = 'Preencha nome, email e mensagem.';
            $statusType = 'warning';
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $status = 'Informe um email valido.';
            $statusType = 'warning';
        } else {
            $sent = contact_form_send($name, $email, $message);
            if ($sent) {
                $status = 'Mensagem enviada com sucesso. Em breve entraremos em contato.';
                $statusType = 'success';
            } else {
                $status = 'Nao foi possivel enviar agora. Tente novamente em alguns minutos.';
                $statusType = 'danger';
                error_log('Contact form mail() failed for email: ' . $email);
            }
        }
    }
}

page_header('Contato');
?>
<div class="container py-4">
    <h1 class="section-title h3 mb-4">Contato</h1>

    <?php if ($status !== null): ?>
        <div class="alert alert-<?= e($statusType) ?>"><?= e($status) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <p><strong>E-mail:</strong> <?= e((string)$settings['public_email']) ?></p>
                    <p><strong>Telefone:</strong> <?= e((string)$settings['public_phone']) ?></p>
                    <p class="mb-0"><strong>Endereco:</strong> <?= e((string)$settings['public_address']) ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5">Formulario de contato</h2>
                    <form method="post" action="/contato/index.php">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <div class="mb-3">
                            <input class="form-control" name="name" placeholder="Nome" required>
                        </div>
                        <div class="mb-3">
                            <input class="form-control" type="email" name="email" placeholder="E-mail" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" name="message" rows="5" placeholder="Mensagem" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-dark">Enviar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php page_footer(); ?>
