<?php
require_once __DIR__ . '/helpers.php';
$pdo = get_db();
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nick = trim($_POST['nickname'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($nick === '' || $pass === '') {
        $errors[] = 'Nickname and password are required.';
    } else {
        // nickname unique
        $stmt = $pdo->prepare('SELECT id FROM users WHERE nickname = ?');
        $stmt->execute([$nick]);
        if ($stmt->fetch()) {
            $errors[] = 'Nickname already taken. Choose a different one.';
        } else {
            // SECURITY NOTE: never check password uniqueness across accounts (it reveals whether a password exists).
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (nickname, password_hash) VALUES (?, ?)');
            $stmt->execute([$nick, $hash]);
            $user_id = $pdo->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $success = true;
            header('Location: index.php'); exit;
        }
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Register — voidbin</title><link rel="stylesheet" href="styles.css"></head><body class="void">
<header><h1>voidbin — register</h1></header>
<main>
<?php if ($errors): ?>
  <div class="error"><?=h(implode('<br>', $errors))?></div>
<?php endif; ?>
<form method="post">
  <label>Nickname: <input name="nickname" required></label><br>
  <label>Password: <input name="password" type="password" required></label><br>
  <button type="submit">Create account</button>
</form>
</main>
</body></html>
