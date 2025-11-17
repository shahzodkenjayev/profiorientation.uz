<?php
// Admin ma'lumotlarini olish
$admin_info = null;
if (isset($_SESSION['admin_id'])) {
    try {
        require_once __DIR__ . '/../../config/database.php';
        $db = getDB();
        $stmt = $db->prepare("SELECT id, username, full_name, email, role, last_login FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin_info = $stmt->fetch();
    } catch (Exception $e) {
        // Xatolik bo'lsa, session ma'lumotlaridan foydalanish
        $admin_info = [
            'username' => $_SESSION['admin_username'] ?? 'Admin',
            'full_name' => $_SESSION['admin_username'] ?? 'Admin',
            'role' => $_SESSION['admin_role'] ?? 'admin'
        ];
    }
}

// Til o'zgartirish uchun
require_once __DIR__ . '/../../includes/language.php';
// Til o'zgarishini tekshirish va yangilash (GET parametridan)
Language::init();
$current_lang = Language::current();
$available_langs = Language::getAvailableLanguages();
$flag_paths = [
    'uz' => ASSETS_PATH . 'images/flags/uz.svg',
    'ru' => ASSETS_PATH . 'images/flags/ru.svg',
    'en' => ASSETS_PATH . 'images/flags/en.svg',
    'tr' => ASSETS_PATH . 'images/flags/tr.svg'
];
?>
<nav class="admin-sidebar">
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
    </div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">📊 Dashboard</a></li>
        <li><a href="exam_dates.php" class="<?= basename($_SERVER['PHP_SELF']) === 'exam_dates.php' ? 'active' : '' ?>">📅 Imtihon Kunlari</a></li>
        <li><a href="users.php" class="<?= basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : '' ?>">👥 Foydalanuvchilar</a></li>
        <li><a href="questions.php" class="<?= basename($_SERVER['PHP_SELF']) === 'questions.php' ? 'active' : '' ?>">❓ Savollar</a></li>
        <li><a href="results.php" class="<?= basename($_SERVER['PHP_SELF']) === 'results.php' ? 'active' : '' ?>">📈 Natijalar</a></li>
        <li><a href="payments.php" class="<?= basename($_SERVER['PHP_SELF']) === 'payments.php' ? 'active' : '' ?>">💳 To'lovlar</a></li>
        <li><a href="settings.php" class="<?= basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : '' ?>">⚙️ Sozlamalar</a></li>
        <li><a href="logout.php" class="logout-link">🚪 Chiqish</a></li>
    </ul>
    
    <!-- Sidebar footer: Til o'zgartirish va foydalanuvchi ma'lumotlari -->
    <div class="sidebar-footer">
        <!-- Til o'zgartirish -->
        <div class="sidebar-language-switcher">
            <div class="language-switcher-compact">
                <button type="button" class="lang-current-compact" id="adminLangToggle">
                    <img src="<?= $flag_paths[$current_lang] ?? '' ?>" alt="<?= $current_lang ?>" class="lang-flag-img-compact">
                    <span class="lang-name-compact"><?= Language::getLanguageName($current_lang) ?></span>
                    <svg class="lang-arrow-compact" width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="lang-dropdown-compact" id="adminLangDropdown">
                    <?php foreach ($available_langs as $code => $name): ?>
                        <?php if ($code !== $current_lang): ?>
                            <?php
                            // Joriy sahifa URL'ini olish
                            $current_url = $_SERVER['REQUEST_URI'];
                            $url_parts = parse_url($current_url);
                            $query_params = [];
                            
                            // Mavjud query parametrlarni olish
                            if (isset($url_parts['query'])) {
                                parse_str($url_parts['query'], $query_params);
                            }
                            
                            // Til parametrini qo'shish/yangilash
                            $query_params['lang'] = $code;
                            
                            // Yangi URL yaratish
                            $new_url = $url_parts['path'];
                            if (!empty($query_params)) {
                                $new_url .= '?' . http_build_query($query_params);
                            }
                            ?>
                            <a href="<?= $new_url ?>" class="lang-option-compact">
                                <img src="<?= $flag_paths[$code] ?? '' ?>" alt="<?= $code ?>" class="lang-flag-img-compact">
                                <span class="lang-name-compact"><?= $name ?></span>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Foydalanuvchi ma'lumotlari -->
        <div class="sidebar-user-info">
            <a href="profile.php" class="user-profile-link" id="userProfileLink">
                <div class="user-avatar">
                    <?= strtoupper(mb_substr($admin_info['full_name'] ?? $admin_info['username'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="user-details">
                    <div class="user-name"><?= htmlspecialchars($admin_info['full_name'] ?? $admin_info['username'] ?? 'Admin') ?></div>
                    <div class="user-role"><?= htmlspecialchars($admin_info['role'] === 'super_admin' ? 'Super Admin' : 'Admin') ?></div>
                </div>
            </a>
        </div>
    </div>
</nav>

<style>
.sidebar-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.1);
}

.sidebar-language-switcher {
    margin-bottom: 12px;
}

.language-switcher-compact {
    position: relative;
    width: 100%;
}

.lang-current-compact {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 8px 12px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    color: #fff;
    font-size: 13px;
    font-weight: 500;
    outline: none;
}

.lang-current-compact:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.3);
}

.lang-current-compact.active {
    background: rgba(255, 255, 255, 0.2);
}

.lang-flag-img-compact {
    width: 18px;
    height: 13px;
    object-fit: cover;
    border-radius: 2px;
    flex-shrink: 0;
}

.lang-name-compact {
    flex: 1;
    text-align: left;
    font-weight: 500;
}

.lang-arrow-compact {
    width: 10px;
    height: 10px;
    color: rgba(255, 255, 255, 0.7);
    transition: transform 0.3s;
    flex-shrink: 0;
}

.lang-current-compact.active .lang-arrow-compact {
    transform: rotate(180deg);
}

.lang-dropdown-compact {
    position: absolute;
    bottom: calc(100% + 8px);
    left: 0;
    right: 0;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px) scale(0.95);
    transition: all 0.3s;
    z-index: 1000;
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.1);
}

.language-switcher-compact.active .lang-dropdown-compact {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

.lang-option-compact {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    text-decoration: none;
    color: #333;
    transition: all 0.2s;
    background: white;
    border: none;
    width: 100%;
    text-align: left;
    font-size: 13px;
    font-weight: 500;
}

.lang-option-compact:hover {
    background: #f5f5f5;
    color: #2563eb;
}

.sidebar-user-info {
    margin-top: 12px;
}

.user-profile-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    text-decoration: none;
    color: #fff;
    transition: all 0.3s;
    cursor: pointer;
}

.user-profile-link:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-1px);
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 600;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.user-details {
    flex: 1;
    min-width: 0;
}

.user-name {
    font-size: 14px;
    font-weight: 600;
    color: #fff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 2px;
}

.user-role {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar-footer {
        padding: 12px;
    }
    
    .lang-name-compact {
        font-size: 12px;
    }
    
    .user-name {
        font-size: 13px;
    }
    
    .user-role {
        font-size: 11px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const langToggle = document.getElementById('adminLangToggle');
    const langDropdown = document.getElementById('adminLangDropdown');
    const langSwitcher = document.querySelector('.language-switcher-compact');
    
    if (langToggle) {
        langToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            langSwitcher.classList.toggle('active');
            langToggle.classList.toggle('active');
        });
    }
    
    document.addEventListener('click', function(e) {
        if (langSwitcher && !langSwitcher.contains(e.target)) {
            langSwitcher.classList.remove('active');
            if (langToggle) langToggle.classList.remove('active');
        }
    });
});
</script>

