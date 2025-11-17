<?php
require_once 'config/config.php';

if (isLoggedIn()) {
    $db = getDB();
    $stmt = $db->prepare("SELECT test_completed FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if ($user && $user['test_completed']) {
        redirect(BASE_URL . 'results/view.php');
    } else {
        redirect(BASE_URL . 'test/start.php');
    }
}
?>
<!DOCTYPE html>
<html lang="<?= Language::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('site.hero_title') ?> - <?= __('site.title') ?></title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/homepage.css">
    <style>
        .register-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            min-width: 180px;
            padding: 8px 0;
            z-index: 1000;
            border: 1px solid rgba(0, 0, 0, 0.06);
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .register-link {
            display: block;
            padding: 12px 20px;
            text-decoration: none;
            color: #0a0a0a;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .register-link:hover {
            background: #f8f9fa;
            color: #2563eb;
        }
        
        .header-buttons {
            position: relative;
        }
        
        /* Login Modal */
        .login-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-modal.active {
            display: flex;
        }
        
        .login-modal-content {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 450px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease-out;
        }
        
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .login-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .login-modal-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #0a0a0a;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 28px;
            color: #999;
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s;
        }
        
        .close-modal:hover {
            background: #f0f0f0;
            color: #0a0a0a;
        }
        
        .login-type-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }
        
        .login-type-btn {
            flex: 1;
            padding: 12px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            font-size: 14px;
        }
        
        .login-type-btn.active {
            border-color: #2563eb;
            background: #2563eb;
            color: white;
        }
        
        .login-section-modal {
            display: none;
        }
        
        .login-section-modal.active {
            display: block;
        }
        
        .login-section-modal .form-group {
            margin-bottom: 20px;
        }
        
        .login-section-modal label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .login-section-modal input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.2s;
            box-sizing: border-box;
        }
        
        .login-section-modal input:focus {
            outline: none;
            border-color: #2563eb;
        }
        
        .btn-login-submit {
            width: 100%;
            padding: 14px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
        }
        
        .btn-login-submit:hover {
            background: #1d4ed8;
        }
        
        .register-link-modal {
            display: block;
            text-align: center;
            margin-top: 20px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 10px;
            text-decoration: none;
            color: #2563eb;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .register-link-modal:hover {
            background: #e9ecef;
        }
        
        .telegram-widget-container {
            text-align: center;
            margin: 20px 0;
        }
        
        .google-signin-container {
            text-align: center;
            margin: 20px 0;
        }
    </style>
    <script>
        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.main-header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
        
        // Login modal
        document.addEventListener('DOMContentLoaded', function() {
            const loginBtn = document.getElementById('loginToggleBtn');
            const loginModal = document.getElementById('loginModal');
            const closeModal = document.getElementById('closeModal');
            const loginTypeBtns = document.querySelectorAll('.login-type-btn');
            const loginSections = document.querySelectorAll('.login-section-modal');
            
            // Login modal ochish
            if (loginBtn && loginModal) {
                loginBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    loginModal.classList.add('active');
                });
            }
            
            // Modal yopish
            if (closeModal) {
                closeModal.addEventListener('click', function() {
                    loginModal.classList.remove('active');
                });
            }
            
            // Modal tashqarisiga bosilganda yopish
            if (loginModal) {
                loginModal.addEventListener('click', function(e) {
                    if (e.target === loginModal) {
                        loginModal.classList.remove('active');
                    }
                });
            }
            
            // Login type tanlash
            loginTypeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const type = this.dataset.type;
                    
                    // Active class o'zgartirish
                    loginTypeBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Section ko'rsatish/yashirish
                    loginSections.forEach(section => {
                        section.classList.remove('active');
                    });
                    
                    const targetSection = document.getElementById(type + '-section-modal');
                    if (targetSection) {
                        targetSection.classList.add('active');
                    }
                });
            });
        });
    </script>
</head>
<body>
    <!-- Header Navigation -->
    <header class="main-header">
        <div class="container">
            <nav class="header-nav">
                <div class="logo">
                    <a href="<?= BASE_URL ?>"><?= __('site.name') ?></a>
                </div>
                <div class="header-buttons">
                    <?php include INCLUDES_PATH . 'language_switcher.php'; ?>
                    <button type="button" id="loginToggleBtn" class="btn-header btn-login"><?= __('nav.login') ?></button>
                    <a href="<?= BASE_URL ?>auth/register" class="btn-header btn-register"><?= __('nav.register') ?></a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-main">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title"><?= __('site.hero_title') ?></h1>
                <p class="hero-subtitle"><?= __('site.hero_subtitle') ?></p>
                <a href="<?= BASE_URL ?>auth/register" class="btn-hero"><?= __('nav.register') ?></a>
            </div>
        </div>
    </section>

    <!-- Problem Stories Section -->
    <section class="stories-section">
        <div class="container">
            <h2 class="section-title"><?= __('site.stories_title') ?></h2>
            
            <div class="stories-grid">
                <div class="story-card">
                    <div class="story-icon">👨‍🏫</div>
                    <div class="story-content">
                        <p class="story-text"><?= __('site.story1') ?></p>
                    </div>
                </div>
                
                <div class="story-card">
                    <div class="story-icon">👨‍🍳</div>
                    <div class="story-content">
                        <p class="story-text"><?= __('site.story2') ?></p>
                    </div>
                </div>
                
                <div class="story-card">
                    <div class="story-icon">🚗</div>
                    <div class="story-content">
                        <p class="story-text"><?= __('site.story3') ?></p>
                    </div>
                </div>
                
                <div class="story-card">
                    <div class="story-icon">📊</div>
                    <div class="story-content">
                        <p class="story-text"><?= __('site.story4') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-card">
                <div class="stats-number">7/10</div>
                <div class="stats-text">
                    <p><?= __('site.stats_text') ?></p>
                    <p class="stats-conclusion"><?= __('site.stats_conclusion') ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Problem Section -->
    <section class="problem-section">
        <div class="container">
            <h2 class="section-title"><?= __('site.problem_title') ?></h2>
            <div class="problem-content">
                <p class="problem-text">
                    <?= __('site.problem_text1') ?>
                </p>
                <p class="problem-text">
                    <?= __('site.problem_text2') ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Solution Section -->
    <section class="solution-section">
        <div class="container">
            <h2 class="section-title"><?= __('site.solution_title') ?></h2>
            <div class="solution-content">
                <div class="expert-info">
                    <div class="expert-badge">
                        <span class="expert-title"><?= __('site.expert_title') ?></span>
                        <span class="expert-name"><?= __('site.expert_name1') ?></span>
                        <span class="expert-name-bold"><?= __('site.expert_name2') ?></span>
                        <span class="expert-name"><?= __('site.expert_name3') ?></span>
                    </div>
                </div>
                
                <div class="solution-text">
                    <p><?= __('site.solution_text1') ?></p>
                    <p><?= __('site.solution_text2') ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing-section">
        <div class="container">
            <div class="pricing-card">
                <div class="pricing-header">
                    <h3><?= __('site.pricing_title') ?></h3>
                </div>
                <div class="pricing-body">
                    <div class="price-main">
                        <span class="price-old"><?= __('site.pricing_old') ?></span>
                        <span class="price-new"><?= __('site.pricing_new') ?></span>
                    </div>
                    <p class="price-note"><?= __('site.pricing_note') ?></p>
                    <div class="price-features">
                        <div class="price-feature">
                            <span class="feature-icon">⏱️</span>
                            <span><?= __('site.pricing_feature1') ?></span>
                        </div>
                        <div class="price-feature">
                            <span class="feature-icon">👨‍⚕️</span>
                            <span><?= __('site.pricing_feature2') ?></span>
                        </div>
                        <div class="price-feature">
                            <span class="feature-icon">📊</span>
                            <span><?= __('site.pricing_feature3') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title"><?= __('site.cta_title') ?></h2>
                <p class="cta-subtitle"><?= __('site.cta_subtitle') ?></p>
                <div class="cta-buttons">
                    <button type="button" onclick="document.getElementById('loginModal').classList.add('active')" class="btn-primary btn-large"><?= __('nav.login') ?></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= __('site.footer_text') ?></p>
        </div>
    </footer>
    
    <!-- Login Modal -->
    <div id="loginModal" class="login-modal">
        <div class="login-modal-content">
            <div class="login-modal-header">
                <h2><?= __('nav.login') ?></h2>
                <button type="button" id="closeModal" class="close-modal">&times;</button>
            </div>
            
            <div class="login-type-buttons">
                <button type="button" class="login-type-btn active" data-type="phone">📱 <?= __('auth.phone') ?></button>
                <button type="button" class="login-type-btn" data-type="telegram">✈️ <?= __('auth.telegram') ?></button>
                <button type="button" class="login-type-btn" data-type="google">🔵 <?= __('auth.google') ?></button>
            </div>
            
            <!-- Phone login section -->
            <div id="phone-section-modal" class="login-section-modal active">
                <form id="phoneLoginForm" method="POST">
                    <input type="hidden" name="login_type" value="phone">
                    <div class="form-group">
                        <label><?= __('auth.phone') ?></label>
                        <input type="tel" id="phone-input-modal" name="phone" placeholder="+998901234567" required>
                    </div>
                    <div class="form-group" id="verification-group-modal" style="display:none;">
                        <label><?= __('auth.verification_code') ?></label>
                        <input type="text" id="verification-code-input-modal" name="verification_code" placeholder="4 xonali kod" maxlength="4" pattern="[0-9]{4}">
                        <small class="text-muted">Telefoningizga yuborilgan kodni kiriting</small>
                    </div>
                    <button type="button" id="send-code-btn-modal" class="btn-login-submit">Kod yuborish</button>
                    <button type="submit" id="verify-code-btn-modal" class="btn-login-submit" style="display:none;"><?= __('nav.login') ?></button>
                </form>
            </div>
            
            <!-- Telegram login section -->
            <div id="telegram-section-modal" class="login-section-modal">
                <div class="telegram-widget-container">
                    <p style="text-align: center; margin-bottom: 20px; color: #666;">
                        <?= __('auth.telegram') ?> orqali kirish:
                    </p>
                    <div style="text-align: center; margin: 20px 0;">
                        <script async src="https://telegram.org/js/telegram-widget.js?22" 
                                data-telegram-login="<?= TELEGRAM_BOT_USERNAME ?>" 
                                data-size="large" 
                                data-onauth="onTelegramAuthModal(user)" 
                                data-request-access="write"
                                data-userpic="true"
                                data-auth-url="<?= BASE_URL ?>auth/telegram_callback"></script>
                    </div>
                </div>
            </div>
            
            <!-- Google login section -->
            <div id="google-section-modal" class="login-section-modal">
                <div class="google-signin-container">
                    <p style="text-align: center; margin-bottom: 20px; color: #666;">
                        <?= __('auth.google') ?> orqali kirish:
                    </p>
                    <div id="google-signin-button-modal" style="text-align: center; margin: 20px 0;"></div>
                </div>
            </div>
            
            <a href="<?= BASE_URL ?>auth/register" class="register-link-modal">
                <?= __('nav.register') ?>
            </a>
        </div>
    </div>
    
    <!-- Google Identity Services -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    
    <script>
        // Telegram auth callback for modal
        function onTelegramAuthModal(user) {
            // Telegram Login Widget dan kelgan ma'lumotlarni to'g'ri formatda yuborish
            const params = new URLSearchParams({
                id: user.id,
                first_name: user.first_name || '',
                last_name: user.last_name || '',
                username: user.username || '',
                auth_date: user.auth_date,
                hash: user.hash
            });
            window.location.href = '<?= BASE_URL ?>auth/telegram_callback?' + params.toString();
        }
        
        // Google Sign-In callback for modal
        function onGoogleSignInModal(response) {
            if (response.credential) {
                window.location.href = '<?= BASE_URL ?>auth/google_callback?credential=' + encodeURIComponent(response.credential);
            }
        }
        
        // Phone login - kod yuborish
        document.addEventListener('DOMContentLoaded', function() {
            const phoneForm = document.getElementById('phoneLoginForm');
            const phoneInput = document.getElementById('phone-input-modal');
            const sendCodeBtn = document.getElementById('send-code-btn-modal');
            const verifyCodeBtn = document.getElementById('verify-code-btn-modal');
            const verificationGroup = document.getElementById('verification-group-modal');
            const verificationCodeInput = document.getElementById('verification-code-input-modal');
            
            if (sendCodeBtn) {
                sendCodeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const phone = phoneInput.value.trim();
                    
                    if (!phone) {
                        alert('Telefon raqamni kiriting!');
                        return;
                    }
                    
                    // AJAX orqali kod yuborish
                    sendCodeBtn.disabled = true;
                    sendCodeBtn.textContent = 'Yuborilmoqda...';
                    
                    fetch('<?= BASE_URL ?>auth/send_code', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'phone=' + encodeURIComponent(phone)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            verificationGroup.style.display = 'block';
                            sendCodeBtn.style.display = 'none';
                            verifyCodeBtn.style.display = 'block';
                            verificationCodeInput.focus();
                            alert('Tasdiqlash kodi yuborildi! Kod: ' + data.code); // Test uchun
                        } else {
                            alert(data.message || 'Xatolik yuz berdi!');
                            sendCodeBtn.disabled = false;
                            sendCodeBtn.textContent = 'Kod yuborish';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Xatolik yuz berdi!');
                        sendCodeBtn.disabled = false;
                        sendCodeBtn.textContent = 'Kod yuborish';
                    });
                });
            }
            
            // Form submit - kodni tasdiqlash
            if (phoneForm) {
                phoneForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const phone = phoneInput.value.trim();
                    const code = verificationCodeInput.value.trim();
                    
                    if (!code) {
                        alert('Tasdiqlash kodini kiriting!');
                        return;
                    }
                    
                    // Form submit qilish
                    const formData = new FormData();
                    formData.append('login_type', 'phone');
                    formData.append('phone', phone);
                    formData.append('verification_code', code);
                    
                    verifyCodeBtn.disabled = true;
                    verifyCodeBtn.textContent = 'Tekshirilmoqda...';
                    
                    fetch('<?= BASE_URL ?>auth/login', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            alert(data.message || 'Xatolik yuz berdi!');
                            verifyCodeBtn.disabled = false;
                            verifyCodeBtn.textContent = '<?= __('nav.login') ?>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Xatolik yuz berdi!');
                        verifyCodeBtn.disabled = false;
                        verifyCodeBtn.textContent = '<?= __('nav.login') ?>';
                    });
                });
            }
        });
        
        // Google Sign-In initialization for modal
        window.addEventListener('load', function() {
            if (typeof google !== 'undefined' && google.accounts) {
                google.accounts.id.initialize({
                    client_id: '<?= GOOGLE_CLIENT_ID ?>',
                    callback: onGoogleSignInModal
                });
                
                // Render button when modal opens and Google section is active
                const loginModal = document.getElementById('loginModal');
                const loginTypeBtns = document.querySelectorAll('.login-type-btn');
                
                if (loginModal && loginTypeBtns.length > 0) {
                    // Google section tanlanganda button render qilish
                    loginTypeBtns.forEach(btn => {
                        btn.addEventListener('click', function() {
                            if (this.dataset.type === 'google') {
                                setTimeout(function() {
                                    const googleButtonContainer = document.getElementById('google-signin-button-modal');
                                    if (googleButtonContainer && googleButtonContainer.children.length === 0) {
                                        google.accounts.id.renderButton(
                                            googleButtonContainer,
                                            { theme: 'outline', size: 'large', text: 'sign_in_with', shape: 'rectangular' }
                                        );
                                    }
                                }, 100);
                            }
                        });
                    });
                }
            }
        });
    </script>
</body>
</html>
