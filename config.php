<?php
// Basic configuration
define('DATA_DIR', __DIR__ . '/data');
define('DB_FILE', DATA_DIR . '/voidbin.db');
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('MAX_UPLOAD_BYTES', 10 * 1024 * 1024); // 10 MB per file
define('ALLOWED_MIME', [
    'text/plain',
    'text/x-php',
    'application/x-php',
    'text/html',
    'text/markdown',
    'text/x-markdown',
    'application/pdf',
    'image/png',
    'image/jpeg',
    'image/gif',
    'application/zip',
]); // adjust as you like
ini_set('session.cookie_httponly', 1);
session_start();
