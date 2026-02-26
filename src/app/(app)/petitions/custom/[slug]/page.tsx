"use client";

import { useState, useRef, useEffect } from "react";
import { ArrowLeft, FileText, X, Download, Plus, Trash2, Save, Edit3 } from "lucide-react";
import Link from "next/link";
import { useParams } from "next/navigation";
import { usePetitionStore, type CustomTemplate } from "@/store/petitionStore";
import { ContextMenu } from "@/components/ui/ContextMenu";

function formatDocument(text: string): string {
  const lines = text.split('\n');
  let html = '';
  
  for (let i = 0; i < lines.length; i++) {
    const line = lines[i];
    
    // İlk satır (tarih) - sağa hizala
    if (i === 0 && line.match(/^\d{2}\.\d{2}\.\d{4}/)) {
      html += `<div style="text-align: right; margin-bottom: 20px;">${line}</div>`;
    }
    // Başlıklar - ortala ve altı çizili
    else if (line.includes('MÜDÜRLÜĞÜNE') || line.includes("MÜDÜRLÜĞÜ'NE")) {
      html += `<div style="text-align: center; text-decoration: underline; font-weight: bold; margin-bottom: 10px;">${line}</div>`;
    }
    // Boş satır
    else if (line.trim() === '') {
      html += '<br/>';
    }
    // Normal satır
    else {
      html += `<div>${line}</div>`;
    }
  }
  
  return html;
}

export default function CustomCategoryPage() {
  const params = useParams();
  const slug = params.slug as string;
  
  const [selectedTemplate, setSelectedTemplate] = useState<CustomTemplate | null>(null);
  const [editedText, setEditedText] = useState("");
  const [formattedHtml, setFormattedHtml] = useState("");
  const editorRef = useRef<HTMLDivElement>(null);
  const isInitialMount = useRef(true);
  
  // Yeni şablon ekleme state'leri
  const [isAddingNew, setIsAddingNew] = useState(false);
  const [newShortName, setNewShortName] = useState("");
  const [newName, setNewName] = useState("");
  
  // Store
  const { customTemplates, customCategories, addTemplate, updateTemplate, deleteTemplate } = usePetitionStore();
  
  // Kategoriyi bul
  const category = (customCategories ?? []).find(c => c.slug === slug);
  const categoryTemplates = (customTemplates ?? []).filter(t => t.category === slug);
  
  // İsim düzenleme modalı
  const [editingTemplate, setEditingTemplate] = useState<CustomTemplate | null>(null);
  const [editShortName, setEditShortName] = useState("");
  const [editName, setEditName] = useState("");
  
  // Hydration
  const [mounted, setMounted] = useState(false);
  useEffect(() => {
    setMounted(true);
  }, []);

  function openTemplateEditor(template: CustomTemplate) {
    setSelectedTemplate(template);
    setIsAddingNew(false);
    setEditedText(template.defaultText);
  }
  
  function openNewTemplate() {
    setIsAddingNew(true);
    setNewShortName("");
    setNewName("");
    setSelectedTemplate({
      id: "new",
      shortName: "YENİ",
      name: "Yeni Belge",
      defaultText: `${new Date().toLocaleDateString('tr-TR')}


_______________


_______________


Saygılarımızla.
`,
      category: slug,
      createdAt: Date.now(),
    });
    setEditedText("");
  }
  
  function saveNewTemplate() {
    if (!newShortName.trim() || !newName.trim()) {
      alert("Lütfen kısa ad ve tam ad girin");
      return;
    }
    
    const currentContent = editorRef.current?.innerText || editedText;
    
    addTemplate({
      shortName: newShortName.toUpperCase(),
      name: newName,
      category: slug,
      defaultText: currentContent,
      createdAt: Date.now(),
    });
    
    closeEditor();
  }
  
  function handleDeleteTemplate(id: string) {
    if (window.confirm("Bu şablonu silmek istediğinize emin misiniz?")) {
      deleteTemplate(id);
    }
  }
  
  function openEditModal(template: CustomTemplate) {
    setEditingTemplate(template);
    setEditShortName(template.shortName);
    setEditName(template.name);
  }
  
  function saveEditedTemplate() {
    if (!editingTemplate) return;
    if (!editShortName.trim() || !editName.trim()) {
      alert("Lütfen kısa ad ve tam ad girin");
      return;
    }
    
    updateTemplate(editingTemplate.id, {
      shortName: editShortName.toUpperCase(),
      name: editName,
    });
    
    setEditingTemplate(null);
  }

  function closeEditor() {
    setSelectedTemplate(null);
    setEditedText("");
    setFormattedHtml("");
    isInitialMount.current = true;
    setIsAddingNew(false);
  }

  // İlk açılışta formatlanmış HTML'i ayarla
  useEffect(() => {
    if (selectedTemplate && isInitialMount.current) {
      setFormattedHtml(formatDocument(selectedTemplate.defaultText));
      isInitialMount.current = false;
    }
  }, [selectedTemplate]);

  function handleExportPDF() {
    const currentContent = editorRef.current?.innerText || editedText;
    
    const printWindow = window.open("", "_blank");
    if (printWindow) {
      printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
          <title>${selectedTemplate?.name} - Belge</title>
          <style>
            @page {
              size: A4;
              margin: 0;
            }
            * {
              margin: 0;
              padding: 0;
              box-sizing: border-box;
            }
            body {
              font-family: 'Times New Roman', serif;
              font-size: 12pt;
              line-height: 1.8;
              color: #000;
            }
            .page {
              width: 210mm;
              height: 297mm;
              position: relative;
              background-image: url('${window.location.origin}/letterhead.png.png');
              background-size: 100% 100%;
              background-position: top center;
              background-repeat: no-repeat;
            }
            .content {
              position: absolute;
              top: 38mm;
              left: 20mm;
              right: 20mm;
              bottom: 58mm;
              font-family: 'Times New Roman', serif;
              font-size: 12pt;
              line-height: 1.6;
            }
            @media print {
              body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
              .page { margin: 0; }
            }
          </style>
        </head>
        <body>
          <div class="page">
            <div class="content">
              ${formatDocument(currentContent)}
            </div>
          </div>
        </body>
        </html>
      `);
      printWindow.document.close();
      setTimeout(() => {
        printWindow.print();
      }, 500);
    }
  }

  // Kategori bulunamadıysa
  if (mounted && !category) {
    return (
      <div className="-m-6 flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col items-center justify-center p-4">
        <div className="text-center">
          <div className="mb-4 text-6xl">🔍</div>
          <h1 className="mb-2 text-2xl font-bold text-white">Kategori Bulunamadı</h1>
          <p className="mb-6 text-white/60">Bu kategori mevcut değil veya silinmiş olabilir.</p>
          <Link
            href="/petitions"
            className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500"
          >
            <ArrowLeft className="h-4 w-4" />
            Dilekçelere Dön
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="-m-6 flex min-h-[calc(100vh-80px)] w-full max-w-none flex-col p-4">
      <div className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] text-white shadow-[0_0_40px_rgba(0,0,0,0.3)]">
        {/* Header */}
        <div className="flex flex-none flex-wrap items-center justify-between gap-3 border-b border-white/10 bg-white/[0.02] px-6 py-4">
          <div className="flex items-center gap-4">
            <Link
              href="/petitions"
              className="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 text-white/60 transition hover:bg-white/10 hover:text-white"
            >
              <ArrowLeft className="h-4 w-4" />
            </Link>
            <div>
              <div className="text-sm font-semibold tracking-wider text-white/70">
                DİLEKÇELER
              </div>
              <div className="text-2xl font-semibold tracking-tight">{category?.title || "Yükleniyor..."}</div>
            </div>
          </div>
          <button
            type="button"
            onClick={openNewTemplate}
            className="inline-flex h-10 items-center gap-2 rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white transition hover:bg-blue-500"
          >
            <Plus className="h-4 w-4" />
            Belge Ekle
          </button>
        </div>

        {/* Stats Bar */}
        <div className="flex flex-none items-center gap-6 border-b border-white/10 px-6 py-3">
          <div className="text-[11px] font-semibold tracking-widest text-white/70">
            Toplam Şablon: {mounted ? categoryTemplates.length : 0}
            <span className="mx-2 text-white/25">|</span>
            Bir şablon seçerek belge metnini düzenleyebilirsiniz
          </div>
        </div>

        {/* Cards Grid */}
        <div className="flex-1 overflow-y-auto p-6">
          {mounted && categoryTemplates.length === 0 ? (
            <div className="flex flex-col items-center justify-center py-16 text-center">
              <div className="mb-4 text-5xl">📄</div>
              <h3 className="mb-2 text-lg font-semibold text-white">Henüz şablon yok</h3>
              <p className="mb-6 text-sm text-white/50">Bu kategoriye ilk şablonunuzu ekleyin</p>
              <button
                type="button"
                onClick={openNewTemplate}
                className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500"
              >
                <Plus className="h-4 w-4" />
                Belge Ekle
              </button>
            </div>
          ) : (
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
              {(mounted ? categoryTemplates : []).map((template) => (
                <ContextMenu
                  key={template.id}
                  items={[
                    {
                      label: "Düzenle",
                      icon: <Edit3 className="h-4 w-4" />,
                      onClick: () => openEditModal(template),
                    },
                    {
                      label: "Sil",
                      icon: <Trash2 className="h-4 w-4" />,
                      onClick: () => handleDeleteTemplate(template.id),
                      danger: true,
                    },
                  ]}
                >
                  <button
                    type="button"
                    onClick={() => openTemplateEditor(template)}
                    className="group flex w-full flex-col rounded-xl border border-white/15 bg-white/[0.04] p-5 text-left shadow-[0_4px_20px_rgba(0,0,0,0.2)] transition-all hover:border-purple-500/40 hover:bg-white/[0.07] hover:shadow-[0_0_25px_rgba(168,85,247,0.15)]"
                  >
                    <div className="mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500/20 text-purple-400 transition group-hover:bg-purple-500/30">
                      <FileText className="h-6 w-6" />
                    </div>
                    <div className="mb-1 text-[16px] font-semibold">{template.shortName}</div>
                    <div className="text-sm text-white/70">{template.name}</div>
                    <div className="mt-3 text-[11px] text-white/40">
                      {new Date(template.createdAt).toLocaleDateString("tr-TR")}
                    </div>
                  </button>
                </ContextMenu>
              ))}
            </div>
          )}
        </div>
      </div>
      
      {/* İsim Düzenleme Modalı */}
      {editingTemplate && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <button
            type="button"
            className="absolute inset-0 bg-black/70 backdrop-blur-sm"
            onClick={() => setEditingTemplate(null)}
            aria-label="Kapat"
          />
          <div className="relative z-10 w-full max-w-md rounded-2xl border border-white/10 bg-[#0B1220] p-6 text-white shadow-xl">
            <h3 className="mb-4 text-lg font-semibold">Şablon Bilgilerini Düzenle</h3>
            <div className="space-y-4">
              <div>
                <label className="mb-1 block text-sm text-white/70">Kısa Ad</label>
                <input
                  type="text"
                  value={editShortName}
                  onChange={(e) => setEditShortName(e.target.value)}
                  className="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white outline-none focus:border-blue-500"
                  placeholder="Örn: BELGE1"
                />
              </div>
              <div>
                <label className="mb-1 block text-sm text-white/70">Tam Ad</label>
                <input
                  type="text"
                  value={editName}
                  onChange={(e) => setEditName(e.target.value)}
                  className="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-white outline-none focus:border-blue-500"
                  placeholder="Örn: Belge Adı"
                />
              </div>
            </div>
            <div className="mt-6 flex justify-end gap-2">
              <button
                type="button"
                onClick={() => setEditingTemplate(null)}
                className="rounded-lg px-4 py-2 text-sm text-white/70 hover:bg-white/10"
              >
                İptal
              </button>
              <button
                type="button"
                onClick={saveEditedTemplate}
                className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500"
              >
                <Save className="h-4 w-4" />
                Kaydet
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Editor Modal - Antetli Kağıt Görünümü */}
      {selectedTemplate && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <button
            type="button"
            className="absolute inset-0 bg-black/70 backdrop-blur-sm"
            onClick={closeEditor}
            aria-label="Kapat"
          />
          <div className="relative z-10 flex h-[95vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-white/10 bg-[#0B1220] text-white shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)]">
            {/* Modal Header - Toolbar */}
            <div className="flex items-center justify-between border-b border-white/10 bg-[#0a0f1a] px-4 py-3">
              <div className="flex items-center gap-3">
                <span className="text-sm font-medium text-white/70">{selectedTemplate.name}</span>
              </div>
              <div className="flex items-center gap-2">
                {isAddingNew && (
                  <>
                    <input
                      type="text"
                      value={newShortName}
                      onChange={(e) => setNewShortName(e.target.value)}
                      placeholder="Kısa Ad (örn: BELGE1)"
                      className="h-8 w-32 rounded-lg border border-white/10 bg-white/5 px-3 text-xs outline-none placeholder:text-white/30 focus:border-cyan-500/50"
                    />
                    <input
                      type="text"
                      value={newName}
                      onChange={(e) => setNewName(e.target.value)}
                      placeholder="Tam Ad (örn: Belge Adı)"
                      className="h-8 w-56 rounded-lg border border-white/10 bg-white/5 px-3 text-xs outline-none placeholder:text-white/30 focus:border-cyan-500/50"
                    />
                    <button
                      type="button"
                      onClick={saveNewTemplate}
                      className="inline-flex h-8 items-center gap-2 rounded-lg bg-cyan-600 px-4 text-xs font-semibold text-white transition hover:bg-cyan-500"
                    >
                      <Save className="h-3.5 w-3.5" />
                      Şablon Olarak Kaydet
                    </button>
                  </>
                )}
                {!isAddingNew && (
                  <button
                    type="button"
                    onClick={() => {
                      setEditedText(selectedTemplate.defaultText);
                      setFormattedHtml(formatDocument(selectedTemplate.defaultText));
                    }}
                    className="rounded-lg px-3 py-1.5 text-xs font-medium text-white/60 transition hover:bg-white/10 hover:text-white"
                  >
                    Sıfırla
                  </button>
                )}
                <button
                  type="button"
                  onClick={handleExportPDF}
                  className="inline-flex h-8 items-center gap-2 rounded-lg bg-green-600 px-4 text-xs font-semibold text-white transition hover:bg-green-500"
                >
                  <Download className="h-3.5 w-3.5" />
                  PDF Olarak Kaydet
                </button>
                <button
                  type="button"
                  className="inline-flex h-8 w-8 items-center justify-center rounded-md text-white/50 hover:bg-white/10 hover:text-white"
                  onClick={closeEditor}
                  aria-label="Kapat"
                >
                  <X className="h-4 w-4" />
                </button>
              </div>
            </div>

            {/* Antetli Kağıt Görünümü - Word Benzeri */}
            <div className="flex-1 overflow-auto bg-gray-400 p-6">
              <div 
                className="relative mx-auto bg-white shadow-2xl" 
                style={{ 
                  width: '794px', 
                  minHeight: '1123px',
                  backgroundImage: 'url(/letterhead.png.png)',
                  backgroundSize: '100% 100%',
                  backgroundPosition: 'top center',
                  backgroundRepeat: 'no-repeat'
                }}
              >
                {/* Editable Content Area - Positioned over letterhead */}
                <div 
                  ref={editorRef}
                  contentEditable
                  suppressContentEditableWarning
                  onBlur={(e) => setEditedText(e.currentTarget.innerText)}
                  className="absolute text-[12pt] leading-[1.6] text-gray-900 outline-none"
                  style={{
                    top: '145px',
                    left: '75px',
                    right: '75px',
                    bottom: '220px',
                    fontFamily: "'Times New Roman', serif",
                    whiteSpace: 'pre-wrap',
                    wordWrap: 'break-word'
                  }}
                  dangerouslySetInnerHTML={{ __html: formattedHtml }}
                />
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
