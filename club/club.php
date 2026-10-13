<?php
/* Le club : événements à venir et événements passés */
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/app/layout.php';
header('Cache-Control: no-store');
$me = current_member();
if (!$me) redirect(base_url());
seed_events();

$today = date('Y-m-d');
$upcoming = q('SELECT * FROM club_events WHERE event_date >= ? ORDER BY event_date ASC, id ASC', [$today])->fetchAll();
$past = q('SELECT e.*, (SELECT COUNT(*) FROM club_photos p WHERE p.event_id = e.id) AS nb,
                  COALESCE(e.cover_photo_id, (SELECT MIN(p2.id) FROM club_photos p2 WHERE p2.event_id = e.id)) AS cover
           FROM club_events e WHERE e.event_date < ? ORDER BY e.event_date DESC, e.id DESC', [$today])->fetchAll();


function excerpt(?string $t, int $n = 180): string {
    $t = trim(preg_replace('/\s+/', ' ', (string)$t));
    return mb_strlen($t) > $n ? rtrim(mb_substr($t, 0, $n)) . '…' : $t;
}

page_start('Le club');
club_header();
?>
<main>
  <section class="c-hero">
    <div class="c-hero-in">
      <h1>Bienvenue parmi nous.</h1>
      <p class="lead">Installez-vous confortablement : tout a été préparé pour vous. Découvrez ce qui s'annonce dans nos salons, et revivez les moments qui les ont fait vibrer.</p>
      <p class="c-pass">Invitation valable jusqu'au <?= h(fr_date($me['expires_at'])) ?></p>
    </div>
  </section>

  <section class="c-section" id="a-venir">
    <div class="c-section-head"><h2>À venir</h2></div>
    <?php if (!$upcoming): ?>
      <p class="c-empty">Le programme se prépare en coulisses. Le Maître de cérémonie vous tiendra informé.</p>
    <?php else: ?>
    <ul class="c-program">
      <?php foreach ($upcoming as $e): ?>
      <li><a href="<?= h(base_url('evenement.php?id=' . $e['id'])) ?>">
        <div class="c-date"><b><?= (int)date('j', strtotime($e['event_date'])) ?></b><span><?= h(fr_month_short($e['event_date'])) ?></span><small><?= date('Y', strtotime($e['event_date'])) ?></small></div>
        <div class="c-prog-txt">
          <h3><?= h($e['title']) ?></h3>
          <p class="c-meta"><?= h(ucfirst(fr_weekday($e['event_date']))) ?><?= $e['event_time'] ? ' · ' . h($e['event_time']) : '' ?><?= $e['place'] ? ' · ' . h($e['place']) : '' ?></p>
          <?php if ($e['description']): ?><p><?= h(excerpt($e['description'])) ?></p><?php endif; ?>
        </div>
        <span class="c-arrow">Détails</span>
      </a></li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </section>

  <section class="c-section" id="passes">
    <div class="c-section-head"><h2>Événements passés</h2></div>
    <?php if (!$past): ?>
      <p class="c-empty">Les souvenirs du club s'écriront bientôt ici.</p>
    <?php else: ?>
    <div class="c-past">
      <?php foreach ($past as $e): ?>
      <a href="<?= h(base_url('evenement.php?id=' . $e['id'])) ?>">
        <figure>
          <?php if ($e['cover']): ?><img src="<?= h(photo_url((int)$e['cover'])) ?>" alt="" loading="lazy">
          <?php else: ?><span class="c-noimg"><img src="<?= h(base_url('assets/nova-mark-or.png')) ?>" alt=""></span><?php endif; ?>
        </figure>
        <h3><?= h($e['title']) ?></h3>
        <p class="c-meta"><?= h(event_date_label($e, false)) ?><?= $e['place'] ? ' · ' . h($e['place']) : '' ?></p>
        <?php if ((int)$e['nb']): ?><p class="c-count"><?= (int)$e['nb'] ?> photo<?= (int)$e['nb'] > 1 ? 's' : '' ?></p><?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>
</main>
<?php page_end();
