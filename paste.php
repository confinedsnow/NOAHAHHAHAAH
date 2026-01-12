<?php
require_once __DIR__ . '/helpers.php';
$pdo = get_db();
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT p.*, u.nickname FROM pastes p LEFT JOIN users u ON p.user_id = u.id WHERE p.id = ?');
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$p) { http_response_code(404); echo 'Paste not found'; exit; }

$att = $pdo->prepare('SELECT * FROM attachments WHERE paste_id = ?');
$att->execute([$id]);
$attachments = $att->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html><head><meta charset="utf-8"><title><?=h($p['title'] ?: 'Paste')?> — voidbin</title><link rel="stylesheet" href="styles.css"></head><body class="void">
<header><h1><?=h($p['title'] ?: 'Untitled')?></h1></header>
<main>
  <div class="meta">by <?=h($p['nickname'] ?: 'anonymous')?> — <?=h($p['created_at'])?></div>
  <pre class="paste"><?=h($p['content'])?></pre>

  <?php if ($attachments): ?>
    <section>
      <h3>Attachments</h3>
      <ul>
      <?php foreach ($attachments as $a): ?>
        <li><a href="download.php?id=<?=urlencode($a['id'])?>"><?=h($a['original_name'])?></a> (<?=h($a['mime'])?>, <?=h($a['size'])?> bytes)</li>
      <?php endforeach; ?>
      </ul>
    </section>
  <?php endif; ?>

</main>
</body></html>
