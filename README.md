# Asmira Ops-Pilot

Bunker yakıt ikmali operasyon yönetim sistemi. Asmira Petrol için geliştirilmiş modern bir Next.js uygulaması.

## Özellikler

- **Dashboard**: Takvim tabanlı operasyon takibi, drag & drop ile tarih değiştirme
- **Araç Evrakları**: Asmira özmal ve tedarikçi araçları için evrak yönetimi
- **Dilekçeler**: Taahhütname ve gümrük dilekçesi şablonları, PDF çıktı
- **Port Wiki**: Liman bazlı operasyonel bilgiler ve iletişim rehberi
- **Ayarlar**: Sistem tercihleri (geliştirme aşamasında)

## Teknolojiler

- **Framework**: Next.js 16 (App Router)
- **Styling**: Tailwind CSS 4
- **Icons**: Lucide React
- **Database**: Supabase (PostgreSQL)
- **PDF İşleme**: pdf-lib, file-saver
- **Deployment**: Netlify

## Kurulum

### 1. Bağımlılıkları yükleyin

```bash
npm install
```

### 2. Ortam değişkenlerini ayarlayın

Proje kök dizininde `.env.local` dosyası oluşturun:

```env
NEXT_PUBLIC_SUPABASE_URL=your_supabase_url
NEXT_PUBLIC_SUPABASE_ANON_KEY=your_supabase_anon_key
```

> **Not**: `env.example` dosyasını referans olarak kullanabilirsiniz.

### 3. Geliştirme sunucusunu başlatın

```bash
npm run dev
```

Tarayıcınızda [http://localhost:3000](http://localhost:3000) adresini açın.

## Proje Yapısı

```
src/
├── app/                    # Next.js App Router sayfaları
│   ├── (app)/              # Ana uygulama layout'u
│   │   ├── dashboard/      # Operasyon takvimi
│   │   ├── vehicle-documents/  # Araç evrakları
│   │   ├── petitions/      # Dilekçe şablonları
│   │   ├── port-wiki/      # Liman bilgileri
│   │   └── settings/       # Ayarlar
│   ├── layout.tsx          # Root layout
│   └── globals.css         # Global stiller
├── components/
│   ├── layout/             # AppShell, Sidebar, Topbar
│   └── ui/                 # Button, Badge, Table, StatusBadge
├── features/
│   ├── petitions/          # Dilekçe domain & data
│   └── vehicle-documents/  # Araç evrakları domain & data
├── lib/
│   ├── constants/          # Navigasyon sabitleri
│   ├── supabase/           # Supabase client
│   └── utils/              # Yardımcı fonksiyonlar
└── db/
    └── schema.sql          # Veritabanı şeması
```

## Scriptler

- `npm run dev` - Geliştirme sunucusu
- `npm run build` - Production build
- `npm run start` - Production sunucusu
- `npm run lint` - ESLint kontrolü

## Deployment

### Vercel (Önerilen)

1. [Vercel](https://vercel.com) hesabı oluşturun
2. GitHub repo'nuzu bağlayın
3. Environment Variables ekleyin:
   - `NEXT_PUBLIC_SUPABASE_URL`
   - `NEXT_PUBLIC_SUPABASE_ANON_KEY`
4. Deploy butonuna tıklayın

### Netlify

1. [Netlify](https://netlify.com) hesabı oluşturun
2. GitHub repo'nuzu bağlayın
3. Build settings otomatik algılanacak (`netlify.toml`)
4. Environment Variables ekleyin
5. Deploy butonuna tıklayın

### Kendi Sunucunuz (VPS/Dedicated)

```bash
# 1. Projeyi klonlayın
git clone https://github.com/your-repo/asmira-ops-pilot.git
cd asmira-ops-pilot

# 2. Bağımlılıkları yükleyin
npm install

# 3. Environment dosyasını oluşturun
cp env.example .env.local
# .env.local dosyasını düzenleyin

# 4. Production build
npm run build

# 5. PM2 ile çalıştırın (önerilen)
npm install -g pm2
pm2 start npm --name "asmira-ops" -- start

# 6. Nginx reverse proxy ayarlayın (HTTPS için)
```

### Docker

```dockerfile
FROM node:20-alpine
WORKDIR /app
COPY package*.json ./
RUN npm ci --only=production
COPY . .
RUN npm run build
EXPOSE 3000
CMD ["npm", "start"]
```

## Supabase Kurulumu

1. [Supabase](https://supabase.com) hesabı oluşturun
2. Yeni proje oluşturun
3. `src/db/schema.sql` dosyasını SQL Editor'da çalıştırın
4. Project Settings > API'den URL ve anon key'i alın
5. `.env.local` dosyasına ekleyin

## Production Checklist

- [ ] Environment variables ayarlandı
- [ ] Supabase veritabanı oluşturuldu
- [ ] Schema.sql çalıştırıldı
- [ ] HTTPS/SSL sertifikası aktif
- [ ] Domain DNS ayarları yapıldı
- [ ] Build hatasız tamamlandı (`npm run build`)

## Lisans

Özel kullanım - Asmira Petrol
