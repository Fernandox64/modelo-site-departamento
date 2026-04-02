<?php
declare(strict_types=1);

$url = $argv[1] ?? '';
if ($url === '') {
    fwrite(STDERR, "Usage: php inspect_old_news_page.php <url>\n");
    exit(1);
}
$ctx = stream_context_create([
    'http' => ['timeout' => 45, 'header' => "User-Agent: decom-news-inspect/1.0\r\n"],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
]);
$html = @file_get_contents($url, false, $ctx);
if ($html === false) {
    fwrite(STDERR, "FAIL\n");
    exit(1);
}
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8,ISO-8859-1,Windows-1252'));
libxml_clear_errors();
$xp = new DOMXPath($dom);

function clean(string $s): string {
    $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $s = mb_convert_encoding($s, 'UTF-8', 'UTF-8,ISO-8859-1,Windows-1252');
    return trim(preg_replace('/\s+/u', ' ', $s) ?? $s);
}

echo "TITLE_TAG: " . clean((string)($xp->query('//title')->item(0)?->textContent ?? '')) . PHP_EOL;
echo "H1_COUNT: " . $xp->query('//h1')->length . PHP_EOL;
foreach ($xp->query('//h1') as $n) {
    echo "H1: " . clean((string)$n->textContent) . PHP_EOL;
}
echo "TIME_COUNT: " . $xp->query('//time')->length . PHP_EOL;
foreach ($xp->query('//time') as $n) {
    echo "TIME: " . clean((string)$n->textContent) . PHP_EOL;
}
echo "P_COUNT_ALL: " . $xp->query('//p')->length . PHP_EOL;
echo "P_COUNT_ARTICLE: " . $xp->query('//article//p')->length . PHP_EOL;
echo "P_COUNT_DIV_CONTENT: " . $xp->query('//div[contains(@class,\"content\")]//p')->length . PHP_EOL;
echo "P_COUNT_DIV_ENTRY: " . $xp->query('//div[contains(@class,\"entry\")]//p')->length . PHP_EOL;
echo "P_COUNT_DIV_POST: " . $xp->query('//div[contains(@class,\"post\")]//p')->length . PHP_EOL;
echo "P_COUNT_MAIN: " . $xp->query('//main//p')->length . PHP_EOL;
echo "---- SAMPLE P ----\n";
$i = 0;
foreach ($xp->query('//p') as $p) {
    $t = clean((string)$p->textContent);
    if ($t === '') continue;
    echo $t . PHP_EOL;
    $i++;
    if ($i >= 12) break;
}
