-- Migration: exam_type maydonini users jadvaliga qo'shish
-- Online/Offline imtihon turini saqlash uchun

ALTER TABLE users 
ADD COLUMN exam_type ENUM('online', 'offline') NULL 
AFTER exam_date;

-- Eslatma: Bu migration faylini ma'lumotlar bazasida bajarish kerak
-- MySQL/MariaDB da: mysql -u username -p database_name < database_migration_add_exam_type.sql
-- Yoki phpMyAdmin orqali bajarish mumkin

