<?php
require_once __DIR__ . '/db.php';

$page    = max(1, (int)($_GET['page']      ?? 1));
$perPage = 5;
$offset  = ($page - 1) * $perPage;
$search   = trim($_GET['search']    ?? '');
$dateFrom = trim($_GET['date_from'] ?? '');
$dateTo   = trim($_GET['date_to']   ?? '');

$where  = [];
$params = [];

if ($search !== '') {
    $where[]  = '(title LIKE ? OR short_description LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
if ($dateFrom !== '') {
    $where[]  = 'DATE(created_at) >= ?';
    $params[] = $dateFrom;
}
if ($dateTo !== '') {
    $where[]  = 'DATE(created_at) <= ?';
    $params[] = $dateTo;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmtCount = db()->prepare("SELECT COUNT(*) FROM news $whereSQL");
$stmtCount->execute($params);
$total      = (int)$stmtCount->fetchColumn();
$totalPages = (int)ceil($total / $perPage);

$stmtNews = db()->prepare(
    "SELECT id, title, short_description, created_at FROM news $whereSQL ORDER BY created_at DESC LIMIT ? OFFSET ?"
);
$stmtNews->execute(array_merge($params, [$perPage, $offset]));
$newsList = $stmtNews->fetchAll();

function buildQuery(array $overrides = []): string {
    $base = array_filter(
        array_merge($_GET, $overrides),
        fn($v) => $v !== '' && $v !== null
    );
    unset($base['page']);
    $qs = http_build_query($base);
    return 'index.php' . ($qs ? '?' . $qs . '&' : '?');
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Новини</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="nav">
    <a href="index.php">Головна</a>
    <a href="admin.php">Адмінка</a>
</nav>
<div class="container">
    <h1>Новини</h1>

    <form class="filters" method="get" action="index.php">
        <div>
            <label for="search">Пошук</label>
            <input type="text" id="search" name="search" placeholder="Ключові слова..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div>
            <label for="date_from">Від</label>
            <input type="date" id="date_from" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>">
        </div>
        <div>
            <label for="date_to">До</label>
            <input type="date" id="date_to" name="date_to" value="<?= htmlspecialchars($dateTo) ?>">
        </div>
        <div>
            <label>&nbsp;</label>
            <button type="submit">Знайти</button>
        </div>
        <div>
            <label>&nbsp;</label>
            <a class="reset" href="index.php">Скинути</a>
        </div>
    </form>

    <?php if (empty($newsList)): ?>
        <p>Новин не знайдено.</p>
    <?php else: ?>
        <ul class="news-list">
            <?php foreach ($newsList as $item): ?>
                <li class="news-item">
                    <div class="meta"><?= date('d.m.Y H:i', strtotime($item['created_at'])) ?></div>
                    <h2><?= htmlspecialchars($item['title']) ?></h2>
                    <p><?= htmlspecialchars($item['short_description']) ?></p>
                    <a href="news.php?id=<?= $item['id'] ?>">Детальніше &rarr;</a>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <?php if ($p === $page): ?>
                        <span class="current"><?= $p ?></span>
                    <?php else: ?>
                        <a href="<?= buildQuery() ?>page=<?= $p ?>"><?= $p ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
