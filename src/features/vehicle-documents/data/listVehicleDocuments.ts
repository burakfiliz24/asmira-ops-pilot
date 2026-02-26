import type { VehicleDocument } from "@/features/vehicle-documents/domain/types";
import { vehicleDocumentsMock } from "@/features/vehicle-documents/data/mock";

export async function listVehicleDocuments(params?: {
  category?: VehicleDocument["category"];
}): Promise<VehicleDocument[]> {
  if (!params?.category) return vehicleDocumentsMock;
  return vehicleDocumentsMock.filter((x) => x.category === params.category);
}
