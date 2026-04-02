<?php
$ctx = stream_context_create([
    'http' => ['timeout' => 30, 'header' => "User-Agent: decom-map-fetch/1.0\r\n"],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
]);

$url = 'https://www3.decom.ufop.br/decom/decom/mapa-do-campus/';
$html = @file_get_contents($url, false, $ctx);
if ($html === false) {
    fwrite(STDERR, "FAIL\n");
    exit(1);
}

$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_clear_errors();
$xp = new DOMXPath($dom);

$seen = [];
$nodes = $xp->query('//img[@src]');
foreach ($nodes as $img) {
    $src = trim((string)$img->getAttribute('src'));
    if ($src === '') {
        continue;
    }
    if (str_starts_with($src, '//')) {
        $src = 'https:' . $src;
    } elseif (str_starts_with($src, '/')) {
        $src = 'https://www3.decom.ufop.br' . $src;
    } elseif (!preg_match('~^https?://~i', $src)) {
        $src = 'https://www3.decom.ufop.br/decom/' . ltrim($src, './');
    }

    if (preg_match('~\.(jpg|jpeg|png|webp|svg)(\?.*)?$~i', $src) && !isset($seen[$src])) {
        $seen[$src] = true;
        echo $src, PHP_EOL;
    }
}
