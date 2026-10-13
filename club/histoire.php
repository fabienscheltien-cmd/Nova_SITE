<?php
/* Notre histoire : le récit du lancement de NOVA Serenity, chapitre après chapitre */
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/app/layout.php';
header('Cache-Control: no-store');
if (!current_member()) redirect(base_url());
seed_story();

$items = q('SELECT s.*, p.width, p.height FROM club_story s JOIN club_photos p ON p.id = s.photo_id
            ORDER BY (s.story_date IS NULL) ASC, s.story_date ASC, s.id ASC')->fetchAll();
function paras(?string $t): string {
    $o = ''; foreach (preg_split('/\R{2,}/', trim((string)$t)) as $p) if (trim($p) !== '') $o .= '<p>' . nl2br(h(trim($p))) . '</p>';
    return $o;
}

page_start('Notre histoire');
club_header('histoire');
?>
<main>
  <header class="c-event-head c-story-head">
    <h1>Notre histoire</h1>
    <div class="c-orn" aria-hidden="true"><span></span></div>
    <div class="c-story-intro"><?= paras(story_intro()) ?></div>
  </header>
  <?php if (!$items): ?>
    <div class="c-section"><p class="c-empty">Les archives du lancement sont en cours d'accrochage.</p></div>
  <?php else: ?>
  <ol class="c-timeline" data-lightbox>
    <?php foreach ($items as $n => $it): ?>
    <li class="c-tl-item">
      <?php if ($it['video']): ?>
      <div class="c-tl-fig c-tl-video">
        <video controls playsinline preload="metadata" poster="<?= h(photo_url((int)$it['photo_id'], true)) ?>" aria-label="<?= h($it['title']) ?>">
          <source src="<?= h(video_url((int)$it['id'])) ?>" type="video/mp4">
        </video>
      </div>
      <?php else: ?>
      <a class="c-tl-fig" href="<?= h(photo_url((int)$it['photo_id'], true)) ?>" data-zoom data-caption="<?= h($it['title']) ?>">
        <img src="<?= h(photo_url((int)$it['photo_id'])) ?>" alt="<?= h($it['title']) ?>" width="<?= (int)$it['width'] ?>" height="<?= (int)$it['height'] ?>" loading="lazy">
      </a>
      <?php endif; ?>
      <div class="c-tl-txt">
        <p class="c-ch-num">Chapitre <?= roman($n + 1) ?><?php if ($it['story_date']): ?><span> · <?= h(ucfirst(fr_month_year($it['story_date']))) ?></span><?php endif; ?></p>
        <h2><?= h($it['title']) ?></h2>
        <?php if (trim((string)$it['quote']) !== ''): ?><blockquote class="c-quote">« <?= h(trim($it['quote'])) ?> »</blockquote><?php endif; ?>
        <?= paras($it['caption']) ?>
      </div>
    </li>
    <?php endforeach; ?>
  </ol>
  <p class="c-story-end">À suivre…</p>
  <script src="<?= h(base_url('assets/lightbox.js')) ?>"></script>
  <?php endif; ?>
</main>
<?php page_end();
