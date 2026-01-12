<?php
require_once __DIR__ . '/config.php';

function get_db() {
    static $pdo = null;
    if ($pdo) return $pdo;
    if (!file_exists(DB_FILE)) {
        // Create DB automatically if missing
        require_once __DIR__ . '/db_init.php';
    }
    $pdo = new PDO('sqlite:' . DB_FILE);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON');
    return $pdo;
}

function current_user() {
    if (!empty($_SESSION['user_id'])) {
        $pdo = get_db();
        $stmt = $pdo->prepare('SELECT id, nickname FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return null;
}

function h($s) {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function random_filename($len = 24) {
    return bin2hex(random_bytes($len/2));
}

function ensure_dirs() {
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
    if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
}
ensure_dirs();
