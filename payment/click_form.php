<?php
require_once '../config/config.php';

if (!isset($_SESSION['click_data'])) {
    redirect(BASE_URL . 'payment/index.php');
}

$click_data = $_SESSION['click_data'];
unset($_SESSION['click_data']);
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Click to'lov</title>
</head>
<body>
    <div style="text-align: center; padding: 50px;">
        <h2>Click to'lov sahifasiga yo'naltirilmoqda...</h2>
        <p>Agar avtomatik yo'naltirilmasangiz, quyidagi tugmani bosing.</p>
        
        <form id="clickForm" method="POST" action="<?= $click_data['url'] ?>">
            <?php foreach ($click_data['params'] as $key => $value): ?>
                <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
            <?php endforeach; ?>
            <button type="submit" style="padding: 15px 30px; font-size: 16px; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer;">
                To'lovni davom ettirish
            </button>
        </form>
    </div>
    
    <script>
        // Avtomatik submit
        document.getElementById('clickForm').submit();
    </script>
</body>
</html>

