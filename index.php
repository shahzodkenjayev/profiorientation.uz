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
        
        // Login toggle button
        document.addEventListener('DOMContentLoaded', function() {
            const loginBtn = document.getElementById('loginToggleBtn');
            const registerDropdown = document.getElementById('registerDropdown');
            
            if (loginBtn && registerDropdown) {
                loginBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    registerDropdown.style.display = registerDropdown.style.display === 'none' ? 'block' : 'none';
                });
                
                // Click outside to close
                document.addEventListener('click', function(e) {
                    if (!loginBtn.contains(e.target) && !registerDropdown.contains(e.target)) {
                        registerDropdown.style.display = 'none';
                    }
                });
            }
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
                    <div id="registerDropdown" class="register-dropdown" style="display: none;">
                        <a href="<?= BASE_URL ?>auth/register" class="register-link"><?= __('nav.register') ?></a>
                    </div>
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
                    <a href="<?= BASE_URL ?>auth/login" class="btn-primary btn-large"><?= __('nav.login') ?></a>
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
</body>
</html>
