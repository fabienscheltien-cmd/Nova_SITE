<?php
/* NOVA CLUB — noyau : base de données, sécurité, e-mails, outils */
declare(strict_types=1);

$CFG = require __DIR__ . '/config.php';
// Réglages de test : pris en compte uniquement sur votre ordinateur (localhost), jamais en ligne
$__host = strtolower(explode(':', $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? ''))[0]);
if (in_array($__host, ['localhost', '127.0.0.1'], true) && is_file(__DIR__ . '/config.local.php')) { $CFG = array_merge($CFG, require __DIR__ . '/config.local.php'); }
date_default_timezone_set($CFG['timezone']);
ini_set('display_errors', '0');

const DATA_DIR = __DIR__ . '/../data';
const PHOTO_DIR = DATA_DIR . '/photos';
const VIDEO_DIR = DATA_DIR . '/videos';

function cfg(string $k) { global $CFG; return $CFG[$k] ?? null; }
function base_url(string $path = ''): string { return rtrim(cfg('base_url'), '/') . '/' . ltrim($path, '/'); }
// Racine du site vitrine : le dossier qui contient le club (https://www.nova-serenity.fr/)
function site_url(string $path = ''): string { return preg_replace('#[^/]*$#', '', rtrim(cfg('base_url'), '/')) . ltrim($path, '/'); }
function base_path(): string { $p = parse_url(cfg('base_url'), PHP_URL_PATH) ?: '/'; return rtrim($p, '/') ?: '/'; }
function is_https(): bool { return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'); }
function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function now(): string { return date('Y-m-d H:i:s'); }
function ip_hash(): string { return hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . '|' . secret()); }
function redirect(string $url) { header('Location: ' . $url); exit; }

/* ---------------------------------------------------------------- base */
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    if (cfg('db_driver') === 'sqlite') {
        $pdo = new PDO('sqlite:' . DATA_DIR . '/club.sqlite');
    } else {
        $pdo = new PDO('mysql:host=' . cfg('db_host') . ';dbname=' . cfg('db_name') . ';charset=utf8mb4', cfg('db_user'), cfg('db_pass'));
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    install_schema($pdo);
    return $pdo;
}

function install_schema(PDO $pdo) {
    $sqlite = cfg('db_driver') === 'sqlite';
    $id = $sqlite ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY';
    $end = $sqlite ? '' : ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
    $tables = [
        "CREATE TABLE IF NOT EXISTS club_settings (k VARCHAR(64) NOT NULL PRIMARY KEY, v TEXT)$end",
        "CREATE TABLE IF NOT EXISTS club_members (id $id, email VARCHAR(190) NOT NULL UNIQUE, status VARCHAR(16) NOT NULL DEFAULT 'pending',
            requested_at DATETIME NULL, approved_at DATETIME NULL, expires_at DATETIME NULL, last_login_at DATETIME NULL)$end",
        "CREATE TABLE IF NOT EXISTS club_tokens (id $id, member_id INT NOT NULL, kind VARCHAR(16) NOT NULL, token_hash CHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL, used_at DATETIME NULL)$end",
        "CREATE TABLE IF NOT EXISTS club_sessions (id $id, member_id INT NOT NULL, token_hash CHAR(64) NOT NULL, created_at DATETIME NOT NULL,
            expires_at DATETIME NOT NULL)$end",
        "CREATE TABLE IF NOT EXISTS club_events (id $id, title VARCHAR(200) NOT NULL, event_date DATE NOT NULL, event_time VARCHAR(60) NULL,
            place VARCHAR(200) NULL, description TEXT NULL, cover_photo_id INT NULL, created_at DATETIME NOT NULL, month_only INT NOT NULL DEFAULT 0)$end",
        "CREATE TABLE IF NOT EXISTS club_photos (id $id, event_id INT NOT NULL, file VARCHAR(80) NOT NULL, width INT NOT NULL, height INT NOT NULL,
            created_at DATETIME NOT NULL)$end",
        "CREATE TABLE IF NOT EXISTS club_attempts (id $id, ip_hash CHAR(64) NOT NULL, action VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL)$end",
        "CREATE TABLE IF NOT EXISTS club_story (id $id, title VARCHAR(200) NOT NULL, story_date DATE NULL, caption TEXT NULL, photo_id INT NOT NULL,
            created_at DATETIME NOT NULL, quote TEXT NULL, video VARCHAR(80) NULL)$end",
    ];
    static $done = false;
    if ($done) return;
    $done = true;
    try { $pdo->query('SELECT s.quote, s.video, e.month_only FROM club_story s, club_events e LIMIT 1'); return; } catch (Throwable $e) { /* installation ou mise à jour */ }
    foreach ($tables as $sql) $pdo->exec($sql);
    $add = ['club_story' => ['quote' => 'TEXT NULL', 'video' => 'VARCHAR(80) NULL'], 'club_events' => ['month_only' => 'INT NOT NULL DEFAULT 0']];
    foreach ($add as $table => $cols) foreach ($cols as $col => $type) {
        try { $pdo->query("SELECT $col FROM $table LIMIT 1"); } catch (Throwable $e) { $pdo->exec("ALTER TABLE $table ADD COLUMN $col $type"); }
    }
}

function q(string $sql, array $args = []): PDOStatement { $st = db()->prepare($sql); $st->execute($args); return $st; }
function setting(string $k, $default = null) { $r = q('SELECT v FROM club_settings WHERE k = ?', [$k])->fetch(); return $r ? $r['v'] : $default; }
function set_setting(string $k, string $v) {
    if (setting($k) === null) q('INSERT INTO club_settings (k, v) VALUES (?, ?)', [$k, $v]);
    else q('UPDATE club_settings SET v = ? WHERE k = ?', [$v, $k]);
}
function secret(): string {
    static $s = null;
    if ($s) return $s;
    $s = setting('secret');
    if (!$s) { $s = bin2hex(random_bytes(32)); set_setting('secret', $s); }
    return $s;
}

/* ---------------------------------------------------------- limitation */
function too_many(string $action, int $max, int $minutes): bool {
    $since = date('Y-m-d H:i:s', time() - $minutes * 60);
    $n = (int) q('SELECT COUNT(*) AS n FROM club_attempts WHERE ip_hash = ? AND action = ? AND created_at > ?', [ip_hash(), $action, $since])->fetch()['n'];
    return $n >= $max;
}
function note_attempt(string $action) {
    q('INSERT INTO club_attempts (ip_hash, action, created_at) VALUES (?, ?, ?)', [ip_hash(), $action, now()]);
    if (random_int(1, 50) === 1) q('DELETE FROM club_attempts WHERE created_at < ?', [date('Y-m-d H:i:s', time() - 86400 * 2)]);
}

/* ------------------------------------------------------------- e-mails */
function personal_domains(): array {
    return ['gmail.com','googlemail.com','hotmail.com','hotmail.fr','hotmail.co.uk','outlook.com','outlook.fr','live.com','live.fr','msn.com',
        'yahoo.com','yahoo.fr','ymail.com','rocketmail.com','icloud.com','me.com','mac.com','aol.com','aol.fr','orange.fr','wanadoo.fr','free.fr',
        'sfr.fr','neuf.fr','cegetel.net','club-internet.fr','bbox.fr','numericable.fr','noos.fr','laposte.net','gmx.fr','gmx.com','gmx.de','gmx.net',
        'web.de','proton.me','protonmail.com','pm.me','tutanota.com','tuta.io','zoho.com','mail.com','yandex.com','yandex.ru','mail.ru',
        'yopmail.com','yopmail.fr','mailinator.com','guerrillamail.com','10minutemail.com','tempmail.com','temp-mail.org','trashmail.com',
        'sharklasers.com','dispostable.com','getnada.com','free.com','hey.com','fastmail.com','libero.it','alice.it','t-online.de','qq.com','163.com'];
}
function normalize_email(string $e): string { return mb_strtolower(trim($e)); }
function email_domain(string $e): string { return substr(strrchr($e, '@') ?: '', 1); }
function is_personal_email(string $e): bool {
    $d = email_domain($e);
    if (in_array($d, personal_domains(), true)) return true;
    return (bool) preg_match('/^(hotmail|outlook|live|yahoo|gmx|yopmail)\./', $d);
}

function send_mail(string $to, string $subject, string $title, string $bodyHtml, string $bodyText): bool {
    $fromName = cfg('mail_from_name'); $from = cfg('mail_from');
    $html = mail_layout($title, $bodyHtml);
    if (cfg('mail_mode') === 'log') {
        file_put_contents(DATA_DIR . '/mail.log', "=== " . now() . "\nTO: $to\nSUBJECT: $subject\n$bodyText\n\n", FILE_APPEND);
        file_put_contents(DATA_DIR . '/last-mail.html', $html);
        return true;
    }
    $b = 'nova' . bin2hex(random_bytes(8));
    $headers = [
        'From: ' . mb_encode_mimeheader($fromName, 'UTF-8', 'Q') . " <$from>",
        'Reply-To: ' . $from,
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $b . '"',
        'X-Mailer: NOVA Club',
    ];
    $msg = "--$b\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($bodyText))
         . "--$b\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($html))
         . "--$b--";
    return mail($to, mb_encode_mimeheader($subject, 'UTF-8', 'Q'), $msg, implode("\r\n", $headers), '-f' . $from);
}

function mail_button(string $url, string $label): string {
    return '<table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px auto"><tr><td style="border:1px solid #b89a5e;border-radius:2px">'
         . '<a href="' . h($url) . '" style="display:inline-block;padding:14px 34px;color:#e9d9b0;text-decoration:none;font-family:Georgia,serif;font-size:15px;letter-spacing:3px;text-transform:uppercase">'
         . h($label) . '</a></td></tr></table>';
}

function mail_layout(string $title, string $inner): string {
    $avatar = h(base_url('assets/maitre-ceremonie.png'));
    return '<!DOCTYPE html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>' . h($title) . '</title></head>'
    . '<body style="margin:0;background:#0b0b0d;padding:32px 12px">'
    . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td align="center">'
    . '<table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;background:#131315;border:1px solid #2a2721">'
    . '<tr><td align="center" style="padding:36px 36px 8px"><img src="' . $avatar . '" width="72" height="72" alt="" style="display:block;border:0">'
    . '<div style="margin-top:14px;font-family:Arial,sans-serif;font-size:11px;letter-spacing:5px;color:#b89a5e;text-transform:uppercase">NOVA Club</div></td></tr>'
    . '<tr><td style="padding:10px 44px 8px;font-family:Georgia,serif;color:#e8e2d6;font-size:16px;line-height:1.7">'
    . '<h1 style="font-family:Georgia,serif;font-weight:normal;font-size:24px;color:#f3ecdc;text-align:center;margin:10px 0 22px">' . h($title) . '</h1>'
    . $inner . '</td></tr>'
    . '<tr><td style="padding:10px 44px 36px;font-family:Georgia,serif;color:#b89a5e;font-size:15px;font-style:italic;text-align:center">— Le Maître de Cérémonie</td></tr>'
    . '</table><div style="font-family:Arial,sans-serif;font-size:11px;color:#6d675c;margin-top:16px">NOVA Club · NOVA Serenity · Paris</div>'
    . '</td></tr></table></body></html>';
}

/* ---------------------------------------------------- jetons & sessions */
function new_token(): string { return bin2hex(random_bytes(32)); }
function thash(string $t): string { return hash('sha256', $t); }
function sign(string $data): string { return substr(hash_hmac('sha256', $data, secret()), 0, 40); }

function member_by_email(string $email) { return q('SELECT * FROM club_members WHERE email = ?', [$email])->fetch(); }
function member_active(array $m): bool { return $m['status'] === 'approved' && $m['expires_at'] && $m['expires_at'] > now(); }

function send_access_link(array $m, bool $first): bool {
    $t = new_token();
    q('INSERT INTO club_tokens (member_id, kind, token_hash, expires_at) VALUES (?, ?, ?, ?)',
      [$m['id'], 'login', thash($t), date('Y-m-d H:i:s', time() + 7 * 86400)]);
    $url = base_url('acces.php?t=' . $t);
    $until = fr_date($m['expires_at']);
    if ($first) {
        $subject = 'Votre place au NOVA Club est réservée';
        $title = 'Bienvenue au club';
        $html = '<p>Le Maître de cérémonie a vérifié votre identité : les portes du NOVA Club vous sont ouvertes.</p>'
              . '<p>Votre invitation est valable jusqu\'au <strong style="color:#e9d9b0">' . h($until) . '</strong>.</p>'
              . mail_button($url, 'Entrer au club')
              . '<p style="font-size:13px;color:#8f887b">Ce lien vous est personnel et reste valable 7 jours. Votre navigateur vous reconnaîtra ensuite jusqu\'à la fin de votre invitation.</p>';
        $text = "Le Maître de cérémonie a vérifié votre identité : les portes du NOVA Club vous sont ouvertes.\n"
              . "Invitation valable jusqu'au $until.\n\nEntrer au club : $url\n\nCe lien vous est personnel et reste valable 7 jours.\n\n— Le Maître de Cérémonie";
    } else {
        $subject = 'Votre clé du NOVA Club';
        $title = 'Le Maître de cérémonie vous a reconnu';
        $html = '<p>Vous faites déjà partie des invités du NOVA Club. Voici une nouvelle clé pour entrer depuis cet appareil.</p>'
              . mail_button($url, 'Entrer au club')
              . '<p style="font-size:13px;color:#8f887b">Lien personnel, valable 7 jours. Votre invitation court jusqu\'au ' . h($until) . '.</p>';
        $text = "Vous faites déjà partie des invités du NOVA Club.\nEntrer au club : $url\n\nLien personnel, valable 7 jours. Invitation valable jusqu'au $until.\n\n— Le Maître de Cérémonie";
    }
    return send_mail($m['email'], $subject, $title, $html, $text);
}

function approve_member(int $id): array {
    $expires = date('Y-m-d H:i:s', strtotime('+' . (int)cfg('access_months') . ' months'));
    q("UPDATE club_members SET status = 'approved', approved_at = ?, expires_at = ? WHERE id = ?", [now(), $expires, $id]);
    $m = q('SELECT * FROM club_members WHERE id = ?', [$id])->fetch();
    send_access_link($m, true);
    return $m;
}

function revoke_member(int $id) {
    q("UPDATE club_members SET status = 'revoked' WHERE id = ?", [$id]);
    q('DELETE FROM club_sessions WHERE member_id = ?', [$id]);
    q('DELETE FROM club_tokens WHERE member_id = ?', [$id]);
}

const MEMBER_COOKIE = 'novaclub';
function cookie_opts(int $expires): array {
    return ['expires' => $expires, 'path' => base_path(), 'secure' => is_https(), 'httponly' => true, 'samesite' => 'Lax'];
}
function open_session(array $m) {
    $t = new_token();
    q('INSERT INTO club_sessions (member_id, token_hash, created_at, expires_at) VALUES (?, ?, ?, ?)', [$m['id'], thash($t), now(), $m['expires_at']]);
    q('UPDATE club_members SET last_login_at = ? WHERE id = ?', [now(), $m['id']]);
    setcookie(MEMBER_COOKIE, $t, cookie_opts(strtotime($m['expires_at'])));
}
function current_member() {
    static $m = false;
    if ($m !== false) return $m;
    $m = null;
    $t = $_COOKIE[MEMBER_COOKIE] ?? '';
    if (!preg_match('/^[a-f0-9]{64}$/', $t)) return $m;
    $r = q("SELECT m.* FROM club_sessions s JOIN club_members m ON m.id = s.member_id
            WHERE s.token_hash = ? AND s.expires_at > ? AND m.status = 'approved' AND m.expires_at > ?", [thash($t), now(), now()])->fetch();
    if ($r) $m = $r;
    return $m;
}
function close_session() {
    $t = $_COOKIE[MEMBER_COOKIE] ?? '';
    if ($t) q('DELETE FROM club_sessions WHERE token_hash = ?', [thash($t)]);
    setcookie(MEMBER_COOKIE, '', cookie_opts(time() - 3600));
}

/* ------------------------------------------------------------- admin */
function admin_session() {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    session_name('novaclub_admin');
    session_set_cookie_params(['lifetime' => 0, 'path' => base_path(), 'secure' => is_https(), 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}
function is_admin(): bool {
    admin_session();
    return !empty($_SESSION['admin']) && ($_SESSION['admin_until'] ?? 0) > time();
}
function admin_emails(): array {
    $l = cfg('admin_emails') ?: (cfg('admin_email') ? [cfg('admin_email')] : []);
    return array_values(array_unique(array_map('normalize_email', (array)$l)));
}
function admin_hash_key(string $email): string { return 'admin_hash_' . substr(hash('sha256', normalize_email($email)), 0, 32); }
function admin_hash(string $email) {
    $h = setting(admin_hash_key($email));
    if (!$h && ($old = setting('admin_hash')) && normalize_email($email) === (admin_emails()[0] ?? '')) $h = $old;  // ancien mot de passe unique
    return $h;
}
function notify_admins(string $subject, string $title, string $html, string $text) {
    foreach (admin_emails() as $a) send_mail($a, $subject, $title, $html, $text);
}
function csrf(): string { admin_session(); if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = new_token(); return $_SESSION['csrf']; }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . h(csrf()) . '">'; }
function check_csrf() { admin_session(); if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(400); exit('Session expirée, rechargez la page.'); } }

/* ------------------------------------------------------------- dates */
function fr_date(string $d, bool $withYear = true): string {
    $mois = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
    $t = strtotime($d);
    return (int)date('j', $t) . ' ' . $mois[(int)date('n', $t) - 1] . ($withYear ? ' ' . date('Y', $t) : '');
}
function fr_weekday(string $d): string { return ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'][(int)date('w', strtotime($d))]; }
function fr_month_short(string $d): string { return ['janv.','févr.','mars','avr.','mai','juin','juil.','août','sept.','oct.','nov.','déc.'][(int)date('n', strtotime($d)) - 1]; }

/* ------------------------------------------------------------- photos */
function save_photo(string $tmp, int $eventId) {
    $info = @getimagesize($tmp);
    if (!$info || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) return null;
    $src = @imagecreatefromstring((string) file_get_contents($tmp));
    if (!$src) return null;
    if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
        $o = (int) (@exif_read_data($tmp)['Orientation'] ?? 1);
        if ($o === 3) $src = imagerotate($src, 180, 0);
        elseif ($o === 6) $src = imagerotate($src, -90, 0);
        elseif ($o === 8) $src = imagerotate($src, 90, 0);
    }
    $name = bin2hex(random_bytes(10));
    if (!is_dir(PHOTO_DIR)) mkdir(PHOTO_DIR, 0755, true);
    [$w, $hgt] = resize_to($src, 2000, PHOTO_DIR . "/$name.jpg", 82);
    resize_to($src, 720, PHOTO_DIR . "/$name-m.jpg", 78);
    imagedestroy($src);
    q('INSERT INTO club_photos (event_id, file, width, height, created_at) VALUES (?, ?, ?, ?, ?)', [$eventId, $name, $w, $hgt, now()]);
    return (int) db()->lastInsertId();
}
function resize_to($src, int $max, string $dest, int $quality): array {
    $w = imagesx($src); $hgt = imagesy($src);
    $k = min(1, $max / max($w, $hgt));
    $nw = (int) round($w * $k); $nh = (int) round($hgt * $k);
    $dst = imagecreatetruecolor($nw, $nh);
    imagefill($dst, 0, 0, imagecolorallocate($dst, 20, 20, 22));
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $hgt);
    imageinterlace($dst, true);
    imagejpeg($dst, $dest, $quality);
    imagedestroy($dst);
    return [$nw, $nh];
}
function delete_photo(int $id) {
    $p = q('SELECT * FROM club_photos WHERE id = ?', [$id])->fetch();
    if (!$p) return;
    @unlink(PHOTO_DIR . '/' . $p['file'] . '.jpg'); @unlink(PHOTO_DIR . '/' . $p['file'] . '-m.jpg');
    q('DELETE FROM club_photos WHERE id = ?', [$id]);
    q('UPDATE club_events SET cover_photo_id = NULL WHERE cover_photo_id = ?', [$id]);
}
function event_date_label(array $e, bool $weekday = true): string {
    if (!empty($e['month_only'])) return ucfirst(fr_month_year($e['event_date']));
    return ($weekday ? ucfirst(fr_weekday($e['event_date'])) . ' ' : '') . fr_date($e['event_date']);
}
function fr_month_year(string $d): string { $m = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre']; $t = strtotime($d); return $m[(int)date('n', $t) - 1] . ' ' . date('Y', $t); }
function photo_url(int $id, bool $full = false): string { return base_url('photo.php?id=' . $id . ($full ? '&t=full' : '')); }

/* ------------------------------------------------------------- vidéos */
function save_video(string $tmp, string $origName = '') {
    $h = @fopen($tmp, 'rb'); if (!$h) return null; $head = fread($h, 12); fclose($h);
    if (strlen($head) < 12 || substr($head, 4, 4) !== 'ftyp') return null;          // MP4 / MOV uniquement
    if (!is_dir(VIDEO_DIR)) mkdir(VIDEO_DIR, 0755, true);
    $name = bin2hex(random_bytes(10)) . '.mp4';
    if (!@copy($tmp, VIDEO_DIR . '/' . $name)) return null;
    return $name;
}
function delete_video(?string $name) { if ($name && preg_match('/^[a-f0-9]{20}\.mp4$/', $name)) @unlink(VIDEO_DIR . '/' . $name); }
function video_url(int $storyId): string { return base_url('media.php?v=' . $storyId); }

/* ------------------------------------------------------ Notre histoire */
function story_intro(): string {
    $t = setting('story_intro');
    return $t !== null ? $t : "Chaque grande aventure a ses premières pages. Les nôtres s'écrivent au fil des rencontres, des projets et des soirées partagées. Bienvenue dans les coulisses de NOVA Serenity.";
}
function roman(int $n): string {
    $m = [1000=>'M',900=>'CM',500=>'D',400=>'CD',100=>'C',90=>'XC',50=>'L',40=>'XL',10=>'X',9=>'IX',5=>'V',4=>'IV',1=>'I']; $r = '';
    foreach ($m as $v => $l) while ($n >= $v) { $r .= $l; $n -= $v; }
    return $r;
}
/* Premier contenu de « Notre histoire », importé une seule fois à l'installation */
function seed_story() {
    if (setting('story_seeded')) return;
    set_setting('story_seeded', '1');
    $dir = DATA_DIR . '/seed-histoire';
    if (!is_file($dir . '/seed.json') || (int)q('SELECT COUNT(*) AS n FROM club_story')->fetch()['n'] > 0) return;
    foreach (json_decode((string)file_get_contents($dir . '/seed.json'), true) ?: [] as $it) {
        $pid = save_photo($dir . '/' . $it['image'], 0); if (!$pid) continue;
        $vid = !empty($it['video']) && is_file($dir . '/' . $it['video']) ? save_video($dir . '/' . $it['video']) : null;
        q('INSERT INTO club_story (title, story_date, caption, quote, photo_id, video, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
          [$it['title'], $it['date'] ?: null, $it['text'], $it['quote'] ?? '', $pid, $vid, now()]);
    }
}

/* Premiers événements passés, importés une seule fois à l'installation */
function seed_events() {
    if (setting('events_seeded')) return;
    set_setting('events_seeded', '1');
    $dir = DATA_DIR . '/seed-evenements';
    if (!is_file($dir . '/seed.json')) return;
    foreach (json_decode((string)file_get_contents($dir . '/seed.json'), true) ?: [] as $ev) {
        if (q('SELECT id FROM club_events WHERE title = ? AND event_date = ?', [$ev['title'], $ev['date']])->fetch()) continue;
        q('INSERT INTO club_events (title, event_date, event_time, place, description, month_only, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
          [$ev['title'], $ev['date'], $ev['time'] ?? '', $ev['place'] ?? '', $ev['text'], !empty($ev['month_only']) ? 1 : 0, now()]);
        $eid = (int)db()->lastInsertId();
        foreach ($ev['photos'] ?? [] as $f) if (is_file($dir . '/' . $f)) save_photo($dir . '/' . $f, $eid);
    }
}
