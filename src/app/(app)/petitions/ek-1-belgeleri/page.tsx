"use client";

import { useRef, useState } from "react";
import { Download, ArrowLeft, RotateCcw, ZoomIn, ZoomOut } from "lucide-react";
import Link from "next/link";

export default function Ek1BelgeleriPage() {
  const contentRef = useRef<HTMLDivElement>(null);
  const [zoom, setZoom] = useState(100);

  const increaseZoom = () => setZoom((prev) => Math.min(prev + 10, 150));
  const decreaseZoom = () => setZoom((prev) => Math.max(prev - 10, 50));

  const getDefaultHtml = () => {
    const today = new Date().toLocaleDateString("tr-TR");
    return `<div style="font-family: 'Times New Roman', serif; font-size: 11pt; color: #000; line-height: 1.4;">
      <!-- Header -->
      <div style="text-align: center; margin-bottom: 5px;">
        <div style="font-size: 14pt; font-weight: bold; text-decoration: underline;">EK-1</div>
      </div>
      <div style="text-align: center; margin-bottom: 15px;">
        <div style="font-size: 11pt;">— <strong style="text-decoration: underline;">İKMAL BİLDİRİM FORMU</strong> —</div>
        <div style="font-size: 9pt;">(SUPPLY NOTIFICATION FORM)</div>
      </div>

      <!-- Tarih -->
      <div style="margin-bottom: 15px; font-size: 10pt;">
        <span style="text-decoration: underline;">Tarih</span> <span style="font-size: 8pt; color: #666;">(Date)</span>: ${today}
      </div>

      <!-- Liman Başkanlığı -->
      <div style="text-align: center; margin-bottom: 15px;">
        <div style="font-size: 12pt; font-weight: bold; text-decoration: underline;">ALİAĞA LİMAN BAŞKANLIĞINA</div>
        <div style="font-size: 9pt; color: #8B0000;">(TO ALİAĞA HARBOURMASTER)</div>
      </div>

      <!-- Ana Tablo -->
      <table style="width: 100%; border-collapse: collapse; margin-bottom: 5px; font-size: 9pt;">
        <tr>
          <th style="border: 1px solid #000; padding: 4px; background: #f5f5f5; width: 28%;"></th>
          <th style="border: 1px solid #000; padding: 4px; background: #f5f5f5; text-align: center; width: 36%;">
            <span style="text-decoration: underline;">Su/Yağ/Yakıt Alacak Gemi</span><br/>
            <span style="font-size: 8pt; font-weight: normal;">(Deriving Vessel)</span>
          </th>
          <th style="border: 1px solid #000; padding: 4px; background: #f5f5f5; text-align: center; width: 36%;">
            <span style="text-decoration: underline;">Su/Yağ/Yakıt Verecek Gemi (Supply Vessel)</span>
          </th>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Geminin Adı</span></strong> <span style="font-size: 8pt; color: #666;">(Name of vessel)</span></td>
          <td style="border: 1px solid #000; padding: 4px;">:</td>
          <td style="border: 1px solid #000; padding: 4px;">KUMBOR</td>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Geminin Cinsi</span></strong> <span style="font-size: 8pt; color: #666;">(Type of vessel)</span></td>
          <td style="border: 1px solid #000; padding: 4px;">:</td>
          <td style="border: 1px solid #000; padding: 4px;">TANKER</td>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">IMO Numarası / Çağrı İşareti</span></strong><br/><span style="font-size: 8pt; color: #666;">(IMO number/ Call sign)</span></td>
          <td style="border: 1px solid #000; padding: 4px;">:</td>
          <td style="border: 1px solid #000; padding: 4px;">9680554</td>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Donatan/İşleten</span></strong> <span style="font-size: 8pt; color: #666;">(Owner/Operator)</span></td>
          <td style="border: 1px solid #000; padding: 4px;">: <span style="color: #8B0000;">DOLDURULACAKTIR.</span></td>
          <td style="border: 1px solid #000; padding: 4px;">ASMİRA LOJİSTİK A.Ş.</td>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">İrtibat Kurulacak Kişi</span></strong> <span style="font-size: 8pt; color: #666;">(Contact person)</span></td>
          <td style="border: 1px solid #000; padding: 4px;">: <span style="color: #8B0000;">DOLDURULACAKTIR.</span></td>
          <td style="border: 1px solid #000; padding: 4px;">TAYLAN TATLI</td>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Adres</span></strong> <span style="font-size: 8pt; color: #666;">(Address)</span></td>
          <td style="border: 1px solid #000; padding: 4px;">: <span style="color: #8B0000;">DOLDURULACAKTIR.</span></td>
          <td style="border: 1px solid #000; padding: 4px;">ALSANCAK MAH. 1456. SOK. NO:83/11 KONAK/İZMİR</td>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Tel. / Faks*</span></strong> <span style="font-size: 8pt; color: #666;">(Tel/Fax)</span></td>
          <td style="border: 1px solid #000; padding: 4px;">: <span style="color: #8B0000;">DOLDURULACAKTIR.</span></td>
          <td style="border: 1px solid #000; padding: 4px;">+90 532 680 62 03</td>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Acentesi</span></strong> <span style="font-size: 8pt; color: #666;">(Agent)</span></td>
          <td style="border: 1px solid #000; padding: 4px;">: <span style="color: #8B0000;">DOLDURULACAKTIR.</span></td>
          <td style="border: 1px solid #000; padding: 4px;">AS-MİRA PETROL A.Ş.</td>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Acente Tel. / Faks*</span></strong> <span style="font-size: 8pt; color: #666;">(Agent Tel/Fax)</span></td>
          <td style="border: 1px solid #000; padding: 4px;">: <span style="color: #8B0000;">DOLDURULACAKTIR.</span></td>
          <td style="border: 1px solid #000; padding: 4px;">0232 422 1989 / 0232 463 0607</td>
        </tr>
      </table>

      <!-- Not -->
      <div style="font-size: 8pt; font-style: italic; margin-bottom: 10px;">
        • <span style="color: #8B0000; text-decoration: underline;">İletişim numaraları günün her saatinde ulaşılabilir numaralar olacaktır.</span> <span style="color: #666;">(Contact numbers must be attainable at all hours of day)</span>
      </div>

      <!-- İkmal Yapılacak Yer -->
      <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9pt;">
        <tr>
          <td style="border: 1px solid #000; padding: 4px; background: #f5f5f5; font-weight: bold;">
            <span style="text-decoration: underline;">İKMAL YAPILACAK YER / MEVKİ</span> <span style="font-weight: normal; font-size: 8pt; color: #666;">(Position/Place of Supply Operation)</span>:
          </td>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;">
            <span style="text-decoration: underline;">Rıhtım, İskele, Şamandıra, Demir Sahası</span><br/>
            <span style="font-size: 8pt; color: #666;">vb.(Quay, pier, buoy, anchorage area etc.)</span>
          </td>
        </tr>
      </table>

      <!-- Tahmini İkmal Zamanı -->
      <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9pt;">
        <tr>
          <th colspan="3" style="border: 1px solid #000; padding: 4px; background: #f5f5f5; text-align: center;">
            <strong style="text-decoration: underline;">TAHMİNİ İKMAL ZAMANI</strong> <span style="font-weight: normal; font-size: 8pt; color: #666;">(Estimated Supply Time)</span>
          </th>
        </tr>
        <tr>
          <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 33%;">
            <span style="text-decoration: underline;">İkmale Başlama Tarih/Saati</span><br/>
            <span style="font-size: 8pt; font-weight: normal; color: #666;">(Commencement of Supply Date and Time)</span>
          </th>
          <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 33%;">
            <span style="text-decoration: underline;">İkmal Bitiş Tarih/Saati</span><br/>
            <span style="font-size: 8pt; font-weight: normal; color: #666;">(Completed of Supply Date and Time)</span>
          </th>
          <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 33%;">
            <span style="text-decoration: underline;">Toplam İkmal Zamanı</span><br/>
            <span style="font-size: 8pt; font-weight: normal; color: #666;">(Total Supply Time)</span>
          </th>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 8px; text-align: center; height: 25px;"></td>
          <td style="border: 1px solid #000; padding: 8px; text-align: center;"></td>
          <td style="border: 1px solid #000; padding: 8px; text-align: center;"></td>
        </tr>
      </table>

      <!-- Verilecek Yakıt -->
      <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9pt;">
        <tr>
          <th colspan="2" style="border: 1px solid #000; padding: 4px; background: #f5f5f5; text-align: center;">
            <strong style="text-decoration: underline;">VERİLECEK YAKITIN, YAĞIN VE SUYUN</strong> <span style="font-weight: normal; font-size: 8pt; color: #666;">(Bunker, Oil or Water to be delivered)</span>
          </th>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px; width: 40%;">
            <strong><span style="text-decoration: underline;">Cinsi ve Miktarı</span></strong> <span style="font-size: 8pt; color: #666;">(Type and amount)</span>
          </td>
          <td style="border: 1px solid #000; padding: 4px;"></td>
        </tr>
      </table>

      <!-- Yakıt Alacak Geminin Acentesi -->
      <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9pt;">
        <tr>
          <th style="border: 1px solid #000; padding: 4px; width: 50%;"></th>
          <th style="border: 1px solid #000; padding: 4px; background: #f5f5f5; text-align: center;">
            <strong style="text-decoration: underline;">Yakıt Alacak Geminin Acentesi</strong><br/>
            <span style="font-size: 8pt; font-weight: normal; color: #666;">(Agent of Deriving Vessel)</span>
          </th>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Adı Soyadı</span></strong> <span style="font-size: 8pt; color: #666;">(Name and Surname)</span></td>
          <td style="border: 1px solid #000; padding: 4px; color: #8B0000;">DOLDURULACAKTIR.</td>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Tarih</span></strong> <span style="font-size: 8pt; color: #666;">(Date)</span></td>
          <td style="border: 1px solid #000; padding: 4px; color: #8B0000;">DOLDURULACAKTIR.</td>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Saat</span></strong> <span style="font-size: 8pt; color: #666;">(Time)</span></td>
          <td style="border: 1px solid #000; padding: 4px; color: #8B0000;">DOLDURULACAKTIR.</td>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">İmza</span></strong> <span style="font-size: 8pt; color: #666;">(Signature)</span> ve <strong><span style="text-decoration: underline;">Kaşe</span></strong> <span style="font-size: 8pt; color: #666;">(Stamp)</span></td>
          <td style="border: 1px solid #000; padding: 4px; color: #8B0000;">DOLDURULACAKTIR.</td>
        </tr>
      </table>

      <!-- Alt İmza Bölümü -->
      <table style="width: 100%; border-collapse: collapse; font-size: 9pt;">
        <tr>
          <th style="border: 1px solid #000; padding: 4px; background: #f5f5f5; text-align: center; width: 50%;">
            <strong style="text-decoration: underline;">İlgili Liman İşletme Tesisi Sorumlusu</strong><br/>
            <span style="font-size: 8pt; font-weight: normal; color: #666;">(Person in Charge of the Relevant Port/Shore Facility)</span>
          </th>
          <th style="border: 1px solid #000; padding: 4px; background: #f5f5f5; text-align: center; width: 50%;">
            <strong style="text-decoration: underline;">Liman Başkanlığı</strong><br/>
            <span style="font-size: 8pt; font-weight: normal; color: #666;">(Harbour Master)</span>
          </th>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Adı Soyadı</span></strong> <span style="font-size: 8pt; color: #666;">(Name and Surname)</span></td>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Adı Soyadı</span></strong> <span style="font-size: 8pt; color: #666;">(Name and Surname)</span></td>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Tarih</span></strong> <span style="font-size: 8pt; color: #666;">(Date)</span></td>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Onay Tarihi</span></strong> <span style="font-size: 8pt; color: #666;">(Date of Approval)</span></td>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Saat</span></strong> <span style="font-size: 8pt; color: #666;">(Time)</span></td>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Onay Saati</span></strong> <span style="font-size: 8pt; color: #666;">(Time of Approval)</span></td>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">İmza</span></strong> <span style="font-size: 8pt; color: #666;">(Signature)</span></td>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">İmza</span></strong> <span style="font-size: 8pt; color: #666;">(Signature)</span></td>
        </tr>
        <tr>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Kaşe</span></strong> <span style="font-size: 8pt; color: #666;">(Stamp)</span></td>
          <td style="border: 1px solid #000; padding: 4px;"><strong><span style="text-decoration: underline;">Kaşe</span></strong> <span style="font-size: 8pt; color: #666;">(Stamp)</span></td>
        </tr>
      </table>
    </div>`;
  };

  function handleReset() {
    if (contentRef.current) {
      contentRef.current.innerHTML = getDefaultHtml();
    }
  }

  function handleExportPDF() {
    const content = contentRef.current?.innerHTML || "";
    const printWindow = window.open("", "_blank");
    if (printWindow) {
      printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
          <title>EK-1 İkmal Bildirim Formu</title>
          <style>
            @page { size: A4; margin: 15mm; }
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'Times New Roman', serif; }
            @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
          </style>
        </head>
        <body>${content}</body>
        </html>
      `);
      printWindow.document.close();
      setTimeout(() => printWindow.print(), 500);
    }
  }

  return (
    <div className="flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col px-2 py-2 sm:px-4">
      <div className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        {/* Header - Toolbar */}
        <div className="flex items-center justify-between border-b border-white/10 bg-[#0a0f1a] px-4 py-3">
          <div className="flex items-center gap-3">
            <Link
              href="/petitions"
              className="flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white"
            >
              <ArrowLeft className="h-4 w-4" />
            </Link>
            <span className="text-sm font-medium text-white/70">EK-1 İkmal Bildirim Formu</span>
          </div>
          <div className="flex items-center gap-2">
            {/* Font Size Controls */}
            <div className="flex items-center gap-1 rounded-lg border border-white/10 px-2 py-1">
              <button
                type="button"
                onClick={decreaseZoom}
                className="flex h-6 w-6 items-center justify-center rounded text-white/60 transition hover:bg-white/10 hover:text-white"
                title="Küçült"
              >
                <ZoomOut className="h-3.5 w-3.5" />
              </button>
              <span className="min-w-[40px] text-center text-xs text-white/70">%{zoom}</span>
              <button
                type="button"
                onClick={increaseZoom}
                className="flex h-6 w-6 items-center justify-center rounded text-white/60 transition hover:bg-white/10 hover:text-white"
                title="Büyüt"
              >
                <ZoomIn className="h-3.5 w-3.5" />
              </button>
            </div>
            <button
              type="button"
              onClick={handleReset}
              className="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10 hover:text-white"
            >
              <RotateCcw className="h-3.5 w-3.5" />
              Sıfırla
            </button>
            <button
              type="button"
              onClick={handleExportPDF}
              className="inline-flex h-8 items-center gap-2 rounded-lg bg-green-600 px-4 text-xs font-semibold text-white transition hover:bg-green-500"
            >
              <Download className="h-3.5 w-3.5" />
              PDF Olarak Kaydet
            </button>
          </div>
        </div>

        {/* Editable Form Preview */}
        <div className="flex-1 overflow-auto bg-gray-400 p-6">
          <div
            ref={contentRef}
            contentEditable
            suppressContentEditableWarning
            className="mx-auto bg-white shadow-2xl outline-none"
            style={{
              width: "794px",
              minHeight: "1123px",
              padding: "50px 60px",
              transform: `scale(${zoom / 100})`,
              transformOrigin: "top center",
            }}
            dangerouslySetInnerHTML={{ __html: getDefaultHtml() }}
          />
        </div>
      </div>
    </div>
  );
}
