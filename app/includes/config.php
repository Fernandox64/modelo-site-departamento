<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

const SITE_NAME = 'Departamento de Computação';
const SITE_SIGLA = 'DECOM';
const SITE_UNIVERSITY = 'Universidade Federal de Ouro Preto';
const SITE_EMAIL = 'decom@ufop.edu.br';
const SITE_PHONE = '+55 31 3559-1692';
const SITE_ADDRESS = 'Campus Morro do Cruzeiro, Ouro Preto - MG';

const ADMIN_MAX_LOGIN_ATTEMPTS = 5;
const ADMIN_LOCKOUT_SECONDS = 900;

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $host = getenv('DB_HOST') ?: 'db';
    $port = getenv('DB_PORT') ?: '3306';
    $database = getenv('DB_DATABASE') ?: 'newsdb';
    $username = getenv('DB_USERNAME') ?: 'newsuser';
    $password = getenv('DB_PASSWORD') ?: 'newspass';
    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    return $pdo;
}
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function sanitize_rich_text(string $html): string {
    $allowed = '<p><br><strong><b><em><i><u><ul><ol><li><a><h2><h3><h4><blockquote><img><table><thead><tbody><tr><td><th><hr>';
    $clean = strip_tags($html, $allowed);
    $clean = preg_replace('/\sstyle\s*=\s*("|\').*?\1/iu', '', $clean) ?? $clean;
    $clean = preg_replace('/\son\w+\s*=\s*("|\').*?\1/iu', '', $clean) ?? $clean;
    $clean = preg_replace('/href\s*=\s*("|\')\s*javascript:[^"\']*\1/iu', 'href="#"', $clean) ?? $clean;
    return trim($clean);
}
function render_rich_text(string $content): string {
    $safe = sanitize_rich_text($content);
    if ($safe === '') {
        return '';
    }
    $hasTag = preg_match('/<\s*[a-z][^>]*>/i', $safe) === 1;
    if (!$hasTag) {
        return nl2br(e($safe));
    }
    return $safe;
}
function page_header(string $title): void { $pageTitle = $title; require __DIR__ . '/header.php'; }
function page_footer(): void { require __DIR__ . '/footer.php'; }
function is_admin_logged_in(): bool { return !empty($_SESSION['admin_ok']); }
function redirect(string $path): void { header("Location: {$path}"); exit; }
function require_admin(): void { if (!is_admin_logged_in()) { redirect('/admin/login.php'); } }
function ensure_site_settings_table(): void {
    static $ready = false;
    if ($ready) {
        return;
    }
    $ready = true;
    try {
        db()->exec(
            "CREATE TABLE IF NOT EXISTS site_settings (
                setting_key VARCHAR(120) NOT NULL PRIMARY KEY,
                setting_value TEXT NOT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );
    } catch (Throwable $e) {
        error_log('Failed ensuring site_settings table: ' . $e->getMessage());
    }
}
function site_setting_get(string $key, string $default = ''): string {
    ensure_site_settings_table();
    try {
        $stmt = db()->prepare('SELECT setting_value FROM site_settings WHERE setting_key = :k');
        $stmt->execute([':k' => $key]);
        $value = $stmt->fetchColumn();
        if ($value === false || $value === null) {
            return $default;
        }
        return (string)$value;
    } catch (Throwable $e) {
        error_log('Failed loading site setting: ' . $e->getMessage());
        return $default;
    }
}
function site_setting_set(string $key, string $value): void {
    ensure_site_settings_table();
    $stmt = db()->prepare(
        'INSERT INTO site_settings (setting_key, setting_value)
         VALUES (:k, :v)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    $stmt->execute([':k' => $key, ':v' => $value]);
}
function normalize_menu_url(string $url, string $fallback): string {
    $url = trim($url);
    if ($url === '') {
        return $fallback;
    }
    if (preg_match('~^https?://~i', $url) === 1 || str_starts_with($url, '/')) {
        return $url;
    }
    return '/' . ltrim($url, '/');
}
function primary_menu_item(string $slot): array {
    $defaults = [
        'graduacao' => ['label' => 'Graduacao', 'url' => '/ensino/ciencia-computacao.php'],
        'pos_graduacao' => ['label' => 'Pos-graduacao', 'url' => '/ensino/pos-graduacao.php'],
    ];
    $default = $defaults[$slot] ?? ['label' => 'Menu', 'url' => '/'];
    $label = trim(site_setting_get('menu_' . $slot . '_label', $default['label']));
    $url = normalize_menu_url(site_setting_get('menu_' . $slot . '_url', $default['url']), $default['url']);
    return [
        'label' => $label !== '' ? $label : $default['label'],
        'url' => $url,
    ];
}
function ensure_ppgcc_tables(): void {
    static $ready = false;
    if ($ready) {
        return;
    }
    $ready = true;
    try {
        db()->exec(
            "CREATE TABLE IF NOT EXISTS ppgcc_page_content (
                id INT NOT NULL PRIMARY KEY,
                title VARCHAR(200) NOT NULL,
                intro_html MEDIUMTEXT NOT NULL,
                ingresso_html MEDIUMTEXT NOT NULL,
                editais_html MEDIUMTEXT NOT NULL,
                grade_html MEDIUMTEXT NOT NULL,
                docencia_html MEDIUMTEXT NOT NULL,
                bolsas_html MEDIUMTEXT NOT NULL,
                graduacao_html MEDIUMTEXT NOT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );
        db()->exec(
            "CREATE TABLE IF NOT EXISTS ppgcc_graduates (
                id INT AUTO_INCREMENT PRIMARY KEY,
                graduate_year INT NOT NULL,
                student_name VARCHAR(220) NOT NULL,
                source_url VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_ppgcc_graduate (graduate_year, student_name)
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );
        db()->exec(
            "CREATE TABLE IF NOT EXISTS ppgcc_notices (
                id INT AUTO_INCREMENT PRIMARY KEY,
                slug VARCHAR(160) NOT NULL UNIQUE,
                title VARCHAR(220) NOT NULL,
                summary TEXT NOT NULL,
                notice_type ENUM('edital','informacao') NOT NULL DEFAULT 'edital',
                notice_url VARCHAR(255) DEFAULT NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                published_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );
        db()->exec(
            "CREATE TABLE IF NOT EXISTS ppgcc_selection_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                group_title VARCHAR(255) NOT NULL,
                item_title VARCHAR(255) NOT NULL,
                item_url VARCHAR(600) NOT NULL,
                item_hash CHAR(64) NOT NULL UNIQUE,
                sort_order INT NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );
    } catch (Throwable $e) {
        error_log('Failed ensuring PPGCC tables: ' . $e->getMessage());
    }
}
function simple_slugify(string $text): string {
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    $text = trim($text, '-');
    if ($text === '') {
        $text = 'item-' . bin2hex(random_bytes(4));
    }
    return substr($text, 0, 150);
}
function ppgcc_notice_unique_slug(string $base, ?int $ignoreId = null): string {
    ensure_ppgcc_tables();
    $slug = simple_slugify($base);
    $i = 1;
    while (true) {
        $sql = 'SELECT id FROM ppgcc_notices WHERE slug = :slug';
        $params = [':slug' => $slug];
        if ($ignoreId !== null) {
            $sql .= ' AND id <> :id';
            $params[':id'] = $ignoreId;
        }
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        if (!$stmt->fetch()) {
            return $slug;
        }
        $i++;
        $slug = substr(simple_slugify($base), 0, 145) . '-' . $i;
    }
}
function ppgcc_default_content(): array {
    return [
        'title' => 'Pos-graduacao em Computacao',
        'intro_html' => '<p>O PPGCC/UFOP oferece Mestrado e Doutorado em Ciencia da Computacao, com foco em pesquisa, inovacao tecnologica e formacao docente.</p>',
        'ingresso_html' => '<p>O ingresso ocorre por edital de processo seletivo. Os criterios incluem analise documental, etapas definidas em edital e requisitos academicos para cada nivel.</p>',
        'editais_html' => '<p>Editais recentes incluem selecao para ingresso (mestrado e doutorado), bolsa de doutorado e oportunidades de doutorado sanduiche (PDSE).</p>',
        'grade_html' => '<p>A grade curricular inclui disciplinas basicas e eletivas. Carga minima de creditos: mestrado (24) e doutorado (36), conforme normas do programa.</p>',
        'docencia_html' => '<p>O estagio em docencia e regulado por normas institucionais e do programa, podendo contabilizar creditos conforme regras vigentes.</p>',
        'bolsas_html' => '<p>O programa publica chamadas e criterios de bolsas (CAPES/CNPq/FAPEMIG e PROAP), sujeitos a disponibilidade e regras internas.</p>',
        'graduacao_html' => '<p>Alunos da graduacao podem cursar disciplinas isoladas da pos, conforme calendario e exigencias documentais divulgadas em cada periodo.</p>',
    ];
}
function ppgcc_content_get(): array {
    ensure_ppgcc_tables();
    try {
        $stmt = db()->query('SELECT * FROM ppgcc_page_content WHERE id = 1');
        $row = $stmt->fetch();
        if ($row) {
            return $row;
        }
        $default = ppgcc_default_content();
        $insert = db()->prepare(
            'INSERT INTO ppgcc_page_content
             (id, title, intro_html, ingresso_html, editais_html, grade_html, docencia_html, bolsas_html, graduacao_html)
             VALUES (1, :title, :intro_html, :ingresso_html, :editais_html, :grade_html, :docencia_html, :bolsas_html, :graduacao_html)'
        );
        $insert->execute([
            ':title' => $default['title'],
            ':intro_html' => $default['intro_html'],
            ':ingresso_html' => $default['ingresso_html'],
            ':editais_html' => $default['editais_html'],
            ':grade_html' => $default['grade_html'],
            ':docencia_html' => $default['docencia_html'],
            ':bolsas_html' => $default['bolsas_html'],
            ':graduacao_html' => $default['graduacao_html'],
        ]);
        return array_merge(['id' => 1], $default);
    } catch (Throwable $e) {
        error_log('Failed loading ppgcc content: ' . $e->getMessage());
        return array_merge(['id' => 1], ppgcc_default_content());
    }
}
function ppgcc_content_save(array $data): void {
    ensure_ppgcc_tables();
    $stmt = db()->prepare(
        'UPDATE ppgcc_page_content
         SET title = :title,
             intro_html = :intro_html,
             ingresso_html = :ingresso_html,
             editais_html = :editais_html,
             grade_html = :grade_html,
             docencia_html = :docencia_html,
             bolsas_html = :bolsas_html,
             graduacao_html = :graduacao_html
         WHERE id = 1'
    );
    $stmt->execute([
        ':title' => trim((string)($data['title'] ?? 'Pos-graduacao em Computacao')),
        ':intro_html' => sanitize_rich_text((string)($data['intro_html'] ?? '')),
        ':ingresso_html' => sanitize_rich_text((string)($data['ingresso_html'] ?? '')),
        ':editais_html' => sanitize_rich_text((string)($data['editais_html'] ?? '')),
        ':grade_html' => sanitize_rich_text((string)($data['grade_html'] ?? '')),
        ':docencia_html' => sanitize_rich_text((string)($data['docencia_html'] ?? '')),
        ':bolsas_html' => sanitize_rich_text((string)($data['bolsas_html'] ?? '')),
        ':graduacao_html' => sanitize_rich_text((string)($data['graduacao_html'] ?? '')),
    ]);
}
function ppgcc_graduate_years(): array {
    ensure_ppgcc_tables();
    try {
        $rows = db()->query('SELECT graduate_year, COUNT(*) AS total FROM ppgcc_graduates GROUP BY graduate_year ORDER BY graduate_year DESC')->fetchAll();
        return $rows ?: [];
    } catch (Throwable $e) {
        error_log('Failed loading graduate years: ' . $e->getMessage());
        return [];
    }
}
function ppgcc_graduates_by_year(int $year): array {
    ensure_ppgcc_tables();
    try {
        $stmt = db()->prepare('SELECT id, graduate_year, student_name, source_url FROM ppgcc_graduates WHERE graduate_year = :y ORDER BY student_name ASC');
        $stmt->execute([':y' => $year]);
        return $stmt->fetchAll() ?: [];
    } catch (Throwable $e) {
        error_log('Failed loading graduates by year: ' . $e->getMessage());
        return [];
    }
}
function ppgcc_notices(int $limit = 8, bool $onlyActive = true): array {
    ensure_ppgcc_tables();
    try {
        $limit = max(1, min($limit, 50));
        $sql = 'SELECT id, slug, title, summary, notice_type, notice_url, is_active, published_at
                FROM ppgcc_notices';
        if ($onlyActive) {
            $sql .= ' WHERE is_active = 1';
        }
        $sql .= ' ORDER BY published_at DESC, id DESC LIMIT ' . $limit;
        return db()->query($sql)->fetchAll() ?: [];
    } catch (Throwable $e) {
        error_log('Failed loading ppgcc notices: ' . $e->getMessage());
        return [];
    }
}
function ppgcc_notice_find(int $id): ?array {
    ensure_ppgcc_tables();
    $stmt = db()->prepare('SELECT * FROM ppgcc_notices WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}
function ppgcc_selection_items_grouped(): array {
    ensure_ppgcc_tables();
    try {
        $rows = db()->query(
            'SELECT id, group_title, item_title, item_url, sort_order
             FROM ppgcc_selection_items
             ORDER BY sort_order ASC, id ASC'
        )->fetchAll();
        $grouped = [];
        foreach ($rows as $r) {
            $g = (string)$r['group_title'];
            if (!isset($grouped[$g])) {
                $grouped[$g] = [];
            }
            $grouped[$g][] = $r;
        }
        return $grouped;
    } catch (Throwable $e) {
        error_log('Failed loading selection items: ' . $e->getMessage());
        return [];
    }
}
function ppgcc_import_selection_page(): array {
    ensure_ppgcc_tables();
    $url = 'https://www3.decom.ufop.br/pos/processoseletivo/';
    $ctx = stream_context_create([
        'http' => ['timeout' => 45, 'header' => "User-Agent: decom-ppgcc-import/1.0\r\n"],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ]);
    $html = @file_get_contents($url, false, $ctx);
    if ($html === false) {
        return ['ok' => false, 'inserted' => 0, 'message' => 'Falha ao acessar a fonte oficial.'];
    }

    $normalizeText = static function (string $text): string {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8,ISO-8859-1,Windows-1252');
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
        return $text;
    };

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $normalizedHtml = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8,ISO-8859-1,Windows-1252');
    $dom->loadHTML($normalizedHtml);
    libxml_clear_errors();
    $xp = new DOMXPath($dom);

    $nodes = $xp->query('//h2|//h3|//a[@href]');
    if (!$nodes) {
        return ['ok' => false, 'inserted' => 0, 'message' => 'Nao foi possivel interpretar o HTML da pagina oficial.'];
    }

    $relevantWords = [
        'edital', 'resultado', 'formulario', 'inscricao', 'comissao', 'barema',
        'pontuacao', 'planilha', 'candidato', 'lista', 'homologacao', 'curriculo', 'final',
    ];
    $groups = [];
    $currentGroup = '';
    foreach ($nodes as $node) {
        $name = strtolower((string)$node->nodeName);
        $text = $normalizeText((string)$node->textContent);
        if ($name === 'h2' || $name === 'h3') {
            if ($text !== '' && preg_match('/(edital|processos seletivos|comissao)/iu', $text) === 1) {
                $currentGroup = $text;
            }
            continue;
        }
        if ($name !== 'a' || $currentGroup === '' || $text === '') {
            continue;
        }
        $href = trim((string)$node->getAttribute('href'));
        if ($href === '' || str_starts_with($href, 'mailto:') || str_starts_with($href, '#')) {
            continue;
        }
        if (str_starts_with($href, '/')) {
            $href = 'https://www3.decom.ufop.br' . $href;
        } elseif (!preg_match('~^https?://~i', $href)) {
            $href = 'https://www3.decom.ufop.br/pos/' . ltrim($href, './');
        }
        $urlLower = strtolower($href);
        $textLower = mb_strtolower($text, 'UTF-8');
        $isDocLink = str_contains($urlLower, 'drive.google.com') || str_contains($urlLower, 'docs.google.com') || str_contains($urlLower, 'forms.gle');
        $hasKeyword = false;
        foreach ($relevantWords as $w) {
            if (str_contains($textLower, $w)) {
                $hasKeyword = true;
                break;
            }
        }
        if (!$isDocLink && !$hasKeyword) {
            continue;
        }
        if (!isset($groups[$currentGroup])) {
            $groups[$currentGroup] = [];
        }
        $groups[$currentGroup][] = ['title' => $text, 'url' => $href];
    }

    try {
        db()->exec('DELETE FROM ppgcc_selection_items');
        $stmt = db()->prepare(
            'INSERT INTO ppgcc_selection_items (group_title, item_title, item_url, item_hash, sort_order)
             VALUES (:g, :t, :u, :h, :o)'
        );
        $order = 1;
        $inserted = 0;
        foreach ($groups as $groupTitle => $items) {
            $seen = [];
            foreach ($items as $it) {
                $hash = hash('sha256', $groupTitle . '|' . $it['title'] . '|' . $it['url']);
                if (isset($seen[$hash])) {
                    continue;
                }
                $seen[$hash] = true;
                $stmt->execute([
                    ':g' => $groupTitle,
                    ':t' => $it['title'],
                    ':u' => $it['url'],
                    ':h' => $hash,
                    ':o' => $order++,
                ]);
                $inserted++;
            }
        }
        return ['ok' => true, 'inserted' => $inserted, 'message' => 'Importacao concluida com sucesso.'];
    } catch (Throwable $e) {
        error_log('Failed importing selection page: ' . $e->getMessage());
        return ['ok' => false, 'inserted' => 0, 'message' => 'Falha ao salvar dados importados.'];
    }
}
function admin_email_config(): string {
    return trim((string)(getenv('ADMIN_EMAIL') ?: ''));
}
function admin_password_hash_config(): string {
    return trim((string)(getenv('ADMIN_PASSWORD_HASH') ?: ''));
}
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string)$_SESSION['csrf_token'];
}
function is_valid_csrf_token(?string $token): bool {
    if (!is_string($token) || $token === '' || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals((string)$_SESSION['csrf_token'], $token);
}
function admin_is_login_locked(): bool {
    $lockUntil = (int)($_SESSION['admin_lock_until'] ?? 0);
    return $lockUntil > time();
}
function admin_register_login_failure(): void {
    $attempts = (int)($_SESSION['admin_login_attempts'] ?? 0) + 1;
    $_SESSION['admin_login_attempts'] = $attempts;
    if ($attempts >= ADMIN_MAX_LOGIN_ATTEMPTS) {
        $_SESSION['admin_lock_until'] = time() + ADMIN_LOCKOUT_SECONDS;
    }
}
function admin_clear_login_failures(): void {
    unset($_SESSION['admin_login_attempts'], $_SESSION['admin_lock_until']);
}
function admin_login(string $email, string $password): bool {
    $adminEmail = admin_email_config();
    $adminPasswordHash = admin_password_hash_config();
    if ($adminEmail === '' || $adminPasswordHash === '') {
        return false;
    }
    $isValidEmail = hash_equals($adminEmail, $email);
    $isValidPassword = password_verify($password, $adminPasswordHash);
    if (!$isValidEmail || !$isValidPassword || admin_is_login_locked()) {
        return false;
    }
    session_regenerate_id(true);
    $_SESSION['admin_ok'] = true;
    admin_clear_login_failures();
    return true;
}
function admin_logout(): void {
    $_SESSION = [];
    session_destroy();
}

function fetch_content_items(string $table): array {
    if (!in_array($table, ['news_items', 'edital_items', 'defesa_items', 'job_items'], true)) {
        return [];
    }
    $sql = "SELECT slug, title, summary, category, content, image FROM {$table} ORDER BY published_at DESC, id DESC";
    return db()->query($sql)->fetchAll();
}

function demo_news(): array {
    try {
        $items = fetch_content_items('news_items');
        if (!empty($items)) {
            return $items;
        }
    } catch (Throwable $e) {
        error_log('Failed loading news_items: ' . $e->getMessage());
    }
    return [];
}
function demo_editais(): array {
    try {
        $items = fetch_content_items('edital_items');
        if (!empty($items)) {
            return $items;
        }
    } catch (Throwable $e) {
        error_log('Failed loading edital_items: ' . $e->getMessage());
    }
    return [];
}
function demo_defesas(): array {
    try {
        $items = fetch_content_items('defesa_items');
        if (!empty($items)) {
            return $items;
        }
    } catch (Throwable $e) {
        error_log('Failed loading defesa_items: ' . $e->getMessage());
    }
    return [
      ['slug'=>'defesas-monografia-2026-1','title'=>'Defesas de monografia 2026/1','summary'=>'Agenda de bancas de monografia do semestre.','category'=>'Defesas','content'=>'Conteúdo demonstrativo para defesas.','image'=>'/assets/cards/noticia-pesquisa.svg']
    ];
}
function demo_jobs(): array {
    try {
        $items = fetch_content_items('job_items');
        if (!empty($items)) {
            return $items;
        }
    } catch (Throwable $e) {
        error_log('Failed loading job_items: ' . $e->getMessage());
    }
    return [
      ['slug'=>'vaga-estagio-web','title'=>'Vaga de estágio em desenvolvimento web','summary'=>'Empresa parceira busca estudante com noções de PHP e banco de dados.','category'=>'Carreiras','content'=>'Conteúdo demonstrativo para estágios e empregos.','image'=>'/assets/cards/noticia-portal.svg']
    ];
}
function find_demo_item(string $slug): ?array {
    foreach (array_merge(demo_news(), demo_editais(), demo_defesas(), demo_jobs()) as $item) {
        if ($item['slug'] === $slug) return $item;
    }
    return null;
}
function card_image_for_slug(string $slug): string {
    $map = [
        'portal-em-teste' => '/assets/cards/noticia-portal.svg',
        'horarios-de-aula-disponiveis' => '/assets/cards/noticia-horarios.svg',
        'grupo-de-pesquisa-abre-chamada' => '/assets/cards/noticia-pesquisa.svg',
        'edital-monitoria-2026-1' => '/assets/cards/edital-monitoria.svg',
        'edital-bolsas-extensao' => '/assets/cards/edital-extensao.svg',
        'qualificacao-mestrado-eduardo-henke-2026-03-26' => '/assets/cards/noticia-pesquisa.svg',
        'horario-aulas-decom-2026-1' => '/assets/cards/noticia-horarios.svg',
        'defesa-doutorado-guilherme-augusto-2026-03-20' => '/assets/cards/noticia-portal.svg',
        'inicio-matriculas-isoladas-ppgcc-2026-1' => '/assets/cards/edital-extensao.svg',
        'grade-disciplinas-matricula-2026-1' => '/assets/cards/edital-monitoria.svg',
        'horarios-monitorias-decom' => '/assets/cards/edital-monitoria.svg',
        'defesas-monografia-2026-1' => '/assets/cards/noticia-pesquisa.svg',
        'vaga-estagio-web' => '/assets/cards/noticia-portal.svg',
    ];
    return $map[$slug] ?? '/assets/cards/noticia-default.svg';
}
function content_image(array $item): string {
    $image = trim((string)($item['image'] ?? ''));
    if ($image !== '') {
        return $image;
    }
    return card_image_for_slug((string)($item['slug'] ?? ''));
}
function fetch_people_items(string $type): array {
    if (!in_array($type, ['docente', 'funcionario'], true)) {
        return [];
    }
    $sql = "SELECT slug, name, role_type, position, degree, website_url, lattes_url, email, phone, room, interests, bio, photo_url
            FROM people_items
            WHERE role_type = :role_type
            ORDER BY sort_order ASC, name ASC, id ASC";
    $stmt = db()->prepare($sql);
    $stmt->execute([':role_type' => $type]);
    return $stmt->fetchAll();
}
function person_initials(string $name): string {
    $name = trim($name);
    if ($name === '') {
        return 'DE';
    }
    $parts = preg_split('/\s+/', $name) ?: [];
    $first = mb_substr($parts[0] ?? '', 0, 1, 'UTF-8');
    $last = mb_substr($parts[count($parts) - 1] ?? '', 0, 1, 'UTF-8');
    $initials = mb_strtoupper($first . $last, 'UTF-8');
    return $initials !== '' ? $initials : 'DE';
}
function person_photo_placeholder(string $name): string {
    $palette = [
        ['#0f4c81', '#0f8ccf'],
        ['#1f6f5f', '#2bb673'],
        ['#6c3a9c', '#8b5cf6'],
        ['#7a2e2e', '#ef4444'],
        ['#3f3f46', '#71717a'],
    ];
    $index = abs(crc32($name)) % count($palette);
    [$from, $to] = $palette[$index];
    $initials = person_initials($name);
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="320" height="320" viewBox="0 0 320 320">'
        . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
        . '<stop offset="0%" stop-color="' . $from . '"/><stop offset="100%" stop-color="' . $to . '"/>'
        . '</linearGradient></defs>'
        . '<rect width="320" height="320" fill="url(#g)"/>'
        . '<text x="50%" y="53%" dominant-baseline="middle" text-anchor="middle"'
        . ' fill="#ffffff" font-family="Arial, Helvetica, sans-serif" font-size="108" font-weight="700">'
        . htmlspecialchars($initials, ENT_QUOTES, 'UTF-8')
        . '</text></svg>';
    return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
}
function person_photo_url(array $item): string {
    $photo = trim((string)($item['photo_url'] ?? ''));
    if ($photo !== '') {
        return $photo;
    }
    return person_photo_placeholder((string)($item['name'] ?? 'DECOM'));
}
function docentes(): array {
    try {
        $items = fetch_people_items('docente');
        if (!empty($items)) {
            return $items;
        }
    } catch (Throwable $e) {
        error_log('Failed loading docente profiles: ' . $e->getMessage());
    }
    return [
        ['name'=>'Ana Paula Ribeiro','position'=>'Professora Adjunta','degree'=>'Doutora em Ciência da Computação','website_url'=>'','lattes_url'=>'','email'=>'ana.ribeiro@ufop.edu.br','phone'=>'(31) 3559-1601','room'=>'Instituto de Ciências Exatas e Biológicas','interests'=>'Engenharia de software e sistemas distribuídos.','bio'=>'Atua em engenharia de software e sistemas distribuídos.','photo_url'=>''],
        ['name'=>'Bruno Carvalho Mendes','position'=>'Professor Associado','degree'=>'Doutor em Ciência da Computação','website_url'=>'','lattes_url'=>'','email'=>'bruno.mendes@ufop.edu.br','phone'=>'(31) 3559-1602','room'=>'Instituto de Ciências Exatas e Biológicas','interests'=>'Inteligência artificial e mineração de dados.','bio'=>'Atua em inteligência artificial e mineração de dados.','photo_url'=>''],
        ['name'=>'Camila Freitas Lopes','position'=>'Professora Adjunta','degree'=>'Doutora em Computação','website_url'=>'','lattes_url'=>'','email'=>'camila.lopes@ufop.edu.br','phone'=>'(31) 3559-1603','room'=>'Instituto de Ciências Exatas e Biológicas','interests'=>'Computação gráfica e IHC.','bio'=>'Atua em computação gráfica e interação humano-computador.','photo_url'=>''],
    ];
}
function funcionarios(): array {
    try {
        $items = fetch_people_items('funcionario');
        if (!empty($items)) {
            return $items;
        }
    } catch (Throwable $e) {
        error_log('Failed loading funcionario profiles: ' . $e->getMessage());
    }
    return [
        ['name'=>'Mariana Souza Almeida','position'=>'Secretária Administrativa','degree'=>'','website_url'=>'','lattes_url'=>'','email'=>'mariana.almeida@ufop.edu.br','phone'=>'(31) 3559-1692','room'=>'Instituto de Ciências Exatas e Biológicas','interests'=>'Atendimento acadêmico e administrativo.','bio'=>'Atendimento acadêmico e administrativo do departamento.','photo_url'=>''],
        ['name'=>'Paulo Henrique Silva','position'=>'Técnico em TI','degree'=>'','website_url'=>'','lattes_url'=>'','email'=>'paulo.silva@ufop.edu.br','phone'=>'(31) 3559-1693','room'=>'Instituto de Ciências Exatas e Biológicas','interests'=>'Infraestrutura e suporte de laboratórios.','bio'=>'Suporte de laboratórios, sistemas e infraestrutura local.','photo_url'=>''],
    ];
}
function fetch_research_labs(): array {
    try {
        $sql = "SELECT slug, name, summary, site_url
                FROM research_labs
                WHERE is_active = 1
                ORDER BY sort_order ASC, name ASC, id ASC";
        return db()->query($sql)->fetchAll();
    } catch (Throwable $e) {
        error_log('Failed loading research_labs: ' . $e->getMessage());
        return [];
    }
}
function research_labs_data(): array {
    $items = fetch_research_labs();
    if (!empty($items)) {
        return $items;
    }
    return [
        ['slug' => 'csilab', 'name' => 'CSILab', 'summary' => 'Laboratorio de Computacao de Sistemas Inteligentes.', 'site_url' => 'https://csilab.ufop.br/'],
        ['slug' => 'gaid', 'name' => 'GAID', 'summary' => 'Laboratorio Tematico em Gerencia e Analise Inteligente de Dados.', 'site_url' => 'http://www.decom.ufop.br/gaid/'],
        ['slug' => 'goal', 'name' => 'GOAL', 'summary' => 'Laboratorio Tematico em Otimizacao e Algoritmos.', 'site_url' => 'http://www.goal.ufop.br'],
        ['slug' => 'imobilis', 'name' => 'iMobilis', 'summary' => 'Laboratorio Tematico em Computacao Movel.', 'site_url' => 'http://www2.decom.ufop.br/imobilis/'],
        ['slug' => 'kryptolab', 'name' => 'KryptoLab', 'summary' => 'Laboratorio de Criptografia e Seguranca de Redes.', 'site_url' => 'https://kryptolab.decom.ufop.br'],
        ['slug' => 'lcad', 'name' => 'LCAD', 'summary' => 'Laboratorio de Computacao Aplicada e Desenvolvimento.', 'site_url' => 'https://lcad.ufop.br/'],
        ['slug' => 'lapdi', 'name' => 'LaPDI', 'summary' => 'Laboratorio Tematico em Processamento de Imagens.', 'site_url' => 'http://www.decom.ufop.br/lapdi/'],
        ['slug' => 'terralab', 'name' => 'TerraLab', 'summary' => 'Laboratorio Tematico em Simulacao e Geoprocessamento.', 'site_url' => 'http://www.decom.ufop.br/terralab/'],
        ['slug' => 'xr4good', 'name' => 'XR4Good', 'summary' => 'Laboratorio Tematico de Realidade Estendida.', 'site_url' => 'http://xr4goodlab.decom.ufop.br/'],
    ];
}
function fetch_research_projects(): array {
    try {
        $sql = "SELECT slug, title, project_type, summary, site_url, coordinator
                FROM research_projects
                WHERE is_active = 1
                ORDER BY sort_order ASC, title ASC, id ASC";
        return db()->query($sql)->fetchAll();
    } catch (Throwable $e) {
        error_log('Failed loading research_projects: ' . $e->getMessage());
        return [];
    }
}
function research_projects_data(): array {
    $items = fetch_research_projects();
    if (!empty($items)) {
        return $items;
    }
    return [
        [
            'slug' => 'projeto-ia-educacao',
            'title' => 'IA aplicada ao apoio ao ensino',
            'project_type' => 'pesquisa',
            'summary' => 'Projeto focado em modelos de aprendizado de maquina para suporte a atividades educacionais.',
            'site_url' => '',
            'coordinator' => 'DECOM/UFOP',
        ],
        [
            'slug' => 'projeto-extensao-cultura-digital',
            'title' => 'Cultura digital e formacao em tecnologia',
            'project_type' => 'extensao',
            'summary' => 'Projeto de extensao com oficinas e atividades para aproximar comunidade e computacao.',
            'site_url' => '',
            'coordinator' => 'DECOM/UFOP',
        ],
    ];
}
function course_data(string $slug): array {
    $courses = [
        'ciencia-da-computacao' => ['name'=>'Bacharelado em Ciência da Computação','summary'=>'Curso voltado à formação sólida em fundamentos da computação, algoritmos, software e sistemas.','content'=>'A proposta curricular contempla programação, algoritmos, estruturas de dados, arquitetura de computadores, bancos de dados, engenharia de software, redes e teoria da computação.','modality'=>'Bacharelado','duration'=>'8 semestres','shift'=>'Integral'],
        'inteligencia-artificial' => ['name'=>'Bacharelado em Inteligência Artificial','summary'=>'Curso voltado à formação em aprendizado de máquina, ciência de dados e sistemas inteligentes.','content'=>'A matriz curricular inclui fundamentos matemáticos, programação, otimização, aprendizado de máquina, mineração de dados e visão computacional.','modality'=>'Bacharelado','duration'=>'8 semestres','shift'=>'Integral'],
    ];
    return $courses[$slug] ?? ['name'=>'Curso','summary'=>'','content'=>'','modality'=>'','duration'=>'','shift'=>''];
}
function page_data(string $slug): array {
    $pages = [
      'quem-somos'=>['title'=>'Quem somos','summary'=>'Apresentação institucional do departamento, sua trajetória e suas áreas de atuação.','content'=>'O Departamento de Computação atua em ensino, pesquisa e extensão, oferecendo cursos de graduação e desenvolvendo ações acadêmicas e tecnológicas.'],
      'comunicacao-logo'=>['title'=>'Comunicação e logo','summary'=>'Diretrizes para uso do nome, identidade visual e materiais institucionais.','content'=>'Esta página pode concentrar versões do logotipo e padrões de comunicação institucional do departamento.'],
      'localizacao'=>['title'=>'Localização','summary'=>'Informações de localização física, acesso e referência institucional.','content'=>'O departamento está localizado no campus universitário, com atendimento presencial em dias úteis.'],
      'mapa-campus'=>['title'=>'Mapa do campus','summary'=>'Mapa de acesso e referência espacial da unidade acadêmica.','content'=>'Página preparada para receber mapa interativo ou orientações de deslocamento.'],
      'horarios-de-aula'=>['title'=>'Horários de Aula','summary'=>'Consulta organizada dos horários de aula por curso, período ou turma.','content'=>'Esta página concentra quadros de horários dos alunos, horários por docente ou planilhas por semestre letivo.'],
      'informacoes-uteis'=>['title'=>'Informações Úteis','summary'=>'Orientações acadêmicas, formulários e instruções operacionais para estudantes.','content'=>'Inclua aqui calendários, orientações de matrícula, aproveitamento de estudos, equivalências e monitorias.'],
      'monografias'=>['title'=>'Monografias','summary'=>'Informações sobre disciplinas de monografia, banca, documentação e cronogramas.','content'=>'Esta página centraliza regulamentos, modelos de documentos, agendas de defesas e orientações para discentes e orientadores.'],
      'pesquisa'=>['title'=>'Pesquisa','summary'=>'Apresentação de linhas de pesquisa, grupos, projetos e produção científica.','content'=>'Esta seção organiza laboratórios, grupos de pesquisa, projetos financiados e oportunidades de iniciação científica.'],
      'extensao'=>['title'=>'Extensão','summary'=>'Catálogo de projetos e ações de extensão vinculados ao departamento.','content'=>'Esta seção apresenta programas, projetos, oficinas, cursos e ações extensionistas.'],
      'cocic'=>['title'=>'Graduacao','summary'=>'Pagina da graduacao com apresentacao do curso, estrutura academica e informacoes uteis para alunos.','content'=>'A graduacao pode publicar aqui informacoes sobre matriz curricular, orientacoes academicas, documentos, calendario e comunicados aos estudantes.'],
    ];
    return $pages[$slug] ?? ['title'=>'Página','summary'=>'','content'=>''];
}
