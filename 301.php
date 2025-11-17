<?php
require_once 'config/config.php';
// 301 redirect - bu sahifa odatda ishlatilmaydi, lekin agar kerak bo'lsa
if (isset($_GET['redirect'])) {
    header('Location: ' . urldecode($_GET['redirect']), true, 301);
    exit;
}
http_response_code(301);
?>
<!DOCTYPE html>
<html lang="<?= Language::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>301 - Permanent Redirect - <?= __('site.title', 'Prof Orientatsiya') ?></title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            padding: 20px;
        }
        .error-content {
            text-align: center;
            background: white;
            padding: 60px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
        }
        .error-code {
            font-size: 120px;
            font-weight: 800;
            color: #f39c12;
            line-height: 1;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .error-message {
            font-size: 18px;
            color: #7f8c8d;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-content">
            <div class="error-code">301</div>
            <h1 class="error-title"><?= __('error.301_title', 'Permanent Redirect') ?></h1>
            <p class="error-message">
                <?= __('error.301_message', 'Sahifa boshqa manzilga ko\'chirilgan.') ?>
            </p>
            <div class="error-actions">
                <a href="<?= BASE_URL ?>" class="btn-primary"><?= __('error.back_home', 'Bosh sahifaga qaytish') ?></a>
            </div>
        </div>
    </div>
</body>
</html>

