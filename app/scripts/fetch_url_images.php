<?php
if ($argc < 2) {
    fwrite(STDERR, "Usage: php fetch_url_images.php <url>\n");
    exit(1);
}

$url = (string)$argv[1];
$ctx = stream_context_create([
    'http' => ['timeout' => 30, 'header' => "User-Agent: decom-image-fetch/1.0\r\n"],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
]);

$html = @file_get_contents($url, false, $ctx);
if ($html === false) {
    fwrite(STDERR, "FAIL\n");
    exit(1);
}

$base = parse_url($url);
$scheme = $base['scheme'] ?? 'https';
$host = $base['host'] ?? '';
$root = $scheme . '://' . $host;
$path = $base['path'] ?? '/';
$dir = rtrim(str_replace('\\', '/', dirname($path)), '/');
$dirBase = $root . ($dir === '' ? '' : '/' . ltrim($dir, '/'));

function abs_url(string $src, string $root, string $dirBase): string
{
    if ($src === '') {
        return '';
    }
    if (str_starts_with($src, '//')) {
        return 'https:' . $src;
    }
    if (preg_match('~^https?://~i', $src)) {
        return $src;
    }
    if (str_starts_with($src, '/')) {
        return $root . $src;
    }
    return $dirBase . '/' . ltrim($src, './');
}

$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_clear_errors();
$xp = new DOMXPath($dom);
$seen = [];

$imgNodes = $xp->query('//img[@src]');
foreach ($imgNodes as $img) {
    $src = abs_url(trim((string)$img->getAttribute('src')), $root, $dirBase);
    if ($src !== '' && preg_match('~\.(jpg|jpeg|png|webp|svg)(\?.*)?$~i', $src)) {
        $seen[$src] = true;
    }
}

$metaNodes = $xp->query('//meta[@property="og:image" or @name="twitter:image"]');
foreach ($metaNodes as $meta) {
    $src = abs_url(trim((string)$meta->getAttribute('content')), $root, $dirBase);
    if ($src !== '' && preg_match('~\.(jpg|jpeg|png|webp|svg)(\?.*)?$~i', $src)) {
        $seen[$src] = true;
    }
}

foreach (array_keys($seen) as $img) {
    echo $img, PHP_EOL;
}
