<?php
require_once __DIR__ . '/helpers.php';
$user = current_user();
$pdo = get_db();

// fetch recent public pastes
$stmt = $pdo->query('SELECT p.id, p.title, p.created_at, u.nickname 
                     FROM pastes p LEFT JOIN users u ON p.user_id = u.id
                     WHERE p.is_public = 1
                     ORDER BY p.created_at DESC LIMIT 25');
$recent = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>voidbin — recent pastes</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body class="void">
  <header>
    <h1>voidbin</h1>
    <nav>
      <?php if ($user): ?>
        Hello, <?=h($user['nickname'])?> | <a href="create_paste.php">New Paste</a> | <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="register.php">Register</a> | <a href="login.php">Login</a> | <a href="create_paste.php">New Paste</a>
      <?php endif; ?>
    </nav>
  </header>

  <main>
    <section>
      <h2>Recent public pastes</h2>
      <ul class="paste-list">
        <?php foreach ($recent as $p): ?>
        <li>
          <a href="paste.php?id=<?=urlencode($p['id'])?>"><?=h($p['title'] ?: '[untitled]')?></a>
          <small>by <?=h($p['nickname'] ?: 'anonymous')?> — <?=h($p['created_at'])?></small>
        </li>
        <?php endforeach; ?>
      </ul>
    </section>
  </main>
  <footer>void-ish • simple paste + attachments</footer>
</body>
</html>
