<?php
require_once __DIR__ . '/db.php';

$id     = (int)($_GET['id'] ?? 0);
$errors = [];
$news   = ['title' => '', 'short_description' => '', 'content' => ''];

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM news WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        header('Location: admin.php');
        exit;
    }
    $news = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title             = trim($_POST['title']             ?? '');
    $short_description = trim($_POST['short_description'] ?? '');
    $content           = trim($_POST['content']           ?? '');

    if ($title === '') {
        $errors['title'] = "Заголовок обов'язковий.";
    } elseif (mb_strlen($title) > 255) {
        $errors['title'] = 'Заголовок не може бути довшим за 255 символів.';
    }
    if ($short_description === '') {
        $errors['short_description'] = "Короткий опис обов'язковий.";
    }
    if ($content === '') {
        $errors['content'] = "Текст новини обов'язковий.";
    }

    if (empty($errors)) {
        if ($id > 0) {
            $stmt = db()->prepare(
                'UPDATE news SET title = ?, short_description = ?, content = ? WHERE id = ?'
            );
            $stmt->execute([$title, $short_description, $content, $id]);
        } else {
            $stmt = db()->prepare(
                'INSERT INTO news (title, short_description, content) VALUES (?, ?, ?)'
            );
            $stmt->execute([$title, $short_description, $content]);
        }
        header('Location: admin.php?saved=1');
        exit;
    }

    $news = ['title' => $title, 'short_description' => $short_description, 'content' => $content];
}

$pageTitle = $id > 0 ? 'Редагувати новину' : 'Нова новина';
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="nav">
    <a href="index.php">Головна</a>
    <a href="admin.php">Адмінка</a>
</nav>
<div class="container">
    <h1><?= $pageTitle ?></h1>
    <p style="margin-bottom:16px"><a href="admin.php">&larr; До списку</a></p>

    <form method="post" action="admin_form.php<?= $id > 0 ? '?id=' . $id : '' ?>" style="max-width:600px">

        <div class="form-group">
            <label for="title">Заголовок *</label>
            <input type="text" id="title" name="title" maxlength="255"
                   value="<?= htmlspecialchars($news['title']) ?>">
            <?php if (isset($errors['title'])): ?>
                <div class="error"><?= htmlspecialchars($errors['title']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="short_description">Короткий опис *</label>
            <textarea id="short_description" name="short_description"><?= htmlspecialchars($news['short_description']) ?></textarea>
            <?php if (isset($errors['short_description'])): ?>
                <div class="error"><?= htmlspecialchars($errors['short_description']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="content">Текст новини *</label>
            <textarea id="content" name="content" style="height:200px"><?= htmlspecialchars($news['content']) ?></textarea>
            <?php if (isset($errors['content'])): ?>
                <div class="error"><?= htmlspecialchars($errors['content']) ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Зберегти</button>
        <a href="admin.php" class="btn btn-secondary" style="margin-left:8px">Скасувати</a>
    </form>
</div>
</body>
</html>
