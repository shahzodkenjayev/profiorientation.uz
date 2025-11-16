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
   - Domain qo'shing: `profiorientation.uz`

3. **Callback URL:**
   - Bot sozlamalarida callback URL qo'shish shart emas
   - Widget avtomatik `data-auth-url` parametridan foydalanadi

### Alternativ usul:

Agar domain sozlash muammo bo'lsa, foydalanuvchilar to'g'ridan-to'g'ri bot linkiga o'tishi mumkin:
- `https://t.me/profiorientatsiya_bot?start=register`

Bu link register sahifasida ko'rsatiladi.

### Test qilish:

1. BotFather orqali domain qo'shing: `profiorientation.uz`
2. `https://profiorientation.uz/auth/register` sahifasiga kiring
3. Telegram tugmasini bosing
4. Telegram Login Widget ishlashi kerak

