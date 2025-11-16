<?php
require_once '../config/config.php';
requireAdmin();

$db = getDB();

$success = '';
$error = '';

// Naqt to'lovni tasdiqlash
if (isset($_GET['action']) && $_GET['action'] === 'approve' && isset($_GET['id'])) {
    $payment_id = intval($_GET['id']);
    
    try {
        $stmt = $db->prepare("UPDATE payments SET payment_status = 'completed', paid_at = NOW() WHERE id = ? AND payment_method = 'cash'");
        $stmt->execute([$payment_id]);
        $success = 'To\'lov tasdiqlandi!';
    } catch (PDOException $e) {
        $error = 'Xatolik: ' . $e->getMessage();
    }
}

// To'lovni bekor qilish
if (isset($_GET['action']) && $_GET['action'] === 'cancel' && isset($_GET['id'])) {
    $payment_id = intval($_GET['id']);
    
    try {
        $stmt = $db->prepare("UPDATE payments SET payment_status = 'cancelled' WHERE id = ?");
        $stmt->execute([$payment_id]);
        $success = 'To\'lov bekor qilindi!';
    } catch (PDOException $e) {
        $error = 'Xatolik: ' . $e->getMessage();
    }
}

// Filtrlash
$filter_status = $_GET['filter_status'] ?? 'all';
$filter_method = $_GET['filter_method'] ?? 'all';
$search = $_GET['search'] ?? '';

$query = "SELECT p.*, u.full_name, u.phone, u.email 
          FROM payments p 
          JOIN users u ON p.user_id = u.id 
          WHERE 1=1";
$params = [];

if ($filter_status !== 'all') {
    $query .= " AND p.payment_status = ?";
    $params[] = $filter_status;
}

if ($filter_method !== 'all') {
    $query .= " AND p.payment_method = ?";
    $params[] = $filter_method;
}

if (!empty($search)) {
    $query .= " AND (u.full_name LIKE ? OR u.phone LIKE ? OR p.order_id LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$query .= " ORDER BY p.created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$payments = $stmt->fetchAll();

// Statistika
$stmt = $db->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN payment_status = 'completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN payment_status = 'completed' THEN amount ELSE 0 END) as total_amount
    FROM payments");
$stats = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="<?= Language::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To'lovlar - Admin</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>To'lovlar</h1>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <!-- Statistika -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Jami to'lovlar</h3>
                    <div class="stat-number"><?= $stats['total'] ?></div>
                </div>
                <div class="stat-card">
                    <h3>Tasdiqlangan</h3>
                    <div class="stat-number"><?= $stats['completed'] ?></div>
                </div>
                <div class="stat-card">
                    <h3>Kutilmoqda</h3>
                    <div class="stat-number"><?= $stats['pending'] ?></div>
                </div>
                <div class="stat-card">
                    <h3>Jami summa</h3>
                    <div class="stat-number"><?= number_format($stats['total_amount'], 0, '.', ' ') ?> so'm</div>
                </div>
            </div>
            
            <!-- Filtrlar -->
            <div class="admin-card">
                <form method="GET" class="form-inline">
                    <div class="form-group">
                        <input type="text" name="search" placeholder="Qidirish..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="form-group">
                        <select name="filter_status">
                            <option value="all" <?= $filter_status === 'all' ? 'selected' : '' ?>>Barcha holatlar</option>
                            <option value="pending" <?= $filter_status === 'pending' ? 'selected' : '' ?>>Kutilmoqda</option>
                            <option value="completed" <?= $filter_status === 'completed' ? 'selected' : '' ?>>Tasdiqlangan</option>
                            <option value="failed" <?= $filter_status === 'failed' ? 'selected' : '' ?>>Muvaffaqiyatsiz</option>
                            <option value="cancelled" <?= $filter_status === 'cancelled' ? 'selected' : '' ?>>Bekor qilingan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="filter_method">
                            <option value="all" <?= $filter_method === 'all' ? 'selected' : '' ?>>Barcha usullar</option>
                            <option value="payme" <?= $filter_method === 'payme' ? 'selected' : '' ?>>Payme</option>
                            <option value="click" <?= $filter_method === 'click' ? 'selected' : '' ?>>Click</option>
                            <option value="cash" <?= $filter_method === 'cash' ? 'selected' : '' ?>>Naqt</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">Qidirish</button>
                </form>
            </div>
            
            <!-- To'lovlar ro'yxati -->
            <div class="admin-card">
                <h2>To'lovlar ro'yxati (<?= count($payments) ?>)</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Foydalanuvchi</th>
                            <th>Order ID</th>
                            <th>Summa</th>
                            <th>Usul</th>
                            <th>Holat</th>
                            <th>Sana</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?= $payment['id'] ?></td>
                                <td>
                                    <div><?= htmlspecialchars($payment['full_name']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($payment['phone'] ?? '') ?></small>
                                </td>
                                <td><code><?= htmlspecialchars($payment['order_id']) ?></code></td>
                                <td><strong><?= number_format($payment['amount'], 0, '.', ' ') ?> so'm</strong></td>
                                <td>
                                    <?php
                                    $methods = [
                                        'payme' => '💳 Payme',
                                        'click' => '📱 Click',
                                        'cash' => '💵 Naqt'
                                    ];
                                    echo $methods[$payment['payment_method']] ?? $payment['payment_method'];
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $statuses = [
                                        'pending' => ['badge warning', 'Kutilmoqda'],
                                        'completed' => ['badge success', 'Tasdiqlangan'],
                                        'failed' => ['badge error', 'Muvaffaqiyatsiz'],
                                        'cancelled' => ['badge error', 'Bekor qilingan']
                                    ];
                                    $status = $statuses[$payment['payment_status']] ?? ['badge', $payment['payment_status']];
                                    ?>
                                    <span class="<?= $status[0] ?>"><?= $status[1] ?></span>
                                </td>
                                <td><?= date('d.m.Y H:i', strtotime($payment['created_at'])) ?></td>
                                <td>
                                    <?php if ($payment['payment_method'] === 'cash' && $payment['payment_status'] === 'pending'): ?>
                                        <a href="?action=approve&id=<?= $payment['id'] ?>" 
                                           class="btn-small btn-success"
                                           onclick="return confirm('To\'lovni tasdiqlaysizmi?')">Tasdiqlash</a>
                                    <?php endif; ?>
                                    <?php if ($payment['payment_status'] === 'pending'): ?>
                                        <a href="?action=cancel&id=<?= $payment['id'] ?>" 
                                           class="btn-small btn-danger"
                                           onclick="return confirm('To\'lovni bekor qilishni tasdiqlaysizmi?')">Bekor qilish</a>
                                    <?php endif; ?>
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

