<?php
// Language switcher component

$current_lang = Language::current();
$available_langs = Language::getAvailableLanguages();

// SVG flag paths
$flag_paths = [
    'uz' => ASSETS_PATH . 'images/flags/uz.svg',
    'ru' => ASSETS_PATH . 'images/flags/ru.svg',
    'en' => ASSETS_PATH . 'images/flags/en.svg',
    'tr' => ASSETS_PATH . 'images/flags/tr.svg'
];
?>
<div class="language-switcher">
    <button type="button" class="lang-current" id="langToggle">
        <img src="<?= $flag_paths[$current_lang] ?? '' ?>" alt="<?= $current_lang ?>" class="lang-flag-img">
        <span class="lang-name"><?= Language::getLanguageName($current_lang) ?></span>
        <svg class="lang-arrow" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    <div class="lang-dropdown" id="langDropdown">
        <?php foreach ($available_langs as $code => $name): ?>
            <?php if ($code !== $current_lang): ?>
                <?php
                $current_url = $_SERVER['REQUEST_URI'];
                $url_parts = parse_url($current_url);
                $query_params = [];
                if (isset($url_parts['query'])) {
                    parse_str($url_parts['query'], $query_params);
                }
                $query_params['lang'] = $code;
                $new_url = $url_parts['path'] . '?' . http_build_query($query_params);
                ?>
                <a href="<?= $new_url ?>" class="lang-option">
                    <img src="<?= $flag_paths[$code] ?? '' ?>" alt="<?= $code ?>" class="lang-flag-img">
                    <span class="lang-name"><?= $name ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<style>
.language-switcher {
    position: relative;
    display: inline-block;
    z-index: 100;
}

.lang-current {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    color: #0a0a0a;
    font-size: 14px;
    font-weight: 500;
    font-family: inherit;
    outline: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.lang-current:hover {
    background: #ffffff;
    border-color: rgba(0, 0, 0, 0.12);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.lang-current.active {
    background: #ffffff;
    border-color: #2563eb;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
}

.lang-flag-img {
    width: 20px;
    height: 15px;
    object-fit: cover;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}

.lang-name {
    font-weight: 500;
    white-space: nowrap;
    min-width: 60px;
}

.lang-arrow {
    width: 12px;
    height: 12px;
    color: #525252;
    transition: transform 0.3s;
    flex-shrink: 0;
}

.lang-current.active .lang-arrow {
    transform: rotate(180deg);
}

.lang-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    min-width: 180px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px) scale(0.95);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1000;
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.06);
}

.language-switcher.active .lang-dropdown,
.lang-dropdown:hover {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

.lang-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    text-decoration: none;
    color: #0a0a0a;
    transition: all 0.2s;
    background: white;
    border: none;
    width: 100%;
    text-align: left;
    font-size: 14px;
    font-weight: 500;
}

.lang-option:first-child {
    padding-top: 16px;
}

.lang-option:last-child {
    padding-bottom: 16px;
}

.lang-option:hover {
    background: #f8f9fa;
    color: #2563eb;
}

.lang-option .lang-flag-img {
    width: 24px;
    height: 18px;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .lang-current {
        padding: 6px 10px;
        font-size: 13px;
    }
    
    .lang-name {
        display: none;
    }
    
    .lang-dropdown {
        min-width: 140px;
    }
    
    .lang-option {
        padding: 10px 14px;
        font-size: 13px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const langToggle = document.getElementById('langToggle');
    const langDropdown = document.getElementById('langDropdown');
    const langSwitcher = document.querySelector('.language-switcher');
    
    // Toggle dropdown
    if (langToggle) {
        langToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            langSwitcher.classList.toggle('active');
            langToggle.classList.toggle('active');
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!langSwitcher.contains(e.target)) {
            langSwitcher.classList.remove('active');
            if (langToggle) langToggle.classList.remove('active');
        }
    });
    
    // Close dropdown when selecting a language
    const langOptions = document.querySelectorAll('.lang-option');
    langOptions.forEach(option => {
        option.addEventListener('click', function() {
            langSwitcher.classList.remove('active');
            if (langToggle) langToggle.classList.remove('active');
        });
    });
});
</script>
