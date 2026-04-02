<?php
declare(strict_types=1);

$url = 'https://www3.decom.ufop.br/pos/processoseletivo/';
$ctx = stream_context_create([
    'http' => ['timeout' => 45, 'header' => "User-Agent: decom-ppgcc-processo/1.0\r\n"],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
]);

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

echo "LEN=" . strlen($html) . PHP_EOL;
echo "TITLE=" . trim((string)($xp->query('//title')->item(0)?->textContent ?? '')) . PHP_EOL;
echo "HEADINGS\n";
foreach ($xp->query('//h1|//h2|//h3') as $h) {
    $t = trim(preg_replace('/\s+/', ' ', (string)$h->textContent) ?? '');
    if ($t !== '') {
        echo "- " . $t . PHP_EOL;
    }
}

echo "LINKS\n";
$count = 0;
foreach ($xp->query('//a[@href]') as $a) {
    $href = trim((string)$a->getAttribute('href'));
    $txt = trim(preg_replace('/\s+/', ' ', (string)$a->textContent) ?? '');
    if ($href === '' || $txt === '') {
        continue;
    }
    if (str_starts_with($href, '/')) {
        $href = 'https://www3.decom.ufop.br' . $href;
    } elseif (!preg_match('~^https?://~i', $href)) {
        $href = 'https://www3.decom.ufop.br/pos/' . ltrim($href, './');
    }
    echo $txt . "\t" . $href . PHP_EOL;
    $count++;
    if ($count >= 180) {
        break;
    }
}
