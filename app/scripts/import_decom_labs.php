<?php
declare(strict_types=1);

const LABS_URL_PRIMARY = 'https://www3.decom.ufop.br/decom/pesquisa/labs/';
const LABS_URL_FALLBACK = 'https://www3.decom.ufop.br/decom/pesquisa/';

function db(): PDO {
    $host = getenv('DB_HOST') ?: 'db';
    $port = getenv('DB_PORT') ?: '3306';
    $database = getenv('DB_DATABASE') ?: 'newsdb';
    $username = getenv('DB_USERNAME') ?: 'newsuser';
    $password = getenv('DB_PASSWORD') ?: 'newspass';
    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
    return new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function ensure_schema(PDO $pdo): void {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS research_labs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(150) NOT NULL UNIQUE,
            name VARCHAR(180) NOT NULL,
            summary TEXT NOT NULL,
            site_url VARCHAR(255) DEFAULT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            sort_order INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    );
}

function fetch_html(string $url): ?string {
    $ctx = stream_context_create([
        'http' => ['timeout' => 20, 'header' => "User-Agent: decom-labs-importer/1.0\r\n"],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ]);
    $html = @file_get_contents($url, false, $ctx);
    return $html === false ? null : $html;
}

function slugify(string $text): string {
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    $text = trim($text, '-');
    return $text !== '' ? substr($text, 0, 140) : 'lab-' . bin2hex(random_bytes(4));
}

function normalize_spaces(string $text): string {
    return trim((string)(preg_replace('/\s+/u', ' ', html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? ''));
}

function parse_labs(string $html): array {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $links = $xpath->query('//a');
    if ($links === false) {
        return [];
    }

    $labs = [];
    foreach ($links as $a) {
        $text = normalize_spaces($a->textContent);
        $href = trim((string)$a->getAttribute('href'));
        if ($text === '' || $href === '') {
            continue;
        }
        if (!preg_match('/Laborat[óo]rio|Lab\b/u', $text)) {
            continue;
        }
        if (!preg_match('/^https?:\/\//i', $href)) {
            continue;
        }
        $text = str_replace(['&nbsp;'], ' ', $text);
        $parts = array_map('trim', explode(' - ', $text, 2));
        $name = $parts[0] !== '' ? $parts[0] : $text;
        $summary = $parts[1] ?? ('Laboratorio de pesquisa do DECOM.');
        $labs[] = [
            'slug' => slugify($name),
            'name' => $name,
            'summary' => rtrim($summary, '.') . '.',
            'site_url' => $href,
        ];
    }

    $unique = [];
    foreach ($labs as $lab) {
        $unique[$lab['slug']] = $lab;
    }
    return array_values($unique);
}

function save_labs(PDO $pdo, array $labs): int {
    $pdo->exec('DELETE FROM research_labs');
    $stmt = $pdo->prepare(
        "INSERT INTO research_labs (slug, name, summary, site_url, is_active, sort_order)
         VALUES (:slug, :name, :summary, :site_url, 1, :sort_order)"
    );
    $i = 0;
    foreach ($labs as $lab) {
        $i++;
        $stmt->execute([
            ':slug' => $lab['slug'],
            ':name' => $lab['name'],
            ':summary' => $lab['summary'],
            ':site_url' => $lab['site_url'],
            ':sort_order' => $i,
        ]);
    }
    return $i;
}

function main(): void {
    $html = fetch_html(LABS_URL_PRIMARY) ?? fetch_html(LABS_URL_FALLBACK);
    if ($html === null) {
        throw new RuntimeException('Falha ao baixar pagina de laboratorios.');
    }

    $labs = parse_labs($html);
    if (empty($labs)) {
        throw new RuntimeException('Nenhum laboratorio encontrado na pagina de origem.');
    }

    $pdo = db();
    ensure_schema($pdo);
    $total = save_labs($pdo, $labs);
    echo "Importacao de laboratorios concluida. Total: {$total}\n";
}

try {
    main();
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, 'Erro: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
