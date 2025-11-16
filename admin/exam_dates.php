<?php
require_once '../config/config.php';
requireAdmin();

$db = getDB();

$success = '';
$error = '';

// Yangi imtihon kuni qo'shish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_exam'])) {
    $exam_date = sanitize($_POST['exam_date'] ?? '');
    $max_participants = intval($_POST['max_participants'] ?? 100);
    
    if (empty($exam_date)) {
        $error = 'Imtihon sanasini kiriting!';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO exam_dates (exam_date, max_participants) VALUES (?, ?)");
            $stmt->execute([$exam_date, $max_participants]);
            $success = 'Imtihon kuni muvaffaqiyatli qo\'shildi!';
        } catch (PDOException $e) {
            $error = 'Xatolik: ' . $e->getMessage();
        }
    }
}

// Imtihon kuni o'chirish yoki o'zgartirish
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id'] ?? 0);
    
    if ($action === 'delete' && $id > 0) {
        $stmt = $db->prepare("DELETE FROM exam_dates WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Imtihon kuni o\'chirildi!';
    } elseif ($action === 'toggle' && $id > 0) {
        $stmt = $db->prepare("UPDATE exam_dates SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Holat o\'zgartirildi!';
    }
}

// Imtihon kunlarini olish
$stmt = $db->query("SELECT * FROM exam_dates ORDER BY exam_date DESC");
$exam_dates = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?= Language::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imtihon Kunlari - Admin</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>Imtihon Kunlari</h1>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <div class="admin-card">
                <h2>Yangi imtihon kuni qo'shish</h2>
                <form method="POST" class="form-inline">
                    <input type="hidden" name="add_exam" value="1">
                    <div class="form-group">
                        <label>Imtihon sanasi va vaqti</label>
                        <input type="datetime-local" name="exam_date" required>
                    </div>
                    <div class="form-group">
                        <label>Maksimal ishtirokchilar</label>
                        <input type="number" name="max_participants" value="100" min="1" required>
                    </div>
                    <button type="submit" class="btn-primary">Qo'shish</button>
                </form>
            </div>
            
            <div class="admin-card">
                <h2>Mavjud imtihon kunlari</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Sana va vaqt</th>
                            <th>Ishtirokchilar</th>
                            <th>Holat</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exam_dates as $exam): ?>
                            <tr>
                                <td><?= $exam['id'] ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($exam['exam_date'])) ?></td>
                                <td><?= $exam['current_participants'] ?> / <?= $exam['max_participants'] ?></td>
                                <td>
                                    <?php if ($exam['is_active']): ?>
                                        <span class="badge success">Faol</span>
                                    <?php else: ?>
                                        <span class="badge error">Nofaol</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?action=toggle&id=<?= $exam['id'] ?>" class="btn-small">Holat</a>
                                    <a href="?action=delete&id=<?= $exam['id'] ?>" 
                                       class="btn-small btn-danger" 
                                       onclick="return confirm('O\'chirishni tasdiqlaysizmi?')">O'chirish</a>
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

