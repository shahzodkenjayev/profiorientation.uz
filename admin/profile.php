<?php
require_once '../config/config.php';
requireAdmin();

$db = getDB();
$admin_id = $_SESSION['admin_id'];

// Admin ma'lumotlarini olish
$stmt = $db->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();

if (!$admin) {
    redirect(BASE_URL . 'admin/logout.php');
}

$success = '';
$error = '';

// Forma yuborilganda
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    try {
        // Ma'lumotlarni yangilash
        if (!empty($full_name) || !empty($email)) {
            $update_fields = [];
            $params = [];
            
            if (!empty($full_name)) {
                $update_fields[] = "full_name = ?";
                $params[] = $full_name;
            }
            
            if (!empty($email)) {
                $update_fields[] = "email = ?";
                $params[] = $email;
            }
            
            if (!empty($update_fields)) {
                $params[] = $admin_id;
                $sql = "UPDATE admins SET " . implode(", ", $update_fields) . " WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                
                // Session yangilash
                $_SESSION['admin_username'] = $admin['username'];
                if (!empty($full_name)) {
                    $admin['full_name'] = $full_name;
                }
                if (!empty($email)) {
                    $admin['email'] = $email;
                }
                
                $success = 'Ma\'lumotlar muvaffaqiyatli yangilandi!';
            }
        }
        
        // Parolni yangilash
        if (!empty($current_password) && !empty($new_password)) {
            if (password_verify($current_password, $admin['password'])) {
                if ($new_password === $confirm_password) {
                    if (strlen($new_password) >= 6) {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("UPDATE admins SET password = ? WHERE id = ?");
                        $stmt->execute([$hashed_password, $admin_id]);
                        $success = 'Parol muvaffaqiyatli yangilandi!';
                    } else {
                        $error = 'Parol kamida 6 belgidan iborat bo\'lishi kerak!';
                    }
                } else {
                    $error = 'Yangi parollar mos kelmayapti!';
                }
            } else {
                $error = 'Joriy parol noto\'g\'ri!';
            }
        }
        
    } catch (PDOException $e) {
        $error = 'Xatolik yuz berdi: ' . $e->getMessage();
    }
    
    // Ma'lumotlarni qayta yuklash
    $stmt = $db->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="<?= Language::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('admin.profile_title', 'Profil') ?> - Admin</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1><?= __('admin.profile_title', 'Profil') ?></h1>
                <a href="dashboard.php" class="btn-secondary">← <?= __('admin.back', 'Orqaga') ?></a>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <div class="admin-card">
                <h2><?= __('admin.profile_info', 'Profil ma\'lumotlari') ?></h2>
                
                <form method="POST" class="admin-form">
                    <div class="form-group">
                        <label><?= __('admin.username', 'Foydalanuvchi nomi') ?></label>
                        <input type="text" value="<?= htmlspecialchars($admin['username']) ?>" disabled class="form-control">
                        <small class="form-text"><?= __('admin.username_note', 'Foydalanuvchi nomini o\'zgartirib bo\'lmaydi') ?></small>
                    </div>
                    
                    <div class="form-group">
                        <label><?= __('admin.full_name', 'To\'liq ism') ?></label>
                        <input type="text" name="full_name" value="<?= htmlspecialchars($admin['full_name'] ?? '') ?>" class="form-control" placeholder="<?= __('admin.enter_full_name', 'To\'liq ismingizni kiriting') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><?= __('admin.email', 'Email') ?></label>
                        <input type="email" name="email" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" class="form-control" placeholder="<?= __('admin.enter_email', 'Email manzilingizni kiriting') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><?= __('admin.role', 'Rol') ?></label>
                        <input type="text" value="<?= htmlspecialchars($admin['role'] === 'super_admin' ? 'Super Admin' : 'Admin') ?>" disabled class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label><?= __('admin.created_at', 'Yaratilgan sana') ?></label>
                        <input type="text" value="<?= date('d.m.Y H:i', strtotime($admin['created_at'])) ?>" disabled class="form-control">
                    </div>
                    
                    <?php if ($admin['last_login']): ?>
                    <div class="form-group">
                        <label><?= __('admin.last_login', 'Oxirgi kirish') ?></label>
                        <input type="text" value="<?= date('d.m.Y H:i', strtotime($admin['last_login'])) ?>" disabled class="form-control">
                    </div>
                    <?php endif; ?>
                    
                    <button type="submit" class="btn-primary"><?= __('admin.save_changes', 'O\'zgarishlarni saqlash') ?></button>
                </form>
            </div>
            
            <div class="admin-card">
                <h2><?= __('admin.change_password', 'Parolni o\'zgartirish') ?></h2>
                
                <form method="POST" class="admin-form">
                    <div class="form-group">
                        <label><?= __('admin.current_password', 'Joriy parol') ?></label>
                        <input type="password" name="current_password" class="form-control" placeholder="<?= __('admin.enter_current_password', 'Joriy parolingizni kiriting') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><?= __('admin.new_password', 'Yangi parol') ?></label>
                        <input type="password" name="new_password" class="form-control" placeholder="<?= __('admin.enter_new_password', 'Yangi parolingizni kiriting') ?>">
                        <small class="form-text"><?= __('admin.password_note', 'Parol kamida 6 belgidan iborat bo\'lishi kerak') ?></small>
                    </div>
                    
                    <div class="form-group">
                        <label><?= __('admin.confirm_password', 'Parolni tasdiqlash') ?></label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="<?= __('admin.confirm_new_password', 'Yangi parolni qayta kiriting') ?>">
                    </div>
                    
                    <button type="submit" class="btn-primary"><?= __('admin.change_password', 'Parolni o\'zgartirish') ?></button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

