<?php
require_once __DIR__ . '/helpers.php';
$pdo = get_db();
$user = current_user();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $is_public = isset($_POST['is_public']) ? 1 : 0;
    if ($content === '') $errors[] = 'Content is required.';
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO pastes (user_id, title, content, is_public) VALUES (?, ?, ?, ?)');
        $uid = $user['id'] ?? null;
        $stmt->execute([$uid, $title, $content, $is_public]);
        $paste_id = $pdo->lastInsertId();

        // handle upload(s) - allow multiple files
        if (!empty($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {
            for ($i=0;$i<count($_FILES['attachments']['name']);$i++) {
                if ($_FILES['attachments']['error'][$i] !== UPLOAD_ERR_OK) continue;
                $tmp = $_FILES['attachments']['tmp_name'][$i];
                $orig = basename($_FILES['attachments']['name'][$i]);
                $size = filesize($tmp);
                if ($size > MAX_UPLOAD_BYTES) {
                    $errors[] = "File {$orig} exceeds max size.";
                    continue;
                }
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $tmp);
                finfo_close($finfo);
                if (!in_array($mime, ALLOWED_MIME)) {
                    $errors[] = "File {$orig} has disallowed type: {$mime}.";
                    continue;
                }
                $stored = random_filename(32);
                $target = UPLOAD_DIR . '/' . $stored;
                if (!move_uploaded_file($tmp, $target)) {
                    $errors[] = "Failed to store file {$orig}.";
                    continue;
                }
                $stmt = $pdo->prepare('INSERT INTO attachments (paste_id, original_name, stored_name, mime, size) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$paste_id, $orig, $stored, $mime, $size]);
            }
        }

        header('Location: paste.php?id=' . urlencode($paste_id));
        exit;
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>New Paste — voidbin</title><link rel="stylesheet" href="styles.css"></head><body class="void">
<header><h1>voidbin — create paste</h1></header>
<main>
<?php if ($errors): ?><div class="error"><?=h(implode('<br>', $errors))?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data">
  <label>Title: <input name="title"></label><br>
  <label>Content:<br><textarea name="content" rows="12" cols="80" required></textarea></label><br>
  <label><input type="checkbox" name="is_public" checked> Public</label><br>
  <label>Attachments: <input type="file" name="attachments[]" multiple></label><br>
  <button type="submit">Create paste</button>
</form>
</main>
</body></html>
