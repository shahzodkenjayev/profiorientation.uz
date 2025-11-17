-- Admin parolini yangilash SQL kodi
-- Username: admin
-- Password: admin123

-- Variant 1: Agar admin mavjud bo'lsa, parolini yangilash
UPDATE admins 
SET password = '$2y$10$IXZX0kWe08E0mRPZ3w4STuSKTyr/2weKNtyryNMXj15ZOqNPo1ujK' 
WHERE username = 'admin';

-- Variant 2: Agar admin mavjud bo'lmasa, yangi admin yaratish
INSERT INTO admins (username, password, full_name, role) 
VALUES ('admin', '$2y$10$IXZX0kWe08E0mRPZ3w4STuSKTyr/2weKNtyryNMXj15ZOqNPo1ujK', 'Bosh Admin', 'super_admin')
ON DUPLICATE KEY UPDATE 
    password = '$2y$10$IXZX0kWe08E0mRPZ3w4STuSKTyr/2weKNtyryNMXj15ZOqNPo1ujK',
    full_name = 'Bosh Admin',
    role = 'super_admin';

-- Eslatma: 
-- Username: admin
-- Password: admin123
-- Bu hash PHP password_hash() funksiyasi orqali yaratilgan

