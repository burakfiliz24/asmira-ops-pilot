# Sunucu Güncelleme Rehberi

## ÖNEMLİ: Güncelleme yaparken veri kaybını önlemek için

Sunucuya yeni dosya yüklerken **SADECE** aşağıdaki klasörleri güncelleyin:

### ✅ Güncellenecek Klasörler
- `pages/` — PHP sayfa dosyaları
- `api/` — API endpoint dosyaları
- `includes/` — Header, footer, auth dosyaları
- `assets/` — CSS, JS, görseller
- `index.php` — Ana router
- `router.php` — PHP built-in server router
- `.htaccess` — Apache ayarları

### ❌ DOKUNULMAYACAK Klasörler
- `data/` — Yüklenen evrak dosyaları (PDF, görseller)
- `logs/` — Hata logları

## Güncelleme Adımları

1. Yeni dosyaları FTP/cPanel ile yükleyin (sadece yukarıdaki ✅ klasörler)
2. `data/` ve `logs/` klasörlerini **KESİNLİKLE silmeyin**
3. Güncelleme sonrası `/api/init-db` adresini bir kez ziyaret edin (yeni tablo/kolon varsa otomatik ekler)
4. Sayfayı yenileyin ve test edin

## Notlar
- Veritabanı (MariaDB) dosya güncellemesinden etkilenmez
- `data/uploads/` içindeki fiziksel evrak dosyaları sunucuda saklanır
- Bu klasör silinirse yüklenen tüm evraklar kaybolur
