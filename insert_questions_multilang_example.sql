-- Ko'p tilli test savollari - MISOL (2 ta savol)
-- Kasb tanlash tizimi uchun
-- Bu misol ko'rsatadi qanday qilib savollarni 4 tilda (uz, ru, en, tr) qo'shish

USE kasb_tanlash;

-- ============================================
-- MISOL 1: Qiziqishlar kategoriyasi
-- ============================================

-- O'ZBEK TILIDA
INSERT INTO questions (category, question_text, question_type, language, order_number) VALUES
('interests', 'Qaysi faoliyat sizni eng ko\'p qiziqtiradi?', 'multiple_choice', 'uz', 1);

SET @q1_uz = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, language, order_number) VALUES
(@q1_uz, 'San\'at va ijodiy ishlar (rasm chizish, yozish, musiqiy asboblar)', 10, 'artist,writer,musician,designer', 'uz', 1),
(@q1_uz, 'Fan va texnologiya (matematika, fizika, kompyuterlar)', 10, 'engineer,scientist,programmer,mathematician', 'uz', 2),
(@q1_uz, 'Tibbiyot va sog\'liqni saqlash (odamlarni davolash, yordam berish)', 10, 'doctor,nurse,psychologist,pharmacist', 'uz', 3),
(@q1_uz, 'Biznes va moliya (savdo, marketing, investitsiya)', 10, 'businessman,marketer,accountant,manager', 'uz', 4),
(@q1_uz, 'Ta\'lim va tarbiya (bolalarni o\'qitish, tarbiyalash)', 10, 'teacher,educator,trainer,professor', 'uz', 5),
(@q1_uz, 'Qonun va huquq (qonunlarni o\'rganish, adolat)', 10, 'lawyer,judge,prosecutor,notary', 'uz', 6);

-- RUS TILIDA
INSERT INTO questions (category, question_text, question_type, language, order_number) VALUES
('interests', 'Какая деятельность вас больше всего интересует?', 'multiple_choice', 'ru', 1);

SET @q1_ru = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, language, order_number) VALUES
(@q1_ru, 'Искусство и творчество (рисование, письмо, музыкальные инструменты)', 10, 'artist,writer,musician,designer', 'ru', 1),
(@q1_ru, 'Наука и технологии (математика, физика, компьютеры)', 10, 'engineer,scientist,programmer,mathematician', 'ru', 2),
(@q1_ru, 'Медицина и здравоохранение (лечение людей, помощь)', 10, 'doctor,nurse,psychologist,pharmacist', 'ru', 3),
(@q1_ru, 'Бизнес и финансы (торговля, маркетинг, инвестиции)', 10, 'businessman,marketer,accountant,manager', 'ru', 4),
(@q1_ru, 'Образование и воспитание (обучение детей)', 10, 'teacher,educator,trainer,professor', 'ru', 5),
(@q1_ru, 'Право и закон (изучение законов, справедливость)', 10, 'lawyer,judge,prosecutor,notary', 'ru', 6);

-- INGLIZ TILIDA
INSERT INTO questions (category, question_text, question_type, language, order_number) VALUES
('interests', 'What activity interests you the most?', 'multiple_choice', 'en', 1);

SET @q1_en = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, language, order_number) VALUES
(@q1_en, 'Arts and creative work (drawing, writing, musical instruments)', 10, 'artist,writer,musician,designer', 'en', 1),
(@q1_en, 'Science and technology (mathematics, physics, computers)', 10, 'engineer,scientist,programmer,mathematician', 'en', 2),
(@q1_en, 'Medicine and healthcare (treating people, helping)', 10, 'doctor,nurse,psychologist,pharmacist', 'en', 3),
(@q1_en, 'Business and finance (trade, marketing, investment)', 10, 'businessman,marketer,accountant,manager', 'en', 4),
(@q1_en, 'Education and upbringing (teaching children)', 10, 'teacher,educator,trainer,professor', 'en', 5),
(@q1_en, 'Law and justice (studying laws, justice)', 10, 'lawyer,judge,prosecutor,notary', 'en', 6);

-- TURK TILIDA
INSERT INTO questions (category, question_text, question_type, language, order_number) VALUES
('interests', 'Hangi aktivite sizi en çok ilgilendiriyor?', 'multiple_choice', 'tr', 1);

SET @q1_tr = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, language, order_number) VALUES
(@q1_tr, 'Sanat ve yaratıcı işler (çizim, yazma, müzik aletleri)', 10, 'artist,writer,musician,designer', 'tr', 1),
(@q1_tr, 'Bilim ve teknoloji (matematik, fizik, bilgisayarlar)', 10, 'engineer,scientist,programmer,mathematician', 'tr', 2),
(@q1_tr, 'Tıp ve sağlık (insanları tedavi etme, yardım etme)', 10, 'doctor,nurse,psychologist,pharmacist', 'tr', 3),
(@q1_tr, 'İş ve finans (ticaret, pazarlama, yatırım)', 10, 'businessman,marketer,accountant,manager', 'tr', 4),
(@q1_tr, 'Eğitim ve yetiştirme (çocukları öğretme)', 10, 'teacher,educator,trainer,professor', 'tr', 5),
(@q1_tr, 'Hukuk ve adalet (yasaları inceleme, adalet)', 10, 'lawyer,judge,prosecutor,notary', 'tr', 6);

-- ============================================
-- MISOL 2: Qobiliyatlar kategoriyasi
-- ============================================

-- O'ZBEK TILIDA
INSERT INTO questions (category, question_text, question_type, language, order_number) VALUES
('abilities', 'Sizning eng kuchli tomoningiz nima?', 'multiple_choice', 'uz', 6);

SET @q2_uz = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, language, order_number) VALUES
(@q2_uz, 'Matematik masalalarni yechish', 10, 'mathematician,engineer,programmer,physicist', 'uz', 1),
(@q2_uz, 'Ijodiy fikrlash va yaratish', 10, 'artist,designer,writer,architect', 'uz', 2),
(@q2_uz, 'Odamlar bilan muloqot qilish', 10, 'psychologist,teacher,manager,hr_specialist', 'uz', 3),
(@q2_uz, 'Tahlil qilish va tadqiq qilish', 10, 'scientist,researcher,analyst,engineer', 'uz', 4),
(@q2_uz, 'Tashkilotchilik va boshqarish', 10, 'manager,businessman,organizer,leader', 'uz', 5),
(@q2_uz, 'Yordam berish va g\'amxo\'rlik qilish', 10, 'doctor,nurse,teacher,social_worker', 'uz', 6);

-- RUS TILIDA
INSERT INTO questions (category, question_text, question_type, language, order_number) VALUES
('abilities', 'Какая ваша самая сильная сторона?', 'multiple_choice', 'ru', 6);

SET @q2_ru = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, language, order_number) VALUES
(@q2_ru, 'Решение математических задач', 10, 'mathematician,engineer,programmer,physicist', 'ru', 1),
(@q2_ru, 'Творческое мышление и создание', 10, 'artist,designer,writer,architect', 'ru', 2),
(@q2_ru, 'Общение с людьми', 10, 'psychologist,teacher,manager,hr_specialist', 'ru', 3),
(@q2_ru, 'Анализ и исследование', 10, 'scientist,researcher,analyst,engineer', 'ru', 4),
(@q2_ru, 'Организация и управление', 10, 'manager,businessman,organizer,leader', 'ru', 5),
(@q2_ru, 'Помощь и забота', 10, 'doctor,nurse,teacher,social_worker', 'ru', 6);

-- INGLIZ TILIDA
INSERT INTO questions (category, question_text, question_type, language, order_number) VALUES
('abilities', 'What is your strongest side?', 'multiple_choice', 'en', 6);

SET @q2_en = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, language, order_number) VALUES
(@q2_en, 'Solving mathematical problems', 10, 'mathematician,engineer,programmer,physicist', 'en', 1),
(@q2_en, 'Creative thinking and creation', 10, 'artist,designer,writer,architect', 'en', 2),
(@q2_en, 'Communicating with people', 10, 'psychologist,teacher,manager,hr_specialist', 'en', 3),
(@q2_en, 'Analysis and research', 10, 'scientist,researcher,analyst,engineer', 'en', 4),
(@q2_en, 'Organization and management', 10, 'manager,businessman,organizer,leader', 'en', 5),
(@q2_en, 'Helping and caring', 10, 'doctor,nurse,teacher,social_worker', 'en', 6);

-- TURK TILIDA
INSERT INTO questions (category, question_text, question_type, language, order_number) VALUES
('abilities', 'En güçlü yönünüz nedir?', 'multiple_choice', 'tr', 6);

SET @q2_tr = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, language, order_number) VALUES
(@q2_tr, 'Matematik problemleri çözme', 10, 'mathematician,engineer,programmer,physicist', 'tr', 1),
(@q2_tr, 'Yaratıcı düşünme ve yaratma', 10, 'artist,designer,writer,architect', 'tr', 2),
(@q2_tr, 'İnsanlarla iletişim kurma', 10, 'psychologist,teacher,manager,hr_specialist', 'tr', 3),
(@q2_tr, 'Analiz ve araştırma', 10, 'scientist,researcher,analyst,engineer', 'tr', 4),
(@q2_tr, 'Organizasyon ve yönetim', 10, 'manager,businessman,organizer,leader', 'tr', 5),
(@q2_tr, 'Yardım etme ve bakım', 10, 'doctor,nurse,teacher,social_worker', 'tr', 6);

-- ============================================
-- YAKUN
-- ============================================

-- Jami: 2 ta savol × 4 til = 8 ta savol
-- Har bir savol uchun 6 ta javob varianti
-- Jami: 8 × 6 = 48 ta javob varianti

-- Eslatma:
-- 1. Har bir til uchun alohida INSERT qilish kerak
-- 2. language maydoni har bir INSERT da belgilanishi kerak
-- 3. order_number bir xil bo'lishi mumkin (chunki har bir til uchun alohida)
-- 4. profession_tags bir xil bo'lishi mumkin (chunki kasb nomlari ingliz tilida)

