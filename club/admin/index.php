<?php
/* NOVA CLUB — espace d'administration */
require __DIR__ . '/../app/bootstrap.php';
require __DIR__ . '/../app/layout.php';
header('Cache-Control: no-store');
header('X-Frame-Options: DENY');
admin_session();

$self = base_url('admin/');
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
function flash_to(string $msg, string $url, bool $err = false) { $_SESSION['flash'] = [$msg, $err]; redirect($url); }
function admin_page_start(string $title) { page_start($title, 'a-body'); echo '<div class="a-wrap">'; }
function admin_page_end() { echo '</div>'; page_end(false); }
function show_flash($flash) { if ($flash) echo '<p class="a-flash' . ($flash[1] ? ' err' : '') . '">' . h($flash[0]) . '</p>'; }

function admin_login(string $email) {
    session_regenerate_id(true);
    $_SESSION['admin'] = 1; $_SESSION['admin_email'] = $email; $_SESSION['admin_until'] = time() + 8 * 3600;
}
function admin_form_page(string $title, string $intro, string $err, string $info, string $fields, string $button, string $extra = '') {
    page_start($title, 'a-body'); ?>
    <main class="a-login">
      <img class="c-avatar" src="<?= h(base_url('assets/maitre-ceremonie.png')) ?>" alt="" width="72" height="72">
      <h1><?= h($title) ?></h1>
      <?php if ($intro) echo '<p class="a-help" style="text-align:center">' . $intro . '</p>'; ?>
      <?php if ($err) echo '<p class="a-flash err" style="margin-top:1rem">' . h($err) . '</p>'; if ($info) echo '<p class="a-flash" style="margin-top:1rem">' . h($info) . '</p>'; ?>
      <form method="post" class="a-form"><?= $fields ?><p style="margin-top:1.6rem"><button class="a-btn plein" type="submit"><?= h($button) ?></button></p></form>
      <?= $extra ?>
    </main>
    <?php page_end(false); exit;
}
$emailField = function (string $v = '') { return '<label for="email">Adresse e-mail</label><input id="email" type="email" name="email" value="' . h($v) . '" required autocomplete="username">'; };

/* ---------------------------------------------------------------- 1. mot de passe oublié (lien reçu par e-mail) */
if (isset($_GET['reset'])) {
    $t = (string)$_GET['reset']; $err = '';
    $tok = preg_match('/^[a-f0-9]{64}$/', $t) ? q("SELECT * FROM club_tokens WHERE token_hash = ? AND kind = 'admin_reset' AND used_at IS NULL AND expires_at > ?", [thash($t), now()])->fetch() : null;
    $who = $tok ? (admin_emails()[(int)$tok['member_id'] - 1] ?? null) : null;
    if (!$tok || !$who) simple_page('Lien expiré', 'Ce lien a expiré', '<p>Demandez un nouveau lien depuis la page de connexion.</p><p><a class="c-btn" href="' . h($self) . '">Connexion</a></p>');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $p1 = (string)($_POST['p1'] ?? ''); $p2 = (string)($_POST['p2'] ?? '');
        if (mb_strlen($p1) < 10) $err = 'Au moins 10 caractères, je vous prie.';
        elseif ($p1 !== $p2) $err = 'Les deux mots de passe ne sont pas identiques.';
        else {
            set_setting(admin_hash_key($who), password_hash($p1, PASSWORD_DEFAULT));
            q('UPDATE club_tokens SET used_at = ? WHERE id = ?', [now(), $tok['id']]);
            admin_login($who); flash_to('Nouveau mot de passe enregistré.', $self);
        }
    }
    admin_form_page('Nouveau mot de passe', h($who), $err, '',
        '<label for="p1">Nouveau mot de passe</label><input id="p1" type="password" name="p1" required minlength="10" autocomplete="new-password">'
        . '<label for="p2">Confirmez-le</label><input id="p2" type="password" name="p2" required minlength="10" autocomplete="new-password">', 'Enregistrer');
}

/* ---------------------------------------------------------------- 2. connexion / première visite */
if (!is_admin() || !in_array($_SESSION['admin_email'] ?? '', admin_emails(), true)) {
    $err = ''; $info = '';
    $email = normalize_email((string)($_POST['email'] ?? ''));
    $isAdmin = in_array($email, admin_emails(), true);
    $step = (string)($_POST['step'] ?? 'login');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (too_many('admin', 8, 15)) $err = 'Trop d\'essais. Patientez 15 minutes.';
        else {
            note_attempt('admin');
            if ($step === 'forgot') {
                if ($isAdmin) {
                    $t = new_token(); $idx = array_search($email, admin_emails(), true) + 1;
                    q("INSERT INTO club_tokens (member_id, kind, token_hash, expires_at) VALUES (?, 'admin_reset', ?, ?)", [$idx, thash($t), date('Y-m-d H:i:s', time() + 3600)]);
                    $url = $self . '?reset=' . $t;
                    send_mail($email, 'NOVA Club — nouveau mot de passe', 'Nouveau mot de passe',
                        '<p style="text-align:center">Vous avez demandé à changer votre mot de passe d\'administration du NOVA Club.</p>' . mail_button($url, 'Choisir un mot de passe')
                        . '<p style="font-size:13px;color:#8f887b;text-align:center">Lien valable 1 heure. Si vous n\'êtes pas à l\'origine de cette demande, ignorez ce message.</p>',
                        "Changer votre mot de passe d'administration du NOVA Club : $url (valable 1 heure)");
                }
                $info = 'Si cette adresse est un compte d\'administration, un lien vient de lui être envoyé.';
            } elseif ($step === 'setup' && $isAdmin && !admin_hash($email)) {
                $p1 = (string)($_POST['p1'] ?? ''); $p2 = (string)($_POST['p2'] ?? '');
                if (!hash_equals((string)cfg('db_pass'), (string)($_POST['dbpass'] ?? ''))) $err = 'Le mot de passe de la base de données ne correspond pas.';
                elseif (mb_strlen($p1) < 10) $err = 'Choisissez un mot de passe d\'au moins 10 caractères.';
                elseif ($p1 !== $p2) $err = 'Les deux mots de passe ne sont pas identiques.';
                else { set_setting(admin_hash_key($email), password_hash($p1, PASSWORD_DEFAULT)); admin_login($email); flash_to('Bienvenue. Votre accès d\'administration est prêt.', $self); }
            } elseif ($isAdmin && !admin_hash($email)) {
                $step = 'setup';   // première visite de ce compte
            } elseif ($isAdmin && password_verify((string)($_POST['password'] ?? ''), (string)admin_hash($email))) {
                admin_login($email); redirect($self);
            } else $err = 'Adresse ou mot de passe incorrect.';
        }
    }
    if ($step === 'setup' && $isAdmin && !admin_hash($email)) {
        admin_form_page('Première visite', 'Choisissez le mot de passe de <strong>' . h($email) . '</strong>. Par sécurité, indiquez aussi le mot de passe de la base de données (celui écrit dans <em>app/config.php</em>).',
            $err, '', '<input type="hidden" name="step" value="setup"><input type="hidden" name="email" value="' . h($email) . '">'
            . '<label for="dbpass">Mot de passe de la base de données</label><input id="dbpass" type="password" name="dbpass" required autocomplete="off">'
            . '<label for="p1">Votre mot de passe d\'administration</label><input id="p1" type="password" name="p1" required minlength="10" autocomplete="new-password">'
            . '<label for="p2">Confirmez-le</label><input id="p2" type="password" name="p2" required minlength="10" autocomplete="new-password">', 'Créer mon accès');
    }
    admin_form_page('Administration', 'NOVA Club', $err, $info,
        '<input type="hidden" name="step" value="login">' . $emailField($email)
        . '<label for="password">Mot de passe</label><input id="password" type="password" name="password" autocomplete="current-password">'
        . '<p class="a-help">Première connexion ? Saisissez votre adresse et cliquez sur « Entrer » : vous pourrez choisir votre mot de passe.</p>', 'Entrer',
        '<form method="post" class="a-form" style="margin-top:1.4rem;border-top:1px solid var(--ligne);padding-top:.4rem"><input type="hidden" name="step" value="forgot">' . $emailField($email)
        . '<p style="margin-top:.8rem"><button class="a-btn discret" type="submit">Mot de passe oublié ?</button></p></form>');
}
$_SESSION['admin_until'] = time() + 8 * 3600;

/* ---------------------------------------------------------------- 4. actions */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$_POST && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0)
        flash_to('Envoi trop lourd pour l\'hébergement : réduisez la taille de la vidéo ou envoyez moins de photos à la fois.', $self . '?p=' . preg_replace('/[^a-z]/', '', (string)($_GET['p'] ?? 'demandes')), true);
    check_csrf();
    $a = (string)($_POST['action'] ?? '');
    $id = (int)($_POST['id'] ?? 0);
    switch ($a) {
        case 'logout':
            $_SESSION = []; session_destroy(); redirect($self);
        case 'accept':
            $m = approve_member($id); flash_to($m['email'] . ' a reçu sa clé d\'accès.', $self . '?p=demandes');
        case 'refuse':
            q("UPDATE club_members SET status = 'revoked' WHERE id = ? AND status = 'pending'", [$id]);
            flash_to('Demande écartée. La personne ne recevra aucune réponse.', $self . '?p=demandes');
        case 'resend':
            $m = q('SELECT * FROM club_members WHERE id = ?', [$id])->fetch();
            if ($m && member_active($m)) send_access_link($m, false);
            flash_to('Une nouvelle clé a été envoyée.', $self . '?p=invites');
        case 'revoke':
            revoke_member($id); flash_to('Accès retiré.', $self . '?p=invites');
        case 'forget':
            q('DELETE FROM club_sessions WHERE member_id = ?', [$id]); q('DELETE FROM club_tokens WHERE member_id = ?', [$id]);
            q('DELETE FROM club_members WHERE id = ?', [$id]);
            flash_to('Adresse supprimée définitivement.', $self . '?p=' . (($_POST['back'] ?? '') === 'demandes' ? 'demandes' : 'invites'));
        case 'event_save':
            $title = trim((string)($_POST['title'] ?? '')); $date = (string)($_POST['event_date'] ?? '');
            if ($title === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) flash_to('Le titre et la date sont obligatoires.', $self . '?p=evenement' . ($id ? '&id=' . $id : ''), true);
            $vals = [$title, $date, trim((string)($_POST['event_time'] ?? '')), trim((string)($_POST['place'] ?? '')), trim((string)($_POST['description'] ?? '')), empty($_POST['month_only']) ? 0 : 1];
            if ($id) q('UPDATE club_events SET title = ?, event_date = ?, event_time = ?, place = ?, description = ?, month_only = ? WHERE id = ?', array_merge($vals, [$id]));
            else { q('INSERT INTO club_events (title, event_date, event_time, place, description, month_only, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)', array_merge($vals, [now()])); $id = (int)db()->lastInsertId(); }
            $n = 0;
            if (!empty($_FILES['photos']['tmp_name']) && is_array($_FILES['photos']['tmp_name'])) {
                foreach ($_FILES['photos']['tmp_name'] as $k => $tmp) if ($tmp && $_FILES['photos']['error'][$k] === UPLOAD_ERR_OK && save_photo($tmp, $id)) $n++;
            }
            flash_to('Événement enregistré' . ($n ? " avec $n photo" . ($n > 1 ? 's' : '') : '') . '.', $self . '?p=evenement&id=' . $id);
        case 'photo_upload':   // envoi photo par photo (depuis le navigateur, photos déjà allégées)
            header('Content-Type: application/json');
            $ok = !empty($_FILES['photo']['tmp_name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK && $id && save_photo($_FILES['photo']['tmp_name'], $id);
            echo json_encode(['ok' => (bool)$ok]); exit;
        case 'event_delete':
            foreach (q('SELECT id FROM club_photos WHERE event_id = ?', [$id])->fetchAll() as $p) delete_photo((int)$p['id']);
            q('DELETE FROM club_events WHERE id = ?', [$id]);
            flash_to('Événement supprimé.', $self . '?p=evenements');
        case 'photo_delete':
            $ev = (int)($_POST['event'] ?? 0); delete_photo($id); flash_to('Photo supprimée.', $self . '?p=evenement&id=' . $ev . '#photos');
        case 'story_save':
            $title = trim((string)($_POST['title'] ?? ''));
            $sd = (string)($_POST['story_date'] ?? ''); $sd = preg_match('/^\d{4}-\d{2}-\d{2}$/', $sd) ? $sd : null;
            $cap = trim((string)($_POST['caption'] ?? '')); $quote = trim((string)($_POST['quote'] ?? ''));
            $back = $self . '?p=histoire' . ($id ? '&id=' . $id : '');
            if ($title === '') flash_to('Donnez un titre à ce visuel.', $back, true);
            $newPhoto = null;
            if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $newPhoto = save_photo($_FILES['image']['tmp_name'], 0);
                if (!$newPhoto) flash_to('Ce fichier n\'est pas une image prise en charge (JPEG, PNG ou WebP).', $back, true);
            }
            $newVideo = null;
            if (!empty($_FILES['video']['name'])) {
                if ($_FILES['video']['error'] !== UPLOAD_ERR_OK) flash_to('La vidéo n\'a pas pu être envoyée (fichier trop lourd pour l\'hébergement ?). Essayez une version plus légère.', $back, true);
                $newVideo = save_video($_FILES['video']['tmp_name']);
                if (!$newVideo) flash_to('Cette vidéo n\'est pas au format MP4.', $back, true);
            }
            if ($id) {
                $old = q('SELECT photo_id, video FROM club_story WHERE id = ?', [$id])->fetch();
                $video = $old['video'] ?? null;
                if ($newVideo || !empty($_POST['no_video'])) { delete_video($video); $video = $newVideo; }
                q('UPDATE club_story SET title = ?, story_date = ?, caption = ?, quote = ?, video = ?' . ($newPhoto ? ', photo_id = ' . (int)$newPhoto : '') . ' WHERE id = ?', [$title, $sd, $cap, $quote, $video, $id]);
                if ($newPhoto && $old) delete_photo((int)$old['photo_id']);
                flash_to('Chapitre mis à jour.', $self . '?p=histoire');
            }
            if (!$newPhoto) flash_to('Choisissez l\'image du chapitre.', $back, true);
            q('INSERT INTO club_story (title, story_date, caption, quote, photo_id, video, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)', [$title, $sd, $cap, $quote, $newPhoto, $newVideo, now()]);
            flash_to('Chapitre ajouté à « Notre histoire ».', $self . '?p=histoire');
        case 'story_intro':
            set_setting('story_intro', trim((string)($_POST['intro'] ?? '')));
            flash_to('Texte d\'ouverture enregistré.', $self . '?p=histoire');
        case 'story_delete':
            $it = q('SELECT photo_id, video FROM club_story WHERE id = ?', [$id])->fetch();
            if ($it) { delete_photo((int)$it['photo_id']); delete_video($it['video']); q('DELETE FROM club_story WHERE id = ?', [$id]); }
            flash_to('Chapitre retiré.', $self . '?p=histoire');
        case 'photo_cover':
            $ev = (int)($_POST['event'] ?? 0); q('UPDATE club_events SET cover_photo_id = ? WHERE id = ?', [$id, $ev]);
            flash_to('Photo de couverture choisie.', $self . '?p=evenement&id=' . $ev . '#photos');
    }
    redirect($self);
}

/* ---------------------------------------------------------------- 5. pages */
$p = (string)($_GET['p'] ?? 'demandes');
seed_events(); seed_story();
$pending = q("SELECT * FROM club_members WHERE status = 'pending' ORDER BY requested_at DESC")->fetchAll();
function act(string $action, int $id, string $label, string $cls = '', string $confirm = '', string $extra = ''): string {
    return '<form method="post" style="display:inline">' . csrf_field() . '<input type="hidden" name="action" value="' . h($action) . '"><input type="hidden" name="id" value="' . $id . '">' . $extra
         . '<button class="a-btn ' . h($cls) . '" type="submit"' . ($confirm ? ' onclick="return confirm(' . h(json_encode($confirm, JSON_UNESCAPED_UNICODE)) . ')"' : '') . '>' . h($label) . '</button></form>';
}

admin_page_start('Administration');
?>
<div class="a-top">
  <h1>NOVA Club · Administration</h1>
  <div class="a-row-actions">
    <a class="a-btn discret" href="<?= h(base_url()) ?>" target="_blank" rel="noopener">Voir le club</a>
    <span class="a-help" style="align-self:center"><?= h($_SESSION['admin_email']) ?></span>
    <?= act('logout', 0, 'Se déconnecter', 'discret') ?>
  </div>
</div>
<nav class="a-tabs">
  <a href="?p=demandes"<?= $p === 'demandes' ? ' aria-current="page"' : '' ?>>Demandes<?= $pending ? '<span class="a-badge">' . count($pending) . '</span>' : '' ?></a>
  <a href="?p=invites"<?= $p === 'invites' ? ' aria-current="page"' : '' ?>>Invités</a>
  <a href="?p=evenements"<?= in_array($p, ['evenements', 'evenement'], true) ? ' aria-current="page"' : '' ?>>Événements</a>
  <a href="?p=histoire"<?= $p === 'histoire' ? ' aria-current="page"' : '' ?>>Notre histoire</a>
</nav>
<?php show_flash($flash);

/* ----- demandes */
if ($p === 'demandes'): ?>
  <section class="a-box"><h2>Demandes en attente</h2>
  <?php if (!$pending): ?><p class="a-help">Aucune demande en attente. Le salon est calme.</p>
  <?php else: ?><div class="a-table-wrap"><table class="a-table"><thead><tr><th>Adresse</th><th>Demandée le</th><th></th></tr></thead><tbody>
    <?php foreach ($pending as $m): ?><tr>
      <td><?= h($m['email']) ?></td><td><?= h(fr_date($m['requested_at'])) ?></td>
      <td><div class="a-row-actions"><?= act('accept', (int)$m['id'], 'Accepter', 'plein') ?><?= act('refuse', (int)$m['id'], 'Écarter', 'discret') ?></div></td>
    </tr><?php endforeach; ?>
  </tbody></table></div>
  <p class="a-help" style="margin-top:1rem">« Écarter » ne prévient pas la personne : si elle se présente à nouveau, elle reçoit le même message d'attente.</p>
  <?php endif; ?></section>

<?php /* ----- invités */
elseif ($p === 'invites'):
  $members = q("SELECT * FROM club_members WHERE status <> 'pending' ORDER BY (status = 'approved') DESC, approved_at DESC, requested_at DESC")->fetchAll(); ?>
  <section class="a-box"><h2>Invités</h2>
  <?php if (!$members): ?><p class="a-help">Aucun invité pour le moment.</p>
  <?php else: ?><div class="a-table-wrap"><table class="a-table"><thead><tr><th>Adresse</th><th>Statut</th><th>Dernière visite</th><th></th></tr></thead><tbody>
    <?php foreach ($members as $m): $active = member_active($m); ?><tr>
      <td><?= h($m['email']) ?></td>
      <td><?php if ($active): ?><span class="a-status ok">Invité jusqu'au <?= h(fr_date($m['expires_at'])) ?></span>
          <?php elseif ($m['status'] === 'approved'): ?><span class="a-status off">Invitation expirée</span>
          <?php else: ?><span class="a-status off">Écarté / retiré</span><?php endif; ?></td>
      <td><?= $m['last_login_at'] ? h(fr_date($m['last_login_at'])) : '—' ?></td>
      <td><div class="a-row-actions">
        <?php if ($active): ?><?= act('resend', (int)$m['id'], 'Renvoyer la clé', 'discret') ?><?= act('revoke', (int)$m['id'], 'Retirer l\'accès', 'danger', 'Retirer l\'accès de cette personne ?') ?>
        <?php else: ?><?= act('accept', (int)$m['id'], 'Réinviter') ?><?php endif; ?>
        <?= act('forget', (int)$m['id'], 'Supprimer', 'danger', 'Supprimer définitivement cette adresse ?') ?>
      </div></td>
    </tr><?php endforeach; ?>
  </tbody></table></div><?php endif; ?></section>

<?php /* ----- liste des événements */
elseif ($p === 'evenements'):
  $events = q('SELECT e.*, (SELECT COUNT(*) FROM club_photos p WHERE p.event_id = e.id) AS nb FROM club_events e ORDER BY event_date DESC')->fetchAll(); ?>
  <p><a class="a-btn plein" href="?p=evenement">+ Nouvel événement</a></p><br>
  <section class="a-box"><h2>Événements</h2>
  <?php if (!$events): ?><p class="a-help">Aucun événement. Créez le premier : il apparaîtra aussitôt dans le club.</p>
  <?php else: ?><div class="a-table-wrap"><table class="a-table"><thead><tr><th>Date</th><th>Titre</th><th>Photos</th><th></th></tr></thead><tbody>
    <?php foreach ($events as $e): ?><tr>
      <td><?= h(event_date_label($e, false)) ?><br><span class="a-status <?= $e['event_date'] >= date('Y-m-d') ? 'wait' : 'off' ?>"><?= $e['event_date'] >= date('Y-m-d') ? 'À venir' : 'Passé' ?></span></td>
      <td><?= h($e['title']) ?></td><td><?= (int)$e['nb'] ?></td>
      <td><div class="a-row-actions"><a class="a-btn" href="?p=evenement&id=<?= (int)$e['id'] ?>">Modifier</a></div></td>
    </tr><?php endforeach; ?>
  </tbody></table></div><?php endif; ?></section>

<?php /* ----- créer / modifier un événement */
elseif ($p === 'evenement'):
  $e = !empty($_GET['id']) ? q('SELECT * FROM club_events WHERE id = ?', [(int)$_GET['id']])->fetch() : null;
  $e = $e ?: ['id' => 0, 'title' => '', 'event_date' => '', 'event_time' => '', 'place' => '', 'description' => '', 'cover_photo_id' => null, 'month_only' => 0];
  $photos = $e['id'] ? q('SELECT * FROM club_photos WHERE event_id = ? ORDER BY id ASC', [$e['id']])->fetchAll() : [];
  $cover = $e['cover_photo_id'] ?: ($photos[0]['id'] ?? null); ?>
  <p><a class="a-btn discret" href="?p=evenements">← Tous les événements</a></p><br>
  <section class="a-box"><h2><?= $e['id'] ? 'Modifier l\'événement' : 'Nouvel événement' ?></h2>
    <form method="post" enctype="multipart/form-data" class="a-form">
      <?= csrf_field() ?><input type="hidden" name="action" value="event_save"><input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
      <label for="title">Titre</label><input id="title" type="text" name="title" value="<?= h($e['title']) ?>" required maxlength="200" placeholder="Soirée de rentrée du club">
      <div class="a-grid2">
        <div><label for="d">Date</label><input id="d" type="date" name="event_date" value="<?= h($e['event_date']) ?>" required></div>
        <div><label for="t">Horaire (visible tant que l'événement est à venir)</label><input id="t" type="text" name="event_time" value="<?= h($e['event_time']) ?>" maxlength="60" placeholder="19h00 – 23h00"><p class="a-help">Affiché uniquement tant que l'événement est à venir.</p></div>
      </div>
      <label style="text-transform:none;letter-spacing:0;font-family:inherit;color:var(--texte);margin-top:.6rem"><input type="checkbox" name="month_only" value="1"<?= !empty($e['month_only']) ? ' checked' : '' ?>> Jour exact inconnu : afficher seulement le mois et l'année</label>
      <label for="pl">Lieu</label><input id="pl" type="text" name="place" value="<?= h($e['place']) ?>" maxlength="200" placeholder="Salons privés, Paris 15e">
      <label for="desc">Texte</label><textarea id="desc" name="description" placeholder="Quelques lignes pour donner envie… Laissez une ligne vide pour commencer un nouveau paragraphe."><?= h($e['description']) ?></textarea>
      <p class="a-help">La mise en page est automatique. Une fois la date passée, l'événement rejoint de lui-même les « Événements passés ».</p>
      <?php if (!$e['id']): ?>
      <label for="ph">Photos (facultatif)</label><input id="ph" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple>
      <p class="a-help">Vous pourrez aussi ajouter les photos plus tard, après la soirée.</p>
      <?php endif; ?>
      <p style="margin-top:1.6rem"><button class="a-btn plein" type="submit">Enregistrer</button></p>
    </form>
  </section>

  <?php if ($e['id']): ?>
  <section class="a-box" id="photos"><h2>Photos</h2>
    <form id="up" class="a-form" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?><input type="hidden" name="action" value="event_save"><input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
      <input type="hidden" name="title" value="<?= h($e['title']) ?>"><input type="hidden" name="event_date" value="<?= h($e['event_date']) ?>">
      <input type="hidden" name="event_time" value="<?= h($e['event_time']) ?>"><input type="hidden" name="place" value="<?= h($e['place']) ?>"><input type="hidden" name="description" value="<?= h($e['description']) ?>">
      <label for="ph2">Ajouter des photos</label><input id="ph2" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple>
      <p class="a-help">Sélectionnez autant de photos que vous voulez, directement depuis votre téléphone ou votre ordinateur : elles sont allégées automatiquement avant l'envoi.</p>
      <p style="margin-top:1rem"><button class="a-btn plein" type="submit">Envoyer les photos</button> <span id="up-status" class="a-help" role="status"></span></p>
    </form>
    <?php if ($photos): ?><div class="a-photos" style="margin-top:1.6rem">
      <?php foreach ($photos as $ph): $isCover = (int)$ph['id'] === (int)$cover; ?>
      <div class="a-photo<?= $isCover ? ' cover' : '' ?>">
        <img src="<?= h(photo_url((int)$ph['id'])) ?>" alt="" loading="lazy">
        <?php if ($isCover): ?><span class="a-cover-tag">Couverture</span><?php endif; ?>
        <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$ph['id'] ?>"><input type="hidden" name="event" value="<?= (int)$e['id'] ?>">
          <?php if (!$isCover): ?><button name="action" value="photo_cover" type="submit">Couverture</button><?php else: ?><span></span><?php endif; ?>
          <button name="action" value="photo_delete" type="submit" onclick="return confirm('Supprimer cette photo ?')">Supprimer</button>
        </form>
      </div>
      <?php endforeach; ?>
    </div><?php endif; ?>
  </section>
  <section class="a-box"><h2>Supprimer l'événement</h2>
    <p class="a-help" style="margin-bottom:1rem">L'événement et toutes ses photos seront définitivement effacés.</p>
    <?= act('event_delete', (int)$e['id'], 'Supprimer l\'événement', 'danger', 'Supprimer définitivement cet événement et ses photos ?') ?>
  </section>
  <script>
  /* Envoi photo par photo, allégées dans le navigateur : pas de limite de taille, progression visible */
  (function(){
    var f=document.getElementById('up'),inp=document.getElementById('ph2'),st=document.getElementById('up-status');
    if(!f||!window.fetch||!window.createImageBitmap)return;
    function shrink(file){
      return createImageBitmap(file).then(function(bm){
        var k=Math.min(1,2000/Math.max(bm.width,bm.height)),c=document.createElement('canvas');
        c.width=Math.round(bm.width*k);c.height=Math.round(bm.height*k);
        c.getContext('2d').drawImage(bm,0,0,c.width,c.height);
        return new Promise(function(r){c.toBlob(r,'image/jpeg',.88);});
      }).catch(function(){return file;});
    }
    f.addEventListener('submit',function(ev){
      var files=[].slice.call(inp.files||[]);if(!files.length)return;
      ev.preventDefault();var done=0,ok=0,btn=f.querySelector('button');btn.disabled=true;
      var csrf=f.querySelector('[name=csrf]').value,id=f.querySelector('[name=id]').value;
      (function next(){
        if(done>=files.length){st.textContent=ok+' photo'+(ok>1?'s':'')+' ajoutée'+(ok>1?'s':'')+(ok<files.length?' ('+(files.length-ok)+' refusée'+((files.length-ok)>1?'s':'')+' : format non pris en charge)':'')+'.';setTimeout(function(){location.href=location.pathname+location.search+'#photos';location.reload();},900);return;}
        st.textContent='Envoi '+(done+1)+' / '+files.length+'…';
        shrink(files[done]).then(function(blob){
          var d=new FormData();d.append('csrf',csrf);d.append('action','photo_upload');d.append('id',id);d.append('photo',blob,'photo.jpg');
          return fetch(location.pathname,{method:'POST',body:d,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(j){if(j.ok)ok++;});
        }).catch(function(){}).then(function(){done++;next();});
      })();
    });
  })();
  </script>
  <?php endif; ?>
<?php /* ----- notre histoire */
elseif ($p === 'histoire'):
  seed_story();
  $edit = !empty($_GET['id']) ? q('SELECT * FROM club_story WHERE id = ?', [(int)$_GET['id']])->fetch() : null;
  $items = q('SELECT * FROM club_story ORDER BY (story_date IS NULL) DESC, story_date DESC, id DESC')->fetchAll(); $total = count($items); ?>
  <section class="a-box"><h2>Texte d'ouverture</h2>
    <form method="post" class="a-form"><?= csrf_field() ?><input type="hidden" name="action" value="story_intro">
      <textarea name="intro" style="min-height:100px" aria-label="Texte d'ouverture"><?= h(story_intro()) ?></textarea>
      <p class="a-help">Affiché en haut de la page, sous le titre « Notre histoire ».</p>
      <p style="margin-top:1rem"><button class="a-btn" type="submit">Enregistrer</button></p>
    </form>
  </section>
  <section class="a-box"><h2><?= $edit ? 'Modifier le chapitre' : 'Ajouter un chapitre' ?></h2>
    <p class="a-help">Les chapitres sont numérotés automatiquement d'après leur date (le plus ancien est le Chapitre I) et s'affichent du plus récent, en haut, au plus ancien, en bas.</p>
    <form method="post" enctype="multipart/form-data" class="a-form" data-shrink>
      <?= csrf_field() ?><input type="hidden" name="action" value="story_save"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
      <div class="a-grid2">
        <div><label for="st">Titre du chapitre</label><input id="st" type="text" name="title" value="<?= h($edit['title'] ?? '') ?>" required maxlength="200" placeholder="Le NOVA Club ouvre ses portes"></div>
        <div><label for="sd">Date</label><input id="sd" type="date" name="story_date" value="<?= h($edit['story_date'] ?? '') ?>"><p class="a-help">Seuls le mois et l'année s'affichent.</p></div>
      </div>
      <label for="sq">Citation (facultatif)</label><textarea id="sq" name="quote" style="min-height:70px" placeholder="Une phrase forte, affichée en italique et entre guillemets."><?= h($edit['quote'] ?? '') ?></textarea>
      <label for="sc">Récit</label><textarea id="sc" name="caption" style="min-height:160px" placeholder="Laissez une ligne vide pour commencer un nouveau paragraphe."><?= h($edit['caption'] ?? '') ?></textarea>
      <?php if ($edit): ?><img src="<?= h(photo_url((int)$edit['photo_id'])) ?>" alt="" style="max-height:140px;width:auto;margin-top:1.2rem;border:1px solid var(--ligne)"><?php endif; ?>
      <label for="img"><?= $edit ? 'Remplacer l\'image (facultatif)' : 'Image' ?></label><input id="img" type="file" name="image" accept="image/jpeg,image/png,image/webp"<?= $edit ? '' : ' required' ?>>
      <label for="vid">Vidéo (facultatif, MP4)</label><input id="vid" type="file" name="video" accept="video/mp4,video/quicktime">
      <p class="a-help">Si une vidéo est ajoutée, l'image sert d'affiche avant la lecture. Préférez une vidéo légère (moins de 20 Mo).</p>
      <?php if (!empty($edit['video'])): ?><label style="text-transform:none;letter-spacing:0;font-family:inherit;color:var(--texte)"><input type="checkbox" name="no_video" value="1"> Retirer la vidéo actuelle</label><?php endif; ?>
      <p style="margin-top:1.4rem"><button class="a-btn plein" type="submit"><?= $edit ? 'Enregistrer' : 'Ajouter le chapitre' ?></button>
        <?php if ($edit): ?><a class="a-btn discret" href="?p=histoire">Annuler</a><?php endif; ?> <span class="a-help" role="status" data-status></span></p>
    </form>
  </section>
  <section class="a-box"><h2>Chapitres publiés</h2>
  <?php if (!$items): ?><p class="a-help">Aucun chapitre pour l'instant.</p>
  <?php else: ?><div class="a-table-wrap"><table class="a-table"><tbody>
    <?php foreach ($items as $n => $it): ?><tr>
      <td style="width:96px"><img src="<?= h(photo_url((int)$it['photo_id'])) ?>" alt="" style="width:80px;height:80px;object-fit:cover;border:1px solid var(--ligne)"></td>
      <td><span class="a-status wait">Chapitre <?= roman($total - $n) ?><?= $it['video'] ? ' · vidéo' : '' ?></span><br><?= h($it['title']) ?><br><span class="a-help"><?= $it['story_date'] ? h(ucfirst(fr_month_year($it['story_date']))) : 'Sans date' ?></span></td>
      <td><div class="a-row-actions"><a class="a-btn" href="?p=histoire&id=<?= (int)$it['id'] ?>">Modifier</a><?= act('story_delete', (int)$it['id'], 'Retirer', 'danger', 'Retirer ce chapitre de « Notre histoire » ?') ?></div></td>
    </tr><?php endforeach; ?>
  </tbody></table></div><?php endif; ?></section>
  <script>
  /* image allégée dans le navigateur avant l'envoi (la vidéo part telle quelle) */
  (function(){var f=document.querySelector('form[data-shrink]');if(!f||!window.fetch||!window.createImageBitmap)return;
    var inp=f.querySelector('input[name=image]'),st=f.querySelector('[data-status]');
    f.addEventListener('submit',function(e){var file=inp.files&&inp.files[0];if(!f.checkValidity())return;e.preventDefault();
      st.textContent='Envoi en cours…';f.querySelector('button').disabled=true;
      var p=file?createImageBitmap(file).then(function(bm){var k=Math.min(1,2000/Math.max(bm.width,bm.height)),c=document.createElement('canvas');
        c.width=Math.round(bm.width*k);c.height=Math.round(bm.height*k);var x=c.getContext('2d');x.fillStyle='#141416';x.fillRect(0,0,c.width,c.height);x.drawImage(bm,0,0,c.width,c.height);
        return new Promise(function(r){c.toBlob(r,'image/jpeg',.9);});}).catch(function(){return file;}):Promise.resolve(null);
      p.then(function(blob){var d=new FormData(f);if(blob)d.set('image',blob,'visuel.jpg');return fetch(location.pathname,{method:'POST',body:d,credentials:'same-origin'});})
      .then(function(r){return r.text().then(function(t){history.replaceState(null,'',r.url);document.open();document.write(t);document.close();});}).catch(function(){f.submit();});});})();
  </script>
<?php endif;
admin_page_end();
