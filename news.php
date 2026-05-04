<?php
require_once __DIR__ . '/db.php';

$id   = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM news WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$news = $stmt->fetch();

if (!$news) {
    http_response_code(404);
?>
<!DOCTYPE html>
<html lang="uk">
<head><meta charset="UTF-8"><title>Не знайдено</title><link rel="stylesheet" href="style.css"></head>
<body>
<nav class="nav"><a href="index.php">Головна</a><a href="admin.php">Адмінка</a></nav>
<div class="container"><p>Новину не знайдено. <a href="index.php">На головну</a></p></div>
</body>
</html>
<?php
    exit;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($news['title']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="nav">
    <a href="index.php">Головна</a>
    <a href="admin.php">Адмінка</a>
</nav>
<div class="container">
    <p style="margin-bottom:16px"><a href="index.php">&larr; До списку новин</a></p>
    <div class="news-detail">
        <h1><?= htmlspecialchars($news['title']) ?></h1>
        <div class="meta"><?= date('d.m.Y H:i', strtotime($news['created_at'])) ?></div>
        <div class="body"><?= htmlspecialchars($news['content']) ?></div>
    </div>
</div>
</body>
</html>
