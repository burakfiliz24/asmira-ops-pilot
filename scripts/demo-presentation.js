const { chromium } = require('playwright');
const nodePath = require('path');

(async () => {
  // Video kayıt klasörü
  const videoDir = nodePath.join(__dirname, '..', 'demo-videos');
  
  // 1. Tarayıcıyı başlat (headless: false sayesinde izleyebilirsin)
  const browser = await chromium.launch({ 
    headless: false, 
    slowMo: 400 // Her işlem arasına 400ms koyar (hızlandırıldı)
  });
  
  const context = await browser.newContext({
    viewport: { width: 2560, height: 1440 }, // 2K (QHD) sunum boyutu
    recordVideo: {
      dir: videoDir,
      size: { width: 2560, height: 1440 }
    }
  });
  const page = await context.newPage();

  // --- MOUSE CURSOR OLUŞTUR ---
  async function injectCursor() {
    await page.evaluate(() => {
      // Eğer zaten varsa ekleme
      if (document.getElementById('demo-cursor')) return;
      
      // Cursor elementi
      const cursor = document.createElement('div');
      cursor.id = 'demo-cursor';
      cursor.innerHTML = `
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M5.5 3.21V20.8c0 .45.54.67.85.35l4.86-4.86a.5.5 0 0 1 .35-.15h6.87c.48 0 .72-.58.38-.92L6.35 2.85a.5.5 0 0 0-.85.36Z" fill="#000" stroke="#fff" stroke-width="1.5"/>
        </svg>
      `;
      cursor.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 24px;
        height: 24px;
        pointer-events: none;
        z-index: 999999;
        transition: left 0.25s cubic-bezier(0.25, 0.1, 0.25, 1), top 0.25s cubic-bezier(0.25, 0.1, 0.25, 1), transform 0.1s ease-out;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
      `;
      document.body.appendChild(cursor);
      
      // Click efekti elementi
      const clickRipple = document.createElement('div');
      clickRipple.id = 'demo-click-ripple';
      clickRipple.style.cssText = `
        position: fixed;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(59, 130, 246, 0.5);
        pointer-events: none;
        z-index: 999998;
        transform: scale(0);
        opacity: 0;
      `;
      document.body.appendChild(clickRipple);
      
      // Gerçek cursor'u gizle
      document.body.style.cursor = 'none';
      document.querySelectorAll('*').forEach(el => {
        el.style.cursor = 'none';
      });
    });
  }
  
  // Mouse'u hareket ettir (animasyonlu)
  async function moveCursor(x, y) {
    await page.evaluate(({x, y}) => {
      const cursor = document.getElementById('demo-cursor');
      if (cursor) {
        cursor.style.left = x + 'px';
        cursor.style.top = y + 'px';
      }
    }, {x, y});
  }
  
  // Tıklama efekti göster
  async function showClick(x, y) {
    await page.evaluate(({x, y}) => {
      const ripple = document.getElementById('demo-click-ripple');
      if (ripple) {
        ripple.style.left = (x - 15) + 'px';
        ripple.style.top = (y - 15) + 'px';
        ripple.style.transform = 'scale(0)';
        ripple.style.opacity = '1';
        
        // Animasyon
        requestAnimationFrame(() => {
          ripple.style.transition = 'transform 0.3s ease-out, opacity 0.3s ease-out';
          ripple.style.transform = 'scale(2)';
          ripple.style.opacity = '0';
        });
      }
    }, {x, y});
  }
  
  // Smooth mouse hareketi ve tıklama
  async function smoothClick(selector) {
    const element = page.locator(selector).first();
    const box = await element.boundingBox();
    if (!box) return;
    
    const targetX = box.x + box.width / 2;
    const targetY = box.y + box.height / 2;
    
    // Mouse'u hedefe doğru hareket ettir (animasyonlu)
    const steps = 20;
    const currentPos = await page.evaluate(() => {
      const cursor = document.getElementById('demo-cursor');
      return cursor ? { 
        x: parseFloat(cursor.style.left) || 100, 
        y: parseFloat(cursor.style.top) || 100 
      } : { x: 100, y: 100 };
    });
    
    for (let i = 1; i <= steps; i++) {
      const progress = i / steps;
      // Easing function (ease-out)
      const eased = 1 - Math.pow(1 - progress, 3);
      const x = currentPos.x + (targetX - currentPos.x) * eased;
      const y = currentPos.y + (targetY - currentPos.y) * eased;
      await moveCursor(x, y);
      await page.waitForTimeout(10);
    }
    
    await page.waitForTimeout(100);
    await showClick(targetX, targetY);
    await page.waitForTimeout(50);
    await element.click();
  }
  
  // Input'a smooth tıklama ve yazma
  async function smoothType(selector, text) {
    await smoothClick(selector);
    await page.waitForTimeout(150);
    const element = page.locator(selector).first();
    await element.type(text, { delay: 60 });
  }

  // Smooth mouse hareketi (tıklama olmadan)
  async function smoothMoveTo(x, y) {
    const steps = 15;
    const currentPos = await page.evaluate(() => {
      const cursor = document.getElementById('demo-cursor');
      return cursor ? { 
        x: parseFloat(cursor.style.left) || 100, 
        y: parseFloat(cursor.style.top) || 100 
      } : { x: 100, y: 100 };
    });
    
    for (let i = 1; i <= steps; i++) {
      const progress = i / steps;
      const eased = 1 - Math.pow(1 - progress, 3);
      const newX = currentPos.x + (x - currentPos.x) * eased;
      const newY = currentPos.y + (y - currentPos.y) * eased;
      await moveCursor(newX, newY);
      await page.waitForTimeout(8);
    }
  }

  // Drag & Drop animasyonu
  async function smoothDragDrop(sourceSelector, targetSelector) {
    const source = page.locator(sourceSelector).first();
    const target = page.locator(targetSelector).first();
    
    const sourceBox = await source.boundingBox();
    const targetBox = await target.boundingBox();
    
    if (!sourceBox || !targetBox) {
      console.log("Drag & Drop: Element bulunamadı");
      return;
    }
    
    const sourceX = sourceBox.x + sourceBox.width / 2;
    const sourceY = sourceBox.y + sourceBox.height / 2;
    const targetX = targetBox.x + targetBox.width / 2;
    const targetY = targetBox.y + targetBox.height / 2;
    
    // Kaynağa git
    await smoothMoveTo(sourceX, sourceY);
    await page.waitForTimeout(200);
    
    // Mouse down efekti
    await page.evaluate(({x, y}) => {
      const cursor = document.getElementById('demo-cursor');
      if (cursor) {
        cursor.style.transform = 'scale(0.9)';
      }
      const ripple = document.getElementById('demo-click-ripple');
      if (ripple) {
        ripple.style.left = (x - 15) + 'px';
        ripple.style.top = (y - 15) + 'px';
        ripple.style.transform = 'scale(1)';
        ripple.style.opacity = '0.5';
        ripple.style.transition = 'none';
      }
    }, {x: sourceX, y: sourceY});
    
    await page.mouse.move(sourceX, sourceY);
    await page.mouse.down();
    await page.waitForTimeout(400);
    
    // Hedefe sürükle (yavaş animasyon)
    const dragSteps = 40;
    for (let i = 1; i <= dragSteps; i++) {
      const progress = i / dragSteps;
      const eased = progress < 0.5 
        ? 2 * progress * progress 
        : 1 - Math.pow(-2 * progress + 2, 2) / 2; // ease-in-out
      const x = sourceX + (targetX - sourceX) * eased;
      const y = sourceY + (targetY - sourceY) * eased;
      await moveCursor(x, y);
      await page.mouse.move(x, y);
      
      // Ripple'ı da hareket ettir
      await page.evaluate(({x, y}) => {
        const ripple = document.getElementById('demo-click-ripple');
        if (ripple) {
          ripple.style.left = (x - 15) + 'px';
          ripple.style.top = (y - 15) + 'px';
        }
      }, {x, y});
      
      await page.waitForTimeout(25);
    }
    
    await page.waitForTimeout(200);
    
    // Mouse up
    await page.mouse.up();
    
    // Efektleri kaldır
    await page.evaluate(() => {
      const cursor = document.getElementById('demo-cursor');
      if (cursor) {
        cursor.style.transform = 'scale(1)';
      }
      const ripple = document.getElementById('demo-click-ripple');
      if (ripple) {
        ripple.style.transition = 'opacity 0.3s ease-out';
        ripple.style.opacity = '0';
      }
    });
    
    await page.waitForTimeout(200);
  }

  // Dosya yükleme simülasyonu
  async function uploadFile(inputSelector, filePath) {
    const input = page.locator(inputSelector).first();
    await input.setInputFiles(filePath);
  }

  // --- SUNUM BAŞLIYOR ---

  // 2. Giriş Sayfasına Git
  await page.goto('http://localhost:3000/login');
  await page.waitForTimeout(600);
  await injectCursor();
  await moveCursor(100, 100); // Başlangıç pozisyonu
  await page.waitForTimeout(600);
  console.log("Sunum Başladı: Giriş Ekranı");

  // 3. Ghost Typing: Kullanıcı adını tek tek yazar
  // Varsayılan kullanıcı: asmira / 123
  await smoothType('input[type="text"]', 'asmira');
  await page.waitForTimeout(200);
  
  await smoothType('input[type="password"]', '123');
  await page.waitForTimeout(200);

  // 4. Giriş Butonuna Tıkla
  await smoothClick('button[type="submit"]');
  console.log("Giriş yapılıyor...");

  // 5. Dashboard'un yüklenmesini bekle
  await page.waitForURL('**/dashboard', { timeout: 10000 });
  await page.waitForTimeout(600);
  await injectCursor(); // Sayfa değişti, cursor'u tekrar ekle
  await moveCursor(100, 100);
  await page.waitForTimeout(600);
  console.log("Dashboard Yüklendi!");

  // Takvimde gezinme - ay değiştirme butonları
  const nextMonthBtn = page.locator('button[aria-label="Sonraki ay"]');
  
  if (await nextMonthBtn.isVisible()) {
    await smoothClick('button[aria-label="Sonraki ay"]');
    await page.waitForTimeout(1200);
    
    await smoothClick('button[aria-label="Önceki ay"]');
    await page.waitForTimeout(1200);
    console.log("Takvim gezintisi tamamlandı.");
  }

  // 6. Yeni İkmal Ekleme Senaryosu
  await smoothClick('button:has-text("İkmal Ekle")');
  await page.waitForTimeout(1200);
  console.log("İkmal Ekleme Modalı Açıldı...");
  
  // Formu doldur
  // Gemi Adı
  await smoothType('input[placeholder="Örn: M/T Asmira Star"]', 'M/V BOSPHORUS');
  await page.waitForTimeout(400);
  
  // Miktar
  await smoothType('input[placeholder="Örn: 850"]', '450');
  await page.waitForTimeout(400);
  
  // Dolum Yeri
  await smoothType('input[placeholder="Örn: Dilovası"]', 'Dilovası');
  await page.waitForTimeout(400);
  
  // İkmal Limanı
  await smoothType('input[placeholder="Örn: İzmit"]', 'İzmit');
  await page.waitForTimeout(400);

  console.log("Form Dolduruldu!");

  // Kaydet butonuna tıkla
  await smoothClick('button:has-text("Kaydet")');
  await page.waitForTimeout(1200);
  
  console.log("✅ Operasyon Başarıyla Oluşturuldu!");
  await page.waitForTimeout(600);

  // 7. Drag & Drop - İkmali 18.01.2026 tarihine taşı
  console.log("Drag & Drop başlıyor - İkmal 18.01.2026 tarihine taşınacak...");
  
  // Eklenen ikmal kartını bul - draggable div içinde M/V BOSPHORUS yazısı olan
  const bosphorusCard = page.locator('div[draggable="true"]:has-text("M/V BOSPHORUS")').first();
  
  // 18 sayısını içeren takvim hücresini bul
  // Takvim hücrelerinde gün numarası sağ üstte gösteriliyor
  const targetDateCell = page.locator('div.group').filter({ hasText: /^18$/ }).first();
  
  // Alternatif: Tüm takvim hücrelerini al ve 18. günü bul
  const allCells = await page.locator('.grid-cols-7 > div.group').all();
  let targetCell = null;
  
  for (const cell of allCells) {
    const text = await cell.innerText();
    // Hücre içinde sadece "18" varsa veya "18" ile başlıyorsa
    if (text.includes('18') && !text.includes('M/V')) {
      const box = await cell.boundingBox();
      if (box) {
        targetCell = cell;
        break;
      }
    }
  }
  
  if (await bosphorusCard.isVisible() && targetCell) {
    const sourceBox = await bosphorusCard.boundingBox();
    const targetBox = await targetCell.boundingBox();
    
    if (sourceBox && targetBox) {
      const sourceX = sourceBox.x + sourceBox.width / 2;
      const sourceY = sourceBox.y + sourceBox.height / 2;
      const targetX = targetBox.x + targetBox.width / 2;
      const targetY = targetBox.y + 30; // Üst kısma bırak
      
      // Kaynağa git
      await smoothMoveTo(sourceX, sourceY);
      await page.waitForTimeout(200);
      
      // Mouse down
      await page.evaluate(({x, y}) => {
        const cursor = document.getElementById('demo-cursor');
        if (cursor) cursor.style.transform = 'scale(0.85)';
      }, {x: sourceX, y: sourceY});
      
      await page.mouse.move(sourceX, sourceY);
      await page.mouse.down();
      await page.waitForTimeout(400);
      
      // Hedefe sürükle - CSS transition ile akıcı animasyon (kasma yok)
      // Cursor'u hedef pozisyona ayarla - CSS transition otomatik animasyon yapacak
      await page.evaluate(({x, y}) => {
        const cursor = document.getElementById('demo-cursor');
        if (cursor) {
          cursor.style.left = x + 'px';
          cursor.style.top = y + 'px';
        }
      }, {x: targetX, y: targetY});
      
      // Mouse'u da hedefe taşı (hızlı, 5 adımda)
      const steps = 5;
      for (let i = 1; i <= steps; i++) {
        const progress = i / steps;
        const x = sourceX + (targetX - sourceX) * progress;
        const y = sourceY + (targetY - sourceY) * progress;
        await page.mouse.move(x, y);
      }
      
      // CSS transition'ın tamamlanmasını bekle
      await page.waitForTimeout(200);
      await page.mouse.up();
      
      await page.evaluate(() => {
        const cursor = document.getElementById('demo-cursor');
        if (cursor) cursor.style.transform = 'scale(1)';
      });
      
      console.log("✅ İkmal 18.01.2026 tarihine taşındı!");
    }
  } else {
    console.log("Drag & Drop: Kart veya hedef hücre bulunamadı, atlanıyor...");
  }
  await page.waitForTimeout(1200);

  // 8. Araç Evrakları Sayfasına Git - Sidebar ile
  console.log("Araç Evrakları sayfasına gidiliyor (sidebar ile)...");
  
  // Sidebar'daki "Araç Evrakları" menüsüne tıkla (accordion açmak için)
  const vehicleDocsMenu = page.locator('aside button:has-text("Araç Evrakları")').first();
  if (await vehicleDocsMenu.isVisible()) {
    const menuBox = await vehicleDocsMenu.boundingBox();
    if (menuBox) {
      await smoothMoveTo(menuBox.x + menuBox.width / 2, menuBox.y + menuBox.height / 2);
      await page.waitForTimeout(200);
      await vehicleDocsMenu.click();
      await page.waitForTimeout(200);
    }
  }
  
  // Alt menüden "Asmira Özmal" seçeneğine tıkla
  const asmiraLink = page.locator('aside a:has-text("Asmira Özmal")').first();
  await page.waitForTimeout(200);
  
  if (await asmiraLink.isVisible()) {
    const linkBox = await asmiraLink.boundingBox();
    if (linkBox) {
      await smoothMoveTo(linkBox.x + linkBox.width / 2, linkBox.y + linkBox.height / 2);
      await page.waitForTimeout(200);
      await asmiraLink.click();
      await page.waitForTimeout(1200);
    }
  } else {
    // Fallback: Doğrudan URL'ye git
    console.log("Sidebar link bulunamadı, doğrudan URL'ye gidiliyor...");
    await page.goto('http://localhost:3000/vehicle-documents/asmira');
    await page.waitForTimeout(1200);
  }
  
  await injectCursor();
  await moveCursor(400, 300);
  await page.waitForTimeout(600);
  console.log("Asmira Özmal Araçları sayfası açıldı!");

  // 9. Yeni Araç Kaydı Oluştur
  console.log("Yeni araç kaydı oluşturuluyor...");
  await smoothClick('button:has-text("Yeni Araç")');
  await page.waitForTimeout(600);
  
  // Araç plakası gir
  await smoothType('input[placeholder="Örn: 34 ASM 014"]', '34 DEMO 001');
  await page.waitForTimeout(200);
  
  // Dorse plakası gir
  await smoothType('input[placeholder="Örn: 34 DOR 123"]', '34 DEMO 901');
  await page.waitForTimeout(200);
  
  // Kaydet
  await smoothClick('button:has-text("Kaydet")');
  await page.waitForTimeout(1200);
  console.log("✅ Yeni araç kaydedildi: 34 DEMO 001 / 34 DEMO 901");

  // 10. Eklenen aracın evrak panelini aç
  console.log("Evrak paneli açılıyor...");
  await page.waitForTimeout(200);
  
  // Sayfadaki son eklenen aracın Evraklar butonunu bul
  // Grid içindeki kartlardan "34 DEMO 001" içeren kartın Evraklar butonuna tıkla
  const evraklarButtons = page.locator('button:has-text("Evraklar")');
  const evraklarCount = await evraklarButtons.count();
  
  if (evraklarCount > 0) {
    // Son eklenen araç en sonda olacak, son Evraklar butonuna tıkla
    const lastEvraklarBtn = evraklarButtons.last();
    const btnBox = await lastEvraklarBtn.boundingBox();
    if (btnBox) {
      await smoothMoveTo(btnBox.x + btnBox.width / 2, btnBox.y + btnBox.height / 2);
      await page.waitForTimeout(200);
      await lastEvraklarBtn.click();
      await page.waitForTimeout(1200);
      console.log("✅ Evrak paneli açıldı!");
    }
  } else {
    console.log("Evraklar butonu bulunamadı!");
  }

  // 11. Ruhsat bölümüne dosya yükle
  console.log("Ruhsat yükleniyor...");
  await page.waitForTimeout(600);
  
  // Demo için örnek bir görsel dosyası oluştur
  const fs = require('fs');
  const path = require('path');
  const demoImagePath = path.join(__dirname, 'demo-ruhsat.png');
  
  // Basit bir PNG dosyası oluştur
  if (!fs.existsSync(demoImagePath)) {
    const minimalPng = Buffer.from([
      0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A, 0x00, 0x00, 0x00, 0x0D,
      0x49, 0x48, 0x44, 0x52, 0x00, 0x00, 0x00, 0x01, 0x00, 0x00, 0x00, 0x01,
      0x08, 0x06, 0x00, 0x00, 0x00, 0x1F, 0x15, 0xC4, 0x89, 0x00, 0x00, 0x00,
      0x0A, 0x49, 0x44, 0x41, 0x54, 0x78, 0x9C, 0x63, 0x00, 0x01, 0x00, 0x00,
      0x05, 0x00, 0x01, 0x0D, 0x0A, 0x2D, 0xB4, 0x00, 0x00, 0x00, 0x00, 0x49,
      0x45, 0x4E, 0x44, 0xAE, 0x42, 0x60, 0x82
    ]);
    fs.writeFileSync(demoImagePath, minimalPng);
  }
  
  // Panel içindeki ilk "PDF veya Görsel Yükle" butonuna tıkla
  const uploadBtn = page.locator('button:has-text("PDF veya Görsel Yükle")').first();
  const uploadBtnVisible = await uploadBtn.isVisible();
  
  if (uploadBtnVisible) {
    const uploadBox = await uploadBtn.boundingBox();
    if (uploadBox) {
      await smoothMoveTo(uploadBox.x + uploadBox.width / 2, uploadBox.y + uploadBox.height / 2);
      await page.waitForTimeout(400);
      
      // Dosya input'unu bul ve dosya yükle
      const fileInput = page.locator('input[type="file"]').first();
      await fileInput.setInputFiles(demoImagePath);
      await page.waitForTimeout(600);
      console.log("✅ Ruhsat dosyası yüklendi!");
    }
  } else {
    console.log("Yükleme butonu görünür değil, panel açık mı kontrol edin!");
  }

  // 12. Geçerlilik tarihini 15.02.2026 olarak gir (süresi geçmiş - bugün 18.02.2026)
  console.log("Geçerlilik tarihi giriliyor (15.02.2026 - süresi geçmiş)...");
  
  // Tarih input'unu bul ve doldur
  const dateInput = page.locator('input[type="date"]').first();
  const dateBox = await dateInput.boundingBox();
  if (dateBox) {
    await smoothMoveTo(dateBox.x + dateBox.width / 2, dateBox.y + dateBox.height / 2);
    await page.waitForTimeout(200);
    await dateInput.click();
    await page.waitForTimeout(200);
    await dateInput.fill('2026-02-15');
    await page.waitForTimeout(600); // Süresi geçmiş uyarısının görünmesi için bekle
    console.log("⚠️ SÜRESİ GEÇMİŞ uyarısı gösteriliyor!");
  }

  // 13. Tarihi 25.02.2026 olarak güncelle - mouse ile tarihe git
  console.log("Tarih güncelleniyor (25.02.2026)...");
  const dateBox2 = await dateInput.boundingBox();
  if (dateBox2) {
    await smoothMoveTo(dateBox2.x + dateBox2.width / 2, dateBox2.y + dateBox2.height / 2);
    await page.waitForTimeout(200);
    await dateInput.click();
    await page.waitForTimeout(200);
    await dateInput.fill('2026-02-25');
    await page.waitForTimeout(600);
    console.log("✅ Tarih güncellendi - artık geçerli!");
  }

  // 14. Kaydet butonuna bas
  console.log("Değişiklikler kaydediliyor...");
  await smoothClick('button:has-text("Kaydet")');
  await page.waitForTimeout(1200);
  console.log("✅ Evrak değişiklikleri kaydedildi!");

  // 15. Paneli kapat ve Tedarikçi Araçları sayfasına git
  console.log("Panel kapatılıyor...");
  
  // Panel overlay'ine veya X butonuna tıklayarak paneli kapat
  const closeBtn = page.locator('button[aria-label="Kapat"]').first();
  if (await closeBtn.isVisible()) {
    await closeBtn.click();
    await page.waitForTimeout(200);
  }
  
  console.log("Tedarikçi Araçları sayfasına gidiliyor...");
  
  // Sidebar'daki "Tedarikçi Araçları" linkine tıkla
  const tedarikciLink = page.locator('aside a:has-text("Tedarikçi Araçları")').first();
  if (await tedarikciLink.isVisible()) {
    const linkBox = await tedarikciLink.boundingBox();
    if (linkBox) {
      await smoothMoveTo(linkBox.x + linkBox.width / 2, linkBox.y + linkBox.height / 2);
      await page.waitForTimeout(200);
      await tedarikciLink.click();
      await page.waitForTimeout(1200);
    }
  } else {
    // Fallback: Doğrudan URL'ye git
    await page.goto('http://localhost:3000/vehicle-documents/suppliers');
    await page.waitForTimeout(1200);
  }
  await injectCursor();
  await moveCursor(400, 300);
  await page.waitForTimeout(600);
  console.log("Tedarikçi Araçları sayfası açıldı!");

  // 16. Yeni Firma Ekle
  console.log("Yeni firma ekleniyor...");
  await smoothClick('button:has-text("Yeni Firma Ekle")');
  await page.waitForTimeout(600);
  
  // Firma adı gir - placeholder: "Örn: KARABURUN NAKLİYAT"
  const firmaInput = page.locator('input[placeholder*="KARABURUN"]').first();
  if (await firmaInput.isVisible()) {
    const inputBox = await firmaInput.boundingBox();
    if (inputBox) {
      await smoothMoveTo(inputBox.x + inputBox.width / 2, inputBox.y + inputBox.height / 2);
      await page.waitForTimeout(200);
      await firmaInput.click();
      await page.waitForTimeout(200);
      await firmaInput.fill('DEMO LOJİSTİK A.Ş.');
      await page.waitForTimeout(200);
    }
  }
  
  // Kaydet
  await smoothClick('button:has-text("Kaydet")');
  await page.waitForTimeout(1200);
  console.log("✅ Yeni firma eklendi: DEMO LOJİSTİK A.Ş.");

  // 17. Eklenen firmaya araç ekle
  console.log("Firmaya yeni araç ekleniyor...");
  
  // Yeni eklenen firmanın "Araç Ekle" butonuna tıkla
  const aracEkleBtn = page.locator('button:has-text("Araç Ekle")').first();
  if (await aracEkleBtn.isVisible()) {
    const btnBox = await aracEkleBtn.boundingBox();
    if (btnBox) {
      await smoothMoveTo(btnBox.x + btnBox.width / 2, btnBox.y + btnBox.height / 2);
      await page.waitForTimeout(200);
      await aracEkleBtn.click();
      await page.waitForTimeout(600);
    }
  }
  
  // Araç plakası gir - placeholder: "Örn: 34 ABC 123"
  const vehiclePlateInput = page.locator('input[placeholder*="34 ABC"]').first();
  if (await vehiclePlateInput.isVisible()) {
    const inputBox = await vehiclePlateInput.boundingBox();
    if (inputBox) {
      await smoothMoveTo(inputBox.x + inputBox.width / 2, inputBox.y + inputBox.height / 2);
      await page.waitForTimeout(200);
      await vehiclePlateInput.click();
      await page.waitForTimeout(200);
      await vehiclePlateInput.fill('34 DLJ 001');
      await page.waitForTimeout(400);
    }
  }
  
  // Dorse plakası gir - placeholder: "Örn: 34 ABD 123"
  const trailerPlateInput = page.locator('input[placeholder*="34 ABD"]').first();
  if (await trailerPlateInput.isVisible()) {
    const inputBox = await trailerPlateInput.boundingBox();
    if (inputBox) {
      await smoothMoveTo(inputBox.x + inputBox.width / 2, inputBox.y + inputBox.height / 2);
      await page.waitForTimeout(200);
      await trailerPlateInput.click();
      await page.waitForTimeout(200);
      await trailerPlateInput.fill('34 DLJ 901');
      await page.waitForTimeout(400);
    }
  }
  
  // Kaydet
  await smoothClick('button:has-text("Kaydet")');
  await page.waitForTimeout(1200);
  console.log("✅ Firmaya yeni araç eklendi: 34 DLJ 001 / 34 DLJ 901");

  // 18. Şoför Evrakları sayfasına git
  console.log("Şoför Evrakları sayfasına gidiliyor...");
  
  // Sidebar'daki "Şoför Evrakları" linkine tıkla
  const soforLink = page.locator('aside a:has-text("Şoför Evrakları")').first();
  if (await soforLink.isVisible()) {
    const linkBox = await soforLink.boundingBox();
    if (linkBox) {
      await smoothMoveTo(linkBox.x + linkBox.width / 2, linkBox.y + linkBox.height / 2);
      await page.waitForTimeout(200);
      await soforLink.click();
      await page.waitForTimeout(1200);
    }
  } else {
    await page.goto('http://localhost:3000/driver-documents');
    await page.waitForTimeout(1200);
  }
  await injectCursor();
  await moveCursor(400, 300);
  await page.waitForTimeout(600);
  console.log("Şoför Evrakları sayfası açıldı!");

  // 19. Yeni Şoför Ekle
  console.log("Yeni şoför ekleniyor...");
  await smoothClick('button:has-text("Yeni Şoför")');
  await page.waitForTimeout(600);
  
  // Şoför adı gir - ÖZKAN YAĞMUR
  console.log("Ad Soyad giriliyor: ÖZKAN YAĞMUR");
  await smoothType('input[placeholder*="Ahmet"]', 'ÖZKAN YAĞMUR');
  await page.waitForTimeout(600);
  
  // TC No gir - 1234567891
  console.log("TC No giriliyor: 1234567891");
  await smoothType('input[placeholder*="12345678901"]', '1234567891');
  await page.waitForTimeout(600);
  
  // Telefon gir - 0507 377 5656
  console.log("Telefon giriliyor: 0507 377 5656");
  await smoothType('input[placeholder*="0532"]', '0507 377 5656');
  await page.waitForTimeout(600);
  
  // Kaydet
  console.log("Şoför kaydediliyor...");
  await smoothClick('button:has-text("Kaydet")');
  await page.waitForTimeout(1200);
  console.log("✅ Yeni şoför eklendi: ÖZKAN YAĞMUR");

  // 20. Eklenen şoförün evrak panelini aç
  console.log("Şoför evrak paneli açılıyor...");
  
  // Sayfadaki son Evraklar butonuna tıkla (yeni eklenen şoför)
  const allEvraklarBtns = page.locator('button:has-text("Evraklar")');
  const soforEvraklarCount = await allEvraklarBtns.count();
  
  if (soforEvraklarCount > 0) {
    const lastBtn = allEvraklarBtns.nth(soforEvraklarCount - 1);
    const btnBox = await lastBtn.boundingBox();
    if (btnBox) {
      await smoothMoveTo(btnBox.x + btnBox.width / 2, btnBox.y + btnBox.height / 2);
      await page.waitForTimeout(200);
      await lastBtn.click();
      await page.waitForTimeout(1200);
      console.log("✅ Şoför evrak paneli açıldı!");
    }
  }

  // 21. Kimlik bölümüne dosya yükle
  console.log("Şoför kimlik görüntüsü yükleniyor...");
  await page.waitForTimeout(600);
  
  const soforUploadBtn = page.locator('button:has-text("PDF veya Görsel Yükle")').first();
  if (await soforUploadBtn.isVisible()) {
    const uploadBox = await soforUploadBtn.boundingBox();
    if (uploadBox) {
      await smoothMoveTo(uploadBox.x + uploadBox.width / 2, uploadBox.y + uploadBox.height / 2);
      await page.waitForTimeout(400);
      
      const soforFileInput = page.locator('input[type="file"]').first();
      await soforFileInput.setInputFiles(demoImagePath);
      await page.waitForTimeout(600);
      console.log("✅ Şoför kimlik görüntüsü yüklendi!");
    }
  }

  // 22. Geçerlilik tarihini 25.02.2026 olarak gir
  console.log("Geçerlilik tarihi giriliyor (25.02.2026)...");
  
  const soforDateInput = page.locator('input[type="date"]').first();
  const soforDateBox = await soforDateInput.boundingBox();
  if (soforDateBox) {
    await smoothMoveTo(soforDateBox.x + soforDateBox.width / 2, soforDateBox.y + soforDateBox.height / 2);
    await page.waitForTimeout(200);
    await soforDateInput.click();
    await page.waitForTimeout(200);
    await soforDateInput.fill('2026-02-25');
    await page.waitForTimeout(600);
    console.log("✅ Tarih girildi: 25.02.2026");
  }

  // 23. Kaydet butonuna bas
  console.log("Şoför evrak değişiklikleri kaydediliyor...");
  await smoothClick('button:has-text("Kaydet")');
  await page.waitForTimeout(1200);
  console.log("✅ Şoför evrak değişiklikleri kaydedildi!");

  // Panel'i kapat
  const soforPanelClose = page.locator('button[aria-label="Kapat"]').first();
  if (await soforPanelClose.isVisible()) {
    await soforPanelClose.click();
    await page.waitForTimeout(200);
  }

  // 24. Evrak Paketi sayfasına git
  console.log("Evrak Paketi sayfasına gidiliyor...");
  
  const evrakPaketiLink = page.locator('aside a:has-text("Evrak Paketi")').first();
  if (await evrakPaketiLink.isVisible()) {
    const linkBox = await evrakPaketiLink.boundingBox();
    if (linkBox) {
      await smoothMoveTo(linkBox.x + linkBox.width / 2, linkBox.y + linkBox.height / 2);
      await page.waitForTimeout(200);
      await evrakPaketiLink.click();
      await page.waitForTimeout(1200);
    }
  } else {
    await page.goto('http://localhost:3000/document-package');
    await page.waitForTimeout(1200);
  }
  await injectCursor();
  await moveCursor(400, 300);
  await page.waitForTimeout(600);
  console.log("Evrak Paketi sayfası açıldı!");

  // 25. Çekici seç (34 DEMO 001) - select elementi açılsın ve seçim görünsün
  console.log("Çekici seçiliyor (34 DEMO 001)...");
  
  const cekiciSelect = page.locator('select').first();
  if (await cekiciSelect.isVisible()) {
    const selectBox = await cekiciSelect.boundingBox();
    if (selectBox) {
      await smoothMoveTo(selectBox.x + selectBox.width / 2, selectBox.y + selectBox.height / 2);
      await page.waitForTimeout(200);
      // Select'e tıkla - dropdown açılsın
      await cekiciSelect.click();
      await page.waitForTimeout(200);
    }
    // Option'ları al ve 34 DEMO 001 içereni bul
    const options = await cekiciSelect.locator('option').allTextContents();
    const demoOption = options.find(o => o.includes('34 DEMO 001'));
    if (demoOption) {
      await cekiciSelect.selectOption({ label: demoOption });
      await page.waitForTimeout(600);
      console.log("✅ Çekici seçildi: 34 DEMO 001");
    }
  }

  // 26. Dorse seç (34 DEMO 901) - ikinci select elementi
  console.log("Dorse seçiliyor (34 DEMO 901)...");
  
  const dorseSelect = page.locator('select').nth(1);
  if (await dorseSelect.isVisible()) {
    const selectBox = await dorseSelect.boundingBox();
    if (selectBox) {
      await smoothMoveTo(selectBox.x + selectBox.width / 2, selectBox.y + selectBox.height / 2);
      await page.waitForTimeout(200);
      // Select'e tıkla - dropdown açılsın
      await dorseSelect.click();
      await page.waitForTimeout(200);
    }
    const dorseOptions = await dorseSelect.locator('option').allTextContents();
    const demoDorseOption = dorseOptions.find(o => o.includes('34 DEMO 901'));
    if (demoDorseOption) {
      await dorseSelect.selectOption({ label: demoDorseOption });
      await page.waitForTimeout(600);
      console.log("✅ Dorse seçildi: 34 DEMO 901");
    }
  }

  // 27. Şoför seçimi için sayfayı aşağı kaydır - şoför bölümü görünsün
  console.log("Şoför seçimi için sayfa kaydırılıyor...");
  
  // Şoför select elementini bul ve görünür yap
  const soforSelectEl = page.locator('select').nth(2);
  if (await soforSelectEl.isVisible()) {
    await soforSelectEl.scrollIntoViewIfNeeded();
    await page.waitForTimeout(200);
  }

  // Şoför seç (ÖZKAN YAĞMUR) - üçüncü select elementi
  console.log("Şoför seçiliyor (ÖZKAN YAĞMUR)...");
  
  const soforSelect = soforSelectEl;
  if (await soforSelect.isVisible()) {
    const selectBox = await soforSelect.boundingBox();
    if (selectBox) {
      await smoothMoveTo(selectBox.x + selectBox.width / 2, selectBox.y + selectBox.height / 2);
      await page.waitForTimeout(200);
      // Select'e tıkla - dropdown açılsın
      await soforSelect.click();
      await page.waitForTimeout(200);
    }
    const soforOptions = await soforSelect.locator('option').allTextContents();
    const demoSoforOption = soforOptions.find(o => o.includes('ÖZKAN YAĞMUR'));
    if (demoSoforOption) {
      await soforSelect.selectOption({ label: demoSoforOption });
      await page.waitForTimeout(600);
      console.log("✅ Şoför seçildi: ÖZKAN YAĞMUR");
    }
  }

  // 28. Evrak seçimi - Ruhsat ve Kimlik için tik emojisi göster (mock)
  console.log("Evraklar seçiliyor...");
  
  // Ruhsat kutucuğunu bul ve görünür yap
  const ruhsatBtn = page.locator('button:has-text("Ruhsat")').first();
  if (await ruhsatBtn.isVisible()) {
    await ruhsatBtn.scrollIntoViewIfNeeded();
    await page.waitForTimeout(200);
  }
  if (await ruhsatBtn.isVisible()) {
    const ruhsatBox = await ruhsatBtn.boundingBox();
    if (ruhsatBox) {
      // Checkbox'a git (sol tarafta)
      await smoothMoveTo(ruhsatBox.x + 25, ruhsatBox.y + ruhsatBox.height / 2);
      await page.waitForTimeout(200);
      
      // Tıklama efekti göster
      await showClick(ruhsatBox.x + 25, ruhsatBox.y + ruhsatBox.height / 2);
      await page.waitForTimeout(200);
      
      // Tik emojisi ekle (mock seçim)
      await page.evaluate(() => {
        const ruhsatBtns = document.querySelectorAll('button');
        for (const btn of ruhsatBtns) {
          if (btn.textContent?.includes('Ruhsat')) {
            const checkbox = btn.querySelector('div.flex.h-5.w-5');
            if (checkbox) {
              checkbox.style.borderColor = '#3b82f6';
              checkbox.style.backgroundColor = '#3b82f6';
              checkbox.innerHTML = '<svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>';
            }
            break;
          }
        }
      });
      await page.waitForTimeout(200);
      console.log("✅ Ruhsat seçildi!");
    }
  }
  
  // Kimlik kutucuğunu bul ve görünür yap
  const kimlikBtn = page.locator('button:has-text("Kimlik")').first();
  if (await kimlikBtn.isVisible()) {
    await kimlikBtn.scrollIntoViewIfNeeded();
    await page.waitForTimeout(200);
  }
  if (await kimlikBtn.isVisible()) {
    const kimlikBox = await kimlikBtn.boundingBox();
    if (kimlikBox) {
      // Checkbox'a git (sol tarafta)
      await smoothMoveTo(kimlikBox.x + 25, kimlikBox.y + kimlikBox.height / 2);
      await page.waitForTimeout(200);
      
      // Tıklama efekti göster
      await showClick(kimlikBox.x + 25, kimlikBox.y + kimlikBox.height / 2);
      await page.waitForTimeout(200);
      
      // Tik emojisi ekle (mock seçim)
      await page.evaluate(() => {
        const kimlikBtns = document.querySelectorAll('button');
        for (const btn of kimlikBtns) {
          if (btn.textContent?.includes('Kimlik')) {
            const checkbox = btn.querySelector('div.flex.h-5.w-5');
            if (checkbox) {
              checkbox.style.borderColor = '#3b82f6';
              checkbox.style.backgroundColor = '#3b82f6';
              checkbox.innerHTML = '<svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>';
            }
            break;
          }
        }
      });
      await page.waitForTimeout(200);
      console.log("✅ Kimlik seçildi!");
    }
  }
  
  await page.waitForTimeout(600);

  // 29. PDF Oluştur butonuna tıkla
  console.log("PDF oluşturuluyor...");
  
  // Sayfayı yukarı kaydır PDF butonunu görmek için
  await page.evaluate(() => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
  await page.waitForTimeout(200);
  
  const pdfBtn = page.locator('button:has-text("PDF Oluştur")').first();
  const pdfBtnBox = await pdfBtn.boundingBox();
  if (pdfBtnBox) {
    await smoothMoveTo(pdfBtnBox.x + pdfBtnBox.width / 2, pdfBtnBox.y + pdfBtnBox.height / 2);
    await page.waitForTimeout(200);
  }
  
  const pdfBtnDisabled = await pdfBtn.isDisabled();
  
  if (!pdfBtnDisabled) {
    await pdfBtn.click();
    await page.waitForTimeout(1200);
    console.log("✅ PDF oluşturuldu ve indirildi!");
  } else {
    console.log("⚠️ PDF Oluştur butonu disabled - evrak seçimi yapılmamış olabilir");
    // Mock olarak butona tıklanmış gibi göster
    await page.waitForTimeout(600);
  }

  // ========================================
  // 30. DİLEKÇELER SAYFASI
  // ========================================
  console.log("\n--- DİLEKÇELER SAYFASI ---");
  console.log("Dilekçeler sayfasına gidiliyor (sidebar ile)...");
  
  // Sidebar'dan Dilekçeler'e tıkla
  const dilekceMenuItem = page.locator('aside a:has-text("Dilekçeler"), aside button:has-text("Dilekçeler")').first();
  if (await dilekceMenuItem.isVisible()) {
    await dilekceMenuItem.scrollIntoViewIfNeeded();
    const menuBox = await dilekceMenuItem.boundingBox();
    if (menuBox) {
      await smoothMoveTo(menuBox.x + menuBox.width / 2, menuBox.y + menuBox.height / 2);
      await page.waitForTimeout(200);
      await dilekceMenuItem.click();
      await page.waitForTimeout(1200);
    }
  } else {
    await page.goto('http://localhost:3000/petitions');
    await page.waitForTimeout(1200);
  }
  await injectCursor();
  await moveCursor(400, 300);
  await page.waitForTimeout(600);
  console.log("Dilekçeler sayfası açıldı!");

  // Taahhütnameler kategorisine tıkla
  console.log("Taahhütnameler kategorisi açılıyor...");
  const taahhutCard = page.locator('text=Taşıma Taahhütnameleri').first();
  if (await taahhutCard.isVisible()) {
    await taahhutCard.scrollIntoViewIfNeeded();
    const taahhutBox = await taahhutCard.boundingBox();
    if (taahhutBox) {
      await smoothMoveTo(taahhutBox.x + taahhutBox.width / 2, taahhutBox.y + taahhutBox.height / 2);
      await page.waitForTimeout(200);
      await taahhutCard.click();
      await page.waitForTimeout(1200);
      console.log("✅ Taahhütnameler kategorisi açıldı!");
      
      // Habaş Taahhütnamesi'ni aç
      console.log("Habaş Taahhütnamesi açılıyor...");
      const habasTemplate = page.locator('text=Habaş').first();
      if (await habasTemplate.isVisible()) {
        await habasTemplate.scrollIntoViewIfNeeded();
        const habasBox = await habasTemplate.boundingBox();
        if (habasBox) {
          await smoothMoveTo(habasBox.x + habasBox.width / 2, habasBox.y + habasBox.height / 2);
          await page.waitForTimeout(200);
          await habasTemplate.click();
          await page.waitForTimeout(1200); // 3 saniye göster
          console.log("✅ Habaş Taahhütnamesi açıldı!");
          
          // Modalı kapat - X butonunu bul (aria-label="Kapat" ve modal header içinde)
          // X butonu: inline-flex h-8 w-8 items-center justify-center rounded-md
          const modalCloseBtn = page.locator('button.inline-flex.h-8.w-8[aria-label="Kapat"]').first();
          if (await modalCloseBtn.isVisible()) {
            const closeBox = await modalCloseBtn.boundingBox();
            if (closeBox) {
              await smoothMoveTo(closeBox.x + closeBox.width / 2, closeBox.y + closeBox.height / 2);
              await page.waitForTimeout(200);
              await modalCloseBtn.click();
              await page.waitForTimeout(200);
              console.log("✅ Modal kapatıldı!");
            }
          } else {
            // Backdrop'a tıkla (arka plan)
            const backdrop = page.locator('button.absolute.inset-0[aria-label="Kapat"]').first();
            if (await backdrop.isVisible()) {
              await page.keyboard.press('Escape');
              await page.waitForTimeout(200);
            }
          }
        }
      }
    }
  }
  await injectCursor();
  await page.waitForTimeout(600);

  // Gümrük Dilekçeleri kategorisine tıkla
  console.log("Gümrük Dilekçeleri kategorisi açılıyor...");
  const gumrukCard = page.locator('text=Gümrük Milli İkmal Dilekçeleri').first();
  if (await gumrukCard.isVisible()) {
    await gumrukCard.scrollIntoViewIfNeeded();
    const gumrukBox = await gumrukCard.boundingBox();
    if (gumrukBox) {
      await smoothMoveTo(gumrukBox.x + gumrukBox.width / 2, gumrukBox.y + gumrukBox.height / 2);
      await page.waitForTimeout(200);
      await gumrukCard.click();
      await page.waitForTimeout(1200);
      console.log("✅ Gümrük Dilekçeleri kategorisi açıldı!");
      
      // İlk gümrük şablonunu aç
      console.log("Gümrük şablonu açılıyor...");
      const gumrukTemplate = page.locator('.cursor-pointer').first();
      if (await gumrukTemplate.isVisible()) {
        await gumrukTemplate.scrollIntoViewIfNeeded();
        const templateBox = await gumrukTemplate.boundingBox();
        if (templateBox) {
          await smoothMoveTo(templateBox.x + templateBox.width / 2, templateBox.y + templateBox.height / 2);
          await page.waitForTimeout(200);
          await gumrukTemplate.click();
          await page.waitForTimeout(1200); // 3 saniye göster
          console.log("✅ Gümrük şablonu açıldı!");
          
          // Modalı kapat - X butonunu bul
          const gumrukCloseBtn = page.locator('button.inline-flex.h-8.w-8[aria-label="Kapat"]').first();
          if (await gumrukCloseBtn.isVisible()) {
            const closeBox = await gumrukCloseBtn.boundingBox();
            if (closeBox) {
              await smoothMoveTo(closeBox.x + closeBox.width / 2, closeBox.y + closeBox.height / 2);
              await page.waitForTimeout(200);
              await gumrukCloseBtn.click();
              await page.waitForTimeout(200);
              console.log("✅ Modal kapatıldı!");
            }
          } else {
            await page.keyboard.press('Escape');
            await page.waitForTimeout(200);
          }
        }
      }
    }
  }
  await injectCursor();
  await page.waitForTimeout(600);

  // EK-1 Belgeleri kategorisine tıkla
  console.log("EK-1 Belgeleri kategorisi açılıyor...");
  const ek1Card = page.locator('text=EK-1 Belgeleri').first();
  if (await ek1Card.isVisible()) {
    await ek1Card.scrollIntoViewIfNeeded();
    const ek1Box = await ek1Card.boundingBox();
    if (ek1Box) {
      await smoothMoveTo(ek1Box.x + ek1Box.width / 2, ek1Box.y + ek1Box.height / 2);
      await page.waitForTimeout(200);
      await ek1Card.click();
      await page.waitForTimeout(1200);
      console.log("✅ EK-1 Belgeleri kategorisi açıldı!");
      
      // İlk EK-1 şablonunu aç
      console.log("EK-1 şablonu açılıyor...");
      const ek1Template = page.locator('.cursor-pointer').first();
      if (await ek1Template.isVisible()) {
        await ek1Template.scrollIntoViewIfNeeded();
        const templateBox = await ek1Template.boundingBox();
        if (templateBox) {
          await smoothMoveTo(templateBox.x + templateBox.width / 2, templateBox.y + templateBox.height / 2);
          await page.waitForTimeout(200);
          await ek1Template.click();
          await page.waitForTimeout(1200); // 3 saniye göster
          console.log("✅ EK-1 şablonu açıldı!");
          
          // Modalı kapat - X butonunu bul
          const ek1CloseBtn = page.locator('button.inline-flex.h-8.w-8[aria-label="Kapat"]').first();
          if (await ek1CloseBtn.isVisible()) {
            const closeBox = await ek1CloseBtn.boundingBox();
            if (closeBox) {
              await smoothMoveTo(closeBox.x + closeBox.width / 2, closeBox.y + closeBox.height / 2);
              await page.waitForTimeout(200);
              await ek1CloseBtn.click();
              await page.waitForTimeout(200);
              console.log("✅ Modal kapatıldı!");
            }
          } else {
            await page.keyboard.press('Escape');
            await page.waitForTimeout(200);
          }
        }
      }
    }
  }
  console.log("✅ Dilekçeler tanıtımı tamamlandı!");

  // ========================================
  // 31. PORT WİKİ SAYFASI
  // ========================================
  console.log("\n--- PORT WİKİ SAYFASI ---");
  console.log("Port Wiki sayfasına gidiliyor (sidebar ile)...");
  
  // Sidebar'dan Port Wiki'ye tıkla
  const portWikiMenuItem = page.locator('aside a:has-text("Port Wiki"), aside button:has-text("Port Wiki")').first();
  if (await portWikiMenuItem.isVisible()) {
    await portWikiMenuItem.scrollIntoViewIfNeeded();
    const menuBox = await portWikiMenuItem.boundingBox();
    if (menuBox) {
      await smoothMoveTo(menuBox.x + menuBox.width / 2, menuBox.y + menuBox.height / 2);
      await page.waitForTimeout(200);
      await portWikiMenuItem.click();
      await page.waitForTimeout(1200);
    }
  } else {
    await page.goto('http://localhost:3000/port-wiki');
    await page.waitForTimeout(1200);
  }
  await injectCursor();
  await moveCursor(400, 300);
  
  // 5 saniye bekle - sayfayı tanıt
  console.log("Port Wiki sayfası tanıtılıyor (5 saniye)...");
  await page.waitForTimeout(5000);
  console.log("Port Wiki sayfası açıldı!");

  // Alsancak Limanı'na tıkla
  console.log("Alsancak Limanı seçiliyor...");
  const alsancakCard = page.locator('text=Alsancak Limanı').first();
  if (await alsancakCard.isVisible()) {
    await alsancakCard.scrollIntoViewIfNeeded();
    const alsancakBox = await alsancakCard.boundingBox();
    if (alsancakBox) {
      await smoothMoveTo(alsancakBox.x + alsancakBox.width / 2, alsancakBox.y + alsancakBox.height / 2);
      await page.waitForTimeout(200);
      await alsancakCard.click();
      await page.waitForTimeout(1200); // 3 saniye göster
      console.log("✅ Alsancak Limanı gösterildi!");
      
      // Paneli kapat - X butonuna tıkla (h-9 w-9 class'lı)
      const closeBtn = page.locator('.relative.z-10 button.h-9.w-9[aria-label="Kapat"]').first();
      if (await closeBtn.isVisible()) {
        const closeBox = await closeBtn.boundingBox();
        if (closeBox) {
          await smoothMoveTo(closeBox.x + closeBox.width / 2, closeBox.y + closeBox.height / 2);
          await page.waitForTimeout(200);
          await closeBtn.click();
          await page.waitForTimeout(200);
          console.log("✅ Panel kapatıldı!");
        }
      }
    }
  }
  await injectCursor();
  await page.waitForTimeout(600);

  // Aliağa Limanı'na tıkla
  console.log("Aliağa Limanı seçiliyor...");
  const aliagaCard = page.locator('text=Aliağa Limanı').first();
  if (await aliagaCard.isVisible()) {
    await aliagaCard.scrollIntoViewIfNeeded();
    const aliagaBox = await aliagaCard.boundingBox();
    if (aliagaBox) {
      await smoothMoveTo(aliagaBox.x + aliagaBox.width / 2, aliagaBox.y + aliagaBox.height / 2);
      await page.waitForTimeout(200);
      await aliagaCard.click();
      await page.waitForTimeout(1200); // 3 saniye göster
      console.log("✅ Aliağa Limanı gösterildi!");
      
      // Paneli kapat - X butonuna tıkla
      const closeBtn2 = page.locator('.relative.z-10 button.h-9.w-9[aria-label="Kapat"]').first();
      if (await closeBtn2.isVisible()) {
        const closeBox2 = await closeBtn2.boundingBox();
        if (closeBox2) {
          await smoothMoveTo(closeBox2.x + closeBox2.width / 2, closeBox2.y + closeBox2.height / 2);
          await page.waitForTimeout(200);
          await closeBtn2.click();
          await page.waitForTimeout(200);
          console.log("✅ Panel kapatıldı!");
        }
      }
    }
  }
  
  console.log("✅ Port Wiki tanıtımı tamamlandı!");

  // Sunum tamamlandı - video kaydını bitir
  console.log("\n🎬 Sunum tamamlandı! Video kaydediliyor...");
  
  // 3 saniye bekle - son sahne görünsün
  await page.waitForTimeout(1200);
  
  // Context'i kapat - video otomatik kaydedilir
  await context.close();
  
  // Video dosyasının yolunu al
  const videoPath = await page.video()?.path();
  if (videoPath) {
    console.log(`\n✅ Video kaydedildi: ${videoPath}`);
  } else {
    console.log(`\n✅ Video kaydedildi: demo-videos klasörüne bakın`);
  }
  
  await browser.close();
  
  console.log("\n📁 Video dosyası: C:\\Users\\burak\\CascadeProjects\\asmira-ops-pilot\\demo-videos\\");
})();
