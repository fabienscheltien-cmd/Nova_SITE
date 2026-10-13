<?php
/* Page d'un événement (programme ou souvenirs en photos) */
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/app/layout.php';
header('Cache-Control: no-store');
if (!current_member()) redirect(base_url());

$e = q('SELECT * FROM club_events WHERE id = ?', [(int)($_GET['id'] ?? 0)])->fetch();
if (!$e) redirect(base_url('club.php'));
$photos = q('SELECT * FROM club_photos WHERE event_id = ? ORDER BY id ASC', [$e['id']])->fetchAll();
$isPast = $e['event_date'] < date('Y-m-d');

page_start($e['title']);
club_header($isPast ? 'passes' : 'venir');
?>
<main>
  <header class="c-event-head">
    <a class="c-back" href="<?= h(base_url('club.php#' . ($isPast ? 'passes' : 'a-venir'))) ?>"><?= $isPast ? 'Événements passés' : 'À venir' ?></a>
    <h1><?= h($e['title']) ?></h1>
    <p class="c-meta"><?= h(event_date_label($e)) ?><?= (!$isPast && $e['event_time']) ? ' · ' . h($e['event_time']) : '' ?><?= $e['place'] ? ' · ' . h($e['place']) : '' ?></p>
  </header>
  <?php if (trim((string)$e['description']) !== ''): ?>
  <div class="c-event-body"><div class="c-desc">
    <?php foreach (preg_split('/\R{2,}/', trim($e['description'])) as $para): ?><p><?= nl2br(h(trim($para))) ?></p><?php endforeach; ?>
  </div></div>
  <?php endif; ?>
  <?php if ($photos): ?>
  <div class="c-gallery" id="galerie" data-lightbox>
    <?php foreach ($photos as $i => $p): ?>
      <a href="<?= h(photo_url((int)$p['id'], true)) ?>" data-zoom data-caption="<?= h($e['title']) ?>"><img src="<?= h(photo_url((int)$p['id'])) ?>" alt="Photo <?= $i + 1 ?> — <?= h($e['title']) ?>" width="<?= (int)$p['width'] ?>" height="<?= (int)$p['height'] ?>" loading="lazy"></a>
    <?php endforeach; ?>
  </div>
  <script src="<?= h(base_url('assets/lightbox.js')) ?>"></script>
  <?php elseif ($isPast): ?>
  <div class="c-event-body"><p class="c-empty">Les photos de cette soirée seront bientôt accrochées aux murs du club.</p></div>
  <?php endif; ?>
</main>
<?php page_end();
