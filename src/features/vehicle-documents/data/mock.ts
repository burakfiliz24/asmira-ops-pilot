import type { VehicleDocument } from "@/features/vehicle-documents/domain/types";

export const vehicleDocumentsMock: VehicleDocument[] = [
  {
    id: "vd_014",
    plate: "34 ASM 014",
    driverName: "Mehmet Yılmaz",
    nationalId: "12345678901",
    srcValid: true,
    psychoValid: true,
    category: "asmira",
  },
  {
    id: "vd_778",
    plate: "41 BKR 778",
    driverName: "Ahmet Kaya",
    nationalId: "10987654321",
    srcValid: false,
    psychoValid: true,
    category: "supplier",
  },
  {
    id: "vd_320",
    plate: "33 MRS 320",
    driverName: "Ali Demir",
    nationalId: "11122233344",
    srcValid: true,
    psychoValid: false,
    category: "asmira",
  },
];
