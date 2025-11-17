-- Professional psixologik test savollari
-- Kasb tanlash tizimi uchun

USE kasb_tanlash;

-- Kategoriyalar:
-- 1. Qiziqishlar (Interests)
-- 2. Qobiliyatlar (Abilities)
-- 3. Temperament (Temperament)
-- 4. Xarakter (Character)
-- 5. Stressga chidamlilik (Stress tolerance)
-- 6. Muloqot ko'nikmalari (Communication skills)
-- 7. Analitik fikrlash (Analytical thinking)
-- 8. Ijodiy qobiliyatlar (Creativity)

-- ============================================
-- 1. QIZIQISHLAR (Interests) - Savollar 1-10
-- ============================================

-- Savol 1
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('interests', 'Qaysi faoliyat sizni eng ko\'p qiziqtiradi?', 'multiple_choice', 1);

SET @q1 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q1, 'San\'at va ijodiy ishlar (rasm chizish, yozish, musiqiy asboblar)', 10, 'artist,writer,musician,designer', 1),
(@q1, 'Fan va texnologiya (matematika, fizika, kompyuterlar)', 10, 'engineer,scientist,programmer,mathematician', 2),
(@q1, 'Tibbiyot va sog\'liqni saqlash (odamlarni davolash, yordam berish)', 10, 'doctor,nurse,psychologist,pharmacist', 3),
(@q1, 'Biznes va moliya (savdo, marketing, investitsiya)', 10, 'businessman,marketer,accountant,manager', 4),
(@q1, 'Ta\'lim va tarbiya (bolalarni o\'qitish, tarbiyalash)', 10, 'teacher,educator,trainer,professor', 5),
(@q1, 'Qonun va huquq (qonunlarni o\'rganish, adolat)', 10, 'lawyer,judge,prosecutor,notary', 6);

-- Savol 2
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('interests', 'Bo\'sh vaqtingizda nima qilasiz?', 'multiple_choice', 2);

SET @q2 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q2, 'Kitob o\'qish yoki yozish', 8, 'writer,teacher,researcher,editor', 1),
(@q2, 'Kompyuter o\'yinlari yoki dasturlash', 8, 'programmer,game_developer,it_specialist', 2),
(@q2, 'Sport yoki jismoniy mashqlar', 8, 'athlete,coach,trainer,physiotherapist', 3),
(@q2, 'Ijodiy loyihalar (rasm, musiqa, dizayn)', 8, 'artist,designer,musician,architect', 4),
(@q2, 'Do\'stlar bilan uchrashish va muloqot', 8, 'psychologist,social_worker,manager,hr_specialist', 5),
(@q2, 'Yangi narsalarni o\'rganish va tadqiq qilish', 8, 'scientist,researcher,engineer,analyst', 6);

-- Savol 3
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('interests', 'Qaysi fan sizga eng qiziq?', 'multiple_choice', 3);

SET @q3 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q3, 'Matematika va fizika', 9, 'engineer,physicist,mathematician,programmer', 1),
(@q3, 'Biologiya va kimyo', 9, 'doctor,biologist,chemist,pharmacist', 2),
(@q3, 'Tarix va adabiyot', 9, 'historian,writer,teacher,journalist', 3),
(@q3, 'Ijtimoiy fanlar (psixologiya, sotsiologiya)', 9, 'psychologist,sociologist,teacher,social_worker', 4),
(@q3, 'San\'at va dizayn', 9, 'artist,designer,architect,interior_designer', 5),
(@q3, 'Iqtisod va menejment', 9, 'economist,businessman,manager,accountant', 6);

-- Savol 4
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('interests', 'Qaysi kasb sizga eng jozibali ko\'rinadi?', 'multiple_choice', 4);

SET @q4 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q4, 'Shifokor yoki hamshira', 10, 'doctor,nurse,pharmacist,psychologist', 1),
(@q4, 'Muhandis yoki dasturchi', 10, 'engineer,programmer,it_specialist,architect', 2),
(@q4, 'O\'qituvchi yoki professor', 10, 'teacher,professor,educator,trainer', 3),
(@q4, 'Yurist yoki prokuror', 10, 'lawyer,judge,prosecutor,notary', 4),
(@q4, 'Biznesmen yoki menejer', 10, 'businessman,manager,marketer,accountant', 5),
(@q4, 'Rassom yoki dizayner', 10, 'artist,designer,architect,interior_designer', 6);

-- Savol 5
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('interests', 'Qaysi sohada ishlashni xohlaysiz?', 'multiple_choice', 5);

SET @q5 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q5, 'Tibbiyot va sog\'liqni saqlash', 9, 'doctor,nurse,pharmacist,psychologist', 1),
(@q5, 'Texnologiya va IT', 9, 'programmer,engineer,it_specialist,data_scientist', 2),
(@q5, 'Ta\'lim va tarbiya', 9, 'teacher,professor,educator,trainer', 3),
(@q5, 'Qonun va huquq', 9, 'lawyer,judge,prosecutor,notary', 4),
(@q5, 'Biznes va moliya', 9, 'businessman,manager,marketer,accountant', 5),
(@q5, 'San\'at va madaniyat', 9, 'artist,designer,musician,journalist', 6);

-- ============================================
-- 2. QOBILIYATLAR (Abilities) - Savollar 6-15
-- ============================================

-- Savol 6
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('abilities', 'Sizning eng kuchli tomoningiz nima?', 'multiple_choice', 6);

SET @q6 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q6, 'Matematik masalalarni yechish', 10, 'mathematician,engineer,programmer,physicist', 1),
(@q6, 'Ijodiy fikrlash va yaratish', 10, 'artist,designer,writer,architect', 2),
(@q6, 'Odamlar bilan muloqot qilish', 10, 'psychologist,teacher,manager,hr_specialist', 3),
(@q6, 'Tahlil qilish va tadqiq qilish', 10, 'scientist,researcher,analyst,engineer', 4),
(@q6, 'Tashkilotchilik va boshqarish', 10, 'manager,businessman,organizer,leader', 5),
(@q6, 'Yordam berish va g\'amxo\'rlik qilish', 10, 'doctor,nurse,teacher,social_worker', 6);

-- Savol 7
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('abilities', 'Qaysi ko\'nikma sizda eng yaxshi rivojlangan?', 'multiple_choice', 7);

SET @q7 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q7, 'Mantiqiy fikrlash', 9, 'engineer,programmer,mathematician,analyst', 1),
(@q7, 'Ijodiy yondashuv', 9, 'artist,designer,writer,architect', 2),
(@q7, 'Muloqot va nutq', 9, 'teacher,lawyer,manager,psychologist', 3),
(@q7, 'Tahlil va tadqiq', 9, 'scientist,researcher,analyst,engineer', 4),
(@q7, 'Boshqarish va tashkilotchilik', 9, 'manager,businessman,organizer,leader', 5),
(@q7, 'Empatiya va yordam berish', 9, 'doctor,nurse,psychologist,social_worker', 6);

-- Savol 8
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('abilities', 'Qaysi vazifani oson bajarasiz?', 'multiple_choice', 8);

SET @q8 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q8, 'Murakkab masalalarni yechish', 8, 'engineer,programmer,mathematician,analyst', 1),
(@q8, 'Yangi g\'oyalar yaratish', 8, 'artist,designer,writer,architect', 2),
(@q8, 'Guruh bilan ishlash', 8, 'manager,teacher,psychologist,hr_specialist', 3),
(@q8, 'Ma\'lumotlarni tahlil qilish', 8, 'scientist,researcher,analyst,engineer', 4),
(@q8, 'Loyihalarni boshqarish', 8, 'manager,businessman,organizer,project_manager', 5),
(@q8, 'Boshqalarga yordam berish', 8, 'doctor,nurse,teacher,social_worker', 6);

-- Savol 9
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('abilities', 'Qaysi fan sizga eng oson keladi?', 'multiple_choice', 9);

SET @q9 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q9, 'Matematika', 9, 'mathematician,engineer,programmer,physicist', 1),
(@q9, 'Til va adabiyot', 9, 'writer,teacher,journalist,translator', 2),
(@q9, 'Ijtimoiy fanlar', 9, 'psychologist,sociologist,teacher,lawyer', 3),
(@q9, 'Tabiiy fanlar (biologiya, kimyo)', 9, 'doctor,biologist,chemist,pharmacist', 4),
(@q9, 'San\'at va dizayn', 9, 'artist,designer,architect,interior_designer', 5),
(@q9, 'Iqtisod va menejment', 9, 'economist,businessman,manager,accountant', 6);

-- Savol 10
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('abilities', 'Qaysi ish turi sizga mos keladi?', 'multiple_choice', 10);

SET @q10 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q10, 'Yolg\'iz ishlash (mustaqil)', 8, 'programmer,writer,researcher,analyst', 1),
(@q10, 'Guruh bilan ishlash', 8, 'manager,teacher,psychologist,hr_specialist', 2),
(@q10, 'Ijodiy ishlar', 8, 'artist,designer,writer,architect', 3),
(@q10, 'Tahlil va tadqiq', 8, 'scientist,researcher,analyst,engineer', 4),
(@q10, 'Boshqarish va tashkilotchilik', 8, 'manager,businessman,organizer,leader', 5),
(@q10, 'Yordam berish va xizmat ko\'rsatish', 8, 'doctor,nurse,teacher,social_worker', 6);

-- ============================================
-- 3. TEMPERAMENT (Temperament) - Savollar 11-20
-- ============================================

-- Savol 11
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('temperament', 'Siz qanday tempoda ishlaysiz?', 'multiple_choice', 11);

SET @q11 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q11, 'Juda tez va faol', 7, 'manager,athlete,salesperson,entrepreneur', 1),
(@q11, 'O\'rtacha tezlikda', 7, 'teacher,engineer,doctor,analyst', 2),
(@q11, 'Sekin va diqqat bilan', 7, 'researcher,programmer,accountant,designer', 3),
(@q11, 'Vaziyatga qarab o\'zgaradi', 7, 'manager,psychologist,lawyer,consultant', 4);

-- Savol 12
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('temperament', 'Qanday muhitda o\'zingizni qulay his qilasiz?', 'multiple_choice', 12);

SET @q12 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q12, 'Faol va shovqinli muhit', 8, 'manager,salesperson,teacher,athlete', 1),
(@q12, 'Tinch va tashkilotli muhit', 8, 'programmer,researcher,accountant,librarian', 2),
(@q12, 'Ijodiy va erkin muhit', 8, 'artist,designer,writer,architect', 3),
(@q12, 'Dinamik va o\'zgaruvchan muhit', 8, 'doctor,lawyer,manager,consultant', 4);

-- Savol 13
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('temperament', 'Qanday vaziyatlarda o\'zingizni qulay his qilasiz?', 'multiple_choice', 13);

SET @q13 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q13, 'Ko\'p odamlar bilan birga', 8, 'teacher,manager,psychologist,salesperson', 1),
(@q13, 'Yolg\'iz yoki kichik guruhda', 8, 'programmer,writer,researcher,designer', 2),
(@q13, 'Ijodiy loyihalarda', 8, 'artist,designer,writer,architect', 3),
(@q13, 'Murakkab masalalarni yechishda', 8, 'engineer,programmer,analyst,researcher', 4);

-- Savol 14
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('temperament', 'Qanday ish rejimi sizga mos?', 'multiple_choice', 14);

SET @q14 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q14, 'Doimiy va barqaror', 7, 'accountant,engineer,programmer,researcher', 1),
(@q14, 'O\'zgaruvchan va dinamik', 7, 'manager,doctor,lawyer,consultant', 2),
(@q14, 'Erkin va moslashuvchan', 7, 'artist,designer,writer,consultant', 3),
(@q14, 'Intensiv va qisqa muddatli', 7, 'athlete,salesperson,entrepreneur,manager', 4);

-- Savol 15
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('temperament', 'Qanday energiya darajasiga egasiz?', 'multiple_choice', 15);

SET @q15 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q15, 'Juda yuqori (doimiy harakat)', 8, 'athlete,manager,salesperson,entrepreneur', 1),
(@q15, 'O\'rtacha (muvozanatli)', 8, 'teacher,engineer,doctor,analyst', 2),
(@q15, 'Past (sokin va diqqatli)', 8, 'researcher,programmer,accountant,designer', 3),
(@q15, 'O\'zgaruvchan (vaziyatga qarab)', 8, 'manager,psychologist,lawyer,consultant', 4);

-- ============================================
-- 4. XARAKTER (Character) - Savollar 16-25
-- ============================================

-- Savol 16
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('character', 'Siz qanday xarakterga egasiz?', 'multiple_choice', 16);

SET @q16 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q16, 'Ochiq va ijtimoiy', 9, 'teacher,manager,psychologist,salesperson', 1),
(@q16, 'Yopiq va mustaqil', 9, 'programmer,writer,researcher,designer', 2),
(@q16, 'Jiddiy va mas\'uliyatli', 9, 'doctor,lawyer,accountant,engineer', 3),
(@q16, 'Ijodiy va erkin', 9, 'artist,designer,writer,architect', 4),
(@q16, 'Lider va qat\'iyatli', 9, 'manager,businessman,leader,organizer', 5),
(@q16, 'Yumshoq va g\'amxo\'r', 9, 'nurse,teacher,psychologist,social_worker', 6);

-- Savol 17
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('character', 'Qanday vaziyatlarda o\'zingizni yaxshi ko\'rsatasiz?', 'multiple_choice', 17);

SET @q17 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q17, 'Guruh oldida nutq so\'zlash', 8, 'teacher,lawyer,manager,psychologist', 1),
(@q17, 'Yolg\'iz ishlash va fikrlash', 8, 'programmer,writer,researcher,designer', 2),
(@q17, 'Murakkab masalalarni yechish', 8, 'engineer,programmer,analyst,researcher', 3),
(@q17, 'Ijodiy loyihalar yaratish', 8, 'artist,designer,writer,architect', 4),
(@q17, 'Boshqalarni boshqarish', 8, 'manager,businessman,leader,organizer', 5),
(@q17, 'Yordam berish va g\'amxo\'rlik qilish', 8, 'doctor,nurse,teacher,social_worker', 6);

-- Savol 18
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('character', 'Qanday xususiyat sizda eng kuchli?', 'multiple_choice', 18);

SET @q18 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q18, 'Muloqot qilish qobiliyati', 9, 'teacher,manager,psychologist,salesperson', 1),
(@q18, 'Mustaqillik va o\'z-o\'ziga ishonch', 9, 'programmer,writer,researcher,designer', 2),
(@q18, 'Mas\'uliyat va ishonchlilik', 9, 'doctor,lawyer,accountant,engineer', 3),
(@q18, 'Ijodkorlik va fantaziya', 9, 'artist,designer,writer,architect', 4),
(@q18, 'Liderlik va qat\'iyat', 9, 'manager,businessman,leader,organizer', 5),
(@q18, 'Empatiya va g\'amxo\'rlik', 9, 'nurse,teacher,psychologist,social_worker', 6);

-- Savol 19
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('character', 'Qanday qarorlar qabul qilasiz?', 'multiple_choice', 19);

SET @q19 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q19, 'Tez va qat\'iy', 8, 'manager,businessman,leader,entrepreneur', 1),
(@q19, 'Sekin va diqqat bilan', 8, 'researcher,analyst,accountant,engineer', 2),
(@q19, 'Boshqalar bilan maslahatlashib', 8, 'teacher,psychologist,manager,consultant', 3),
(@q19, 'Intuitsiya asosida', 8, 'artist,designer,writer,architect', 4);

-- Savol 20
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('character', 'Qanday ish uslubiga egasiz?', 'multiple_choice', 20);

SET @q20 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q20, 'Tashkilotli va rejalashtirilgan', 8, 'manager,accountant,engineer,organizer', 1),
(@q20, 'Erkin va moslashuvchan', 8, 'artist,designer,writer,consultant', 2),
(@q20, 'Guruh bilan hamkorlikda', 8, 'teacher,psychologist,manager,hr_specialist', 3),
(@q20, 'Mustaqil va o\'z-o\'zidan', 8, 'programmer,writer,researcher,designer', 4);

-- ============================================
-- 5. STRESSGA CHIDAMLILIK (Stress tolerance) - Savollar 21-30
-- ============================================

-- Savol 21
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('stress_tolerance', 'Qanday vaziyatlarda stressga duch kelasiz?', 'multiple_choice', 21);

SET @q21 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q21, 'Juda kam (oson boshqaraman)', 10, 'manager,doctor,lawyer,athlete', 1),
(@q21, 'Ba\'zan (o\'rtacha)', 7, 'teacher,engineer,analyst,programmer', 2),
(@q21, 'Ko\'pincha (qiyin)', 5, 'researcher,designer,writer,accountant', 3),
(@q21, 'Doimiy (juda qiyin)', 3, 'artist,designer,writer,researcher', 4);

-- Savol 22
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('stress_tolerance', 'Qanday ish sharoitida o\'zingizni qulay his qilasiz?', 'multiple_choice', 22);

SET @q22 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q22, 'Yuqori bosim va tezlik', 9, 'doctor,lawyer,manager,athlete', 1),
(@q22, 'O\'rtacha yuklama', 8, 'teacher,engineer,analyst,programmer', 2),
(@q22, 'Sokin va barqaror', 7, 'researcher,accountant,designer,writer', 3),
(@q22, 'Erkin va stresssiz', 6, 'artist,designer,writer,consultant', 4);

-- Savol 23
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('stress_tolerance', 'Qanday vaziyatlarda o\'zingizni yaxshi boshqarasiz?', 'multiple_choice', 23);

SET @q23 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q23, 'Favqulodda vaziyatlar', 10, 'doctor,lawyer,manager,firefighter', 1),
(@q23, 'Murakkab masalalar', 8, 'engineer,programmer,analyst,researcher', 2),
(@q23, 'Oddiy va barqaror ishlar', 7, 'accountant,designer,writer,teacher', 3),
(@q23, 'Ijodiy va erkin ishlar', 6, 'artist,designer,writer,consultant', 4);

-- Savol 24
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('stress_tolerance', 'Qanday ish yuklamasi sizga mos?', 'multiple_choice', 24);

SET @q24 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q24, 'Juda yuqori (ko\'p ish)', 9, 'manager,doctor,lawyer,entrepreneur', 1),
(@q24, 'O\'rtacha (muvozanatli)', 8, 'teacher,engineer,analyst,programmer', 2),
(@q24, 'Past (sokin)', 7, 'researcher,accountant,designer,writer', 3),
(@q24, 'O\'zgaruvchan', 8, 'consultant,manager,psychologist,lawyer', 4);

-- Savol 25
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('stress_tolerance', 'Qanday vaziyatlarda o\'zingizni qulay his qilasiz?', 'multiple_choice', 25);

SET @q25 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q25, 'Favqulodda va tezkor vaziyatlar', 9, 'doctor,lawyer,manager,firefighter', 1),
(@q25, 'Barqaror va rejalashtirilgan', 8, 'engineer,accountant,programmer,researcher', 2),
(@q25, 'Sokin va stresssiz', 7, 'designer,writer,teacher,researcher', 3),
(@q25, 'Ijodiy va erkin', 7, 'artist,designer,writer,consultant', 4);

-- ============================================
-- 6. MULOQOT KO\'NIKMALARI (Communication) - Savollar 26-35
-- ============================================

-- Savol 26
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('communication', 'Qanday muloqot uslubiga egasiz?', 'multiple_choice', 26);

SET @q26 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q26, 'Ochiq va ijtimoiy', 10, 'teacher,manager,psychologist,salesperson', 1),
(@q26, 'O\'rtacha (kerak bo\'lganda)', 8, 'engineer,doctor,analyst,programmer', 2),
(@q26, 'Yopiq va kam gapiruvchi', 6, 'researcher,programmer,accountant,designer', 3),
(@q26, 'Vaziyatga qarab o\'zgaradi', 8, 'manager,lawyer,consultant,psychologist', 4);

-- Savol 27
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('communication', 'Qanday guruhda o\'zingizni qulay his qilasiz?', 'multiple_choice', 27);

SET @q27 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q27, 'Katta guruhda (10+ odam)', 9, 'teacher,manager,psychologist,salesperson', 1),
(@q27, 'O\'rtacha guruhda (5-10 odam)', 8, 'engineer,doctor,analyst,programmer', 2),
(@q27, 'Kichik guruhda (2-5 odam)', 7, 'researcher,programmer,designer,writer', 3),
(@q27, 'Yolg\'iz yoki 1-2 kishi bilan', 6, 'programmer,writer,researcher,designer', 4);

-- Savol 28
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('communication', 'Qanday muloqot shaklini afzal ko\'rasiz?', 'multiple_choice', 28);

SET @q28 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q28, 'Shaxsiy uchrashuv', 9, 'psychologist,doctor,lawyer,consultant', 1),
(@q28, 'Telefon yoki video qo\'ng\'iroq', 8, 'manager,salesperson,consultant,hr_specialist', 2),
(@q28, 'Xat yoki elektron pochta', 7, 'programmer,researcher,analyst,accountant', 3),
(@q28, 'Ijtimoiy tarmoqlar', 7, 'marketer,designer,writer,journalist', 4);

-- Savol 29
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('communication', 'Qanday vaziyatlarda o\'zingizni yaxshi ko\'rsatasiz?', 'multiple_choice', 29);

SET @q29 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q29, 'Guruh oldida nutq so\'zlash', 10, 'teacher,lawyer,manager,psychologist', 1),
(@q29, 'Shaxsiy suhbat', 9, 'psychologist,doctor,lawyer,consultant', 2),
(@q29, 'Yozma muloqot', 8, 'writer,journalist,researcher,analyst', 3),
(@q29, 'Yolg\'iz ishlash', 6, 'programmer,researcher,designer,writer', 4);

-- Savol 30
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('communication', 'Qanday muloqot qobiliyatiga egasiz?', 'multiple_choice', 30);

SET @q30 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q30, 'Juda yaxshi (oson muloqot)', 10, 'teacher,manager,psychologist,salesperson', 1),
(@q30, 'Yaxshi (o\'rtacha)', 8, 'engineer,doctor,lawyer,consultant', 2),
(@q30, 'O\'rtacha (qiyin)', 6, 'programmer,researcher,analyst,accountant', 3),
(@q30, 'Yomon (juda qiyin)', 4, 'programmer,researcher,designer,writer', 4);

-- ============================================
-- 7. ANALITIK FIKRLASH (Analytical thinking) - Savollar 31-40
-- ============================================

-- Savol 31
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('analytical', 'Qanday masalalarni yechishni yaxshi ko\'rasiz?', 'multiple_choice', 31);

SET @q31 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q31, 'Murakkab matematik masalalar', 10, 'mathematician,engineer,programmer,physicist', 1),
(@q31, 'Mantiqiy va strategik masalalar', 9, 'engineer,programmer,analyst,researcher', 2),
(@q31, 'Ijodiy va yechim topish', 8, 'designer,architect,writer,artist', 3),
(@q31, 'Ijtimoiy va psixologik masalalar', 8, 'psychologist,teacher,manager,consultant', 4);

-- Savol 32
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('analytical', 'Qanday fikrlash uslubiga egasiz?', 'multiple_choice', 32);

SET @q32 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q32, 'Mantiqiy va ketma-ket', 10, 'engineer,programmer,analyst,mathematician', 1),
(@q32, 'Tahliliy va tafsilotli', 9, 'researcher,analyst,accountant,engineer', 2),
(@q32, 'Ijodiy va intuitiv', 8, 'artist,designer,writer,architect', 3),
(@q32, 'Holatga qarab o\'zgaradi', 8, 'manager,psychologist,lawyer,consultant', 4);

-- Savol 33
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('analytical', 'Qanday ma\'lumotlarni tahlil qilishni yaxshi ko\'rasiz?', 'multiple_choice', 33);

SET @q33 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q33, 'Raqamli ma\'lumotlar', 10, 'mathematician,engineer,programmer,analyst', 1),
(@q33, 'Ijtimoiy va psixologik ma\'lumotlar', 9, 'psychologist,sociologist,researcher,teacher', 2),
(@q33, 'Ijodiy va vizual ma\'lumotlar', 8, 'designer,artist,architect,writer', 3),
(@q33, 'Ilmiy va texnik ma\'lumotlar', 9, 'scientist,engineer,researcher,programmer', 4);

-- Savol 34
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('analytical', 'Qanday yondashuv bilan masalalarni yechasiz?', 'multiple_choice', 34);

SET @q34 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q34, 'Mantiqiy va ketma-ket', 10, 'engineer,programmer,analyst,mathematician', 1),
(@q34, 'Tahliliy va tafsilotli', 9, 'researcher,analyst,accountant,engineer', 2),
(@q34, 'Ijodiy va intuitiv', 8, 'artist,designer,writer,architect', 3),
(@q34, 'Holatga qarab o\'zgaradi', 8, 'manager,psychologist,lawyer,consultant', 4);

-- Savol 35
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('analytical', 'Qanday masalalarni oson yechasiz?', 'multiple_choice', 35);

SET @q35 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q35, 'Matematik va mantiqiy', 10, 'mathematician,engineer,programmer,physicist', 1),
(@q35, 'Tahliliy va tadqiqiy', 9, 'researcher,analyst,engineer,scientist', 2),
(@q35, 'Ijodiy va yechim topish', 8, 'designer,architect,writer,artist', 3),
(@q35, 'Ijtimoiy va psixologik', 8, 'psychologist,teacher,manager,consultant', 4);

-- ============================================
-- 8. IJODIY QOBILIYATLAR (Creativity) - Savollar 36-45
-- ============================================

-- Savol 36
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('creativity', 'Qanday ijodiy faoliyatni yaxshi ko\'rasiz?', 'multiple_choice', 36);

SET @q36 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q36, 'Rasm chizish va dizayn', 10, 'artist,designer,architect,interior_designer', 1),
(@q36, 'Yozish va adabiyot', 10, 'writer,journalist,editor,translator', 2),
(@q36, 'Musiqa va san\'at', 10, 'musician,composer,artist,performer', 3),
(@q36, 'Texnik yechimlar yaratish', 9, 'engineer,architect,programmer,designer', 4),
(@q36, 'Ijodiy loyihalar boshqarish', 9, 'manager,producer,director,organizer', 5),
(@q36, 'Yangi g\'oyalar yaratish', 9, 'entrepreneur,innovator,researcher,designer', 6);

-- Savol 37
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('creativity', 'Qanday ijodiy qobiliyatga egasiz?', 'multiple_choice', 37);

SET @q37 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q37, 'Juda yuqori (doimiy ijod)', 10, 'artist,designer,writer,architect', 1),
(@q37, 'Yuqori (ko\'pincha)', 9, 'designer,writer,architect,engineer', 2),
(@q37, 'O\'rtacha (ba\'zan)', 7, 'manager,teacher,consultant,programmer', 3),
(@q37, 'Past (kam)', 5, 'accountant,researcher,analyst,engineer', 4);

-- Savol 38
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('creativity', 'Qanday loyihalarni yaratishni yaxshi ko\'rasiz?', 'multiple_choice', 38);

SET @q38 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q38, 'Vizual va dizayn loyihalar', 10, 'artist,designer,architect,interior_designer', 1),
(@q38, 'Yozma va adabiy loyihalar', 10, 'writer,journalist,editor,translator', 2),
(@q38, 'Texnik va innovatsion loyihalar', 9, 'engineer,programmer,architect,designer', 3),
(@q38, 'Ijtimoiy va tashkiliy loyihalar', 8, 'manager,organizer,producer,director', 4);

-- Savol 39
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('creativity', 'Qanday ijodiy uslubga egasiz?', 'multiple_choice', 39);

SET @q39 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q39, 'Erkin va intuitiv', 10, 'artist,designer,writer,architect', 1),
(@q39, 'Tashkilotli va rejalashtirilgan', 8, 'designer,architect,engineer,manager', 2),
(@q39, 'Eksperimental va yangi', 9, 'innovator,researcher,designer,engineer', 3),
(@q39, 'An\'anaviy va klassik', 7, 'teacher,researcher,analyst,accountant', 4);

-- Savol 40
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('creativity', 'Qanday ijodiy muhitda o\'zingizni qulay his qilasiz?', 'multiple_choice', 40);

SET @q40 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q40, 'Erkin va cheklanmagan', 10, 'artist,designer,writer,architect', 1),
(@q40, 'Ijodiy va ilhomlantiruvchi', 9, 'designer,writer,architect,musician', 2),
(@q40, 'Tashkilotli va qo\'llab-quvvatlovchi', 8, 'manager,organizer,producer,director', 3),
(@q40, 'Sokin va diqqatli', 7, 'researcher,designer,writer,analyst', 4);

-- ============================================
-- QO'SHIMCHA SAVOLLAR (Additional) - Savollar 41-50
-- ============================================

-- Savol 41 - Ish muhiti
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('work_environment', 'Qanday ish muhitida ishlashni xohlaysiz?', 'multiple_choice', 41);

SET @q41 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q41, 'Ofis (bino ichida)', 8, 'manager,accountant,programmer,analyst', 1),
(@q41, 'Uydan (remote)', 7, 'programmer,writer,designer,consultant', 2),
(@q41, 'Ochiq havo', 8, 'architect,engineer,athlete,farmer', 3),
(@q41, 'Maxsus muassasa (shifoxona, maktab)', 9, 'doctor,teacher,nurse,psychologist', 4),
(@q41, 'Ijodiy studiya', 9, 'artist,designer,musician,writer', 5),
(@q41, 'Sahna yoki maydon', 9, 'athlete,performer,musician,actor', 6);

-- Savol 42 - Ish vaqti
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('work_schedule', 'Qanday ish jadvaliga egasiz?', 'multiple_choice', 42);

SET @q42 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q42, 'Doimiy (9-18)', 8, 'manager,accountant,engineer,programmer', 1),
(@q42, 'Erkin (o\'zingiz belgilaysiz)', 9, 'consultant,writer,designer,artist', 2),
(@q42, 'O\'zgaruvchan (shifrlash)', 8, 'doctor,nurse,lawyer,manager', 3),
(@q42, 'Qisqa muddatli (loyihalar)', 8, 'consultant,designer,writer,programmer', 4);

-- Savol 43 - Maqsad va motivatsiya
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('motivation', 'Sizni nima eng ko\'p motivatsiya qiladi?', 'multiple_choice', 43);

SET @q43 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q43, 'Moliyaviy muvaffaqiyat', 8, 'businessman,manager,entrepreneur,salesperson', 1),
(@q43, 'Ijodiy ifoda', 9, 'artist,designer,writer,musician', 2),
(@q43, 'Ijtimoiy ta\'sir', 9, 'teacher,doctor,psychologist,social_worker', 3),
(@q43, 'Ilmiy yutuqlar', 9, 'scientist,researcher,engineer,professor', 4),
(@q43, 'Shaxsiy rivojlanish', 8, 'consultant,coach,trainer,psychologist', 5),
(@q43, 'Yordam berish', 9, 'doctor,nurse,teacher,social_worker', 6);

-- Savol 44 - Qiyinchilik darajasi
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('difficulty', 'Qanday qiyinchilik darajasidagi ishlarni afzal ko\'rasiz?', 'multiple_choice', 44);

SET @q44 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q44, 'Juda murakkab (qiyin masalalar)', 10, 'engineer,programmer,scientist,researcher', 1),
(@q44, 'O\'rtacha murakkab', 8, 'manager,analyst,accountant,teacher', 2),
(@q44, 'Oddiy va barqaror', 7, 'clerk,operator,assistant,receptionist', 3),
(@q44, 'Turli darajadagi', 8, 'manager,consultant,lawyer,doctor', 4);

-- Savol 45 - Natija va ta\'sir
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('impact', 'Qanday natijalarni ko\'rishni xohlaysiz?', 'multiple_choice', 45);

SET @q45 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q45, 'Aniq va tezkor natijalar', 8, 'salesperson,manager,entrepreneur,athlete', 1),
(@q45, 'Uzoq muddatli ta\'sir', 9, 'teacher,doctor,researcher,engineer', 2),
(@q45, 'Ijodiy va ko\'rinadigan natijalar', 9, 'artist,designer,writer,architect', 3),
(@q45, 'Ijtimoiy va insoniy ta\'sir', 9, 'psychologist,teacher,doctor,social_worker', 4);

-- ============================================
-- YAKUNIY SAVOLLAR (Final) - Savollar 46-50
-- ============================================

-- Savol 46 - Karyera maqsadlari
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('career_goals', 'Karyerada nima qilishni xohlaysiz?', 'multiple_choice', 46);

SET @q46 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q46, 'Yuqori lavozimga ko\'tarilish', 9, 'manager,businessman,executive,leader', 1),
(@q46, 'Mutaxassis bo\'lish', 9, 'engineer,doctor,lawyer,programmer', 2),
(@q46, 'O\'z biznesini yaratish', 9, 'entrepreneur,businessman,innovator,startup_founder', 3),
(@q46, 'Ijodiy erkinlik', 9, 'artist,designer,writer,consultant', 4),
(@q46, 'Ijtimoiy ta\'sir', 9, 'teacher,doctor,psychologist,social_worker', 5),
(@q46, 'Ilmiy yutuqlar', 9, 'scientist,researcher,professor,engineer', 6);

-- Savol 47 - Qadriyatlar
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('values', 'Siz uchun eng muhim nima?', 'multiple_choice', 47);

SET @q47 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q47, 'Moliyaviy barqarorlik', 8, 'businessman,manager,accountant,engineer', 1),
(@q47, 'Ijodiy erkinlik', 9, 'artist,designer,writer,architect', 2),
(@q47, 'Ijtimoiy adolat', 9, 'lawyer,judge,teacher,social_worker', 3),
(@q47, 'Ilm va bilim', 9, 'scientist,researcher,professor,engineer', 4),
(@q47, 'Yordam berish', 9, 'doctor,nurse,teacher,psychologist', 5),
(@q47, 'Shaxsiy rivojlanish', 8, 'consultant,coach,trainer,psychologist', 6);

-- Savol 48 - Ish va hayot muvozanati
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('work_life_balance', 'Ish va hayot muvozanati siz uchun qanchalik muhim?', 'multiple_choice', 48);

SET @q48 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q48, 'Juda muhim (muvozanat)', 9, 'teacher,designer,consultant,programmer', 1),
(@q48, 'Muhim (o\'rtacha)', 8, 'manager,engineer,analyst,accountant', 2),
(@q48, 'Kam muhim (ish ustuvor)', 7, 'doctor,lawyer,manager,entrepreneur', 3),
(@q48, 'Umuman muhim emas', 6, 'athlete,performer,entrepreneur,startup_founder', 4);

-- Savol 49 - O\'qish va rivojlanish
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('learning', 'Qanday o\'qish uslubini afzal ko\'rasiz?', 'multiple_choice', 49);

SET @q49 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q49, 'Amaliy va tajriba orqali', 9, 'engineer,doctor,programmer,designer', 1),
(@q49, 'Nazariy va kitob orqali', 8, 'researcher,professor,analyst,writer', 2),
(@q49, 'Guruh bilan o\'rganish', 8, 'teacher,manager,psychologist,consultant', 3),
(@q49, 'Mustaqil o\'rganish', 7, 'programmer,writer,researcher,designer', 4);

-- Savol 50 - Umumiy qiziqish
INSERT INTO questions (category, question_text, question_type, order_number) VALUES
('general_interest', 'Umuman olganda, qaysi sohada ishlashni xohlaysiz?', 'multiple_choice', 50);

SET @q50 = LAST_INSERT_ID();
INSERT INTO answer_options (question_id, option_text, score, profession_tags, order_number) VALUES
(@q50, 'Tibbiyot va sog\'liqni saqlash', 10, 'doctor,nurse,pharmacist,psychologist', 1),
(@q50, 'Texnologiya va IT', 10, 'programmer,engineer,it_specialist,data_scientist', 2),
(@q50, 'Ta\'lim va tarbiya', 10, 'teacher,professor,educator,trainer', 3),
(@q50, 'Qonun va huquq', 10, 'lawyer,judge,prosecutor,notary', 4),
(@q50, 'Biznes va moliya', 10, 'businessman,manager,marketer,accountant', 5),
(@q50, 'San\'at va madaniyat', 10, 'artist,designer,musician,journalist', 6),
(@q50, 'Ilm va tadqiqot', 10, 'scientist,researcher,engineer,professor', 7),
(@q50, 'Ijtimoiy xizmatlar', 10, 'social_worker,psychologist,teacher,nurse', 8);

-- ============================================
-- YAKUN
-- ============================================

-- Jami: 50 ta savol
-- Kategoriyalar:
-- - interests (Qiziqishlar): 5 savol
-- - abilities (Qobiliyatlar): 5 savol
-- - temperament (Temperament): 5 savol
-- - character (Xarakter): 5 savol
-- - stress_tolerance (Stressga chidamlilik): 5 savol
-- - communication (Muloqot): 5 savol
-- - analytical (Analitik fikrlash): 5 savol
-- - creativity (Ijodiy qobiliyatlar): 5 savol
-- - work_environment (Ish muhiti): 1 savol
-- - work_schedule (Ish jadvali): 1 savol
-- - motivation (Motivatsiya): 1 savol
-- - difficulty (Qiyinchilik): 1 savol
-- - impact (Ta\'sir): 1 savol
-- - career_goals (Karyera maqsadlari): 1 savol
-- - values (Qadriyatlar): 1 savol
-- - work_life_balance (Ish-hayot muvozanati): 1 savol
-- - learning (O\'qish): 1 savol
-- - general_interest (Umumiy qiziqish): 1 savol

