<?php
/* NOVA CLUB — gabarit commun des pages du club */

function page_start(string $title, string $bodyClass = '') { ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">
<title><?= h($title) ?> — NOVA Club</title>
<link rel="icon" type="image/png" sizes="32x32" href="<?= h(base_url('assets/favicon-32.png')) ?>">
<link rel="icon" type="image/png" sizes="192x192" href="<?= h(base_url('assets/favicon-192.png')) ?>">
<link rel="apple-touch-icon" href="<?= h(base_url('assets/apple-touch-icon.png')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Barlow+Condensed:wght@400;500;600&family=Nunito+Sans:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= h(base_url('assets/club.css')) ?>?v=1">
</head>
<body class="<?= h($bodyClass) ?>">
<?php }

function club_header(string $active = '') { ?>
<header class="c-top">
  <a class="c-brand" href="<?= h(base_url('club.php')) ?>" aria-label="NOVA Club, accueil du club">
    <img class="c-brand-logo" src="<?= h(base_url('assets/nova-club-ligne-retro.png')) ?>" alt="NOVA Club" width="756" height="204">
  </a>
  <nav class="c-nav" aria-label="Menu du club">
    <a href="<?= h(base_url('club.php#a-venir')) ?>"<?= $active === 'venir' ? ' aria-current="page"' : '' ?>>À venir</a>
    <a href="<?= h(base_url('club.php#passes')) ?>"<?= $active === 'passes' ? ' aria-current="page"' : '' ?>><span class="c-long">Événements </span>passés</a>
    <a href="<?= h(base_url('histoire.php')) ?>"<?= $active === 'histoire' ? ' aria-current="page"' : '' ?>><span class="c-long">Notre </span>histoire</a>
    <a class="c-quit" href="<?= h(site_url('#club')) ?>">Quitter<span class="c-long"> le club</span></a>
  </nav>
</header>
<?php }

function page_end(bool $withFooter = true) {
    if ($withFooter) { ?>
<footer class="c-foot">
  <img class="c-foot-mark" src="<?= h(base_url('assets/nova-mark-retro.png')) ?>" alt="" width="296" height="270">
  <p>NOVA Club · un espace réservé aux invités de <a href="<?= h(site_url()) ?>">NOVA Serenity</a></p>
  <p><a href="<?= h(base_url('confidentialite.php')) ?>">Confidentialité</a></p>
</footer>
<?php } ?>
</body>
</html>
<?php }

/* petite page centrée (acceptation, clé d'accès, messages) */
function simple_page(string $title, string $heading, string $html) {
    page_start($title, 'c-simple');
    echo '<main class="c-card"><img class="c-avatar" src="' . h(base_url('assets/maitre-ceremonie.png')) . '" alt="" width="84" height="84">';
    echo '<p class="c-kicker">NOVA Club</p><h1>' . h($heading) . '</h1>' . $html . '<p class="c-sign">— Le Maître de Cérémonie</p></main>';
    page_end(false);
    exit;
}
