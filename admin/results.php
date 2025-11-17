<?php
require_once '../config/config.php';
requireAdmin();

$db = getDB();

// Filtrlash
$search = $_GET['search'] ?? '';
$filter_profession = $_GET['filter_profession'] ?? '';

$query = "SELECT tr.*, u.full_name, u.class_number, u.school_name 
          FROM test_results tr 
          JOIN users u ON tr.user_id = u.id 
          WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (u.full_name LIKE ? OR tr.profession_name LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
}

if (!empty($filter_profession)) {
    $query .= " AND tr.profession_name = ?";
    $params[] = $filter_profession;
}

$query .= " ORDER BY tr.completed_at DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll();

// Kasblar ro'yxati
$stmt = $db->query("SELECT DISTINCT profession_name FROM test_results ORDER BY profession_name");
$professions = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="<?= Language::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Natijalar - Admin</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>Test Natijalari</h1>
            </div>
            
            <div class="admin-card">
                <form method="GET" class="form-inline">
                    <div class="form-group">
                        <input type="text" name="search" placeholder="Qidirish..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="form-group">
                        <select name="filter_profession">
                            <option value="">Barcha kasblar</option>
                            <?php foreach ($professions as $prof): ?>
                                <option value="<?= htmlspecialchars($prof) ?>" <?= $filter_profession === $prof ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($prof) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">Qidirish</button>
                </form>
            </div>
            
            <div class="admin-card">
                <h2>Natijalar (<?= count($results) ?>)</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Foydalanuvchi</th>
                            <th>Sinf</th>
                            <th>Kasb</th>
                            <th>Moslik %</th>
                            <th>Shaxsiyat</th>
                            <th>Yakunlangan</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $result): ?>
                            <tr>
                                <td><?= $result['id'] ?></td>
                                <td><?= htmlspecialchars($result['full_name']) ?></td>
                                <td><?= $result['class_number'] ?>-sinf</td>
                                <td><?= htmlspecialchars($result['profession_name']) ?></td>
                                <td>
                                    <strong><?= number_format($result['match_percentage'], 1) ?>%</strong>
                                </td>
                                <td><?= htmlspecialchars($result['personality_type']) ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($result['completed_at'])) ?></td>
                                <td>
                                    <a href="user_detail.php?id=<?= $result['user_id'] ?>" class="btn-small">Batafsil</a>
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

