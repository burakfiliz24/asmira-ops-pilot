const BASE = "";

async function request<T>(url: string, options?: RequestInit): Promise<T> {
  const res = await fetch(`${BASE}${url}`, {
    ...options,
    headers: {
      "Content-Type": "application/json",
      ...options?.headers,
    },
  });
  if (!res.ok) {
    const body = await res.json().catch(() => ({}));
    throw new Error(body.error || `API Error: ${res.status}`);
  }
  return res.json();
}

// ============ AUTH ============
export const authApi = {
  login: (username: string, password: string) =>
    request<{ id: string; username: string; name: string; role: string }>("/api/auth/login", {
      method: "POST",
      body: JSON.stringify({ username, password }),
    }),
};

// ============ USERS ============
export const usersApi = {
  getAll: () =>
    request<{ id: string; username: string; password: string; name: string; role: string }[]>("/api/users"),
  create: (data: { username: string; password: string; name: string; role: string }) =>
    request("/api/users", { method: "POST", body: JSON.stringify(data) }),
  update: (id: string, data: Record<string, unknown>) =>
    request("/api/users", { method: "PUT", body: JSON.stringify({ id, ...data }) }),
  delete: (id: string) =>
    request("/api/users", { method: "DELETE", body: JSON.stringify({ id }) }),
};

// ============ OPERATIONS ============
type Operation = {
  id: string;
  vesselName: string;
  imoNumber?: string;
  quantity: number;
  unit: string;
  loadingPlace?: string;
  port: string;
  date: string;
  status: string;
  driverName: string;
  driverPhone: string;
  agentNote: string;
};

export const operationsApi = {
  getAll: () => request<Operation[]>("/api/operations"),
  create: (data: Operation) =>
    request("/api/operations", { method: "POST", body: JSON.stringify(data) }),
  update: (id: string, updates: Partial<Operation>) =>
    request("/api/operations", { method: "PUT", body: JSON.stringify({ id, ...updates }) }),
  delete: (id: string) =>
    request("/api/operations", { method: "DELETE", body: JSON.stringify({ id }) }),
};

// ============ TRUCKS ============
type TruckData = {
  id: string;
  plate: string;
  category: string;
  documents: { type: string; label: string; fileName?: string; filePath?: string; expiryDate?: string }[];
};

export const trucksApi = {
  getAll: () => request<TruckData[]>("/api/trucks"),
  create: (data: { plate: string; category: string; documents?: { type: string; label: string }[] }) =>
    request<{ id: string }>("/api/trucks", { method: "POST", body: JSON.stringify(data) }),
  update: (id: string, plate: string) =>
    request("/api/trucks", { method: "PUT", body: JSON.stringify({ id, plate }) }),
  delete: (id: string) =>
    request("/api/trucks", { method: "DELETE", body: JSON.stringify({ id }) }),
};

// ============ TRAILERS ============
export const trailersApi = {
  getAll: () => request<TruckData[]>("/api/trailers"),
  create: (data: { plate: string; category: string; documents?: { type: string; label: string }[] }) =>
    request<{ id: string }>("/api/trailers", { method: "POST", body: JSON.stringify(data) }),
  update: (id: string, plate: string) =>
    request("/api/trailers", { method: "PUT", body: JSON.stringify({ id, plate }) }),
  delete: (id: string) =>
    request("/api/trailers", { method: "DELETE", body: JSON.stringify({ id }) }),
};

// ============ DRIVERS ============
type DriverData = {
  id: string;
  name: string;
  tcNo: string;
  phone: string;
  documents: { type: string; label: string; fileName?: string; filePath?: string; expiryDate?: string }[];
};

export const driversApi = {
  getAll: () => request<DriverData[]>("/api/drivers"),
  create: (data: { name: string; tcNo: string; phone: string; documents?: { type: string; label: string }[] }) =>
    request<{ id: string }>("/api/drivers", { method: "POST", body: JSON.stringify(data) }),
  update: (id: string, data: { name: string; tcNo: string; phone: string }) =>
    request("/api/drivers", { method: "PUT", body: JSON.stringify({ id, ...data }) }),
  delete: (id: string) =>
    request("/api/drivers", { method: "DELETE", body: JSON.stringify({ id }) }),
};

// ============ VEHICLE SETS ============
type VehicleSetData = { id: string; truckId: string; trailerId: string; category: string };

export const vehicleSetsApi = {
  getAll: () => request<VehicleSetData[]>("/api/vehicle-sets"),
  create: (data: { truckId: string; trailerId: string; category: string }) =>
    request<{ id: string }>("/api/vehicle-sets", { method: "POST", body: JSON.stringify(data) }),
  delete: (id: string) =>
    request("/api/vehicle-sets", { method: "DELETE", body: JSON.stringify({ id }) }),
};

// ============ DOCUMENTS ============
export const documentsApi = {
  upload: async (file: File, ownerId: string, ownerType: string, docType: string, expiryDate?: string) => {
    const formData = new FormData();
    formData.append("file", file);
    formData.append("ownerId", ownerId);
    formData.append("ownerType", ownerType);
    formData.append("docType", docType);
    if (expiryDate) formData.append("expiryDate", expiryDate);

    const res = await fetch("/api/documents/upload", { method: "POST", body: formData });
    if (!res.ok) throw new Error("Dosya yüklenemedi");
    return res.json() as Promise<{ success: boolean; fileName: string; filePath: string }>;
  },

  updateExpiry: (ownerId: string, ownerType: string, docType: string, expiryDate: string | null) =>
    request("/api/documents/update", {
      method: "PUT",
      body: JSON.stringify({ ownerId, ownerType, docType, expiryDate }),
    }),

  deleteDoc: (ownerId: string, ownerType: string, docType: string) =>
    request("/api/documents/update", {
      method: "DELETE",
      body: JSON.stringify({ ownerId, ownerType, docType }),
    }),

  getDownloadUrl: (filePath: string) => `/api/documents/download/${filePath}`,
};

// ============ PETITION CATEGORIES ============
type PetitionCategoryData = { id: string; title: string; description: string; icon: string; slug: string };

export const petitionCategoriesApi = {
  getAll: () => request<PetitionCategoryData[]>("/api/petition-categories"),
  create: (data: { id?: string; title: string; description: string; icon: string; slug: string }) =>
    request<{ id: string }>("/api/petition-categories", { method: "POST", body: JSON.stringify(data) }),
  update: (id: string, data: Record<string, unknown>) =>
    request("/api/petition-categories", { method: "PUT", body: JSON.stringify({ id, ...data }) }),
  delete: (id: string) =>
    request("/api/petition-categories", { method: "DELETE", body: JSON.stringify({ id }) }),
};

// ============ PETITION TEMPLATES ============
type PetitionTemplateData = {
  id: string; shortName: string; name: string; defaultText: string;
  category: string; isDefault: number; createdAt: number;
};

export const petitionTemplatesApi = {
  getAll: () => request<PetitionTemplateData[]>("/api/petition-templates"),
  create: (data: { id?: string; shortName: string; name: string; defaultText: string; category: string; isDefault?: boolean; createdAt: number }) =>
    request<{ id: string }>("/api/petition-templates", { method: "POST", body: JSON.stringify(data) }),
  update: (id: string, data: Record<string, unknown>) =>
    request("/api/petition-templates", { method: "PUT", body: JSON.stringify({ id, ...data }) }),
  delete: (id: string) =>
    request("/api/petition-templates", { method: "DELETE", body: JSON.stringify({ id }) }),
};
