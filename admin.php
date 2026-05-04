<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $stmt = db()->prepare('DELETE FROM news WHERE id = ?');
    $stmt->execute([(int)$_POST['delete_id']]);
    header('Location: admin.php?deleted=1');
    exit;
}

$stmt     = db()->query('SELECT id, title, created_at FROM news ORDER BY created_at DESC');
$newsList = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Адмінка — Новини</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="nav">
    <a href="index.php">Головна</a>
    <a href="admin.php">Адмінка</a>
</nav>
<div class="container">
    <h1>Управління новинами</h1>

    <?php if (isset($_GET['deleted'])): ?>
        <p class="alert-success">Новину видалено.</p>
    <?php endif; ?>
    <?php if (isset($_GET['saved'])): ?>
        <p class="alert-success">Новину збережено.</p>
    <?php endif; ?>

    <p style="margin-bottom:16px">
        <a href="admin_form.php" class="btn btn-primary">+ Додати новину</a>
    </p>

    <?php if (empty($newsList)): ?>
        <p>Новин поки немає.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width:50px">ID</th>
                    <th>Заголовок</th>
                    <th style="width:160px">Дата</th>
                    <th style="width:170px">Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($newsList as $item): ?>
                    <tr>
                        <td><?= $item['id'] ?></td>
                        <td><a href="news.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['title']) ?></a></td>
                        <td><?= date('d.m.Y H:i', strtotime($item['created_at'])) ?></td>
                        <td>
                            <a href="admin_form.php?id=<?= $item['id'] ?>" class="btn btn-secondary">Редагувати</a>
                            <form method="post" action="admin.php" style="display:inline"
                                  onsubmit="return confirm('Видалити новину?')">
                                <input type="hidden" name="delete_id" value="<?= $item['id'] ?>">
                                <button type="submit" class="btn btn-danger">Видалити</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
