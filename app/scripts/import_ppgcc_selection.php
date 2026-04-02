<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';

$result = ppgcc_import_selection_page();
if (($result['ok'] ?? false) === true) {
    echo 'Importacao concluida. Itens inseridos: ' . (string)($result['inserted'] ?? 0) . PHP_EOL;
    exit(0);
}

fwrite(STDERR, 'Falha: ' . (string)($result['message'] ?? 'Erro desconhecido') . PHP_EOL);
exit(1);
