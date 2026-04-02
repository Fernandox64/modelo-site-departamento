<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';

ensure_ppgcc_tables();

$content = [
    'title' => 'Pos-graduacao em Computacao',
    'intro_html' => '<p>O Programa de Pos-graduacao em Ciencia da Computacao (PPGCC/UFOP) oferece cursos de Mestrado e Doutorado com foco em pesquisa, ensino e inovacao tecnologica.</p><p>As informacoes desta pagina foram consolidadas a partir de fontes oficiais do PPGCC/DECOM e podem ser atualizadas pelo painel administrativo.</p>',
    'ingresso_html' => '<ul><li>Ingresso por edital de processo seletivo (Mestrado e Doutorado).</li><li>Regras, etapas e cronograma definidos em cada edital vigente.</li><li>Documentacao e requisitos academicos informados na pagina oficial do processo seletivo.</li></ul><p><a href=\"https://www3.decom.ufop.br/pos/processoseletivo/\" target=\"_blank\" rel=\"noopener\">Pagina oficial de processo seletivo</a></p>',
    'editais_html' => '<ul><li>Editais de ingresso para Mestrado e Doutorado.</li><li>Editais de bolsas e chamadas internas (ex.: bolsa de doutorado, PDSE).</li><li>Publicacoes de calendario academico e matriculas.</li></ul><p><a href=\"https://www3.decom.ufop.br/pos/processoseletivo/\" target=\"_blank\" rel=\"noopener\">Editais e selecoes</a></p>',
    'grade_html' => '<ul><li>Estrutura curricular com disciplinas basicas e eletivas.</li><li>Carga minima de creditos informada pelo programa: Mestrado (24) e Doutorado (36).</li><li>Oferta semestral e ementas publicadas na area de grade curricular.</li></ul><p><a href=\"https://www3.decom.ufop.br/pos/grade-curricular/\" target=\"_blank\" rel=\"noopener\">Grade curricular e disciplinas</a></p>',
    'docencia_html' => '<ul><li>O estagio em docencia segue regulamentos institucionais e normas do programa.</li><li>Ha formularios e resolucoes especificas sobre atividades academicas e estagio.</li><li>A obrigatoriedade pode variar conforme regras vigentes e perfil do discente.</li></ul><p><a href=\"https://www3.decom.ufop.br/pos/resolucoes/\" target=\"_blank\" rel=\"noopener\">Resolucoes do programa</a></p>',
    'bolsas_html' => '<ul><li>Bolsas e auxilios vinculados a chamadas e disponibilidade institucional.</li><li>Editais podem incluir CAPES/CNPq/FAPEMIG e programas como PDSE.</li><li>Critrios de concessao, manutencao e classificacao sao divulgados em editais.</li></ul>',
    'graduacao_html' => '<ul><li>Alunos de graduacao podem participar via disciplinas isoladas, conforme calendario e regras.</li><li>A cada periodo sao divulgados prazos de matricula e documentacao exigida.</li><li>Integracao com laboratorios e grupos de pesquisa favorece a transicao para a pos.</li></ul>',
];

ppgcc_content_get();
ppgcc_content_save($content);

db()->exec('DELETE FROM ppgcc_notices');
$seedNotices = [
    [
        'title' => 'Edital PPGCC 04/2025 - Ingresso 2026 (Mestrado e Doutorado)',
        'summary' => 'Processo seletivo para ingresso no PPGCC com vagas para Mestrado e Doutorado.',
        'notice_type' => 'edital',
        'notice_url' => 'https://www3.decom.ufop.br/pos/processoseletivo/',
        'published_at' => '2025-10-01 09:00:00',
    ],
    [
        'title' => 'Edital PPGCC 02/2026 - Classificacao para bolsas de Doutorado',
        'summary' => 'Chamada para classificacao de discentes de doutorado para manutencao de bolsas (dedicacao parcial).',
        'notice_type' => 'edital',
        'notice_url' => 'https://www3.decom.ufop.br/pos/processoseletivo/',
        'published_at' => '2026-03-01 09:00:00',
    ],
    [
        'title' => 'Edital PPGCC 01/2026 - PDSE Doutorado Sanduiche',
        'summary' => 'Selecao interna para o Programa Institucional de Doutorado Sanduiche no Exterior.',
        'notice_type' => 'edital',
        'notice_url' => 'https://www3.decom.ufop.br/pos/processoseletivo/',
        'published_at' => '2026-02-10 09:00:00',
    ],
    [
        'title' => 'Calendario e orientacoes de matricula em disciplinas isoladas',
        'summary' => 'Informes para matricula, incluindo orientacao para alunos de graduacao interessados em disciplinas isoladas.',
        'notice_type' => 'informacao',
        'notice_url' => 'https://www3.decom.ufop.br/pos/noticias/',
        'published_at' => '2026-01-25 09:00:00',
    ],
];
$insertNotice = db()->prepare(
    'INSERT INTO ppgcc_notices (slug, title, summary, notice_type, notice_url, is_active, published_at)
     VALUES (:slug, :title, :summary, :notice_type, :notice_url, 1, :published_at)'
);
foreach ($seedNotices as $n) {
    $insertNotice->execute([
        ':slug' => ppgcc_notice_unique_slug((string)$n['title']),
        ':title' => (string)$n['title'],
        ':summary' => (string)$n['summary'],
        ':notice_type' => (string)$n['notice_type'],
        ':notice_url' => (string)$n['notice_url'],
        ':published_at' => (string)$n['published_at'],
    ]);
}

$ctx = stream_context_create([
    'http' => ['timeout' => 40, 'header' => "User-Agent: decom-ppgcc-import/1.0\r\n"],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
]);

function clean_text(string $text): string
{
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8,ISO-8859-1,Windows-1252');
    $text = preg_replace('/\s+/u', ' ', trim($text)) ?? trim($text);
    return $text;
}

function looks_like_name(string $line): bool
{
    $u = mb_strtoupper($line, 'UTF-8');
    $ban = [
        'AREA RESTRITA', 'O PROGRAMA', 'CONHECA', 'REGIMENTO', 'RESOLUCOES', 'ATAS', 'CREDENCIAMENTO',
        'COORDENACAO', 'FORMULARIOS', 'NOTICIAS', 'DOCENTES', 'DISCENTES', 'ALUNOS', 'HISTORICO DE EGRESSOS',
        'GRADE CURRICULAR', 'PESQUISA', 'CENTRAL MULTIUSUARIO', 'EDITAIS'
    ];
    foreach ($ban as $b) {
        if (str_contains($u, $b)) {
            return false;
        }
    }
    if (mb_strlen($line, 'UTF-8') < 8 || mb_strlen($line, 'UTF-8') > 180) {
        return false;
    }
    if (preg_match('/\d/', $line) === 1) {
        return false;
    }
    return preg_match('/^[\p{L}\s\'\-\.]+$/u', $line) === 1 && preg_match('/\s/u', $line) === 1;
}

$startUrl = 'https://www3.decom.ufop.br/pos/discentes/egressos/';
$html = @file_get_contents($startUrl, false, $ctx);
if ($html === false) {
    fwrite(STDERR, "Falha ao ler pagina de egressos.\n");
    exit(1);
}

libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($html);
$xp = new DOMXPath($dom);

$yearLinks = [];
foreach ($xp->query('//a[@href]') as $a) {
    $href = trim((string)$a->getAttribute('href'));
    $txt = clean_text((string)$a->textContent);
    if ($href === '') {
        continue;
    }
    $abs = $href;
    if (str_starts_with($href, '/')) {
        $abs = 'https://www3.decom.ufop.br' . $href;
    } elseif (!preg_match('~^https?://~i', $href)) {
        $abs = 'https://www3.decom.ufop.br/pos/' . ltrim($href, './');
    }
    if (preg_match('/\b(20\d{2})\b/', $txt, $m) || preg_match('/\b(20\d{2})\b/', $abs, $m)) {
        $year = (int)$m[1];
        if ($year >= 2000 && $year <= 2100) {
            $yearLinks[$year] = $abs;
        }
    }
}
krsort($yearLinks);

db()->exec('DELETE FROM ppgcc_graduates');
$insert = db()->prepare('INSERT INTO ppgcc_graduates (graduate_year, student_name, source_url) VALUES (:y, :n, :s)');

$total = 0;
foreach ($yearLinks as $year => $url) {
    $page = @file_get_contents($url, false, $ctx);
    if ($page === false) {
        continue;
    }
    $d = new DOMDocument();
    $d->loadHTML($page);
    $x = new DOMXPath($d);
    $seen = [];
    foreach ($x->query('//li|//td|//p') as $node) {
        $line = clean_text((string)$node->textContent);
        if ($line === '' || isset($seen[$line])) {
            continue;
        }
        if (!looks_like_name($line)) {
            continue;
        }
        $seen[$line] = true;
    }
    foreach (array_keys($seen) as $name) {
        $insert->execute([
            ':y' => $year,
            ':n' => $name,
            ':s' => $url,
        ]);
        $total++;
    }
}

echo "Importacao concluida. Egressos inseridos: {$total}\n";
