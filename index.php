<?php
require_once 'config/config.php';

// Imtihon sanalarini olish
$db = getDB();
$stmt = $db->prepare("SELECT * FROM exam_dates WHERE exam_date > NOW() ORDER BY exam_date ASC");
$stmt->execute();
$exam_dates = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?= Language::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('site.hero_title') ?> - <?= __('site.title') ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/homepage.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    
    <?php include INCLUDES_PATH . 'language_switcher.php'; ?>
</head>
<body>
    <header class="main-header" id="header">
        <nav class="header-nav">
            <div class="logo">
                <a href="<?= BASE_URL ?>">
                    <?= __('site.name') ?>
                </a>
            </div>
            
            <div class="header-buttons">
                <button type="button" class="btn-header btn-login" id="loginBtn">
                    <?= __('nav.login') ?>
                </button>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero-main">
            <div class="container hero-content" data-aos="fade-up" data-aos-duration="1000">
                <h1 class="hero-title">
                    <?= __('site.hero_title') ?>
                </h1>
                <p class="hero-subtitle">
                    <?= __('site.hero_subtitle') ?>
                </p>
                <button type="button" class="btn-hero" id="heroLoginBtn">
                    <?= __('nav.login') ?>
                    <i class="ri-arrow-right-circle-fill" style="margin-left: 10px; font-size: 20px;"></i>
                </button>
            </div>
        </section>

        <section class="stories-section">
            <div class="container">
                <h2 class="section-title" data-aos="fade-up"><?= __('site.stories_title') ?></h2>
                
                <div class="stories-grid">
                    <div class="story-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="story-icon">
                            <i class="ri-medicine-bottle-line"></i>
                        </div>
                        <p class="story-text"><?= __('site.story1') ?></p>
                    </div>

                    <div class="story-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="story-icon">
                            <i class="ri-restaurant-line"></i>
                        </div>
                        <p class="story-text"><?= __('site.story2') ?></p>
                    </div>

                    <div class="story-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="story-icon">
                            <i class="ri-steering-2-line"></i>
                        </div>
                        <p class="story-text"><?= __('site.story3') ?></p>
                    </div>

                    <div class="story-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="story-icon">
                            <i class="ri-bar-chart-grouped-line"></i>
                        </div>
                        <p class="story-text"><?= __('site.story4') ?></p>
                    </div>
                </div>
            </div>
        </section>

        <section class="stats-section">
            <div class="container stats-card" data-aos="zoom-in">
                <div class="stats-number">7/10</div>
                <div class="stats-text">
                    <p><?= __('site.stats_text') ?></p>
                </div>
                <div class="stats-conclusion">
                    <?= __('site.stats_conclusion') ?>
                </div>
            </div>
        </section>

        <section class="problem-section">
            <div class="container problem-content">
                <h2 class="section-title" data-aos="fade-up"><?= __('site.problem_title') ?></h2>
                
                <div class="problem-text" data-aos="fade-right">
                    <p><?= __('site.problem_text1') ?></p>
                </div>
                <div class="problem-text" data-aos="fade-left">
                    <p><?= __('site.problem_text2') ?></p>
                </div>
            </div>
        </section>

        <section class="solution-section">
            <div class="container solution-content">
                <div class="expert-badge" data-aos="flip-up">
                    <span class="expert-title"><?= __('site.expert_title') ?></span>
                    <span class="expert-name-bold"><?= __('site.expert_name2') ?></span>
                    <span class="expert-name"><?= __('site.expert_name1') ?></span>
                    <span class="expert-name" style="font-size: 12px; opacity: 0.7;"><?= __('site.expert_name3') ?></span>
                </div>

                <div class="solution-text" data-aos="fade-up">
                    <h2 style="margin-bottom: 20px; color: #065f46;"><?= __('site.solution_title') ?></h2>
                    <p><i class="ri-check-double-line"></i> <?= __('site.solution_text1') ?></p>
                    <p><i class="ri-lightbulb-line"></i> <?= __('site.solution_text2') ?></p>
                </div>
            </div>
        </section>

        <section class="pricing-section">
            <div class="container">
                <h2 class="section-title"><?= __('site.pricing_title') ?></h2>
                
                <div class="pricing-card" data-aos="fade-up">
                    <div class="pricing-header">
                        <h3>Professional Test</h3>
                        <div class="price-main">
                            <span class="price-old"><?= __('site.pricing_old') ?></span>
                            <span class="price-new"><?= __('site.pricing_new') ?></span>
                        </div>
                        <div class="price-note">
                            <i class="ri-flashlight-fill"></i> <?= __('site.pricing_note') ?>
                        </div>
                    </div>
                    <div class="pricing-body">
                        <div class="price-features">
                            <div class="price-feature">
                                <i class="ri-time-line feature-icon"></i>
                                <span><?= __('site.pricing_feature1') ?></span>
                            </div>
                            <div class="price-feature">
                                <i class="ri-psychotherapy-line feature-icon"></i>
                                <span><?= __('site.pricing_feature2') ?></span>
                            </div>
                            <div class="price-feature">
                                <i class="ri-file-list-3-line feature-icon"></i>
                                <span><?= __('site.pricing_feature3') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section" id="cta" style="background: var(--dark); padding: 100px 0; text-align: center;">
            <div class="container cta-content" data-aos="zoom-in-up">
                <h2 class="cta-title" style="color: var(--white); font-size: 48px; font-weight: 800; margin-bottom: 20px;"><?= __('site.cta_title') ?></h2>
                <p class="cta-subtitle" style="color: #94a3b8; font-size: 20px; margin-bottom: 40px;"><?= __('site.cta_subtitle') ?></p>
                <button type="button" class="btn-hero" id="ctaLoginBtn" style="border: none; cursor: pointer;">
                    <?= __('nav.login') ?>
                </button>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="footer-title"><?= __('site.name') ?></h3>
                    <p class="footer-description"><?= __('site.hero_subtitle') ?></p>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-heading"><?= __('footer.quick_links') ?></h4>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_URL ?>"><?= __('footer.home') ?></a></li>
                        <li><a href="<?= BASE_URL ?>#about"><?= __('footer.about') ?></a></li>
                        <li><a href="<?= BASE_URL ?>#pricing"><?= __('footer.pricing') ?></a></li>
                        <li><a href="<?= BASE_URL ?>#contact"><?= __('footer.contact') ?></a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4 class="footer-heading"><?= __('footer.contact') ?></h4>
                    <ul class="footer-contact">
                        <li>
                            <span class="contact-icon">📧</span>
                            <a href="mailto:info@profiorientation.uz">info@profiorientation.uz</a>
                        </li>
                        <li>
                            <span class="contact-icon">📱</span>
                            <a href="tel:+998901234567">+998 90 123 45 67</a>
                        </li>
                        <li>
                            <span class="contact-icon">✈️</span>
                            <a href="https://t.me/profiorientatsiya_bot" target="_blank">@profiorientatsiya_bot</a>
                        </li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4 class="footer-heading"><?= __('footer.follow_us') ?></h4>
                    <div class="footer-social">
                        <a href="https://t.me/profiorientatsiya_bot" target="_blank" class="social-link" aria-label="Telegram">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.174 1.858-.924 6.654-1.305 8.835-.152.86-.45 1.147-.74 1.175-.64.056-1.125-.422-1.745-.826-.308-.207-.541-.337-.875-.54-.387-.25-.145-.388.09-.613.19-.18 3.247-2.977 3.307-3.23.015-.062.03-.3-.117-.425-.146-.125-.363-.082-.52-.049-.22.043-3.71 2.35-4.19 2.61-.394.214-.56.252-.762.252-.16 0-.4-.038-.585-.07-.23-.04-.44-.18-.58-.33-.22-.24-.38-.6-.38-1.05 0-.4.08-1.01.12-1.45.08-.88.17-1.75.25-2.63.05-.55.1-1.1.15-1.65.03-.28.06-.56.09-.84.01-.1.02-.2.03-.3.01-.05.02-.1.03-.15.01-.03.02-.05.03-.08.01-.02.02-.04.03-.06.01-.01.02-.02.03-.03.01-.01.02-.01.03-.02.01 0 .02-.01.03-.01.01 0 .02 0 .03.01.01 0 .02.01.03.02.01.01.02.02.03.03.01.02.02.04.03.06.01.03.02.05.03.08.01.05.02.1.03.15.01.1.02.2.03.3.03.28.06.56.09.84.05.55.1 1.1.15 1.65.08.88.17 1.75.25 2.63.04.44.12 1.05.12 1.45 0 .45-.16.81-.38 1.05-.14.15-.35.29-.58.33-.19.03-.43.07-.59.07-.2 0-.37-.04-.76-.25-.48-.26-3.97-2.57-4.19-2.61-.16-.03-.37-.08-.52.05-.15.12-.13.36-.12.43.06.25 3.12 3.05 3.31 3.23.24.23.48.36.88.61.62.33 1.11.49 1.75.75.76.31 1.35.48 2.15.48.64 0 1.28-.21 1.75-.6.47-.38.8-.95.95-1.6.15-.65.3-1.4.45-2.15.3-1.5.6-3 .9-4.5.15-.75.3-1.5.45-2.25.08-.38.15-.75.23-1.13.04-.19.08-.38.12-.57.02-.1.04-.2.06-.3.01-.05.02-.1.03-.15.01-.03.02-.05.03-.08.01-.02.02-.04.03-.06.01-.01.02-.02.03-.03.01-.01.02-.01.03-.02.01 0 .02-.01.03-.01.01 0 .02 0 .03.01.01 0 .02.01.03.02.01.01.02.02.03.03.01.02.02.04.03.06.01.03.02.05.03.08.01.05.02.1.03.15.02.1.04.2.06.3.04.19.08.38.12.57.08.38.15.75.23 1.13.15.75.3 1.5.45 2.25.3 1.5.6 3 .9 4.5z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= __('site.footer_text') ?></p>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div id="loginModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><?= __('nav.login') ?></h2>
                <button type="button" class="modal-close" id="closeModal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="login-tabs">
                    <button type="button" class="tab-btn active" data-tab="phone">📱 <?= __('auth.phone') ?></button>
                    <button type="button" class="tab-btn" data-tab="telegram">✈️ <?= __('auth.telegram') ?></button>
                    <button type="button" class="tab-btn" data-tab="google">🔵 <?= __('auth.google') ?></button>
                </div>

                <!-- Phone Login -->
                <div id="phoneTab" class="tab-content active">
                    <form id="phoneLoginForm">
                        <div class="form-group">
                            <label><?= __('auth.phone_number') ?></label>
                            <input type="tel" name="phone" id="phoneInput" placeholder="+998901234567" required>
                        </div>
                        <div class="form-group" id="codeGroup" style="display: none;">
                            <label><?= __('auth.verification_code') ?></label>
                            <input type="text" name="verification_code" id="codeInput" placeholder="4 xonali kod" maxlength="4" pattern="[0-9]{4}">
                            <small class="text-muted"><?= __('auth.enter_code') ?></small>
                            <button type="button" id="resendCode" class="btn-link"><?= __('auth.resend_code') ?></button>
                        </div>
                        <button type="submit" class="btn-primary" id="phoneSubmitBtn"><?= __('auth.send_code') ?></button>
                    </form>
                </div>

                <!-- Telegram Login -->
                <div id="telegramTab" class="tab-content">
                    <div style="text-align: center; margin: 20px 0;">
                        <script async src="https://telegram.org/js/telegram-widget.js?22" 
                                data-telegram-login="<?= TELEGRAM_BOT_USERNAME ?>" 
                                data-size="large" 
                                data-onauth="onTelegramAuth(user)" 
                                data-request-access="write"
                                data-userpic="true"
                                data-auth-url="<?= BASE_URL ?>auth/telegram_callback"></script>
                    </div>
                </div>

                <!-- Google Login -->
                <div id="googleTab" class="tab-content">
                    <div style="text-align: center; margin: 20px 0;">
                        <div id="g_id_onload"
                             data-client_id="<?= GOOGLE_CLIENT_ID ?>"
                             data-callback="onGoogleSignIn"
                             data-auto_prompt="false">
                        </div>
                        <div class="g_id_signin"
                             data-type="standard"
                             data-size="large"
                             data-theme="outline"
                             data-text="sign_in_with"
                             data-shape="rectangular"
                             data-logo_alignment="left">
                        </div>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                    <a href="<?= BASE_URL ?>auth/register" class="btn-link"><?= __('nav.register') ?></a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        // AOS Init
        AOS.init({
            once: true,
            offset: 100,
            duration: 800,
        });

        // Header Scroll Effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Modal Functions
        const loginModal = document.getElementById('loginModal');
        const loginBtn = document.getElementById('loginBtn');
        const heroLoginBtn = document.getElementById('heroLoginBtn');
        const ctaLoginBtn = document.getElementById('ctaLoginBtn');
        const closeModal = document.getElementById('closeModal');

        function openModal() {
            loginModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeModalFunc() {
            loginModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        loginBtn?.addEventListener('click', openModal);
        heroLoginBtn?.addEventListener('click', openModal);
        ctaLoginBtn?.addEventListener('click', openModal);
        closeModal?.addEventListener('click', closeModalFunc);
        loginModal?.addEventListener('click', function(e) {
            if (e.target === loginModal) {
                closeModalFunc();
            }
        });

        // Tab Switching
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const tab = this.dataset.tab;
                
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                this.classList.add('active');
                document.getElementById(tab + 'Tab').classList.add('active');
            });
        });

        // Phone Login Form
        const phoneLoginForm = document.getElementById('phoneLoginForm');
        const phoneInput = document.getElementById('phoneInput');
        const codeInput = document.getElementById('codeInput');
        const codeGroup = document.getElementById('codeGroup');
        const phoneSubmitBtn = document.getElementById('phoneSubmitBtn');
        let codeSent = false;

        phoneLoginForm?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!codeSent) {
                // Send code
                const phone = phoneInput.value;
                if (!phone) {
                    alert('<?= __('auth.phone_number') ?>');
                    return;
                }

                try {
                    const response = await fetch('<?= BASE_URL ?>auth/send_code.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'phone=' + encodeURIComponent(phone)
                    });

                    const data = await response.json();
                    if (data.success) {
                        codeSent = true;
                        codeGroup.style.display = 'block';
                        phoneSubmitBtn.textContent = '<?= __('nav.login') ?>';
                        alert(data.message);
                    } else {
                        alert(data.message || 'Xatolik yuz berdi!');
                    }
                } catch (error) {
                    alert('Xatolik yuz berdi!');
                }
            } else {
                // Verify code and login
                const phone = phoneInput.value;
                const code = codeInput.value;

                try {
                    const formData = new FormData();
                    formData.append('login_type', 'phone');
                    formData.append('phone', phone);
                    formData.append('verification_code', code);

                    const response = await fetch('<?= BASE_URL ?>auth/login', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();
                    if (data.success) {
                        window.location.href = data.redirect || '<?= BASE_URL ?>dashboard';
                    } else {
                        alert(data.message || 'Noto\'g\'ri kod!');
                    }
                } catch (error) {
                    alert('Xatolik yuz berdi!');
                }
            }
        });

        // Telegram Auth Callback
        function onTelegramAuth(user) {
            const callbackUrl = '<?= BASE_URL ?>auth/telegram_callback';
            const params = new URLSearchParams(user);
            window.location.href = callbackUrl + '?' + params.toString();
        }

        // Google Sign-In Callback
        function onGoogleSignIn(response) {
            const credential = response.credential;
            const callbackUrl = '<?= BASE_URL ?>auth/google_callback';
            window.location.href = callbackUrl + '?credential=' + encodeURIComponent(credential);
        }

        // Google Identity Services Init
        window.addEventListener('load', function() {
            if (typeof google !== 'undefined' && google.accounts) {
                google.accounts.id.initialize({
                    client_id: '<?= GOOGLE_CLIENT_ID ?>',
                    callback: onGoogleSignIn
                });
            }
        });
    </script>
</body>
</html>
