<?php
require_once '../config/config.php';
requireAdmin();

$db = getDB();

$user_id = intval($_GET['id'] ?? 0);

if ($user_id <= 0) {
    redirect(BASE_URL . 'admin/users.php');
}

// Foydalanuvchi ma'lumotlarini olish
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    redirect(BASE_URL . 'admin/users.php');
}

// Test natijalarini olish
$stmt = $db->prepare("SELECT * FROM test_results WHERE user_id = ?");
$stmt->execute([$user_id]);
$result = $stmt->fetch();

// Javoblarni olish
$stmt = $db->prepare("SELECT ua.*, q.question_text, q.category, ao.option_text, ao.score 
                     FROM user_answers ua 
                     JOIN questions q ON ua.question_id = q.id 
                     LEFT JOIN answer_options ao ON ua.answer_option_id = ao.id 
                     WHERE ua.user_id = ? 
                     ORDER BY q.category, q.order_number");
$stmt->execute([$user_id]);
$answers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?= Language::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foydalanuvchi Batafsil - Admin</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>Foydalanuvchi Batafsil</h1>
                <a href="users.php" class="btn-secondary">← Orqaga</a>
            </div>
            
            <div class="admin-card">
                <h2>Asosiy ma'lumotlar</h2>
                <table class="data-table">
                    <tr>
                        <th>ID</th>
                        <td><?= $user['id'] ?></td>
                    </tr>
                    <tr>
                        <th>To'liq ism</th>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Sinf</th>
                        <td><?= $user['class_number'] ?>-sinf</td>
                    </tr>
                    <tr>
                        <th>Maktab</th>
                        <td><?= htmlspecialchars($user['school_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Kirish turi</th>
                        <td>
                            <?php
                            $login_types = [
                                'phone' => '📱 Telefon: ' . htmlspecialchars($user['phone'] ?? ''),
                                'telegram' => '✈️ Telegram: ' . htmlspecialchars($user['telegram_id'] ?? ''),
                                'google' => '🔵 Google: ' . htmlspecialchars($user['email'] ?? '')
                            ];
                            echo $login_types[$user['login_type']] ?? $user['login_type'];
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Imtihon sanasi</th>
                        <td>
                            <?php if ($user['exam_date']): ?>
                                <?= date('d.m.Y H:i', strtotime($user['exam_date'])) ?>
                            <?php else: ?>
                                <span class="text-muted">Belgilanmagan</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Test holati</th>
                        <td>
                            <?php if ($user['test_completed']): ?>
                                <span class="badge success">Yakunlangan</span>
                            <?php else: ?>
                                <span class="badge warning">Kutilmoqda</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Ro'yxatdan o'tgan</th>
                        <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                    </tr>
                </table>
            </div>
            
            <?php if ($result): ?>
                <div class="admin-card">
                    <h2>Test natijalari</h2>
                    <table class="data-table">
                        <tr>
                            <th>Kasb</th>
                            <td><?= htmlspecialchars($result['profession_name']) ?></td>
                        </tr>
                        <tr>
                            <th>Moslik %</th>
                            <td><strong><?= number_format($result['match_percentage'], 1) ?>%</strong></td>
                        </tr>
                        <tr>
                            <th>Shaxsiyat turi</th>
                            <td><?= htmlspecialchars($result['personality_type']) ?></td>
                        </tr>
                        <tr>
                            <th>Kuchli tomonlar</th>
                            <td><?= htmlspecialchars($result['strengths']) ?></td>
                        </tr>
                        <tr>
                            <th>Tavsiyalar</th>
                            <td><?= htmlspecialchars($result['recommendations']) ?></td>
                        </tr>
                        <tr>
                            <th>Yakunlangan</th>
                            <td><?= date('d.m.Y H:i', strtotime($result['completed_at'])) ?></td>
                        </tr>
                    </table>
                </div>
            <?php endif; ?>
            
            <?php if (count($answers) > 0): ?>
                <div class="admin-card">
                    <h2>Javoblar (<?= count($answers) ?>)</h2>
                    <div style="max-height: 500px; overflow-y: auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Kategoriya</th>
                                    <th>Savol</th>
                                    <th>Javob</th>
                                    <th>Ball</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($answers as $answer): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($answer['category']) ?></td>
                                        <td><?= htmlspecialchars(mb_substr($answer['question_text'], 0, 50)) ?>...</td>
                                        <td><?= htmlspecialchars($answer['option_text'] ?? '') ?></td>
                                        <td><?= $answer['score'] ?? 0 ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

