<?php
/* Les photos ne sont servies qu'aux invités connectés (et à l'administratrice) */
require __DIR__ . '/app/bootstrap.php';
if (!current_member() && !is_admin()) { http_response_code(403); exit; }
$p = q('SELECT * FROM club_photos WHERE id = ?', [(int)($_GET['id'] ?? 0)])->fetch();
if (!$p) { http_response_code(404); exit; }
$f = PHOTO_DIR . '/' . $p['file'] . (($_GET['t'] ?? '') === 'full' ? '.jpg' : '-m.jpg');
if (!is_file($f)) { http_response_code(404); exit; }
header('Content-Type: image/jpeg');
header('Content-Length: ' . filesize($f));
header('Cache-Control: private, max-age=604800');
readfile($f);
