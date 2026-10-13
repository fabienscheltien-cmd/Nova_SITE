<?php
/* Lien « Accepter » reçu par l'administratrice */
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/app/layout.php';
header('Cache-Control: no-store');

$id = (int)($_REQUEST['id'] ?? 0);
$k = (string)($_REQUEST['k'] ?? '');
if (!$id || !hash_equals(sign('accept' . $id), $k)) simple_page('Lien invalide', 'Ce lien n\'est pas valide', '<p>Le lien d\'acceptation est incomplet ou a été modifié.</p>');
$m = q('SELECT * FROM club_members WHERE id = ?', [$id])->fetch();
if (!$m) simple_page('Demande introuvable', 'Demande introuvable', '<p>Cette demande n\'existe plus.</p>');

if (member_active($m)) simple_page('Déjà accepté', 'Déjà parmi les invités',
    '<p><strong>' . h($m['email']) . '</strong> a déjà accès au club, jusqu\'au ' . h(fr_date($m['expires_at'])) . '.</p>');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $m = approve_member($id);
    simple_page('Invitation envoyée', 'L\'invitation est partie',
        '<p><strong>' . h($m['email']) . '</strong> vient de recevoir sa clé d\'accès au club, valable jusqu\'au ' . h(fr_date($m['expires_at'])) . '.</p>'
        . '<p><a class="c-btn" href="' . h(base_url('admin/')) . '">Espace d\'administration</a></p>');
}

// Confirmation en un clic (évite qu'un antivirus de messagerie accepte à votre place en ouvrant le lien)
simple_page('Accepter une demande', 'Ouvrir la porte ?',
    '<p>Accepter <strong>' . h($m['email']) . '</strong> au NOVA Club pour ' . (int)cfg('access_months') . ' mois ?</p>'
    . '<form method="post"><input type="hidden" name="id" value="' . $id . '"><input type="hidden" name="k" value="' . h($k) . '">'
    . '<button class="c-btn" type="submit">Accepter</button></form>'
    . '<p class="c-muted">Pour refuser, fermez simplement cette page.</p>');
