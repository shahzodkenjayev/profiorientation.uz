<?php
// Til faylini yuklash (fayl strukturangizga qarab yo'lni to'g'rilang)
// Agar til fayli 'lang.php' da bo'lsa:
$lang = include 'lang.php'; 

// Agar to'g'ridan-to'g'ri shu faylda array bo'lsa, yuqoridagi $lang o'zgaruvchisini o'chirmang,
// shunchaki include o'rniga arrayni ishlating.
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['site']['title'] ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <link rel="stylesheet" href="homepage.css">
</head>
<body>

    <header class="main-header" id="header">
        <nav class="header-nav">
            <div class="logo">
                <a href="/">
                    <i class="ri-compass-3-line" style="vertical-align: middle; margin-right: 5px;"></i>
                    <?= $lang['site']['name'] ?>
                </a>
            </div>
            
            <div class="header-buttons">
                <a href="/login" class="btn-header btn-login">
                    <i class="ri-login-circle-line"></i> <?= $lang['auth']['login'] ?>
                </a>
                <a href="/register" class="btn-header btn-register">
                    <?= $lang['auth']['register'] ?> <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero-main">
            <div class="container hero-content" data-aos="fade-up" data-aos-duration="1000">
                <h1 class="hero-title">
                    <?= $lang['site']['hero_title'] ?>
                </h1>
                <p class="hero-subtitle">
                    <?= $lang['site']['hero_subtitle'] ?>
                </p>
                <a href="#cta" class="btn-hero">
                    <?= $lang['payment']['start_test'] ?> 
                    <i class="ri-arrow-right-circle-fill" style="margin-left: 10px; font-size: 20px;"></i>
                </a>
            </div>
        </section>

        <section class="stories-section">
            <div class="container">
                <h2 class="section-title" data-aos="fade-up"><?= $lang['site']['stories_title'] ?></h2>
                
                <div class="stories-grid">
                    <div class="story-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="story-icon">
                            <i class="ri-medicine-bottle-line"></i>
                        </div>
                        <p class="story-text"><?= $lang['site']['story1'] ?></p>
                    </div>

                    <div class="story-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="story-icon">
                            <i class="ri-restaurant-line"></i>
                        </div>
                        <p class="story-text"><?= $lang['site']['story2'] ?></p>
                    </div>

                    <div class="story-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="story-icon">
                            <i class="ri-steering-2-line"></i>
                        </div>
                        <p class="story-text"><?= $lang['site']['story3'] ?></p>
                    </div>

                    <div class="story-card" data-aos="fade-up" data-aos-delay="400">
                        <div class="story-icon">
                            <i class="ri-bar-chart-grouped-line"></i>
                        </div>
                        <p class="story-text"><?= $lang['site']['story4'] ?></p>
                    </div>
                </div>
            </div>
        </section>

        <section class="stats-section">
            <div class="container stats-card" data-aos="zoom-in">
                <div class="stats-number">7/10</div>
                <div class="stats-text">
                    <p><?= $lang['site']['stats_text'] ?></p>
                </div>
                <div class="stats-conclusion">
                    <?= $lang['site']['stats_conclusion'] ?>
                </div>
            </div>
        </section>

        <section class="problem-section">
            <div class="container problem-content">
                <h2 class="section-title" data-aos="fade-up"><?= $lang['site']['problem_title'] ?></h2>
                
                <div class="problem-text" data-aos="fade-right">
                    <p><?= $lang['site']['problem_text1'] ?></p>
                </div>
                <div class="problem-text" data-aos="fade-left">
                    <p><?= $lang['site']['problem_text2'] ?></p>
                </div>
            </div>
        </section>

        <section class="solution-section">
            <div class="container solution-content">
                <div class="expert-badge" data-aos="flip-up">
                    <span class="expert-title"><?= $lang['site']['expert_title'] ?></span>
                    <span class="expert-name-bold"><?= $lang['site']['expert_name2'] ?></span>
                    <span class="expert-name"><?= $lang['site']['expert_name1'] ?></span>
                    <span class="expert-name" style="font-size: 12px; opacity: 0.7;"><?= $lang['site']['expert_name3'] ?></span>
                </div>

                <div class="solution-text" data-aos="fade-up">
                    <h2 style="margin-bottom: 20px; color: #065f46;"><?= $lang['site']['solution_title'] ?></h2>
                    <p><i class="ri-check-double-line"></i> <?= $lang['site']['solution_text1'] ?></p>
                    <p><i class="ri-lightbulb-line"></i> <?= $lang['site']['solution_text2'] ?></p>
                </div>
            </div>
        </section>

        <section class="pricing-section">
            <div class="container">
                <h2 class="section-title"><?= $lang['site']['pricing_title'] ?></h2>
                
                <div class="pricing-card" data-aos="fade-up">
                    <div class="pricing-header">
                        <h3>Professional Test</h3>
                    </div>
                    <div class="pricing-body">
                        <div class="price-main">
                            <span class="price-old"><?= $lang['site']['pricing_old'] ?></span>
                            <span class="price-new"><?= $lang['site']['pricing_new'] ?></span>
                            <div class="price-note">
                                <i class="ri-flashlight-fill"></i> <?= $lang['site']['pricing_note'] ?>
                            </div>
                        </div>
                        
                        <div class="price-features">
                            <div class="price-feature">
                                <i class="ri-time-line feature-icon"></i>
                                <span><?= $lang['site']['pricing_feature1'] ?></span>
                            </div>
                            <div class="price-feature">
                                <i class="ri-psychotherapy-line feature-icon"></i>
                                <span><?= $lang['site']['pricing_feature2'] ?></span>
                            </div>
                            <div class="price-feature">
                                <i class="ri-file-list-3-line feature-icon"></i>
                                <span><?= $lang['site']['pricing_feature3'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section" id="cta" style="background: var(--dark); padding: 100px 0; text-align: center;">
            <div class="container cta-content" data-aos="zoom-in-up">
                <h2 class="cta-title"><?= $lang['site']['cta_title'] ?></h2>
                <p class="cta-subtitle"><?= $lang['site']['cta_subtitle'] ?></p>
                
                <div style="max-width: 400px; margin: 30px auto; background: rgba(255,255,255,0.1); padding: 30px; border-radius: 16px;">
                    <form action="/register" method="GET">
                        <div style="margin-bottom: 15px;">
                            <input type="text" placeholder="<?= $lang['auth']['full_name'] ?>" style="width: 100%; padding: 12px; border-radius: 8px; border: none;">
                        </div>
                        <div style="margin-bottom: 20px;">
                            <input type="tel" placeholder="<?= $lang['auth']['phone'] ?>" style="width: 100%; padding: 12px; border-radius: 8px; border: none;">
                        </div>
                        <button type="submit" class="btn-hero" style="width: 100%; justify-content: center; border: none; cursor: pointer;">
                            <?= $lang['nav']['register'] ?>
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4 class="footer-title"><?= $lang['site']['name'] ?></h4>
                    <p class="footer-description">
                        <?= $lang['site']['hero_subtitle'] ?>
                    </p>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-title"><?= $lang['footer']['quick_links'] ?></h4>
                    <ul class="footer-links">
                        <li><a href="#"><?= $lang['footer']['home'] ?></a></li>
                        <li><a href="#"><?= $lang['footer']['about'] ?></a></li>
                        <li><a href="#"><?= $lang['footer']['pricing'] ?></a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4 class="footer-title"><?= $lang['footer']['contact'] ?></h4>
                    <ul class="footer-contact">
                        <li>
                            <i class="ri-phone-line contact-icon"></i>
                            <a href="tel:+998901234567">+998 90 123 45 67</a>
                        </li>
                        <li>
                            <i class="ri-mail-line contact-icon"></i>
                            <a href="mailto:info@proforientatsiya.uz">info@proforientatsiya.uz</a>
                        </li>
                        <li>
                            <i class="ri-map-pin-line contact-icon"></i>
                            <a href="#">Tashkent, Uzbekistan</a>
                        </li>
                    </ul>
                    <div class="footer-social" style="margin-top: 20px;">
                        <a href="#" class="social-link"><i class="ri-telegram-fill"></i></a>
                        <a href="#" class="social-link"><i class="ri-instagram-line"></i></a>
                        <a href="#" class="social-link"><i class="ri-facebook-fill"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p><?= $lang['site']['footer_text'] ?> | Developed with <i class="ri-heart-fill" style="color: #ef4444;"></i></p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize Animations
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
    </script>
</body>
</html>