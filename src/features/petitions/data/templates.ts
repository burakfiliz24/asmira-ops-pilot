import type { PetitionTemplate } from "../domain/types";

export const petitionTemplates: PetitionTemplate[] = [
  // Taahhütname Şablonları
  {
    id: "taahhut-tasima",
    title: "Taşıma Taahhütnamesi",
    description: "Tehlikeli madde taşımacılığı için gerekli taahhütname belgesi",
    icon: "FileCheck",
    category: "taahhutname",
    fields: [
      { id: "portName", label: "Liman", type: "select", options: ["HABAŞ Limanı", "IDÇ Limanı", "Ambarlı Limanı", "İzmit Limanı", "Yarımca Limanı", "Tüpraş Limanı", "Diğer"], required: true },
      { id: "companyName", label: "Firma Adı", type: "text", placeholder: "Asmira Özmal Ltd. Şti.", required: true },
      { id: "vehiclePlate", label: "Araç Plakası", type: "text", placeholder: "34 ASM 014", required: true },
      { id: "trailerPlate", label: "Dorse Plakası", type: "text", placeholder: "34 DOR 123", required: true },
      { id: "driverName", label: "Şoför Adı Soyadı", type: "text", placeholder: "Mehmet Yılmaz", required: true },
      { id: "driverTC", label: "Şoför TC Kimlik No", type: "text", placeholder: "12345678901", required: true },
      { id: "productType", label: "Taşınan Ürün", type: "text", placeholder: "Akaryakıt / Fuel Oil", required: true },
      { id: "quantity", label: "Miktar (MT)", type: "text", placeholder: "500", required: true },
      { id: "date", label: "Tarih", type: "date", required: true },
    ],
  },
  // Liman Dilekçeleri
  {
    id: "liman-giris",
    title: "Liman Giriş İzni Dilekçesi",
    description: "Araç ve personelin liman sahasına giriş izni talebi",
    icon: "LogIn",
    category: "liman",
    fields: [
      { id: "portAuthority", label: "Liman İdaresi", type: "text", placeholder: "İzmit Liman Başkanlığı", required: true },
      { id: "companyName", label: "Firma Adı", type: "text", placeholder: "Asmira Özmal Ltd. Şti.", required: true },
      { id: "vehiclePlate", label: "Araç Plakası", type: "text", placeholder: "34 ASM 014", required: true },
      { id: "trailerPlate", label: "Dorse Plakası", type: "text", placeholder: "34 DOR 123" },
      { id: "driverName", label: "Şoför Adı Soyadı", type: "text", placeholder: "Mehmet Yılmaz", required: true },
      { id: "driverTC", label: "Şoför TC Kimlik No", type: "text", placeholder: "12345678901", required: true },
      { id: "vesselName", label: "Gemi Adı", type: "text", placeholder: "M/T Asmira Star", required: true },
      { id: "purpose", label: "Giriş Amacı", type: "select", options: ["Yakıt İkmali", "Yük Teslimi", "Yük Alımı", "Diğer"], required: true },
      { id: "entryDate", label: "Giriş Tarihi", type: "date", required: true },
      { id: "exitDate", label: "Çıkış Tarihi", type: "date", required: true },
    ],
  },
];
