<?php
require_once '../config/config.php';
requireLogin();

$db = getDB();

// Foydalanuvchi ma'lumotlarini olish
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Test natijalarini olish
$stmt = $db->prepare("SELECT * FROM test_results WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$result = $stmt->fetch();

if (!$result) {
    redirect(BASE_URL . 'test/start.php');
}

$strengths = explode(', ', $result['strengths']);
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Natijalari - Kasb Tanlash</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/results.css">
</head>
<body>
    <div class="container">
        <div class="results-header">
            <h1>Test Natijalari</h1>
            <p>Salom, <?= htmlspecialchars($user['full_name']) ?>!</p>
        </div>
        
        <div class="results-container">
            <div class="result-card main-result">
                <h2>🎯 Sizga mos kasb</h2>
                <div class="profession-name"><?= htmlspecialchars($result['profession_name']) ?></div>
                <div class="match-percentage">
                    <div class="percentage-circle">
                        <span><?= number_format($result['match_percentage'], 1) ?>%</span>
                    </div>
                    <p>Moslik darajasi</p>
                </div>
            </div>
            
            <div class="result-card">
                <h3>📋 Tavsif</h3>
                <p><?= htmlspecialchars($result['profession_description']) ?></p>
            </div>
            
            <div class="result-card">
                <h3>🧠 Shaxsiyat turi</h3>
                <div class="personality-badge personality-<?= strtolower($result['personality_type']) ?>">
                    <?= htmlspecialchars($result['personality_type']) ?>
                </div>
            </div>
            
            <div class="result-card">
                <h3>💪 Kuchli tomonlar</h3>
                <ul class="strengths-list">
                    <?php foreach ($strengths as $strength): ?>
                        <li><?= htmlspecialchars($strength) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="result-card">
                <h3>💡 Tavsiyalar</h3>
                <p><?= htmlspecialchars($result['recommendations']) ?></p>
            </div>
            
            <div class="result-actions">
                <a href="<?= BASE_URL ?>" class="btn-primary">Bosh sahifaga qaytish</a>
                <button onclick="window.print()" class="btn-secondary">Natijani chop etish</button>
            </div>
        </div>
    </div>
</body>
</html>

