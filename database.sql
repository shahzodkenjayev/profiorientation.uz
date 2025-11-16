-- Kasb tanlash tizimi database struktura

CREATE DATABASE IF NOT EXISTS kasb_tanlash CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kasb_tanlash;

-- Foydalanuvchilar jadvali
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(20) UNIQUE,
    telegram_id VARCHAR(100) UNIQUE,
    google_id VARCHAR(100) UNIQUE,
    email VARCHAR(100),
    full_name VARCHAR(255),
    class_number INT(2),
    school_name VARCHAR(255),
    login_type ENUM('phone', 'telegram', 'google') NOT NULL,
    exam_date DATETIME,
    test_completed TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_phone (phone),
    INDEX idx_telegram (telegram_id),
    INDEX idx_google (google_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Test savollari jadvali
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('multiple_choice', 'scale', 'yes_no') DEFAULT 'multiple_choice',
    weight DECIMAL(3,2) DEFAULT 1.00,
    order_number INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Javoblar variantlari
CREATE TABLE IF NOT EXISTS answer_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_text TEXT NOT NULL,
    score INT NOT NULL,
    profession_tags VARCHAR(255),
    order_number INT,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    INDEX idx_question (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Foydalanuvchi javoblari
CREATE TABLE IF NOT EXISTS user_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    question_id INT NOT NULL,
    answer_option_id INT,
    answer_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (answer_option_id) REFERENCES answer_options(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_question (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Test natijalari
CREATE TABLE IF NOT EXISTS test_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    profession_name VARCHAR(255),
    profession_description TEXT,
    match_percentage DECIMAL(5,2),
    personality_type VARCHAR(100),
    strengths TEXT,
    recommendations TEXT,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin sozlamalari
CREATE TABLE IF NOT EXISTS admin_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adminlar jadvali
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    email VARCHAR(100),
    role ENUM('super_admin', 'admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Imtihon kunlari
CREATE TABLE IF NOT EXISTS exam_dates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_date DATETIME NOT NULL,
    max_participants INT DEFAULT 100,
    current_participants INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_date (exam_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- To'lovlar jadvali
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_id VARCHAR(100) UNIQUE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('payme', 'click', 'cash') NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
    transaction_id VARCHAR(255),
    payment_data TEXT,
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_order (order_id),
    INDEX idx_status (payment_status),
    INDEX idx_method (payment_method)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- To'lov sozlamalari
CREATE TABLE IF NOT EXISTS payment_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Boshlang'ich sozlamalar
INSERT INTO admin_settings (setting_key, setting_value, description) VALUES
('default_exam_date', '', 'Default imtihon sanasi'),
('test_duration', '60', 'Test davomiyligi (daqiqa)'),
('min_questions', '30', 'Minimal savollar soni'),
('test_price', '1900000', 'Test narxi (so\'m)'),
('discount_price', '950000', 'Chegirmali narx (so\'m)'),
('discount_limit', '100', 'Chegirma limiti (foydalanuvchi soni)');

-- To'lov sozlamalari
INSERT INTO payment_settings (setting_key, setting_value, description) VALUES
('payme_merchant_id', '', 'Payme Merchant ID'),
('payme_secret_key', '', 'Payme Secret Key'),
('payme_test_mode', '1', 'Payme test rejimi (1-yes, 0-no)'),
('click_merchant_id', '', 'Click Merchant ID'),
('click_service_id', '', 'Click Service ID'),
('click_secret_key', '', 'Click Secret Key'),
('click_test_mode', '1', 'Click test rejimi (1-yes, 0-no)');

-- Til sozlamalari
INSERT INTO admin_settings (setting_key, setting_value, description) VALUES
('default_language', 'uz', 'Default til (uz, ru, en, tr)');

-- Default admin (username: admin, password: admin123 - o'zgartirish kerak!)
-- Parolni o'zgartirish uchun: php -r "echo password_hash('yangi_parol', PASSWORD_DEFAULT);"
-- admin123 hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi (bu "password" uchun)
-- Yangi admin123 hash yaratish kerak
INSERT INTO admins (username, password, full_name, role) VALUES
('admin', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'Bosh Admin', 'super_admin');

