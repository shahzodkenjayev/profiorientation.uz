-- Database strukturasini ko'p tilli qo'llab-quvvatlash uchun yangilash
-- Questions va Answer Options jadvallariga language maydoni qo'shish

USE kasb_tanlash;

-- Questions jadvaliga language maydoni qo'shish
ALTER TABLE questions 
ADD COLUMN IF NOT EXISTS language VARCHAR(10) DEFAULT 'uz' AFTER question_text,
ADD INDEX IF NOT EXISTS idx_language (language);

-- Answer Options jadvaliga language maydoni qo'shish
ALTER TABLE answer_options 
ADD COLUMN IF NOT EXISTS language VARCHAR(10) DEFAULT 'uz' AFTER option_text,
ADD INDEX IF NOT EXISTS idx_language (language);

-- Mavjud ma'lumotlarni o'zbek tiliga belgilash (agar bo'sh bo'lsa)
UPDATE questions SET language = 'uz' WHERE language IS NULL OR language = '';
UPDATE answer_options SET language = 'uz' WHERE language IS NULL OR language = '';

