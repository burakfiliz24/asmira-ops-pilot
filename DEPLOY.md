# Asmira OPS - cPanel / PHP 8.0 + MariaDB Kurulum Rehberi

## Gereksinimler

- **PHP** 8.0 veya 8.4
- **MariaDB** (veya MySQL 5.7+)
- **cPanel / Plesk** paylaşımlı hosting
- **mod_rewrite** aktif (Apache)

---

## Adım 1: Build (Kendi Bilgisayarınızda)

```bash
npm run build:php
```

Bu komut `out/` dizinine statik HTML/CSS/JS dosyalarını oluşturur.

---

## Adım 2: MariaDB Veritabanı Oluşturma (cPanel)

1. cPanel → **MySQL Veritabanları** bölümüne girin
2. Yeni veritabanı oluşturun: `asmira_ops`
3. Yeni kullanıcı oluşturun: `asmira_user` (güçlü şifre ile)
4. Kullanıcıyı veritabanına ekleyin → **Tüm ayrıcalıkları** verin

---

## Adım 3: Dosyaları Yükleme (cPanel Dosya Yöneticisi veya FTP)

### Klasör yapısı:

```
public_html/
├── index.html          ← out/ içinden
├── _next/              ← out/_next/ içinden
├── dashboard/          ← out/dashboard/ içinden
├── login/              ← out/login/ içinden
├── ... (out/ içindeki tüm klasörler)
├── .htaccess           ← server/.htaccess dosyası
├── api/
│   ├── .htaccess       ← server/api/.htaccess
│   ├── config.php      ← server/api/config.php (DB bilgilerini düzenle!)
│   ├── db.php          ← server/api/db.php
│   ├── helpers.php     ← server/api/helpers.php
│   ├── init-db.php     ← server/api/init-db.php
│   ├── users.php       ← server/api/users.php
│   ├── operations.php  ← server/api/operations.php
│   ├── trucks.php      ← server/api/trucks.php
│   ├── trailers.php    ← server/api/trailers.php
│   ├── drivers.php     ← server/api/drivers.php
│   ├── vehicle-sets.php← server/api/vehicle-sets.php
│   ├── auth/
│   │   └── login.php   ← server/api/auth/login.php
│   └── documents/
│       ├── upload.php   ← server/api/documents/upload.php
│       ├── update.php   ← server/api/documents/update.php
│       └── download.php ← server/api/documents/download.php
└── data/
    └── uploads/         ← (boş klasör oluşturun, 755 izin)
```

**Kısaca:**
1. `out/` içindeki **tüm** dosya ve klasörleri → `public_html/` kopyalayın
2. `server/` içindeki dosyaları → `public_html/` ile birleştirin
3. `public_html/data/uploads/` klasörünü oluşturun (755 izin)

---

## Adım 4: Veritabanı Bağlantısını Yapılandırma

`public_html/api/config.php` dosyasını düzenleyin:

```php
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'cpanel_kullanici_asmira_ops');  // cPanel prefix ile
define('DB_USER', 'cpanel_kullanici_asmira_user'); // cPanel prefix ile
define('DB_PASS', 'guclu_sifreniz');
define('DB_CHARSET', 'utf8mb4');
```

> **Not:** cPanel'de veritabanı adı genellikle `cpanelkullanici_dbadi` formatındadır.

---

## Adım 5: Tabloları Oluşturma

Tarayıcıda şu adresi ziyaret edin:

```
http://siteniz.com/api/init-db
```

Başarılı olursa `{"success":true,"message":"Veritabanı tabloları oluşturuldu"}` yanıtı alırsınız.

Bu adım:
- Tüm tabloları otomatik oluşturur
- Varsayılan admin kullanıcısını ekler: `asmira` / `123`

---

## Adım 6: Giriş Yapın

```
http://siteniz.com/login
```

- **Kullanıcı adı:** asmira
- **Şifre:** 123

> ⚠️ İlk girişten sonra Ayarlar sayfasından şifrenizi değiştirin!

---

## Sorun Giderme

### "500 Internal Server Error"
- `api/config.php` dosyasındaki DB bilgilerini kontrol edin
- cPanel → PHP Sürümü → 8.0 veya 8.4 seçili mi kontrol edin
- PHP PDO MySQL eklentisinin aktif olduğundan emin olun

### "404 Not Found" (sayfa yenilendiğinde)
- `.htaccess` dosyasının `public_html/` kök dizininde olduğundan emin olun
- cPanel → Apache Handlers → `mod_rewrite` aktif mi kontrol edin

### Dosya yükleme çalışmıyor
- `public_html/data/uploads/` klasörü 755 izinli olmalı
- PHP `upload_max_filesize` ve `post_max_size` değerlerini kontrol edin

---

## Güncelleme

1. Kendi bilgisayarınızda: `npm run build:php`
2. Yeni `out/` dosyalarını cPanel'e yükleyin (API dosyalarını **değiştirmeyin**)

---

## Önemli Notlar

- **Yedekleme:** cPanel → Yedekler bölümünden düzenli yedek alın
- **Güvenlik:** İlk girişten sonra admin şifresini mutlaka değiştirin
- **Upload dizini:** `data/uploads/` klasörünü `.gitignore`'a eklemeyi unutmayın
- **PHP sürümü:** 8.0 veya 8.4 önerilir
