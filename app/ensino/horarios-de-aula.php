<?php
declare(strict_types=1);

require __DIR__ . '/../includes/config.php';

$horarios = horarios_page_get();
page_header((string)$horarios['title']);
?>
<style>
.horarios-schedule table thead th {
    background: #0d6efd;
    color: #fff;
    font-weight: 600;
}
.horarios-schedule table tbody td.schedule-filled {
    background: #dbeafe;
    color: #0b3a8a;
    font-weight: 600;
}
.horarios-schedule table tbody td:first-child {
    background: #f8fafc;
    font-weight: 600;
}
</style>
<div class="container py-4">
    <h1 class="section-title h3 mb-3"><?= e((string)$horarios['title']) ?></h1>
    <p class="lead mb-4"><?= e((string)$horarios['summary']) ?></p>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <?= render_rich_text((string)$horarios['intro_html']) ?>
            <?php if (!empty($horarios['source_url'])): ?>
                <a class="btn btn-outline-primary btn-sm mt-2" target="_blank" rel="noopener" href="<?= e((string)$horarios['source_url']) ?>">
                    Abrir pagina oficial de horarios
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow-sm mb-4 horarios-schedule">
        <div class="card-body">
            <?= render_rich_text((string)$horarios['schedule_html']) ?>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <?= render_rich_text((string)$horarios['other_electives_html']) ?>
        </div>
    </div>

    <div class="card news-card">
        <div class="card-body">
            <h2 class="h5 mb-3">Links e arquivos de horarios</h2>
            <div><?= render_rich_text((string)$horarios['links_html']) ?></div>
            <?php if (!empty($horarios['last_sync'])): ?>
                <p class="small text-muted mb-0 mt-3">
                    Ultima importacao automatica: <?= e((string)$horarios['last_sync']) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const schedule = document.querySelector('.horarios-schedule');
  if (!schedule) return;
  schedule.querySelectorAll('table tbody tr').forEach((row) => {
    const cells = row.querySelectorAll('td');
    cells.forEach((cell, index) => {
      if (index === 0) return;
      const value = (cell.textContent || '').trim();
      if (value !== '') {
        cell.classList.add('schedule-filled');
      }
    });
  });
});
</script>
<?php page_footer(); ?>
