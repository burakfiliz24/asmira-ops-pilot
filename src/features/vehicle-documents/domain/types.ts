export type VehicleDocument = {
  id: string;
  plate: string;
  driverName: string;
  nationalId: string;
  srcValid: boolean;
  psychoValid: boolean;
  category: "asmira" | "supplier";
};
