<?php
require_once '../config/config.php';

if (!isset($_SESSION['payme_data'])) {
    redirect(BASE_URL . 'payment/index.php');
}

$payme_data = $_SESSION['payme_data'];
unset($_SESSION['payme_data']);
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payme to'lov</title>
</head>
<body>
    <div style="text-align: center; padding: 50px;">
        <h2>Payme to'lov sahifasiga yo'naltirilmoqda...</h2>
        <p>Agar avtomatik yo'naltirilmasangiz, quyidagi tugmani bosing.</p>
        
        <form id="paymeForm" method="POST" action="<?= $payme_data['url'] ?>">
            <input type="hidden" name="data" value="<?= $payme_data['data'] ?>">
            <input type="hidden" name="signature" value="<?= $payme_data['signature'] ?>">
            <button type="submit" style="padding: 15px 30px; font-size: 16px; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer;">
                To'lovni davom ettirish
            </button>
        </form>
    </div>
    
    <script>
        // Avtomatik submit
        document.getElementById('paymeForm').submit();
    </script>
</body>
</html>

