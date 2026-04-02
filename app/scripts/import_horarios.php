<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';

$result = horarios_import_from_legacy();
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;

if (($result['ok'] ?? false) !== true) {
    exit(1);
}
