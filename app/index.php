<?php
require __DIR__ . '/includes/config.php';

$news = [];
try {
    $stmt = db()->query(
        "SELECT slug, title, summary, category, content, image
         FROM news_items
         ORDER BY published_at DESC, id DESC
         LIMIT 4"
    );
    $news = $stmt->fetchAll();
} catch (Throwable $e) {
    $news = array_slice(demo_news(), 0, 4);
}
$editaisCards = array_slice(demo_editais(), 0, 2);
$defesas = demo_defesas();
$jobs = demo_jobs();
$heroSlides = hero_carousel_get();
$academicCalendar = academic_calendar_fetch_ufop();
$calendarYear = (int)($academicCalendar['year'] ?? (int)date('Y'));
$calendarEventsByDate = $academicCalendar['events_by_date'] ?? [];
$calendarUpcoming = $academicCalendar['upcoming'] ?? [];
$calendarSourcePage = (string)($academicCalendar['source_page'] ?? 'https://www.prograd.ufop.br/calendario-academico');

page_header('Inicio');
?>
<section class="hero py-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div id="heroCarousel" class="carousel slide hero-carousel shadow-lg" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <?php foreach ($heroSlides as $idx => $slide): ?>
                            <button
                                type="button"
                                data-bs-target="#heroCarousel"
                                data-bs-slide-to="<?= e((string)$idx) ?>"
                                class="<?= $idx === 0 ? 'active' : '' ?>"
                                <?= $idx === 0 ? 'aria-current="true"' : '' ?>
                                aria-label="Slide <?= e((string)($idx + 1)) ?>">
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="carousel-inner rounded-4 overflow-hidden">
                        <?php foreach ($heroSlides as $idx => $slide): ?>
                            <div class="carousel-item<?= $idx === 0 ? ' active' : '' ?>">
                                <img src="<?= e((string)$slide['image']) ?>" class="d-block w-100 hero-slide-image" alt="<?= e((string)$slide['title']) ?>">
                                <div class="carousel-caption text-start">
                                    <span class="badge text-bg-light mb-2"><?= e((string)$slide['badge']) ?></span>
                                    <?php if ($idx === 0): ?>
                                        <h1 class="display-6 fw-bold mb-2"><?= e((string)$slide['title']) ?></h1>
                                    <?php else: ?>
                                        <h2 class="h2 fw-bold mb-2"><?= e((string)$slide['title']) ?></h2>
                                    <?php endif; ?>
                                    <p class="lead mb-<?= $idx === 0 ? '3' : '0' ?>"><?= e((string)$slide['text']) ?></p>
                                    <?php if ($idx === 0): ?>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a class="btn btn-light" href="/noticias/index.php">Ultimas noticias</a>
                                            <a class="btn btn-outline-light" href="/noticias/editais.php">Editais</a>
                                            <a class="btn btn-outline-light" href="/admin/dashboard.php">Area admin</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Proximo</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-8">
            <h2 class="section-title h4 mb-3">Noticias</h2>
            <div class="row g-3">
                <?php foreach ($news as $item): ?>
                    <div class="col-md-6">
                        <a class="card card-link h-100 shadow-sm overflow-hidden" href="/noticias/ver.php?slug=<?= urlencode($item['slug']) ?>">
                            <img class="news-card-cover" src="<?= e(content_image($item)) ?>" alt="<?= e($item['title']) ?>">
                            <div class="card-body">
                                <span class="badge text-bg-primary"><?= e($item['category']) ?></span>
                                <h3 class="h5 mt-2"><?= e($item['title']) ?></h3>
                                <p class="text-muted mb-0"><?= e($item['summary']) ?></p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <h2 class="section-title h4 mt-4 mb-3">Editais</h2>
            <div class="row g-3">
                <?php foreach ($editaisCards as $item): ?>
                    <div class="col-md-6">
                        <a class="card card-link h-100 shadow-sm overflow-hidden" href="/noticias/ver.php?slug=<?= urlencode($item['slug']) ?>">
                            <img class="news-card-cover" src="<?= e(content_image($item)) ?>" alt="<?= e($item['title']) ?>">
                            <div class="card-body">
                                <span class="badge text-bg-secondary"><?= e($item['category']) ?></span>
                                <h3 class="h5 mt-2"><?= e($item['title']) ?></h3>
                                <p class="text-muted mb-0"><?= e($item['summary']) ?></p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4 side-widget">
                <div class="card-body">
                    <h2 class="h5">Acesso rapido</h2>
                    <div class="list-group list-group-flush">
                        <a class="list-group-item list-group-item-action bg-transparent side-widget-link" href="/pessoal/docentes.php">Docentes</a>
                        <a class="list-group-item list-group-item-action bg-transparent side-widget-link" href="/ensino/ciencia-computacao.php">Curso de Ciencia da Computacao</a>
                        <a class="list-group-item list-group-item-action bg-transparent side-widget-link" href="/ensino/inteligencia-artificial.php">Curso de Inteligencia Artificial</a>
                        <a class="list-group-item list-group-item-action bg-transparent side-widget-link" href="<?= e((string)$menuGraduacao['url']) ?>"><?= e((string)$menuGraduacao['label']) ?></a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 side-widget">
                <div class="card-body">
                    <h2 class="h5">Acesso do Aluno</h2>
                    <div class="d-grid gap-2">
                        <a class="btn btn-primary btn-sm" href="/pessoal/atendimento-docentes.php">Atendimento Docentes</a>
                        <a class="btn btn-outline-secondary btn-sm" href="/ensino/horarios-de-aula.php">Horarios de Aula</a>
                    </div>
                    <div class="student-calendar mt-3 p-3 border rounded-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <button class="btn btn-sm btn-light border calendar-nav-btn" type="button" id="calendarPrev" aria-label="Mes anterior">&lsaquo;</button>
                            <h3 class="h4 mb-0 fw-bold" id="calendarTitle"></h3>
                            <button class="btn btn-sm btn-light border calendar-nav-btn" type="button" id="calendarNext" aria-label="Proximo mes">&rsaquo;</button>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small text-muted">Calendario oficial UFOP (PROGRAD)</span>
                            <a class="btn btn-outline-primary btn-sm" href="<?= e($calendarSourcePage) ?>" target="_blank" rel="noopener">Abrir PROGRAD</a>
                        </div>
                        <div class="table-responsive">
                            <table id="studentCalendarTable" class="table table-bordered table-sm mb-2 calendar-table"></table>
                        </div>
                        <div class="d-flex gap-2 mb-2">
                            <span class="badge text-bg-danger">Feriado</span>
                            <span class="badge text-bg-warning text-dark">Evento</span>
                        </div>
                        <div id="calendarDetailsTitle" class="fw-semibold text-secondary mb-1">Detalhes do dia</div>
                        <ul id="calendarDetailsList" class="ps-3 mb-0 small"></ul>
                        <div class="fw-semibold text-secondary mt-3 mb-1">Proximos eventos</div>
                        <ul class="ps-3 mb-0 small">
                            <?php if (!empty($calendarUpcoming)): ?>
                                <?php foreach (array_slice($calendarUpcoming, 0, 5) as $upcoming): ?>
                                    <?php $upcomingTs = strtotime((string)($upcoming['date'] ?? '')); ?>
                                    <li class="<?= (($upcoming['type'] ?? 'event') === 'holiday') ? 'text-danger' : '' ?>">
                                        <?= e((string)($upcomingTs !== false ? date('d/m', $upcomingTs) : '--/--')) ?> - <?= e((string)($upcoming['title'] ?? 'Evento')) ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>Sem eventos futuros encontrados na fonte oficial.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php foreach (['Defesas' => $defesas, 'Estagios e Empregos' => $jobs] as $title => $items): ?>
                <div class="card shadow-sm mb-4 side-widget">
                    <div class="card-body">
                        <h2 class="h5"><?= e($title) ?></h2>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($items as $item): ?>
                                <li class="list-group-item px-0">
                                    <a class="side-widget-link" href="/noticias/ver.php?slug=<?= urlencode($item['slug']) ?>"><?= e($item['title']) ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card news-card mt-4">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <h2 class="h4 mb-2">Quero ingressar em Ciencia da Computacao (UFOP)</h2>
                <p class="mb-0 text-muted">
                    Veja um apanhado geral do curso com descricao, eixos da grade curricular, avaliacao no MEC
                    e referencia de nota para ingresso via SISU/ENEM.
                </p>
            </div>
            <a class="btn btn-primary" href="/ensino/ciencia-computacao.php">Ver guia do ingressante</a>
        </div>
    </div>

</div>
<script>
    (function () {
        var eventsByDate = <?= json_encode($calendarEventsByDate, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        var calendarYear = <?= e((string)$calendarYear) ?>;
        var months = ['Janeiro', 'Fevereiro', 'Marco', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        var weekdays = ['d', 's', 't', 'q', 'q', 's', 's'];

        var titleEl = document.getElementById('calendarTitle');
        var tableEl = document.getElementById('studentCalendarTable');
        var detailsTitleEl = document.getElementById('calendarDetailsTitle');
        var detailsListEl = document.getElementById('calendarDetailsList');
        var prevBtn = document.getElementById('calendarPrev');
        var nextBtn = document.getElementById('calendarNext');
        if (!titleEl || !tableEl || !detailsTitleEl || !detailsListEl || !prevBtn || !nextBtn) {
            return;
        }

        var today = new Date();
        var currentMonth = today.getFullYear() === calendarYear ? today.getMonth() : 0;
        var selectedDate = calendarYear + '-' + String(currentMonth + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');

        function formatIso(monthIndex, day) {
            return calendarYear + '-' + String(monthIndex + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
        }

        function renderDetails(isoDate) {
            var meta = eventsByDate[isoDate] || { items: [] };
            detailsTitleEl.textContent = 'Detalhes do dia ' + Number(isoDate.slice(8, 10));
            detailsListEl.innerHTML = '';
            if (!meta.items || meta.items.length === 0) {
                detailsListEl.innerHTML = '<li>Sem eventos/feriados neste dia.</li>';
                return;
            }
            meta.items.forEach(function (item) {
                var li = document.createElement('li');
                li.textContent = item.title || 'Evento';
                if ((item.type || 'event') === 'holiday') {
                    li.className = 'text-danger';
                }
                detailsListEl.appendChild(li);
            });
        }

        function renderCalendar() {
            titleEl.textContent = months[currentMonth] + ' ' + calendarYear;
            var firstWeekday = new Date(calendarYear, currentMonth, 1).getDay();
            var daysInMonth = new Date(calendarYear, currentMonth + 1, 0).getDate();
            var html = '<thead><tr>';
            weekdays.forEach(function (d) { html += '<th class="text-center">' + d + '</th>'; });
            html += '</tr></thead><tbody>';

            var day = 1;
            for (var row = 0; row < 6; row++) {
                html += '<tr>';
                for (var col = 0; col < 7; col++) {
                    var idx = row * 7 + col;
                    if (idx < firstWeekday || day > daysInMonth) {
                        html += '<td class="calendar-empty"></td>';
                    } else {
                        var iso = formatIso(currentMonth, day);
                        var meta = eventsByDate[iso];
                        var isHoliday = !!(meta && meta.holiday);
                        var hasEvent = !!(meta && meta.items && meta.items.length > 0);
                        var selected = iso === selectedDate ? ' selected' : '';
                        html += '<td class="text-center ' + (isHoliday ? 'calendar-holiday-bg' : '') + '">';
                        html += '<button type="button" class="calendar-day-btn' + selected + '" data-date="' + iso + '">' + day + '</button>';
                        if (isHoliday || hasEvent) {
                            html += '<span class="calendar-dot ' + (isHoliday ? 'dot-holiday' : 'dot-event') + '"></span>';
                        }
                        html += '</td>';
                        day++;
                    }
                }
                html += '</tr>';
            }
            html += '</tbody>';
            tableEl.innerHTML = html;

            tableEl.querySelectorAll('[data-date]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    selectedDate = btn.getAttribute('data-date') || selectedDate;
                    renderCalendar();
                    renderDetails(selectedDate);
                });
            });
        }

        prevBtn.addEventListener('click', function () {
            currentMonth = (currentMonth + 11) % 12;
            selectedDate = formatIso(currentMonth, 1);
            renderCalendar();
            renderDetails(selectedDate);
        });

        nextBtn.addEventListener('click', function () {
            currentMonth = (currentMonth + 1) % 12;
            selectedDate = formatIso(currentMonth, 1);
            renderCalendar();
            renderDetails(selectedDate);
        });

        renderCalendar();
        renderDetails(selectedDate);
    })();
</script>
<?php page_footer(); ?>
