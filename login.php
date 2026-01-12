<?php
require_once __DIR__ . '/helpers.php';
$pdo = get_db();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nick = trim($_POST['nickname'] ?? '');
    $pass = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE nickname = ?');
    $stmt->execute([$nick]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row || !password_verify($pass, $row['password_hash'])) {
        $errors[] = 'Invalid nickname or password.';
    } else {
        $_SESSION['user_id'] = $row['id'];
        header('Location: index.php'); exit;
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Login — voidbin</title><link rel="stylesheet" href="styles.css"></head><body class="void">
<header><h1>voidbin — login</h1></header>
<main>
<?php if ($errors): ?><div class="error"><?=h(implode('<br>', $errors))?></div><?php endif; ?>
<form method="post">
  <label>Nickname: <input name="nickname" required></label><br>
  <label>Password: <input name="password" type="password" required></label><br>
  <button type="submit">Login</button>
</form>
</main>
</body></html>
