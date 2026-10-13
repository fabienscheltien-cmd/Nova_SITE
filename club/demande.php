<?php
/* Réception d'une demande d'accès (appelée par la page d'entrée) */
require __DIR__ . '/app/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function reply(bool $ok, string $kind, string $msg) { echo json_encode(['ok' => $ok, 'kind' => $kind, 'msg' => $msg], JSON_UNESCAPED_UNICODE); exit; }

$WAIT = "Le Maître de cérémonie vérifie votre identité pour accéder au club. Il vous tiendra informé par e-mail.";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); reply(false, 'method', 'Méthode non autorisée.'); }
if (!empty($_POST['site'])) reply(true, 'wait', $WAIT);                       // robot : on ne dit rien
if (too_many('demande', 6, 60)) reply(false, 'limit', "Le Maître de cérémonie a déjà beaucoup de cartes en main. Revenez un peu plus tard.");
note_attempt('demande');

$email = normalize_email((string)($_POST['email'] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190)
    reply(false, 'invalid', "Cette adresse ne figure dans aucun annuaire connu. Vérifiez-la, je vous prie.");
if (is_personal_email($email))
    reply(false, 'perso', "Le club ne reçoit qu'en tenue de travail : présentez-vous avec votre adresse e-mail professionnelle.");

$m = member_by_email($email);

if ($m && member_active($m)) {
    send_access_link($m, false);
    reply(true, 'known', "Le Maître de cérémonie vous a reconnu. Une clé d'accès vient de vous être envoyée par e-mail.");
}
if ($m && in_array($m['status'], ['pending', 'revoked'], true)) reply(true, 'wait', $WAIT);  // déjà en attente, ou refus silencieux

if ($m) {   // invitation expirée : nouvelle demande
    q("UPDATE club_members SET status = 'pending', requested_at = ?, approved_at = NULL, expires_at = NULL WHERE id = ?", [now(), $m['id']]);
    $id = (int)$m['id'];
} else {
    q("INSERT INTO club_members (email, status, requested_at) VALUES (?, 'pending', ?)", [$email, now()]);
    $id = (int)db()->lastInsertId();
}

$accept = base_url('accepter.php?id=' . $id . '&k=' . sign('accept' . $id));
$renew = $m ? '<p style="color:#8f887b;font-size:14px">Cette personne avait déjà été invitée ; son invitation de 6 mois est arrivée à échéance.</p>' : '';
$html = '<p style="text-align:center">Une nouvelle personne se présente à la porte du club :</p>'
      . '<p style="text-align:center;font-size:20px;color:#e9d9b0;margin:18px 0 4px">' . h($email) . '</p>'
      . '<p style="text-align:center;color:#8f887b;font-size:14px">Société : ' . h(email_domain($email)) . ' · le ' . h(fr_date(now())) . ' à ' . date('H\hi') . '</p>'
      . $renew . mail_button($accept, 'Accepter')
      . '<p style="font-size:14px;color:#8f887b;text-align:center">Pour refuser, ignorez simplement ce message : la personne ne recevra aucune réponse.<br>'
      . 'Toutes les demandes : <a href="' . h(base_url('admin/')) . '" style="color:#b89a5e">espace d\'administration</a></p>';
$text = "Nouvelle demande d'accès au NOVA Club : $email (" . email_domain($email) . ")\n\nAccepter : $accept\n\nPour refuser, ignorez ce message.\nAdministration : " . base_url('admin/');
notify_admins("Demande d'accès au NOVA Club — $email", 'Une demande d\'accès', $html, $text);

reply(true, 'wait', $WAIT);
