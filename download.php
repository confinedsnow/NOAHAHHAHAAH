<?php
require_once __DIR__ . '/helpers.php';
$pdo = get_db();
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM attachments WHERE id = ?');
$stmt->execute([$id]);
$a = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$a) { http_response_code(404); echo 'File not found'; exit; }
$path = UPLOAD_DIR . '/' . $a['stored_name'];
if (!file_exists($path)) { http_response_code(404); echo 'File missing'; exit; }
header('Content-Type: ' . ($a['mime'] ?: 'application/octet-stream'));
header('Content-Length: ' . $a['size']);
header('Content-Disposition: attachment; filename="' . basename($a['original_name']) . '"');
readfile($path);
exit;
