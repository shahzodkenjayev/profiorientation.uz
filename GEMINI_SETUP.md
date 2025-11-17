# Gemini AI Setup Guide

## Gemini API Key olish

1. **Google AI Studio ga kirish**
   - https://makersuite.google.com/app/apikey ga o'ting
   - Yoki https://aistudio.google.com/app/apikey

2. **API Key yaratish**
   - "Create API Key" tugmasini bosing
   - Google akkauntingiz bilan kirish
   - API Key avtomatik yaratiladi

3. **API Key ni nusxalash**
   - Yaratilgan API Key ni nusxalang
   - Bu key faqat bir marta ko'rsatiladi!

## .env faylga qo'shish

`.env` faylga quyidagi qatorni qo'shing:

```env
GEMINI_API_KEY=your_api_key_here
```

**Muhim:** `your_api_key_here` o'rniga o'zingizning API Key ingizni yozing.

## Misol

```env
GEMINI_API_KEY=AIzaSyD1234567890abcdefghijklmnopqrstuvwxyz
```

## Xavfsizlik

- API Key ni hech qachon GitHub ga yuklamang!
- `.env` fayl `.gitignore` da bo'lishi kerak
- API Key ni boshqalar bilan baham ko'rmang

## Database yangilash

Ko'p tilli qo'llab-quvvatlash uchun database ni yangilang:

```sql
-- MySQL terminal yoki phpMyAdmin orqali
mysql -u root -p kasb_tanlash < database_multilang_update.sql
```

Yoki phpMyAdmin da `database_multilang_update.sql` faylini import qiling.

## Qo'llab-quvvatlanadigan tillar

- `uz` - O'zbek
- `ru` - Rus
- `en` - Ingliz
- `tr` - Turk

## Funksiyalar

1. **AI yordamida savol generatsiya qilish**
   - Kategoriya va tilni tanlang
   - "Generatsiya qilish" tugmasini bosing
   - AI avtomatik savol va javob variantlarini yaratadi

2. **Qo'lda savol qo'shish**
   - Barcha maydonlarni to'ldiring
   - Javob variantlarini qo'shing
   - "Qo'shish" tugmasini bosing

3. **Mavjud savolni tarjima qilish**
   - Savollar ro'yxatida tarjima qilish kerak bo'lgan tilni tanlang
   - AI avtomatik tarjima qiladi va yangi savol sifatida saqlaydi

## Xatoliklar

Agar "Gemini API Key topilmadi!" xatosi chiqsa:
- `.env` faylda `GEMINI_API_KEY` mavjudligini tekshiring
- API Key to'g'ri yozilganligini tekshiring
- Server ni qayta ishga tushiring

Agar "models/gemini-pro is not found" xatosi chiqsa:
- Bu xato tuzatildi! Endi `gemini-1.5-flash` modeli ishlatiladi
- Kod yangilandi, server ni qayta ishga tushiring

Agar "AI xatosi" chiqsa:
- API Key to'g'ri ekanligini tekshiring
- Internet ulanishini tekshiring
- API limitlarini tekshiring (kunlik limit bor)
- Ishlatilayotgan model: `gemini-1.5-flash`

## Ishlatilayotgan model

Tizim `gemini-1.5-flash` modelidan foydalanadi. Bu:
- Tez ishlaydi
- Yuqori sifatli javoblar beradi
- `generateContent` metodini qo'llab-quvvatlaydi
- v1beta API versiyasida mavjud

