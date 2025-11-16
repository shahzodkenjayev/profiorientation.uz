<?php
require_once '../config/config.php';
requireAdmin();

$db = getDB();

// Statistika
$stmt = $db->query("SELECT COUNT(*) as total FROM users");
$total_users = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE test_completed = 1");
$completed_tests = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM exam_dates WHERE is_active = 1");
$active_exams = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM questions");
$total_questions = $stmt->fetch()['total'];

// So'nggi foydalanuvchilar
$stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 10");
$recent_users = $stmt->fetchAll();

// So'nggi natijalar
$stmt = $db->query("SELECT tr.*, u.full_name, u.class_number 
                    FROM test_results tr 
                    JOIN users u ON tr.user_id = u.id 
                    ORDER BY tr.completed_at DESC LIMIT 10");
$recent_results = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?= Language::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>Dashboard</h1>
                <p>Salom, <?= htmlspecialchars($_SESSION['admin_username']) ?>!</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Jami foydalanuvchilar</h3>
                    <div class="stat-number"><?= $total_users ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>Test yakunlaganlar</h3>
                    <div class="stat-number"><?= $completed_tests ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>Faol imtihonlar</h3>
                    <div class="stat-number"><?= $active_exams ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>Jami savollar</h3>
                    <div class="stat-number"><?= $total_questions ?></div>
                </div>
            </div>
            
            <div class="admin-grid">
                <div class="admin-card">
                    <h2>So'nggi foydalanuvchilar</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ism</th>
                                <th>Sinf</th>
                                <th>Maktab</th>
                                <th>Test</th>
                                <th>Sana</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                                    <td><?= $user['class_number'] ?>-sinf</td>
                                    <td><?= htmlspecialchars($user['school_name']) ?></td>
                                    <td>
                                        <?php if ($user['test_completed']): ?>
                                            <span class="badge success">Yakunlangan</span>
                                        <?php else: ?>
                                            <span class="badge warning">Kutilmoqda</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="admin-card">
                    <h2>So'nggi natijalar</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ism</th>
                                <th>Kasb</th>
                                <th>Moslik</th>
                                <th>Sana</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_results as $result): ?>
                                <tr>
                                    <td><?= htmlspecialchars($result['full_name']) ?></td>
                                    <td><?= htmlspecialchars($result['profession_name']) ?></td>
                                    <td><?= number_format($result['match_percentage'], 1) ?>%</td>
                                    <td><?= date('d.m.Y', strtotime($result['completed_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

