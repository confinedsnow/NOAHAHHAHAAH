<?php
require_once __DIR__ . '/config.php';
if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
$pdo = new PDO('sqlite:' . DB_FILE);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA foreign_keys = ON');

// users: nickname unique, password hash
$pdo->exec("
CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY,
  nickname TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
");

// pastes: user_id nullable (anonymous), content, title, is_public
$pdo->exec("
CREATE TABLE IF NOT EXISTS pastes (
  id INTEGER PRIMARY KEY,
  user_id INTEGER,
  title TEXT,
  content TEXT NOT NULL,
  is_public INTEGER DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  expire_at DATETIME,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
);
");

// attachments
$pdo->exec("
CREATE TABLE IF NOT EXISTS attachments (
  id INTEGER PRIMARY KEY,
  paste_id INTEGER NOT NULL,
  original_name TEXT NOT NULL,
  stored_name TEXT NOT NULL,
  mime TEXT,
  size INTEGER,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(paste_id) REFERENCES pastes(id) ON DELETE CASCADE
);
");

echo \"Database initialized at: \" . DB_FILE . \"\\n\";
