<?php
require_once '../config/config.php';
requireAdmin();

$db = getDB();

// Foydalanuvchilarni olish
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'all';

$query = "SELECT * FROM users WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (full_name LIKE ? OR phone LIKE ? OR email LIKE ?)";
    $search_param = "%$search%";
    $params = [$search_param, $search_param, $search_param];
}

if ($filter === 'completed') {
    $query .= " AND test_completed = 1";
} elseif ($filter === 'pending') {
    $query .= " AND test_completed = 0";
}

$query .= " ORDER BY created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?= Language::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foydalanuvchilar - Admin</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>Foydalanuvchilar</h1>
            </div>
            
            <div class="admin-card">
                <form method="GET" class="form-inline">
                    <div class="form-group">
                        <input type="text" name="search" placeholder="Qidirish..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="form-group">
                        <select name="filter">
                            <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Barchasi</option>
                            <option value="completed" <?= $filter === 'completed' ? 'selected' : '' ?>>Test yakunlaganlar</option>
                            <option value="pending" <?= $filter === 'pending' ? 'selected' : '' ?>>Test kutilmoqda</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">Qidirish</button>
                </form>
            </div>
            
            <div class="admin-card">
                <h2>Foydalanuvchilar ro'yxati (<?= count($users) ?>)</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ism</th>
                            <th>Kirish turi</th>
                            <th>Imtihon sanasi</th>
                            <th>Test</th>
                            <th>Ro'yxatdan o'tgan</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['full_name'] ?? '') ?></td>
                                <td>
                                    <?php
                                    $login_types = [
                                        'phone' => '📱 Telefon',
                                        'telegram' => '✈️ Telegram',
                                        'google' => '🔵 Google'
                                    ];
                                    echo $login_types[$user['login_type']] ?? $user['login_type'];
                                    ?>
                                </td>
                                <td>
                                    <?php if ($user['exam_date']): ?>
                                        <?= date('d.m.Y H:i', strtotime($user['exam_date'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Belgilanmagan</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['test_completed']): ?>
                                        <span class="badge success">Yakunlangan</span>
                                    <?php else: ?>
                                        <span class="badge warning">Kutilmoqda</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <a href="user_detail.php?id=<?= $user['id'] ?>" class="btn-small">Batafsil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

