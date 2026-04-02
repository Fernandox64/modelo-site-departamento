<?php
$ctx = stream_context_create([
    'http' => ['timeout' => 40, 'header' => "User-Agent: decom-ppgcc-fetch/1.0\r\n"],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
]);

$startUrl = 'https://www3.decom.ufop.br/pos/discentes/egressos/';
$html = @file_get_contents($startUrl, false, $ctx);
if ($html === false) {
    fwrite(STDERR, "FAIL_START\n");
    exit(1);
}

libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($html);
$xp = new DOMXPath($dom);

$yearLinks = [];
foreach ($xp->query('//a[@href]') as $a) {
    $href = trim((string)$a->getAttribute('href'));
    $txt = trim(preg_replace('/\s+/', ' ', $a->textContent) ?? '');
    if ($href === '' || $txt === '') {
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

echo "YEARS\n";
foreach ($yearLinks as $y => $u) {
    echo $y . "\t" . $u . PHP_EOL;
}
echo "----\n";

foreach ($yearLinks as $year => $url) {
    $page = @file_get_contents($url, false, $ctx);
    if ($page === false) {
        continue;
    }
    $d = new DOMDocument();
    $d->loadHTML($page);
    $x = new DOMXPath($d);
    $items = [];

    // Try table rows first.
    foreach ($x->query('//table//tr') as $tr) {
        $cells = [];
        foreach ($x->query('.//th|.//td', $tr) as $c) {
            $cells[] = trim(preg_replace('/\s+/', ' ', $c->textContent) ?? '');
        }
        if (count($cells) >= 2) {
            $line = implode(' | ', array_filter($cells, fn($v) => $v !== ''));
            if ($line !== '') {
                $items[] = $line;
            }
        }
    }

    // Fallback: list items.
    if (count($items) < 3) {
        $items = [];
        foreach ($x->query('//li') as $li) {
            $txt = trim(preg_replace('/\s+/', ' ', $li->textContent) ?? '');
            if ($txt !== '' && mb_strlen($txt) > 5) {
                $items[] = $txt;
            }
        }
    }

    echo "YEAR " . $year . PHP_EOL;
    foreach (array_slice(array_values(array_unique($items)), 0, 1200) as $line) {
        echo $line . PHP_EOL;
    }
    echo "====\n";
}
