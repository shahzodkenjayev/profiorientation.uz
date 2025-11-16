<?php
require_once 'config/config.php';
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="<?= Language::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Ruhsat etilmagan - <?= __('site.title', 'Prof Orientatsiya') ?></title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            color: #f5576c;
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
        .icon-lock {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-content">
            <div class="icon-lock">🔒</div>
            <div class="error-code">403</div>
            <h1 class="error-title"><?= __('error.403_title', 'Ruhsat etilmagan') ?></h1>
            <p class="error-message">
                <?= __('error.403_message', 'Kechirasiz, siz bu sahifaga kirish huquqiga ega emassiz. Bu sahifa himoyalangan yoki sizga ruhsat berilmagan.') ?>
            </p>
            <div class="error-actions">
                <a href="<?= BASE_URL ?>" class="btn-primary"><?= __('error.back_home', 'Bosh sahifaga qaytish') ?></a>
                <a href="javascript:history.back()" class="btn-secondary"><?= __('error.go_back', 'Orqaga') ?></a>
            </div>
        </div>
    </div>
</body>
</html>

