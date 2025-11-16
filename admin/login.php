<?php
require_once '../config/config.php';

if (isAdmin()) {
    redirect(BASE_URL . 'admin/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Barcha maydonlarni to\'ldiring!';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_role'] = $admin['role'];
                
                // Last login yangilash
                $stmt = $db->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
                $stmt->execute([$admin['id']]);
                
                redirect(BASE_URL . 'admin/dashboard.php');
            } else {
                $error = 'Noto\'g\'ri foydalanuvchi nomi yoki parol!';
            }
        } catch (PDOException $e) {
            $error = 'Xatolik yuz berdi: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= Language::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('admin.login_title', 'Admin Kirish') ?> - <?= __('site.title', 'Prof Orientatsiya') ?></title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/admin.css">
    <style>
        .admin-login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .admin-login-wrapper::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveBackground 20s linear infinite;
        }

        @keyframes moveBackground {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .admin-login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px;
        }

        .admin-login-box {
            background: rgba(255, 255, 255, 0.98);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .admin-login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .admin-login-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .admin-login-box h1 {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
            background: none;
            -webkit-background-clip: unset;
            -webkit-text-fill-color: #2c3e50;
        }

        .admin-login-subtitle {
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 0;
        }

        .admin-login-box .form-group {
            margin-bottom: 24px;
        }

        .admin-login-box .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .admin-login-box .form-group input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
            background: #f8f9fa;
        }

        .admin-login-box .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .admin-login-box .btn-primary {
            width: 100%;
            padding: 16px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 12px;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .admin-login-box .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .admin-login-box .alert {
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            font-size: 14px;
            border-left: 4px solid;
        }

        .admin-login-box .alert-error {
            background: #fff5f5;
            color: #c53030;
            border-left-color: #c53030;
        }

        .admin-login-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .admin-login-footer p {
            color: #7f8c8d;
            font-size: 13px;
            margin: 0;
        }

        .admin-login-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .admin-login-footer a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-login-box {
                padding: 40px 30px;
                border-radius: 16px;
            }

            .admin-login-logo {
                width: 70px;
                height: 70px;
                font-size: 32px;
            }

            .admin-login-box h1 {
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {
            .admin-login-box {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-login-wrapper">
        <div class="admin-login-container">
            <div class="admin-login-box">
                <div class="admin-login-header">
                    <div class="admin-login-logo">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h1><?= __('admin.login_title', 'Admin Panel') ?></h1>
                    <p class="admin-login-subtitle"><?= __('admin.login_subtitle', 'Tizimga kirish uchun ma\'lumotlaringizni kiriting') ?></p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <strong>⚠️</strong> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="adminLoginForm">
                    <div class="form-group">
                        <label for="username"><?= __('admin.username', 'Foydalanuvchi nomi') ?></label>
                        <input 
                            type="text" 
                            id="username"
                            name="username" 
                            placeholder="<?= __('admin.username_placeholder', 'Foydalanuvchi nomingizni kiriting') ?>" 
                            required 
                            autofocus
                            autocomplete="username"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="password"><?= __('admin.password', 'Parol') ?></label>
                        <input 
                            type="password" 
                            id="password"
                            name="password" 
                            placeholder="<?= __('admin.password_placeholder', 'Parolingizni kiriting') ?>" 
                            required
                            autocomplete="current-password"
                        >
                    </div>
                    
                    <button type="submit" class="btn-primary">
                        <span><?= __('admin.login_button', 'Kirish') ?></span>
                    </button>
                </form>

                <div class="admin-login-footer">
                    <p><?= __('site.footer_text', 'Prof Orientatsiya. Barcha huquqlar himoyalangan.') ?></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form validation
        document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            if (!username || !password) {
                e.preventDefault();
                alert('<?= __('admin.fill_all_fields', 'Barcha maydonlarni to\'ldiring!') ?>');
                return false;
            }
        });

        // Input focus effects
        const inputs = document.querySelectorAll('.admin-login-box input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    </script>
</body>
</html>

