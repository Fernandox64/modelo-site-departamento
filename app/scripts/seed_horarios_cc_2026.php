<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';

$h = horarios_page_get();
horarios_page_save([
    'title' => 'Horarios de Aula',
    'summary' => 'Horarios de Aula - Bacharelado em Ciencia da Computacao (2026-1)',
    'intro_html' => $h['intro_html'] ?? '<p>Grade de horarios por periodo e eletivas.</p>',
    'schedule_html' => horarios_cc_2026_template_html(),
    'other_electives_html' => horarios_cc_2026_outras_eletivas_html(),
    'links_html' => $h['links_html'] ?? '',
    'source_url' => $h['source_url'] ?? 'https://zeppelin10.ufop.br/HorarioAulas/index.xhtml',
]);

echo "Modelo de horarios 2026-1 aplicado.\n";
