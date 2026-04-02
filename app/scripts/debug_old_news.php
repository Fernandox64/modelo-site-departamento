<?php
declare(strict_types=1);

function clean(string $text): string {
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8,ISO-8859-1,Windows-1252');
    return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
}
function fetch(string $url): ?string {
    $ctx = stream_context_create([
        'http' => ['timeout' => 45, 'header' => "User-Agent: decom-news-debug/1.0\r\n"],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ]);
    $h = @file_get_contents($url, false, $ctx);
    return $h === false ? null : $h;
}
function parseLinks(string $html, string $base): array {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8,ISO-8859-1,Windows-1252'));
    libxml_clear_errors();
    $xp = new DOMXPath($dom);
    $out = [];
    foreach ($xp->query('//a[@href]') as $a) {
        $href = trim((string)$a->getAttribute('href'));
        $txt = clean((string)$a->textContent);
        if ($href === '' || $txt === '') continue;
        if (str_starts_with($href, '/')) {
            $href = 'https://www3.decom.ufop.br' . $href;
        } elseif (!preg_match('~^https?://~i', $href)) {
            $href = rtrim($base, '/') . '/' . ltrim($href, './');
        }
        $out[] = [$txt, $href];
    }
    return $out;
}

$start = 'https://www3.decom.ufop.br/decom/noticias/noticias/';
$html = fetch($start);
if ($html === null) { echo "FAIL\n"; exit(1); }
$links = parseLinks($html, $start);
$cand = [];
foreach ($links as [$t,$u]) {
    $ul = strtolower($u);
    if (str_contains($ul, '/decom/noticias/noticias/') && !str_contains($ul, '/acervo/') && !str_contains($ul, '/categoria/')) {
        $cand[$u] = $t;
    }
}
echo "CANDIDATOS_INICIAIS=" . count($cand) . PHP_EOL;
$i=0;
foreach ($cand as $u=>$t) {
    echo "URL\t$u\t$t\n";
    $h = fetch($u);
    if ($h !== null) {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($h, 'HTML-ENTITIES', 'UTF-8,ISO-8859-1,Windows-1252'));
        libxml_clear_errors();
        $xp = new DOMXPath($dom);
        $title = clean((string)($xp->query('//h1')->item(0)?->textContent ?? ''));
        $p1 = $xp->query('//article//p')->length;
        $p2 = $xp->query('//div[contains(@class,"entry")]//p')->length;
        $p3 = $xp->query('//div[contains(@class,"post")]//p')->length;
        $p4 = $xp->query('//p')->length;
        echo "META\ttitle=$title\tarticleP=$p1\tentryP=$p2\tpostP=$p3\tallP=$p4\n";
    }
    $i++;
    if ($i>=12) break;
}
