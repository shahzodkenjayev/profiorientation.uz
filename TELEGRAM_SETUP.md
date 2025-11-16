# Telegram Bot Sozlash Ko'rsatmasi

## "Bot domain invalid" xatosini hal qilish

Telegram Login Widget ishlashi uchun bot sozlamalarida domain qo'shish kerak.

### Qadamlar:

1. **BotFather ga kirish:**
   - Telegram'da `@BotFather` ga yozing
   - `/mybots` buyrug'ini yuboring
   - Botingizni tanlang: `@profiorientatsiya_bot`

2. **Domain qo'shish:**
   - `Bot Settings` → `Domain` ni tanlang
   - Domain qo'shing:
     - Localhost uchun: `localhost`
     - Yoki to'liq path: `localhost/ptest`
     - Production uchun: `yourdomain.com`

3. **Callback URL:**
   - Bot sozlamalarida callback URL qo'shish shart emas
   - Widget avtomatik `data-auth-url` parametridan foydalanadi

### Alternativ usul:

Agar domain sozlash muammo bo'lsa, foydalanuvchilar to'g'ridan-to'g'ri bot linkiga o'tishi mumkin:
- `https://t.me/profiorientatsiya_bot?start=register`

Bu link register sahifasida ko'rsatiladi.

### Test qilish:

1. BotFather orqali domain qo'shing
2. `http://localhost/ptest/auth/register.php` sahifasiga kiring
3. Telegram tugmasini bosing
4. Telegram Login Widget ishlashi kerak

