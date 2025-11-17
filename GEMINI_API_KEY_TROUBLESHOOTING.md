# Gemini API Key Muammolarini Hal Qilish

## API Key faolligini tekshirish

### 1. Test skripti orqali

```bash
# Browser'da oching
http://localhost/ptest/test_gemini_api.php

# Yoki terminal orqali
php test_gemini_api.php
```

### 2. Google AI Studio orqali

1. https://aistudio.google.com/app/apikey ga o'ting
2. Google akkauntingiz bilan kirish
3. API Key'lar ro'yxatini ko'ring
4. Holatni tekshiring:
   - ✅ **Active** - API Key faol
   - ❌ **Expired** - Muddati tugagan (yangi yarating)
   - ❌ **Revoked** - Bekor qilingan (yangi yarating)

## Umumiy muammolar va yechimlar

### Muammo 1: "Barcha modellar sinab ko'rildi"

**Sabab:** API Key ishlamayapti yoki noto'g'ri.

**Yechim:**
1. Yangi API Key yarating: https://aistudio.google.com/app/apikey
2. Eski API Key ni o'chiring (agar kerak bo'lsa)
3. Yangi API Key ni `.env` faylga qo'shing:
   ```env
   GEMINI_API_KEY=AIzaSy...your_new_api_key_here
   ```
4. Server ni qayta ishga tushiring:
   ```bash
   # PHP-FPM
   sudo systemctl restart php-fpm
   
   # Apache
   sudo systemctl reload apache2
   
   # Nginx
   sudo systemctl reload nginx
   ```

### Muammo 2: "API Key topilmadi"

**Sabab:** `.env` faylda `GEMINI_API_KEY` mavjud emas.

**Yechim:**
1. `.env` faylni oching
2. Quyidagini qo'shing:
   ```env
   GEMINI_API_KEY=your_api_key_here
   ```
3. Server ni qayta ishga tushiring

### Muammo 3: "API Key noto'g'ri formatda"

**Sabab:** API Key da bo'sh joylar yoki noto'g'ri format.

**Yechim:**
1. API Key ni to'liq nusxalang (bo'sh joylar bo'lmasin)
2. `.env` faylda quyidagicha yozing:
   ```env
   GEMINI_API_KEY=AIzaSy...your_key_without_spaces
   ```
3. Qo'shtirnoqlar kerak emas!

### Muammo 4: "API Key 180 kun ishlatilmasa, avtomatik o'chadi"

**Sabab:** API Key eskirgan.

**Yechim:**
1. Yangi API Key yarating
2. `.env` faylga qo'shing
3. Server ni qayta ishga tushiring

### Muammo 5: "Quota yetib borgan"

**Sabab:** Kunlik API limiti yetib borgan.

**Yechim:**
1. Keyinroq qayta urinib ko'ring (ertaga)
2. Yoki Google AI Studio'da billing sozlamalarini tekshiring
3. Yoki yangi API Key yarating (har bir API Key alohida limitga ega)

## .env faylni tekshirish

### Server'da (SSH orqali):

```bash
# .env fayl mavjudligini tekshirish
ls -la .env

# .env fayl mazmunini ko'rish (API Key ko'rinadi!)
cat .env | grep GEMINI_API_KEY

# Yoki faqat API Key ni ko'rish
grep GEMINI_API_KEY .env
```

### Localhost'da:

```bash
# Windows (PowerShell)
Get-Content .env | Select-String "GEMINI_API_KEY"

# Linux/Mac
cat .env | grep GEMINI_API_KEY
```

## API Key yaratish

### Qadam 1: Google AI Studio ga kirish
1. https://aistudio.google.com/app/apikey ga o'ting
2. Google akkauntingiz bilan kirish

### Qadam 2: API Key yaratish
1. "Create API Key" tugmasini bosing
2. Project tanlang (yoki yangi yarating)
3. API Key avtomatik yaratiladi

### Qadam 3: API Key ni saqlash
1. API Key ni darhol nusxalang (faqat bir marta ko'rsatiladi!)
2. Xavfsiz joyga saqlang
3. `.env` faylga qo'shing

## API Key format

To'g'ri format:
```
GEMINI_API_KEY=AIzaSyAbCdEfGhIjKlMnOpQrStUvWxYz1234567890
```

Noto'g'ri formatlar:
```
GEMINI_API_KEY="AIzaSy..."  # Qo'shtirnoqlar kerak emas
GEMINI_API_KEY= AIzaSy...   # Bo'sh joy key dan keyin
GEMINI_API_KEY=AIzaSy ...   # Bo'sh joy key ichida
```

## Server ni qayta ishga tushirish

### PHP-FPM:
```bash
sudo systemctl restart php-fpm
```

### Apache:
```bash
sudo systemctl reload apache2
# Yoki
sudo service apache2 reload
```

### Nginx:
```bash
sudo systemctl reload nginx
```

### XAMPP (Windows):
- XAMPP Control Panel'da Apache ni Stop qiling
- Keyin Start qiling

## Test qilish

1. Test skriptini oching: `http://localhost/ptest/test_gemini_api.php`
2. Yoki admin panelda: `https://profiorientation.uz/admin/questions`
3. "AI yordamida savol generatsiya qilish" bo'limida test qiling

## Qo'shimcha yordam

Agar muammo davom etsa:
1. Test skriptini ishga tushiring va natijani ko'ring
2. Server loglarini tekshiring (debug rejimida)
3. Google AI Studio'da API Key holatini tekshiring
4. Internet ulanishini tekshiring

## Xavfsizlik

⚠️ **Muhim:**
- API Key ni hech qachon GitHub ga yuklamang!
- `.env` fayl `.gitignore` da bo'lishi kerak
- API Key ni boshqalar bilan baham ko'rmang
- Agar API Key oshkor bo'lsa, darhol yangi yarating va eskisini o'chiring

