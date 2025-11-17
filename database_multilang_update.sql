-- Database strukturasini ko'p tilli qo'llab-quvvatlash uchun yangilash
-- Questions va Answer Options jadvallariga language maydoni qo'shish

USE kasb_tanlash;

-- Questions jadvaliga language maydoni qo'shish
-- MySQL'da IF NOT EXISTS qo'llab-quvvatlanmaydi, shuning uchun avval tekshiramiz
SET @dbname = DATABASE();
SET @tablename = 'questions';
SET @columnname = 'language';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(10) DEFAULT ''uz'' AFTER question_text')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Questions jadvaliga language index qo'shish
SET @indexname = 'idx_language';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (index_name = @indexname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD INDEX ', @indexname, ' (', @columnname, ')')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Answer Options jadvaliga language maydoni qo'shish
SET @tablename = 'answer_options';
SET @columnname = 'language';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(10) DEFAULT ''uz'' AFTER option_text')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Answer Options jadvaliga language index qo'shish
SET @indexname = 'idx_language';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (index_name = @indexname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD INDEX ', @indexname, ' (', @columnname, ')')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Mavjud ma'lumotlarni o'zbek tiliga belgilash (agar bo'sh bo'lsa)
UPDATE questions SET language = 'uz' WHERE language IS NULL OR language = '';
UPDATE answer_options SET language = 'uz' WHERE language IS NULL OR language = '';

