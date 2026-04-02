<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';

function old_news_slugify(string $text): string
{
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    $text = trim($text, '-');
    if ($text === '') {
        $text = 'noticia-' . bin2hex(random_bytes(4));
    }
    return substr($text, 0, 150);
}

function old_news_clean(string $text): string
{
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8,ISO-8859-1,Windows-1252');
    $text = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    return $text;
}

function old_news_fetch(string $url): ?string
{
    $ctx = stream_context_create([
        'http' => ['timeout' => 45, 'header' => "User-Agent: decom-news-import/1.0\r\n"],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ]);
    $html = @file_get_contents($url, false, $ctx);
    return $html === false ? null : $html;
}

function old_news_parse_links(string $html, string $baseUrl): array
{
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8,ISO-8859-1,Windows-1252'));
    libxml_clear_errors();
    $xp = new DOMXPath($dom);

    $links = [];
    foreach ($xp->query('//a[@href]') as $a) {
        $href = trim((string)$a->getAttribute('href'));
        $txt = old_news_clean((string)$a->textContent);
        if ($href === '' || $txt === '') {
            continue;
        }
        if (str_starts_with($href, '/')) {
            $href = 'https://www3.decom.ufop.br' . $href;
        } elseif (!preg_match('~^https?://~i', $href)) {
            $href = rtrim($baseUrl, '/') . '/' . ltrim($href, './');
        }
        $links[] = ['text' => $txt, 'url' => $href];
    }
    return $links;
}

$start = 'https://www3.decom.ufop.br/decom/noticias/noticias/';
$indexHtml = old_news_fetch($start);
if ($indexHtml === null) {
    fwrite(STDERR, "Falha ao acessar indice de noticias.\n");
    exit(1);
}

$firstLinks = old_news_parse_links($indexHtml, $start);

$candidatePages = [$start => true];
$maxPage = 1;
foreach ($firstLinks as $l) {
    $u = $l['url'];
    $ul = strtolower($u);
    if (str_contains($ul, '?page=')) {
        $candidatePages[$u] = true;
        if (preg_match('/[?&]page=(\d+)/i', $u, $m) === 1) {
            $p = (int)$m[1];
            if ($p > $maxPage) {
                $maxPage = $p;
            }
        }
    }
}
if ($maxPage > 1) {
    for ($p = 1; $p <= $maxPage; $p++) {
        $candidatePages[$start . '?page=' . $p] = true;
    }
}

$newsLinks = [];
foreach (array_keys($candidatePages) as $pageUrl) {
    $html = old_news_fetch($pageUrl);
    if ($html === null) {
        continue;
    }
    foreach (old_news_parse_links($html, $pageUrl) as $l) {
        $u = $l['url'];
        $t = $l['text'];
        $ul = strtolower($u);
        if (!str_contains($ul, '/decom/noticias/')) {
            continue;
        }
        if (str_contains($ul, '/categoria/') || str_contains($ul, '/tag/') || str_contains($ul, 'mailto:')) {
            continue;
        }
        if (!str_contains($ul, '/acervo/')) {
            continue;
        }
        $newsLinks[$u] = $t;
    }
}

$pdo = db();
$inserted = 0;
$skipped = 0;

$insertStmt = $pdo->prepare(
    'INSERT INTO news_items (slug, title, summary, category, content, image, published_at)
     VALUES (:slug, :title, :summary, :category, :content, :image, :published_at)
     ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        summary = VALUES(summary),
        category = VALUES(category),
        content = VALUES(content),
        image = VALUES(image),
        published_at = VALUES(published_at)'
);

foreach ($newsLinks as $url => $fallbackTitle) {
    $html = old_news_fetch($url);
    if ($html === null) {
        $skipped++;
        continue;
    }
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8,ISO-8859-1,Windows-1252'));
    libxml_clear_errors();
    $xp = new DOMXPath($dom);

    $title = '';
    $h1Nodes = $xp->query('//h1');
    if ($h1Nodes && $h1Nodes->length > 0) {
        foreach ($h1Nodes as $h1) {
            $candidate = old_news_clean((string)$h1->textContent);
            if ($candidate !== '' && mb_strtolower($candidate, 'UTF-8') !== 'menu') {
                $title = $candidate;
                break;
            }
        }
    }
    if ($title === '') {
        $titleNode = $xp->query('//title')->item(0);
        $title = old_news_clean((string)($titleNode?->textContent ?? ''));
    }
    if ($title === '') {
        $title = $fallbackTitle !== '' ? $fallbackTitle : 'Noticia importada';
    }

    $paragraphs = [];
    foreach ($xp->query('//p') as $p) {
        $txt = old_news_clean((string)$p->textContent);
        $low = mb_strtolower($txt, 'UTF-8');
        if ($txt === '' || mb_strlen($txt, 'UTF-8') < 10) {
            continue;
        }
        if (str_contains($low, 'departamento de comput') || str_contains($low, 'universidade federal de ouro preto') || str_contains($low, 'decom@ufop.edu.br')) {
            continue;
        }
        if (preg_match('/escort|aposta|casino|bet/iu', $txt) === 1) {
            continue;
        }
        if ($txt === $title) {
            continue;
        }
        if ($txt !== '' && mb_strlen($txt, 'UTF-8') > 10) {
            $paragraphs[] = $txt;
        }
    }
    $paragraphs = array_values(array_unique($paragraphs));
    if (empty($paragraphs)) {
        $skipped++;
        continue;
    }

    $summary = mb_substr($paragraphs[0], 0, 280, 'UTF-8');
    $content = '<p>' . implode('</p><p>', array_map(static fn($x) => htmlspecialchars($x, ENT_QUOTES, 'UTF-8'), $paragraphs)) . '</p>';

    $dateText = $title . ' ' . implode(' ', array_slice($paragraphs, 0, 3));
    $publishedAt = date('Y-m-d H:i:s');
    if ($dateText !== '') {
        if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $dateText, $m) === 1) {
            $publishedAt = sprintf('%04d-%02d-%02d 12:00:00', (int)$m[3], (int)$m[2], (int)$m[1]);
        } elseif (preg_match('/(20\d{2})-(\d{2})-(\d{2})/', $dateText, $m) === 1) {
            $publishedAt = sprintf('%04d-%02d-%02d 12:00:00', (int)$m[1], (int)$m[2], (int)$m[3]);
        }
    }

    $slug = 'oldnews-' . substr(hash('sha1', $url), 0, 24);

    try {
        $insertStmt->execute([
            ':slug' => $slug,
            ':title' => $title,
            ':summary' => $summary,
            ':category' => 'Departamento',
            ':content' => $content,
            ':image' => '/assets/cards/noticia-portal.svg',
            ':published_at' => $publishedAt,
        ]);
        $inserted++;
    } catch (Throwable $e) {
        $skipped++;
    }
}

echo 'Importacao finalizada. Links candidatos: ' . count($newsLinks) . PHP_EOL;
echo 'Noticias inseridas: ' . $inserted . PHP_EOL;
echo 'Ignoradas/falhas: ' . $skipped . PHP_EOL;
