<?php
declare(strict_types=1);

$url = $argv[1] ?? 'https://www3.decom.ufop.br/decom/noticias/noticias/?page=1';
$ctx = stream_context_create([
    'http' => ['timeout' => 45, 'header' => "User-Agent: decom-news-hrefs/1.0\r\n"],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
]);
$html = @file_get_contents($url, false, $ctx);
if ($html === false) {
    fwrite(STDERR, "FAIL\n");
    exit(1);
}
echo "URL: $url\n";
echo "LEN: " . strlen($html) . "\n";
preg_match_all('~href=["\']([^"\']+)["\']~i', $html, $m);
$all = array_values(array_unique($m[1] ?? []));
echo "TOTAL_HREFS: " . count($all) . "\n";
foreach ($all as $h) {
    $hl = strtolower($h);
    if (
        str_contains($hl, 'acervo') ||
        str_contains($hl, '/noticias/') ||
        str_contains($hl, '/decom/noticias/') ||
        str_contains($hl, '?page=')
    ) {
        echo $h . "\n";
    }
}
