# Google OAuth 2.0 Sozlash Ko'rsatmasi

## Google Client ID va Secret Olish

### 1. Google Cloud Console ga kirish
- [Google Cloud Console](https://console.cloud.google.com/) ga kiring
- Google hisobingiz bilan tizimga kiring

### 2. Loyiha yaratish
- Yuqoridagi loyiha tanlovidan yangi loyiha yarating
- Yoki mavjud loyihani tanlang

### 3. OAuth consent screen sozlash
1. "APIs & Services" → "OAuth consent screen" ga kiring
2. **User Type**: External (yoki Internal, agar Workspace bo'lsa)
3. **App name**: `Prof Orientatsiya`
4. **User support email**: Emailingizni kiriting
5. **Developer contact**: Emailingizni kiriting
6. "Save and Continue" ni bosing
7. Scopes bo'limida "Save and Continue" ni bosing
8. Test users bo'limida (agar External tanlagan bo'lsangiz) "Save and Continue" ni bosing
9. Summary bo'limida "Back to Dashboard" ni bosing

### 4. OAuth 2.0 Client ID yaratish
1. "APIs & Services" → "Credentials" ga kiring
2. "+ CREATE CREDENTIALS" → "OAuth client ID" ni tanlang
3. **Application type**: `Web application` ni tanlang
4. **Name**: `Prof Orientatsiya Web Client`
5. **Authorized JavaScript origins**:
   ```
   https://profiorientation.uz
   ```
6. **Authorized redirect URIs**:
   ```
   https://profiorientation.uz/auth/google_callback
   ```
7. "CREATE" tugmasini bosing

### 5. Client ID va Secret ni olish
- **Client ID** va **Client secret** ko'rsatiladi
- **Client secret** ni darhol ko'chirib oling (keyin ko'rsatilmaydi)
- Agar secret'ni yo'qotgan bo'lsangiz, yangi Client ID yaratishingiz kerak

### 6. .env fayliga qo'shish

`.env` faylida quyidagi qatorlarni to'ldiring:

```env
GOOGLE_CLIENT_ID=your_client_id_here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_client_secret_here
```

**Misol:**
```env
GOOGLE_CLIENT_ID=123456789-abcdefghijklmnop.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-abcdefghijklmnopqrstuvwxyz
```

### 7. Test qilish

1. `.env` faylini saqlang
2. Saytni qayta yuklang
3. `https://profiorientation.uz/auth/register` sahifasiga kiring
4. "Google orqali kirish" tugmasini bosing
5. Google account tanlash oynasi ochilishi kerak

## Muammolar va yechimlar

### "Error 400: redirect_uri_mismatch"
- Google Cloud Console'da **Authorized redirect URIs** ga to'g'ri URL qo'shilganligini tekshiring
- URL to'liq bo'lishi kerak: `https://profiorientation.uz/auth/google_callback`

### "Error 403: access_denied"
- OAuth consent screen'da **Publishing status** ni tekshiring
- Test mode'da faqat test user'lar ishlatishi mumkin
- Production'da "Publish App" ni bosing

### Client secret yo'qolgan
- Yangi OAuth Client ID yarating
- Eski Client ID'ni o'chirib, yangisini yarating

## Xavfsizlik

- **Client Secret** ni hech qachon GitHub'ga yuklamang
- `.env` fayli `.gitignore` da bo'lishi kerak
- Production'da `.env` faylini himoya qiling

