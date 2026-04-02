<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';

$newUrl = 'https://zeppelin10.ufop.br/HorarioAulas/index.xhtml';

site_setting_set('horarios_source_url', $newUrl);

$currentLinks = site_setting_get('horarios_links_html', '');
if (trim($currentLinks) === '' || str_contains($currentLinks, 'horarios_alunos')) {
    site_setting_set(
        'horarios_links_html',
        '<ul><li><a href="' . $newUrl . '" target="_blank" rel="noopener">Horario de Aulas UFOP (oficial)</a></li></ul>'
    );
}

echo "URL de horarios atualizada para: {$newUrl}\n";
