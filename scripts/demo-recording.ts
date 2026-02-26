/**
 * Asmira Ops Pilot - Demo Recording Script
 * 
 * Bu script Playwright kullanarak otomatik demo videosu için
 * mouse hareketleri ve sayfa gezintisi yapar.
 * 
 * Kullanım:
 * 1. Terminalde: npx tsx scripts/demo-recording.ts
 * 2. OBS veya Windows Game Bar ile ekran kaydı başlatın
 * 3. Script otomatik olarak tüm sayfaları gezecek
 */

import { chromium, Page } from 'playwright';
import * as path from 'path';
import * as fs from 'fs';

// ============ AYARLAR ============
const CONFIG = {
  baseUrl: 'http://localhost:3000',
  credentials: {
    username: 'asmira',
    password: '123'
  },
  demo: {
    ikmal: {
      vesselName: 'M/T Asmira Kumbor',
      quantity: '25',
      loadingPlace: 'Aliaga',
      port: 'Alsancak',
      date: '2026-01-20'
    },
    vehicle: {
      plate: '35 AS 774',
      trailerPlate: '35 COC 329'
    },
    driver: {
      name: 'Mehmet Yildirim',
      tcNo: '12345678901',
      phone: '0532 123 45 67'
    },
    document: {
      expiredDate: '2026-02-13',
      validDate: '2026-06-03'
    }
  },
  timing: {
    mouseMoveDuration: 500,
    typeDelay: 50,
    clickDelay: 300,
    pageLoadWait: 1500,
    sectionPause: 2000,
    scrollPause: 400,
    showcasePause: 4000,
    highlightDuration: 4000,
  },
  browser: {
    width: 1920,
    height: 1080,
    headless: false,
  }
};

// Cursor pozisyonu
let cursorPos = { x: 960, y: 540 };

// ============ YARDIMCI FONKSIYONLAR ============

async function sleep(ms: number): Promise<void> {
  return new Promise(resolve => setTimeout(resolve, ms));
}

// Cursor pozisyonunu guncelle
async function updateCursor(page: Page, x: number, y: number): Promise<void> {
  cursorPos = { x, y };
  await page.evaluate(({ x, y }) => {
    (window as any).__cursorX = x;
    (window as any).__cursorY = y;
  }, { x, y });
}

// Smooth mouse hareketi
async function smoothMouseMove(page: Page, targetX: number, targetY: number, duration: number = CONFIG.timing.mouseMoveDuration): Promise<void> {
  const startX = cursorPos.x;
  const startY = cursorPos.y;
  const steps = Math.max(20, Math.floor(duration / 16));
  
  for (let i = 0; i <= steps; i++) {
    const t = i / steps;
    const ease = 1 - Math.pow(1 - t, 3);
    const newX = startX + (targetX - startX) * ease;
    const newY = startY + (targetY - startY) * ease;
    
    await updateCursor(page, newX, newY);
    await page.mouse.move(newX, newY);
    await sleep(duration / steps);
  }
}

// Click efekti
async function clickEffect(page: Page): Promise<void> {
  await page.evaluate(() => {
    const cursor = document.getElementById('demo-cursor');
    if (cursor) {
      cursor.style.transform = 'translate(-50%, -50%) scale(0.7)';
      setTimeout(() => {
        cursor.style.transform = 'translate(-50%, -50%) scale(1)';
      }, 150);
    }
  });
}

// Element'e smooth hareket et ve tikla
async function smoothClick(page: Page, selector: string, waitAfter: number = CONFIG.timing.clickDelay): Promise<void> {
  const element = await page.waitForSelector(selector, { timeout: 10000 });
  if (!element) throw new Error(`Element not found: ${selector}`);
  
  const box = await element.boundingBox();
  if (!box) throw new Error(`Cannot get bounding box: ${selector}`);
  
  const centerX = box.x + box.width / 2;
  const centerY = box.y + box.height / 2;
  
  await smoothMouseMove(page, centerX, centerY);
  await sleep(200);
  await clickEffect(page);
  await page.mouse.click(centerX, centerY);
  await sleep(waitAfter);
}

// Yavas yazma
async function typeSlowly(page: Page, text: string): Promise<void> {
  for (const char of text) {
    await page.keyboard.type(char);
    await sleep(CONFIG.timing.typeDelay);
  }
}

// Smooth scroll
async function smoothScroll(page: Page, container: string, amount: number, duration: number = 1000): Promise<void> {
  const steps = 20;
  const stepAmount = amount / steps;
  
  for (let i = 0; i < steps; i++) {
    await page.evaluate(({ sel, amt }) => {
      const el = document.querySelector(sel);
      if (el) el.scrollBy({ top: amt, behavior: 'auto' });
    }, { sel: container, amt: stepAmount });
    await sleep(duration / steps);
  }
}

// Highlight efekti
async function highlightElement(page: Page, selector: string, label?: string): Promise<void> {
  const element = await page.$(selector);
  if (!element) return;
  
  const box = await element.boundingBox();
  if (!box) return;
  
  await smoothMouseMove(page, box.x + box.width / 2, box.y + box.height / 2);
  await sleep(300);
  
  await page.evaluate(({ x, y, w, h, text }) => {
    const highlight = document.createElement('div');
    highlight.id = 'demo-highlight';
    highlight.style.cssText = `
      position: fixed;
      left: ${x - 15}px;
      top: ${y - 15}px;
      width: ${w + 30}px;
      height: ${h + 30}px;
      border: 3px solid #3b82f6;
      border-radius: 16px;
      z-index: 999999;
      pointer-events: none;
      box-shadow: 0 0 30px rgba(59,130,246,0.6);
      animation: pulse-hl 1.5s ease-in-out infinite;
    `;
    
    if (text) {
      const labelEl = document.createElement('div');
      labelEl.style.cssText = `
        position: absolute;
        top: -35px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
      `;
      labelEl.textContent = text;
      highlight.appendChild(labelEl);
    }
    
    const style = document.createElement('style');
    style.id = 'demo-hl-style';
    style.textContent = `
      @keyframes pulse-hl {
        0%, 100% { box-shadow: 0 0 30px rgba(59,130,246,0.6); }
        50% { box-shadow: 0 0 50px rgba(59,130,246,0.8); }
      }
    `;
    document.head.appendChild(style);
    document.body.appendChild(highlight);
  }, { x: box.x, y: box.y, w: box.width, h: box.height, text: label });
  
  await sleep(CONFIG.timing.highlightDuration);
  
  await page.evaluate(() => {
    document.getElementById('demo-highlight')?.remove();
    document.getElementById('demo-hl-style')?.remove();
  });
  await sleep(300);
}

// ============ DEMO SENARYOSU ============

async function runDemo(): Promise<void> {
  console.log('Demo kaydı baslatiliyor...\n');
  
  const browser = await chromium.launch({
    headless: CONFIG.browser.headless,
    args: [`--window-size=${CONFIG.browser.width},${CONFIG.browser.height}`, '--disable-infobars']
  });
  
  const context = await browser.newContext({
    viewport: { width: CONFIG.browser.width, height: CONFIG.browser.height },
    deviceScaleFactor: 1,
  });
  
  // Mouse cursor overlay - her sayfada
  await context.addInitScript(() => {
    (window as any).__cursorX = window.innerWidth / 2;
    (window as any).__cursorY = window.innerHeight / 2;
    
    function createCursor() {
      if (document.getElementById('demo-cursor')) return;
      
      const cursor = document.createElement('div');
      cursor.id = 'demo-cursor';
      cursor.innerHTML = '<div class="cursor-ring"></div><div class="cursor-dot"></div>';
      cursor.style.cssText = `
        position: fixed;
        left: ${(window as any).__cursorX}px;
        top: ${(window as any).__cursorY}px;
        width: 40px;
        height: 40px;
        pointer-events: none;
        z-index: 2147483647;
        transform: translate(-50%, -50%);
        transition: transform 0.1s ease-out;
      `;
      
      const style = document.createElement('style');
      style.id = 'demo-cursor-style';
      style.textContent = `
        #demo-cursor .cursor-ring {
          width: 40px;
          height: 40px;
          border-radius: 50%;
          background: radial-gradient(circle, rgba(59,130,246,0.8) 0%, rgba(59,130,246,0.4) 40%, transparent 70%);
          box-shadow: 0 0 30px rgba(59,130,246,0.7), 0 0 60px rgba(59,130,246,0.3);
          animation: cursor-pulse 2s ease-in-out infinite;
        }
        #demo-cursor .cursor-dot {
          position: absolute;
          left: 50%;
          top: 50%;
          transform: translate(-50%, -50%);
          width: 10px;
          height: 10px;
          border-radius: 50%;
          background: white;
          box-shadow: 0 0 15px white, 0 0 30px rgba(59,130,246,0.8);
        }
        @keyframes cursor-pulse {
          0%, 100% { transform: scale(1); }
          50% { transform: scale(1.15); }
        }
      `;
      document.head.appendChild(style);
      document.body.appendChild(cursor);
    }
    
    if (document.body) createCursor();
    else document.addEventListener('DOMContentLoaded', createCursor);
    
    setInterval(() => {
      const c = document.getElementById('demo-cursor');
      if (c) {
        c.style.left = (window as any).__cursorX + 'px';
        c.style.top = (window as any).__cursorY + 'px';
      }
    }, 16);
  });
  
  const page = await context.newPage();
  
  // Ornek PDF dosyasi olustur
  const dummyPdfPath = path.join(__dirname, 'dummy.pdf');
  if (!fs.existsSync(dummyPdfPath)) {
    // Basit bir PDF olustur
    const pdfContent = Buffer.from('%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R>>endobj\nxref\n0 4\n0000000000 65535 f \n0000000009 00000 n \n0000000052 00000 n \n0000000101 00000 n \ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF');
    fs.writeFileSync(dummyPdfPath, pdfContent);
  }
  
  try {
    // ========== 1. LOGIN ==========
    console.log('Login sayfasina gidiliyor...');
    await page.goto(CONFIG.baseUrl + '/login');
    await sleep(2000);
    
    console.log('Kullanici adi giriliyor...');
    await smoothClick(page, 'input[type="text"]');
    await typeSlowly(page, CONFIG.credentials.username);
    await sleep(500);
    
    console.log('Sifre giriliyor...');
    await smoothClick(page, 'input[type="password"]');
    await typeSlowly(page, CONFIG.credentials.password);
    await sleep(500);
    
    console.log('Giris yapiliyor...');
    await smoothClick(page, 'button[type="submit"]', 2000);
    await page.waitForURL('**/dashboard', { timeout: 10000 });
    
    // ========== 2. DASHBOARD - 10 SANIYE ==========
    console.log('Dashboard gosteriliyor (10 saniye)...');
    await sleep(10000);
    
    // ========== 3. IKMAL EKLE ==========
    console.log('Ikmal Ekle butonuna basiliyor...');
    // Plus ikonu olan butonu bul
    const ikmalBtn = await page.$('button:has(svg.lucide-plus)');
    if (ikmalBtn) {
      const box = await ikmalBtn.boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(300);
        await ikmalBtn.click();
        await sleep(1000);
      }
    }
    
    console.log('Ikmal bilgileri giriliyor...');
    await sleep(500);
    
    // Modal icindeki inputlari bul - fixed modal icinde
    const modalInputs = await page.$$('div.fixed input:not([type="date"]):not([type="hidden"])');
    console.log(`Modal'da ${modalInputs.length} input bulundu`);
    
    // Gemi Adi (ilk input)
    if (modalInputs[0]) {
      const box = await modalInputs[0].boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(200);
        await modalInputs[0].click();
        await typeSlowly(page, CONFIG.demo.ikmal.vesselName);
        await sleep(300);
      }
    }
    // Miktar (ikinci input)
    if (modalInputs[1]) {
      const box = await modalInputs[1].boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(200);
        await modalInputs[1].click();
        await typeSlowly(page, CONFIG.demo.ikmal.quantity);
        await sleep(300);
      }
    }
    // Dolum Yeri (ucuncu input)
    if (modalInputs[2]) {
      const box = await modalInputs[2].boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(200);
        await modalInputs[2].click();
        await typeSlowly(page, CONFIG.demo.ikmal.loadingPlace);
        await sleep(300);
      }
    }
    // Ikmal Limani (dorduncu input)
    if (modalInputs[3]) {
      const box = await modalInputs[3].boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(200);
        await modalInputs[3].click();
        await typeSlowly(page, CONFIG.demo.ikmal.port);
        await sleep(300);
      }
    }
    
    // Tarih inputu
    const dateInput = await page.$('div.fixed input[type="date"]');
    if (dateInput) {
      const box = await dateInput.boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(200);
        await dateInput.fill(CONFIG.demo.ikmal.date);
        await sleep(500);
      }
    }
    
    console.log('Ikmal kaydediliyor...');
    // Modal'daki Kaydet butonunu bul - text icerigi ile
    const kaydetBtn = await page.$('div.fixed button:has-text("Kaydet")');
    if (kaydetBtn) {
      const box = await kaydetBtn.boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(300);
        await kaydetBtn.click();
        await sleep(1500);
      }
    }
    
    // ========== 4. ARAC EVRAKLARI ==========
    console.log('Arac Evraklari bolumune gidiliyor...');
    // Sidebar'da "Arac Evraklari" metnini iceren butonu bul
    const aracBtn = await page.locator('aside button:has-text("Araç Evrakları")').first();
    if (await aracBtn.isVisible()) {
      const box = await aracBtn.boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(300);
        await aracBtn.click();
        await sleep(800);
      }
    }
    
    console.log('Asmira Ozmal sayfasina giris...');
    // Alt menudeki Asmira Ozmal linkine tikla
    const asmiraLink = await page.$('aside a[href="/vehicle-documents/asmira"]');
    if (asmiraLink) {
      const box = await asmiraLink.boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(300);
        await asmiraLink.click();
        await sleep(2000);
      }
    }
    
    // ========== 5. YENI ARAC EKLE ==========
    console.log('Yeni Arac/Dorse ekleniyor...');
    // Plus ikonu olan butonu bul (header'daki)
    const yeniAracBtn = await page.$('div.px-6 button:has(svg.lucide-plus)');
    if (yeniAracBtn) {
      const box = await yeniAracBtn.boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(300);
        await yeniAracBtn.click();
        await sleep(1000);
      }
    }
    
    await sleep(500);
    // Modal icindeki inputlari bul
    const vehicleInputs = await page.$$('div.fixed input:not([type="date"]):not([type="hidden"])');
    
    if (vehicleInputs[0]) {
      const box = await vehicleInputs[0].boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(200);
        await vehicleInputs[0].click();
        await typeSlowly(page, CONFIG.demo.vehicle.plate);
        await sleep(300);
      }
    }
    if (vehicleInputs[1]) {
      const box = await vehicleInputs[1].boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(200);
        await vehicleInputs[1].click();
        await typeSlowly(page, CONFIG.demo.vehicle.trailerPlate);
        await sleep(300);
      }
    }
    
    console.log('Arac kaydediliyor...');
    // Modal'daki Kaydet butonunu bul
    const aracKaydetBtn = await page.$('div.fixed button:has-text("Kaydet")');
    if (aracKaydetBtn) {
      const box = await aracKaydetBtn.boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(300);
        await aracKaydetBtn.click();
        await sleep(2000);
      }
    }
    
    // ========== 6. EVRAKLAR PANELI ==========
    console.log('Evraklar paneli aciliyor...');
    
    // Ana icerik alanindaki Evraklar butonunu bul
    const allButtons = await page.$$('button');
    for (const btn of allButtons) {
      const text = await btn.textContent();
      if (text && text.includes('Evraklar')) {
        const isInAside = await btn.evaluate((el) => el.closest('aside') !== null);
        if (!isInAside) {
          const box = await btn.boundingBox();
          if (box) {
            await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
            await sleep(300);
            await btn.click();
            console.log('Evraklar butonuna tiklandi');
            break;
          }
        }
      }
    }
    
    await sleep(2000);
    
    // ========== 7. TUM EVRAKLARI YAVASCA GOSTER ==========
    console.log('Evrak basliklari gosteriliyor...');
    
    const evrakItems = await page.$$('div.rounded-lg.border.p-4');
    console.log(`${evrakItems.length} evrak bulundu`);
    
    // Yavas scroll ile tum evraklari goster
    for (let i = 0; i < evrakItems.length; i++) {
      const item = evrakItems[i];
      const box = await item.boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(400);
        
        // Her 3 evrakta bir scroll yap
        if (i > 0 && i % 3 === 0) {
          await page.evaluate(() => {
            const container = document.querySelector('div.flex-1.overflow-y-auto');
            if (container) container.scrollBy({ top: 200, behavior: 'smooth' });
          });
          await sleep(600);
        }
      }
    }
    
    // Basa don
    await page.evaluate(() => {
      const container = document.querySelector('div.flex-1.overflow-y-auto');
      if (container) container.scrollTo({ top: 0, behavior: 'smooth' });
    });
    await sleep(1000);
    
    // ========== 8. RUHSAT PDF YUKLE ==========
    console.log('Ruhsat dosyasi yukleniyor...');
    
    const fileInputs = await page.$$('input[type="file"]');
    if (fileInputs.length > 0) {
      await fileInputs[0].setInputFiles(dummyPdfPath);
      await sleep(1500);
    }
    
    // ========== 9. SURESI GECMIS TARIH GIR ==========
    console.log('Suresi gecmis tarih giriliyor: ' + CONFIG.demo.document.expiredDate);
    
    const panelDateInputs = await page.$$('div.fixed input[type="date"]');
    if (panelDateInputs.length > 0) {
      const firstDateInput = panelDateInputs[0];
      const box = await firstDateInput.boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(300);
        await firstDateInput.click();
        await sleep(200);
        await firstDateInput.fill(CONFIG.demo.document.expiredDate);
        await sleep(2000);
        
        // Suresi gecmis uyarisini goster
        console.log('Suresi gecmis evrak uyarisi gosteriliyor...');
        const expiredBadge = await page.$('span:has-text("SURESI GECMIS")');
        if (expiredBadge) {
          await highlightElement(page, 'span:has-text("SURESI GECMIS")', 'Suresi Gecmis Evrak!');
        }
        
        // ========== 10. GECERLI TARIH GIR ==========
        console.log('Gecerli tarih giriliyor: ' + CONFIG.demo.document.validDate);
        await sleep(500);
        
        await firstDateInput.click();
        await sleep(200);
        await firstDateInput.fill(CONFIG.demo.document.validDate);
        await sleep(2000);
        console.log('Evrak artik gecerli!');
      }
    }
    
    // Panel kapat
    console.log('Panel kapatiliyor...');
    const backdrop = await page.$('button.absolute.inset-0');
    if (backdrop) {
      await backdrop.click();
      await sleep(1000);
    }
    
    // ========== 11. SOFOR EVRAKLARI ==========
    console.log('Sofor Evraklari bolumune gidiliyor...');
    // Sofor Evraklari linkine tikla
    const soforLink = await page.$('aside a[href="/driver-documents"]');
    if (soforLink) {
      const box = await soforLink.boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(300);
        await soforLink.click();
        await sleep(2000);
      }
    }
    
    // ========== 12. YENI SOFOR EKLE ==========
    console.log('Yeni Sofor ekleniyor...');
    // Plus ikonu olan butonu bul
    const yeniSoforBtn = await page.$('div.px-6 button:has(svg.lucide-plus)');
    if (yeniSoforBtn) {
      const box = await yeniSoforBtn.boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(300);
        await yeniSoforBtn.click();
        await sleep(1000);
      }
    }
    
    await sleep(500);
    // Modal icindeki inputlari bul
    const driverInputs = await page.$$('div.fixed input:not([type="date"]):not([type="hidden"])');
    
    if (driverInputs[0]) {
      const box = await driverInputs[0].boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(200);
        await driverInputs[0].click();
        await typeSlowly(page, CONFIG.demo.driver.name);
        await sleep(300);
      }
    }
    if (driverInputs[1]) {
      const box = await driverInputs[1].boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(200);
        await driverInputs[1].click();
        await typeSlowly(page, CONFIG.demo.driver.tcNo);
        await sleep(300);
      }
    }
    if (driverInputs[2]) {
      const box = await driverInputs[2].boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(200);
        await driverInputs[2].click();
        await typeSlowly(page, CONFIG.demo.driver.phone);
        await sleep(300);
      }
    }
    
    console.log('Sofor kaydediliyor...');
    // Modal'daki Kaydet butonunu bul
    const soforKaydetBtn = await page.$('div.fixed button:has-text("Kaydet")');
    if (soforKaydetBtn) {
      const box = await soforKaydetBtn.boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(300);
        await soforKaydetBtn.click();
        await sleep(2000);
      }
    }
    
    // ========== 13. SOFOR EVRAKLARI PANELI ==========
    console.log('Sofor evraklari paneli aciliyor...');
    
    const driverButtons = await page.$$('button');
    for (const btn of driverButtons) {
      const text = await btn.textContent();
      if (text && text.includes('Evraklar')) {
        const isInAside = await btn.evaluate((el) => el.closest('aside') !== null);
        if (!isInAside) {
          const box = await btn.boundingBox();
          if (box) {
            await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
            await sleep(300);
            await btn.click();
            break;
          }
        }
      }
    }
    
    await sleep(2000);
    
    // Sofor evraklarini goster
    console.log('Sofor evraklari gosteriliyor...');
    const driverEvrakItems = await page.$$('div.rounded-lg.border.p-4');
    
    for (let i = 0; i < Math.min(driverEvrakItems.length, 10); i++) {
      const item = driverEvrakItems[i];
      const box = await item.boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(400);
        
        if (i > 0 && i % 3 === 0) {
          await page.evaluate(() => {
            const container = document.querySelector('div.flex-1.overflow-y-auto');
            if (container) container.scrollBy({ top: 200, behavior: 'smooth' });
          });
          await sleep(600);
        }
      }
    }
    
    // Basa don
    await page.evaluate(() => {
      const container = document.querySelector('div.flex-1.overflow-y-auto');
      if (container) container.scrollTo({ top: 0, behavior: 'smooth' });
    });
    await sleep(1000);
    
    // Sofor evragina PDF yukle
    console.log('Sofor evragina dosya yukleniyor...');
    const driverFileInputs = await page.$$('input[type="file"]');
    if (driverFileInputs.length > 0) {
      await driverFileInputs[0].setInputFiles(dummyPdfPath);
      await sleep(1500);
    }
    
    // Suresi gecmis tarih gir
    console.log('Sofor evragi icin suresi gecmis tarih giriliyor...');
    const driverDateInputs = await page.$$('div.fixed input[type="date"]');
    if (driverDateInputs.length > 0) {
      const dateInput = driverDateInputs[0];
      const box = await dateInput.boundingBox();
      if (box) {
        await smoothMouseMove(page, box.x + box.width/2, box.y + box.height/2);
        await sleep(300);
        await dateInput.fill(CONFIG.demo.document.expiredDate);
        await sleep(2000);
        
        // Gecerli tarih gir
        console.log('Gecerli tarih giriliyor...');
        await dateInput.fill(CONFIG.demo.document.validDate);
        await sleep(2000);
      }
    }
    
    // Panel kapat
    console.log('Sofor evraklari paneli kapatiliyor...');
    const driverBackdrop = await page.$('div.fixed button.absolute');
    if (driverBackdrop) {
      await driverBackdrop.click();
      await sleep(1000);
    }
    
    // ========== FINAL ==========
    console.log('\nDemo tamamlandi!');
    console.log('Simdi ekran kaydini durdurabilirsiniz.\n');
    
    await sleep(5000);
    
  } catch (error) {
    console.error('Hata:', error);
  } finally {
    // Dummy PDF'i sil
    if (fs.existsSync(dummyPdfPath)) {
      fs.unlinkSync(dummyPdfPath);
    }
    await browser.close();
  }
}

runDemo().catch(console.error);
