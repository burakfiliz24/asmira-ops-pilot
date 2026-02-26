export type PetitionField = {
  id: string;
  label: string;
  type: "text" | "date" | "textarea" | "select";
  placeholder?: string;
  required?: boolean;
  options?: string[]; // for select type
};

export type PetitionTemplate = {
  id: string;
  title: string;
  description: string;
  icon: string; // lucide icon name
  category: "taahhutname" | "liman";
  fields: PetitionField[];
};

export type GeneratedPetition = {
  id: string;
  templateId: string;
  templateTitle: string;
  createdAt: string;
  data: Record<string, string>;
};
