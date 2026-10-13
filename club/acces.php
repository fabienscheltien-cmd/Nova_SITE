<?php
/* Clé d'accès personnelle reçue par l'invité */
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/app/layout.php';
header('Cache-Control: no-store');

$t = (string)($_REQUEST['t'] ?? '');
$tok = preg_match('/^[a-f0-9]{64}$/', $t)
     ? q("SELECT * FROM club_tokens WHERE token_hash = ? AND kind = 'login'", [thash($t)])->fetch() : null;
$m = $tok ? q('SELECT * FROM club_members WHERE id = ?', [$tok['member_id']])->fetch() : null;

$retry = '<p><a class="c-btn" href="' . h(base_url()) . '">Revenir à la porte</a></p><p class="c-muted">Présentez-vous à nouveau avec votre adresse e-mail : si vous faites partie des invités, une nouvelle clé vous sera envoyée.</p>';
if (!$tok || !$m) simple_page('Clé invalide', 'Cette clé n\'ouvre aucune porte', $retry);
if (!member_active($m)) simple_page('Invitation expirée', 'Votre invitation a pris fin', '<p>Votre accès au club n\'est plus actif.</p>' . $retry);
if ($tok['used_at'] || $tok['expires_at'] < now()) {
    if (current_member()) redirect(base_url('club.php'));
    simple_page('Clé expirée', 'Cette clé a déjà servi', $retry);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    q('UPDATE club_tokens SET used_at = ? WHERE id = ?', [now(), $tok['id']]);
    open_session($m);
    redirect(base_url('club.php'));
}

simple_page('Entrer au club', 'Le Maître de cérémonie vous attend',
    '<p>Votre place est réservée jusqu\'au ' . h(fr_date($m['expires_at'])) . '.</p>'
    . '<form method="post"><input type="hidden" name="t" value="' . h($t) . '"><button class="c-btn" type="submit">Entrer au club</button></form>'
    . '<p class="c-muted">Cet appareil vous reconnaîtra ensuite jusqu\'à la fin de votre invitation.</p>');
