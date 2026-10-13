<?php
/* Vidéos de « Notre histoire » : réservées aux invités connectés (lecture progressive, compatible iPhone) */
require __DIR__ . '/app/bootstrap.php';
if (!current_member() && !is_admin()) { http_response_code(403); exit; }
$it = q('SELECT video FROM club_story WHERE id = ?', [(int)($_GET['v'] ?? 0)])->fetch();
$f = ($it && $it['video'] && preg_match('/^[a-f0-9]{20}\.mp4$/', $it['video'])) ? VIDEO_DIR . '/' . $it['video'] : '';
if (!$f || !is_file($f)) { http_response_code(404); exit; }
$size = filesize($f); $start = 0; $end = $size - 1;
header('Content-Type: video/mp4'); header('Accept-Ranges: bytes'); header('Cache-Control: private, max-age=604800');
if (isset($_SERVER['HTTP_RANGE']) && preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $r)) {
    if ($r[1] !== '') { $start = (int)$r[1]; if ($r[2] !== '') $end = min((int)$r[2], $size - 1); }
    elseif ($r[2] !== '') { $start = max(0, $size - (int)$r[2]); }
    if ($start > $end || $start >= $size) { http_response_code(416); header("Content-Range: bytes */$size"); exit; }
    http_response_code(206); header("Content-Range: bytes $start-$end/$size");
}
$len = $end - $start + 1; header('Content-Length: ' . $len);
$h = fopen($f, 'rb'); fseek($h, $start);
while ($len > 0 && !feof($h)) { $chunk = fread($h, min(262144, $len)); echo $chunk; flush(); $len -= strlen($chunk); }
fclose($h);
