# Serverga Deploy Qilish Ko'rsatmasi

## GitHub'dan Serverga Olish

### 1. Serverga SSH orqali kirish
```bash
ssh user@your-server.com
```

### 2. Loyihani klonlash
```bash
cd /var/www/html  # yoki boshqa web root papkasi
git clone git@github.com:shahzodkenjayev/profiorientation.uz.git profiorientation.uz
cd profiorientation.uz
```

### 3. .env faylini yaratish
```bash
cp .env.example .env
nano .env  # yoki boshqa editor
```

### 4. .env faylini to'ldirish
```env
APP_DEBUG=false
BASE_URL=https://profiorientation.uz/
TIMEZONE=Asia/Tashkent

DB_HOST=localhost
DB_USER=your_db_user
DB_PASS=your_db_password
DB_NAME=profiorientation
DB_CHARSET=utf8mb4

# Telegram Bot
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_BOT_USERNAME=profiorientatsiya_bot

# Payment gateways
PAYME_MERCHANT_ID=your_merchant_id
PAYME_SECRET_KEY=your_secret_key
CLICK_MERCHANT_ID=your_merchant_id
CLICK_SERVICE_ID=your_service_id
CLICK_SECRET_KEY=your_secret_key
```

### 5. Database yaratish
```bash
mysql -u root -p
CREATE DATABASE profiorientation CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit
mysql -u root -p profiorientation < database.sql
```

### 6. File permissions
```bash
chmod 644 .env
chmod 755 assets/
chmod 755 uploads/
```

### 7. Apache/Nginx sozlamalari

#### Apache (.htaccess allaqachon mavjud)
```apache
# Asosiy domen: profiorientation.uz
<VirtualHost *:80>
    ServerName profiorientation.uz
    ServerAlias www.profiorientation.uz
    DocumentRoot /var/www/html/profiorientation.uz
    
    <Directory /var/www/html/profiorientation.uz>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>
    
    # HTTPS ga redirect (SSL sozlangandan keyin)
    # RewriteEngine On
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>

<VirtualHost *:443>
    ServerName profiorientation.uz
    ServerAlias www.profiorientation.uz
    DocumentRoot /var/www/html/profiorientation.uz
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/profiorientation.uz/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/profiorientation.uz/privkey.pem
    
    <Directory /var/www/html/profiorientation.uz>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>
</VirtualHost>

# Zaxira domen: profiorientation.cybernode.uz
<VirtualHost *:80>
    ServerName profiorientation.cybernode.uz
    ServerAlias www.profiorientation.cybernode.uz
    DocumentRoot /var/www/html/profiorientation.uz
    
    <Directory /var/www/html/profiorientation.uz>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>
    
    # HTTPS ga redirect (SSL sozlangandan keyin)
    # RewriteEngine On
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>

<VirtualHost *:443>
    ServerName profiorientation.cybernode.uz
    ServerAlias www.profiorientation.cybernode.uz
    DocumentRoot /var/www/html/profiorientation.uz
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/profiorientation.cybernode.uz/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/profiorientation.cybernode.uz/privkey.pem
    
    <Directory /var/www/html/profiorientation.uz>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>
</VirtualHost>
```

#### Nginx
```nginx
# HTTP - HTTPS ga redirect (asosiy domen)
server {
    listen 80;
    server_name profiorientation.uz www.profiorientation.uz;
    return 301 https://profiorientation.uz$request_uri;
}

# HTTPS (asosiy domen)
server {
    listen 443 ssl http2;
    server_name profiorientation.uz www.profiorientation.uz;
    root /var/www/html/profiorientation.uz;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/profiorientation.uz/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/profiorientation.uz/privkey.pem;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }
    
    # PHP kengaytmasini yashirish
    location ~ ^/([^/]+)$ {
        try_files $uri $uri/ /$1.php?$query_string;
    }
}

# HTTP - HTTPS ga redirect (zaxira domen)
server {
    listen 80;
    server_name profiorientation.cybernode.uz www.profiorientation.cybernode.uz;
    return 301 https://profiorientation.cybernode.uz$request_uri;
}

# HTTPS (zaxira domen)
server {
    listen 443 ssl http2;
    server_name profiorientation.cybernode.uz www.profiorientation.cybernode.uz;
    root /var/www/html/profiorientation.uz;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/profiorientation.cybernode.uz/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/profiorientation.cybernode.uz/privkey.pem;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }
    
    # PHP kengaytmasini yashirish
    location ~ ^/([^/]+)$ {
        try_files $uri $uri/ /$1.php?$query_string;
    }
}
```

### 8. SSL sertifikat (HTTPS)
```bash
# Asosiy domen uchun
sudo certbot --nginx -d profiorientation.uz -d www.profiorientation.uz
# yoki
sudo certbot --apache -d profiorientation.uz -d www.profiorientation.uz

# Zaxira domen uchun
sudo certbot --nginx -d profiorientation.cybernode.uz -d www.profiorientation.cybernode.uz
# yoki
sudo certbot --apache -d profiorientation.cybernode.uz -d www.profiorientation.cybernode.uz
```

### 9. Yangilanishlar
```bash
cd /var/www/html/profiorientation.uz
git pull origin main
```

## Xavfsizlik

1. `.env` faylini himoya qiling
2. Database parolini kuchli qiling
3. Admin parolini o'zgartiring
4. File permissions'ni to'g'ri sozlang
5. SSL sertifikat o'rnating

## Monitoring

- Error loglar: `/var/log/apache2/error.log` yoki `/var/log/nginx/error.log`
- Application loglar: `logs/` papkasi (agar mavjud bo'lsa)

