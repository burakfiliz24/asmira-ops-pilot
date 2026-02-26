import { create } from 'zustand';

// Types
export type VehicleDocumentType = 
  | "ruhsat"
  | "tasitKarti"
  | "t9Adr"
  | "trafikSigortasi"
  | "tehlikeliMaddeSigortasi"
  | "kasko"
  | "tuvturk"
  | "egzozEmisyon"
  | "sayacKalibrasyon"
  | "takografKalibrasyon"
  | "faaliyetBelgesi"
  | "yetkiBelgesi"
  | "hortumBasin"
  | "tankMuayeneSertifikasi"
  | "vergiLevhasi";
export type DriverDocumentType = 
  | "kimlik"
  | "ehliyet"
  | "src5Psikoteknik"
  | "adliSicil"
  | "iseGirisBildirge"
  | "ikametgah"
  | "kkdZimmet"
  | "saglikMuayene"
  | "isgEgitimBelgesi"
  | "yanginEgitimSertifikasi";

export type DocumentInfo = {
  type: string;
  label: string;
  fileName: string | null;
  fileUrl: string | null;
  fileBlob: Blob | null;
  expiryDate: string | null;
};

// Çekici (Araç)
export type Truck = {
  id: string;
  plate: string;
  category: "asmira" | "supplier";
  documents: DocumentInfo[];
};

// Dorse
export type Trailer = {
  id: string;
  plate: string;
  category: "asmira" | "supplier";
  documents: DocumentInfo[];
};

// Araç Seti (Çekici + Dorse eşleştirmesi) - operasyonlar için
export type VehicleSet = {
  id: string;
  truckId: string;
  trailerId: string;
  category: "asmira" | "supplier";
};

// Geriye uyumluluk için Vehicle tipi (deprecated - VehicleSet kullanın)
export type Vehicle = {
  id: string;
  vehiclePlate: string;
  trailerPlate: string;
  category: "asmira" | "supplier";
  vehicleDocuments: DocumentInfo[];
  trailerDocuments: DocumentInfo[];
};

export type Driver = {
  id: string;
  name: string;
  tcNo: string;
  phone: string;
  documents: DocumentInfo[];
};

// Document Labels
export const vehicleDocumentLabels: Record<VehicleDocumentType, string> = {
  ruhsat: "Ruhsat",
  tasitKarti: "Taşıt Kartı",
  t9Adr: "T9 ADR",
  trafikSigortasi: "Trafik Sigortası",
  tehlikeliMaddeSigortasi: "Tehlikeli Madde Sigortası",
  kasko: "Kasko",
  tuvturk: "TÜVTÜRK",
  egzozEmisyon: "Egzoz Emisyon",
  sayacKalibrasyon: "Sayaç Kalibrasyon",
  takografKalibrasyon: "Takograf Kalibrasyon",
  faaliyetBelgesi: "Faaliyet Belgesi",
  yetkiBelgesi: "Yetki Belgesi",
  hortumBasin: "Hortum Basın.",
  tankMuayeneSertifikasi: "Tank Muayene Sertifikası",
  vergiLevhasi: "Vergi Levhası",
};

export const driverDocumentLabels: Record<DriverDocumentType, string> = {
  kimlik: "Kimlik",
  ehliyet: "Ehliyet",
  src5Psikoteknik: "SRC 5 Psikoteknik",
  adliSicil: "Adli Sicil",
  iseGirisBildirge: "İşe Giriş Bildirgesi",
  ikametgah: "İkametgah",
  kkdZimmet: "KKD Zimmet",
  saglikMuayene: "Sağlık Muayene",
  isgEgitimBelgesi: "İSG Eğitim Belgesi",
  yanginEgitimSertifikasi: "Yangın Eğitim Sertifikası",
};

// Default Documents
export const createDefaultVehicleDocuments = (): DocumentInfo[] => [
  { type: "ruhsat", label: vehicleDocumentLabels.ruhsat, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "tasitKarti", label: vehicleDocumentLabels.tasitKarti, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "t9Adr", label: vehicleDocumentLabels.t9Adr, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "trafikSigortasi", label: vehicleDocumentLabels.trafikSigortasi, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "tehlikeliMaddeSigortasi", label: vehicleDocumentLabels.tehlikeliMaddeSigortasi, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "kasko", label: vehicleDocumentLabels.kasko, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "tuvturk", label: vehicleDocumentLabels.tuvturk, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "egzozEmisyon", label: vehicleDocumentLabels.egzozEmisyon, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "sayacKalibrasyon", label: vehicleDocumentLabels.sayacKalibrasyon, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "takografKalibrasyon", label: vehicleDocumentLabels.takografKalibrasyon, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "faaliyetBelgesi", label: vehicleDocumentLabels.faaliyetBelgesi, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "yetkiBelgesi", label: vehicleDocumentLabels.yetkiBelgesi, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "hortumBasin", label: vehicleDocumentLabels.hortumBasin, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "tankMuayeneSertifikasi", label: vehicleDocumentLabels.tankMuayeneSertifikasi, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "vergiLevhasi", label: vehicleDocumentLabels.vergiLevhasi, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
];

export const createDefaultDriverDocuments = (): DocumentInfo[] => [
  { type: "kimlik", label: driverDocumentLabels.kimlik, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "ehliyet", label: driverDocumentLabels.ehliyet, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "src5Psikoteknik", label: driverDocumentLabels.src5Psikoteknik, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "adliSicil", label: driverDocumentLabels.adliSicil, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "iseGirisBildirge", label: driverDocumentLabels.iseGirisBildirge, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "ikametgah", label: driverDocumentLabels.ikametgah, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "kkdZimmet", label: driverDocumentLabels.kkdZimmet, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "saglikMuayene", label: driverDocumentLabels.saglikMuayene, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "isgEgitimBelgesi", label: driverDocumentLabels.isgEgitimBelgesi, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
  { type: "yanginEgitimSertifikasi", label: driverDocumentLabels.yanginEgitimSertifikasi, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null },
];

// Initial Data - Çekiciler (Trucks)
const initialTrucks: Truck[] = [
  { id: "truck_asmira_001", plate: "34 ASM 014", category: "asmira", documents: createDefaultVehicleDocuments() },
  { id: "truck_asmira_002", plate: "33 MRS 320", category: "asmira", documents: createDefaultVehicleDocuments() },
  { id: "truck_supplier_001", plate: "35 ABC 123", category: "supplier", documents: createDefaultVehicleDocuments() },
  { id: "truck_supplier_002", plate: "06 DEF 789", category: "supplier", documents: createDefaultVehicleDocuments() },
];

// Initial Data - Dorseler (Trailers)
const initialTrailers: Trailer[] = [
  { id: "trailer_asmira_001", plate: "34 DOR 123", category: "asmira", documents: createDefaultVehicleDocuments() },
  { id: "trailer_asmira_002", plate: "33 DOR 320", category: "asmira", documents: createDefaultVehicleDocuments() },
  { id: "trailer_asmira_003", plate: "34 DOR 456", category: "asmira", documents: createDefaultVehicleDocuments() },
  { id: "trailer_supplier_001", plate: "35 XYZ 456", category: "supplier", documents: createDefaultVehicleDocuments() },
  { id: "trailer_supplier_002", plate: "06 GHI 012", category: "supplier", documents: createDefaultVehicleDocuments() },
];

// Initial Data - Araç Setleri (Varsayılan eşleştirmeler)
const initialVehicleSets: VehicleSet[] = [
  { id: "set_asmira_001", truckId: "truck_asmira_001", trailerId: "trailer_asmira_001", category: "asmira" },
  { id: "set_asmira_002", truckId: "truck_asmira_002", trailerId: "trailer_asmira_002", category: "asmira" },
  { id: "set_supplier_001", truckId: "truck_supplier_001", trailerId: "trailer_supplier_001", category: "supplier" },
  { id: "set_supplier_002", truckId: "truck_supplier_002", trailerId: "trailer_supplier_002", category: "supplier" },
];

// Geriye uyumluluk için Vehicle listesi oluştur
const createVehiclesFromSets = (trucks: Truck[], trailers: Trailer[], sets: VehicleSet[]): Vehicle[] => {
  return sets.map(set => {
    const truck = trucks.find(t => t.id === set.truckId);
    const trailer = trailers.find(t => t.id === set.trailerId);
    return {
      id: set.id,
      vehiclePlate: truck?.plate || '',
      trailerPlate: trailer?.plate || '',
      category: set.category,
      vehicleDocuments: truck?.documents || createDefaultVehicleDocuments(),
      trailerDocuments: trailer?.documents || createDefaultVehicleDocuments(),
    };
  });
};

const initialDrivers: Driver[] = [
  { id: "d_001", name: "Ahmet Yılmaz", tcNo: "12345678901", phone: "0532 123 45 67", documents: createDefaultDriverDocuments() },
  { id: "d_002", name: "Mehmet Demir", tcNo: "98765432109", phone: "0533 987 65 43", documents: createDefaultDriverDocuments() },
];

// Document target type
export type DocumentTarget = 'truck' | 'trailer';

// Store Interface
interface DocumentStore {
  // Yeni yapı
  trucks: Truck[];
  trailers: Trailer[];
  vehicleSets: VehicleSet[];
  drivers: Driver[];
  
  // Geriye uyumluluk (computed)
  vehicles: Vehicle[];
  
  // Truck Actions
  addTruck: (truck: Omit<Truck, 'id' | 'documents'>) => void;
  updateTruck: (id: string, data: Partial<Pick<Truck, 'plate'>>) => void;
  deleteTruck: (id: string) => void;
  uploadTruckDocument: (truckId: string, docType: string, file: File) => void;
  updateTruckDocument: (truckId: string, docType: string, data: Partial<DocumentInfo>) => void;
  deleteTruckDocument: (truckId: string, docType: string) => void;
  
  // Trailer Actions
  addTrailer: (trailer: Omit<Trailer, 'id' | 'documents'>) => void;
  updateTrailer: (id: string, data: Partial<Pick<Trailer, 'plate'>>) => void;
  deleteTrailer: (id: string) => void;
  uploadTrailerDocument: (trailerId: string, docType: string, file: File) => void;
  updateTrailerDocument: (trailerId: string, docType: string, data: Partial<DocumentInfo>) => void;
  deleteTrailerDocument: (trailerId: string, docType: string) => void;
  
  // VehicleSet Actions
  addVehicleSet: (set: Omit<VehicleSet, 'id'>) => void;
  updateVehicleSet: (id: string, data: Partial<Pick<VehicleSet, 'truckId' | 'trailerId'>>) => void;
  deleteVehicleSet: (id: string) => void;
  
  // Driver Actions
  addDriver: (driver: Omit<Driver, 'id' | 'documents'>) => void;
  updateDriver: (id: string, data: Partial<Pick<Driver, 'name' | 'tcNo' | 'phone'>>) => void;
  deleteDriver: (id: string) => void;
  uploadDriverDocument: (driverId: string, docType: string, file: File) => void;
  updateDriverDocument: (driverId: string, docType: string, data: Partial<DocumentInfo>) => void;
  deleteDriverDocument: (driverId: string, docType: string) => void;
  
  // Selectors
  getAsmiraTrucks: () => Truck[];
  getSupplierTrucks: () => Truck[];
  getAsmiraTrailers: () => Trailer[];
  getSupplierTrailers: () => Trailer[];
  getAsmiraVehicleSets: () => VehicleSet[];
  getSupplierVehicleSets: () => VehicleSet[];
  getTruckById: (id: string) => Truck | undefined;
  getTrailerById: (id: string) => Trailer | undefined;
  
  // Geriye uyumluluk - Vehicle Actions (deprecated)
  addVehicle: (vehicle: Omit<Vehicle, 'id' | 'vehicleDocuments' | 'trailerDocuments'>) => void;
  updateVehicle: (id: string, data: Partial<Pick<Vehicle, 'vehiclePlate' | 'trailerPlate'>>) => void;
  deleteVehicle: (id: string) => void;
  uploadVehicleDocument: (vehicleId: string, target: DocumentTarget, docType: string, file: File) => void;
  updateVehicleDocument: (vehicleId: string, target: DocumentTarget, docType: string, data: Partial<DocumentInfo>) => void;
  deleteVehicleDocument: (vehicleId: string, target: DocumentTarget, docType: string) => void;
  
  // Geriye uyumluluk - Selectors
  getAsmiraVehicles: () => Vehicle[];
  getSupplierVehicles: () => Vehicle[];
  getAllVehicles: () => Vehicle[];
  getAllDrivers: () => Driver[];
}

export const useDocumentStore = create<DocumentStore>((set, get) => ({
  // State
  trucks: initialTrucks,
  trailers: initialTrailers,
  vehicleSets: initialVehicleSets,
  drivers: initialDrivers,
  
  // Geriye uyumluluk - vehicles artık getAllVehicles() ile alınmalı
  vehicles: createVehiclesFromSets(initialTrucks, initialTrailers, initialVehicleSets),
  
  // Truck Actions
  addTruck: (truckData) => {
    const newTruck: Truck = {
      id: `truck_${truckData.category}_${Date.now()}`,
      ...truckData,
      documents: createDefaultVehicleDocuments(),
    };
    set((state) => ({ trucks: [newTruck, ...state.trucks] }));
  },
  
  updateTruck: (id, data) => {
    set((state) => ({
      trucks: state.trucks.map((t) => t.id === id ? { ...t, ...data } : t),
    }));
  },
  
  deleteTruck: (id) => {
    set((state) => ({
      trucks: state.trucks.filter((t) => t.id !== id),
      vehicleSets: state.vehicleSets.filter((s) => s.truckId !== id),
    }));
  },
  
  uploadTruckDocument: (truckId, docType, file) => {
    const fileUrl = URL.createObjectURL(file);
    set((state) => ({
      trucks: state.trucks.map((t) => {
        if (t.id !== truckId) return t;
        return {
          ...t,
          documents: t.documents.map((d) =>
            d.type === docType ? { ...d, fileName: file.name, fileUrl, fileBlob: file } : d
          ),
        };
      }),
    }));
  },
  
  updateTruckDocument: (truckId, docType, data) => {
    set((state) => ({
      trucks: state.trucks.map((t) => {
        if (t.id !== truckId) return t;
        return {
          ...t,
          documents: t.documents.map((d) => d.type === docType ? { ...d, ...data } : d),
        };
      }),
    }));
  },
  
  deleteTruckDocument: (truckId, docType) => {
    set((state) => ({
      trucks: state.trucks.map((t) => {
        if (t.id !== truckId) return t;
        return {
          ...t,
          documents: t.documents.map((d) =>
            d.type === docType ? { ...d, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null } : d
          ),
        };
      }),
    }));
  },
  
  // Trailer Actions
  addTrailer: (trailerData) => {
    const newTrailer: Trailer = {
      id: `trailer_${trailerData.category}_${Date.now()}`,
      ...trailerData,
      documents: createDefaultVehicleDocuments(),
    };
    set((state) => ({ trailers: [newTrailer, ...state.trailers] }));
  },
  
  updateTrailer: (id, data) => {
    set((state) => ({
      trailers: state.trailers.map((t) => t.id === id ? { ...t, ...data } : t),
    }));
  },
  
  deleteTrailer: (id) => {
    set((state) => ({
      trailers: state.trailers.filter((t) => t.id !== id),
      vehicleSets: state.vehicleSets.filter((s) => s.trailerId !== id),
    }));
  },
  
  uploadTrailerDocument: (trailerId, docType, file) => {
    const fileUrl = URL.createObjectURL(file);
    set((state) => ({
      trailers: state.trailers.map((t) => {
        if (t.id !== trailerId) return t;
        return {
          ...t,
          documents: t.documents.map((d) =>
            d.type === docType ? { ...d, fileName: file.name, fileUrl, fileBlob: file } : d
          ),
        };
      }),
    }));
  },
  
  updateTrailerDocument: (trailerId, docType, data) => {
    set((state) => ({
      trailers: state.trailers.map((t) => {
        if (t.id !== trailerId) return t;
        return {
          ...t,
          documents: t.documents.map((d) => d.type === docType ? { ...d, ...data } : d),
        };
      }),
    }));
  },
  
  deleteTrailerDocument: (trailerId, docType) => {
    set((state) => ({
      trailers: state.trailers.filter((t) => t.id !== trailerId),
    }));
  },
  
  // VehicleSet Actions
  addVehicleSet: (setData) => {
    const newSet: VehicleSet = {
      id: `set_${setData.category}_${Date.now()}`,
      ...setData,
    };
    set((state) => ({ vehicleSets: [newSet, ...state.vehicleSets] }));
  },
  
  updateVehicleSet: (id, data) => {
    set((state) => ({
      vehicleSets: state.vehicleSets.map((s) => s.id === id ? { ...s, ...data } : s),
    }));
  },
  
  deleteVehicleSet: (id) => {
    set((state) => ({
      vehicleSets: state.vehicleSets.filter((s) => s.id !== id),
    }));
  },
  
  // Driver Actions
  addDriver: (driverData) => {
    const newDriver: Driver = {
      id: `d_${Date.now()}`,
      ...driverData,
      documents: createDefaultDriverDocuments(),
    };
    set((state) => ({ drivers: [newDriver, ...state.drivers] }));
  },
  
  updateDriver: (id, data) => {
    set((state) => ({
      drivers: state.drivers.map((d) => d.id === id ? { ...d, ...data } : d),
    }));
  },
  
  deleteDriver: (id) => {
    set((state) => ({
      drivers: state.drivers.filter((d) => d.id !== id),
    }));
  },
  
  uploadDriverDocument: (driverId, docType, file) => {
    const fileUrl = URL.createObjectURL(file);
    set((state) => ({
      drivers: state.drivers.map((d) => {
        if (d.id !== driverId) return d;
        return {
          ...d,
          documents: d.documents.map((doc) =>
            doc.type === docType ? { ...doc, fileName: file.name, fileUrl, fileBlob: file } : doc
          ),
        };
      }),
    }));
  },

  updateDriverDocument: (driverId, docType, data) => {
    set((state) => ({
      drivers: state.drivers.map((d) => {
        if (d.id !== driverId) return d;
        return {
          ...d,
          documents: d.documents.map((doc) => doc.type === docType ? { ...doc, ...data } : doc),
        };
      }),
    }));
  },
  
  deleteDriverDocument: (driverId, docType) => {
    set((state) => ({
      drivers: state.drivers.map((d) => {
        if (d.id !== driverId) return d;
        return {
          ...d,
          documents: d.documents.map((doc) =>
            doc.type === docType ? { ...doc, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null } : doc
          ),
        };
      }),
    }));
  },
  
  // Selectors - Yeni
  getAsmiraTrucks: () => get().trucks.filter((t) => t.category === "asmira"),
  getSupplierTrucks: () => get().trucks.filter((t) => t.category === "supplier"),
  getAsmiraTrailers: () => get().trailers.filter((t) => t.category === "asmira"),
  getSupplierTrailers: () => get().trailers.filter((t) => t.category === "supplier"),
  getAsmiraVehicleSets: () => get().vehicleSets.filter((s) => s.category === "asmira"),
  getSupplierVehicleSets: () => get().vehicleSets.filter((s) => s.category === "supplier"),
  getTruckById: (id) => get().trucks.find((t) => t.id === id),
  getTrailerById: (id) => get().trailers.find((t) => t.id === id),
  
  // Selectors - Geriye uyumluluk
  getAsmiraVehicles: () => createVehiclesFromSets(
    get().trucks.filter((t) => t.category === "asmira"),
    get().trailers.filter((t) => t.category === "asmira"),
    get().vehicleSets.filter((s) => s.category === "asmira")
  ),
  getSupplierVehicles: () => createVehiclesFromSets(
    get().trucks.filter((t) => t.category === "supplier"),
    get().trailers.filter((t) => t.category === "supplier"),
    get().vehicleSets.filter((s) => s.category === "supplier")
  ),
  getAllVehicles: () => createVehiclesFromSets(get().trucks, get().trailers, get().vehicleSets),
  getAllDrivers: () => get().drivers,
  
  // Geriye uyumluluk - Vehicle Actions (deprecated - yeni yapıyı kullanın)
  addVehicle: (vehicleData) => {
    // Hem truck hem trailer oluştur ve set olarak eşleştir
    const truckId = `truck_${vehicleData.category}_${Date.now()}`;
    const trailerId = `trailer_${vehicleData.category}_${Date.now() + 1}`;
    const setId = `set_${vehicleData.category}_${Date.now() + 2}`;
    
    const newTruck: Truck = {
      id: truckId,
      plate: vehicleData.vehiclePlate,
      category: vehicleData.category,
      documents: createDefaultVehicleDocuments(),
    };
    const newTrailer: Trailer = {
      id: trailerId,
      plate: vehicleData.trailerPlate,
      category: vehicleData.category,
      documents: createDefaultVehicleDocuments(),
    };
    const newSet: VehicleSet = {
      id: setId,
      truckId,
      trailerId,
      category: vehicleData.category,
    };
    
    set((state) => ({
      trucks: [newTruck, ...state.trucks],
      trailers: [newTrailer, ...state.trailers],
      vehicleSets: [newSet, ...state.vehicleSets],
    }));
  },
  
  updateVehicle: (id, data) => {
    // VehicleSet ID ile truck/trailer'ı bul ve güncelle
    const vehicleSet = get().vehicleSets.find((s) => s.id === id);
    if (!vehicleSet) return;
    
    if (data.vehiclePlate) {
      set((state) => ({
        trucks: state.trucks.map((t) => 
          t.id === vehicleSet.truckId ? { ...t, plate: data.vehiclePlate! } : t
        ),
      }));
    }
    if (data.trailerPlate) {
      set((state) => ({
        trailers: state.trailers.map((t) => 
          t.id === vehicleSet.trailerId ? { ...t, plate: data.trailerPlate! } : t
        ),
      }));
    }
  },
  
  deleteVehicle: (id) => {
    // VehicleSet'i sil (truck ve trailer kalır)
    set((state) => ({
      vehicleSets: state.vehicleSets.filter((s) => s.id !== id),
    }));
  },
  
  uploadVehicleDocument: (vehicleId, target, docType, file) => {
    const fileUrl = URL.createObjectURL(file);
    const vehicleSet = get().vehicleSets.find((s) => s.id === vehicleId);
    if (!vehicleSet) return;
    
    if (target === 'truck') {
      set((state) => ({
        trucks: state.trucks.map((t) => {
          if (t.id !== vehicleSet.truckId) return t;
          return {
            ...t,
            documents: t.documents.map((d) =>
              d.type === docType ? { ...d, fileName: file.name, fileUrl, fileBlob: file } : d
            ),
          };
        }),
      }));
    } else {
      set((state) => ({
        trailers: state.trailers.map((t) => {
          if (t.id !== vehicleSet.trailerId) return t;
          return {
            ...t,
            documents: t.documents.map((d) =>
              d.type === docType ? { ...d, fileName: file.name, fileUrl, fileBlob: file } : d
            ),
          };
        }),
      }));
    }
  },
  
  updateVehicleDocument: (vehicleId, target, docType, data) => {
    const vehicleSet = get().vehicleSets.find((s) => s.id === vehicleId);
    if (!vehicleSet) return;
    
    if (target === 'truck') {
      set((state) => ({
        trucks: state.trucks.map((t) => {
          if (t.id !== vehicleSet.truckId) return t;
          return {
            ...t,
            documents: t.documents.map((d) => d.type === docType ? { ...d, ...data } : d),
          };
        }),
      }));
    } else {
      set((state) => ({
        trailers: state.trailers.map((t) => {
          if (t.id !== vehicleSet.trailerId) return t;
          return {
            ...t,
            documents: t.documents.map((d) => d.type === docType ? { ...d, ...data } : d),
          };
        }),
      }));
    }
  },
  
  deleteVehicleDocument: (vehicleId, target, docType) => {
    const vehicleSet = get().vehicleSets.find((s) => s.id === vehicleId);
    if (!vehicleSet) return;
    
    if (target === 'truck') {
      set((state) => ({
        trucks: state.trucks.map((t) => {
          if (t.id !== vehicleSet.truckId) return t;
          return {
            ...t,
            documents: t.documents.map((d) =>
              d.type === docType ? { ...d, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null } : d
            ),
          };
        }),
      }));
    } else {
      set((state) => ({
        trailers: state.trailers.map((t) => {
          if (t.id !== vehicleSet.trailerId) return t;
          return {
            ...t,
            documents: t.documents.map((d) =>
              d.type === docType ? { ...d, fileName: null, fileUrl: null, fileBlob: null, expiryDate: null } : d
            ),
          };
        }),
      }));
    }
  },
}));
