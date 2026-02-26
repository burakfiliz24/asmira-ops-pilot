import { create } from 'zustand';
import { persist } from 'zustand/middleware';

export type CustomCategory = {
  id: string;
  title: string;
  description: string;
  icon: "FileText" | "FileCheck" | "LogIn" | "Truck" | "Ship" | "Anchor";
  slug: string;
};

export type CustomTemplate = {
  id: string;
  shortName: string;
  name: string;
  defaultText: string;
  category: string;
  isDefault?: boolean;
  createdAt: number;
};

export type UploadedDocument = {
  id: string;
  name: string;
  fileName: string;
  fileBlob: Blob | null;
  fileUrl: string | null;
  category: string;
  createdAt: number;
};

interface PetitionStore {
  customCategories: CustomCategory[];
  customTemplates: CustomTemplate[];
  uploadedDocuments: UploadedDocument[];
  addCategory: (category: Omit<CustomCategory, 'id' | 'slug'>) => void;
  updateCategory: (id: string, data: Partial<Pick<CustomCategory, 'title' | 'description' | 'icon'>>) => void;
  deleteCategory: (id: string) => void;
  addTemplate: (template: Omit<CustomTemplate, 'id'>) => void;
  updateTemplate: (id: string, data: Partial<Omit<CustomTemplate, 'id'>>) => void;
  deleteTemplate: (id: string) => void;
  initializeDefaults: (category: string, defaults: Array<{ shortName: string; name: string; defaultText: string }>) => void;
  addUploadedDocument: (doc: Omit<UploadedDocument, 'id' | 'createdAt'>) => void;
  deleteUploadedDocument: (id: string) => void;
}

function generateSlug(title: string): string {
  return title
    .toLowerCase()
    .replace(/ğ/g, 'g')
    .replace(/ü/g, 'u')
    .replace(/ş/g, 's')
    .replace(/ı/g, 'i')
    .replace(/ö/g, 'o')
    .replace(/ç/g, 'c')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '');
}

export const usePetitionStore = create<PetitionStore>()(
  persist(
    (set, get) => ({
      customCategories: [],
      customTemplates: [],
      uploadedDocuments: [],

      addCategory: (categoryData) => {
        const newCategory: CustomCategory = {
          id: `cat_${Date.now()}`,
          slug: generateSlug(categoryData.title),
          ...categoryData,
        };
        set((state) => ({
          customCategories: [...state.customCategories, newCategory],
        }));
      },

      updateCategory: (id, data) => {
        set((state) => ({
          customCategories: state.customCategories.map((cat) =>
            cat.id === id
              ? {
                  ...cat,
                  ...data,
                  slug: data.title ? generateSlug(data.title) : cat.slug,
                }
              : cat
          ),
        }));
      },

      deleteCategory: (id) => {
        set((state) => ({
          customCategories: state.customCategories.filter((cat) => cat.id !== id),
        }));
      },

      addTemplate: (templateData) => {
        const newTemplate: CustomTemplate = {
          ...templateData,
          id: `tpl_${Date.now()}`,
        };
        set((state) => ({
          customTemplates: [...state.customTemplates, newTemplate],
        }));
      },

      updateTemplate: (id, data) => {
        set((state) => ({
          customTemplates: state.customTemplates.map((tpl) =>
            tpl.id === id ? { ...tpl, ...data } : tpl
          ),
        }));
      },

      deleteTemplate: (id) => {
        set((state) => ({
          customTemplates: state.customTemplates.filter((tpl) => tpl.id !== id),
        }));
      },

      initializeDefaults: (category, defaults) => {
        const existing = get().customTemplates.filter(t => t.category === category);
        if (existing.length > 0) return;
        
        const defaultTemplates: CustomTemplate[] = defaults.map((d, i) => ({
          id: `tpl_default_${category}_${i}`,
          shortName: d.shortName,
          name: d.name,
          defaultText: d.defaultText,
          category,
          isDefault: true,
          createdAt: Date.now(),
        }));
        
        set((state) => ({
          customTemplates: [...state.customTemplates, ...defaultTemplates],
        }));
      },

      addUploadedDocument: (docData) => {
        const fileUrl = docData.fileBlob ? URL.createObjectURL(docData.fileBlob) : null;
        const newDoc: UploadedDocument = {
          id: `doc_${Date.now()}`,
          ...docData,
          fileUrl,
          createdAt: Date.now(),
        };
        set((state) => ({
          uploadedDocuments: [...state.uploadedDocuments, newDoc],
        }));
      },

      deleteUploadedDocument: (id) => {
        set((state) => ({
          uploadedDocuments: state.uploadedDocuments.filter((doc) => doc.id !== id),
        }));
      },
    }),
    {
      name: 'petition-store',
    }
  )
);
