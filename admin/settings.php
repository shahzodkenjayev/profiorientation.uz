<?php
require_once '../config/config.php';
requireAdmin();

$db = getDB();

$success = '';
$error = '';

// Sozlamalarni yangilash
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'setting_') === 0) {
            $setting_key = str_replace('setting_', '', $key);
            $stmt = $db->prepare("UPDATE admin_settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([sanitize($value), $setting_key]);
        }
    }
    $success = 'Sozlamalar yangilandi!';
}

// Sozlamalarni olish
$stmt = $db->query("SELECT setting_key, setting_value FROM admin_settings");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="<?= Language::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sozlamalar - Admin</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>Sozlamalar</h1>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <div class="admin-card">
                <h2>Tizim sozlamalari</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Default imtihon sanasi</label>
                        <input type="datetime-local" 
                               name="setting_default_exam_date" 
                               value="<?= htmlspecialchars($settings['default_exam_date'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Test davomiyligi (daqiqa)</label>
                        <input type="number" 
                               name="setting_test_duration" 
                               value="<?= htmlspecialchars($settings['test_duration'] ?? '60') ?>"
                               min="1">
                    </div>
                    
                    <div class="form-group">
                        <label>Minimal savollar soni</label>
                        <input type="number" 
                               name="setting_min_questions" 
                               value="<?= htmlspecialchars($settings['min_questions'] ?? '30') ?>"
                               min="1">
                    </div>
                    
                    <button type="submit" class="btn-primary">Saqlash</button>
                </form>
            </div>
            
            <div class="admin-card">
                <h2>API sozlamalari</h2>
                <p class="text-muted">Bu sozlamalar <code>config/config.php</code> faylida o'zgartiriladi.</p>
                <ul>
                    <li>Google OAuth Client ID</li>
                    <li>Telegram Bot Token</li>
                    <li>SMS API Key</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>

