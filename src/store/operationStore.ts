import { create } from "zustand";
import { persist } from "zustand/middleware";

export type OperationStatus = "planned" | "approaching" | "active" | "completed" | "cancelled";
export type Unit = "MT" | "L";

export type SupplyOperation = {
  id: string;
  vesselName: string;
  imoNumber?: string;
  quantity: number;
  unit: Unit;
  loadingPlace?: string;
  port: string;
  date: string;
  status: OperationStatus;
  driverName: string;
  driverPhone: string;
  agentNote: string;
};

type OperationStore = {
  operations: SupplyOperation[];
  addOperation: (op: SupplyOperation) => void;
  updateOperation: (id: string, updates: Partial<SupplyOperation>) => void;
  deleteOperation: (id: string) => void;
  setOperations: (ops: SupplyOperation[]) => void;
};

const initialOperations: SupplyOperation[] = [
  {
    id: "op_seed_1",
    vesselName: "M/T Asmira Star",
    imoNumber: "9361354",
    quantity: 850,
    unit: "MT",
    port: "İzmit",
    date: "2026-01-29",
    status: "active",
    driverName: "Mehmet Yılmaz",
    driverPhone: "+90 5xx xxx xx xx",
    agentNote: "Pilotaj teyidi bekleniyor. ETA 09:30.",
  },
  {
    id: "op_seed_2",
    vesselName: "M/V Bosphorus",
    imoNumber: "9284765",
    quantity: 420.5,
    unit: "MT",
    port: "Ambarlı",
    date: "2026-01-28",
    status: "planned",
    driverName: "Ahmet Kaya",
    driverPhone: "+90 5xx xxx xx xx",
    agentNote: "Terminal slot: 14:00-16:00.",
  },
];

export const useOperationStore = create<OperationStore>()(
  persist(
    (set) => ({
      operations: initialOperations,
      addOperation: (op) =>
        set((state) => ({ operations: [op, ...state.operations] })),
      updateOperation: (id, updates) =>
        set((state) => ({
          operations: state.operations.map((op) =>
            op.id === id ? { ...op, ...updates } : op
          ),
        })),
      deleteOperation: (id) =>
        set((state) => ({
          operations: state.operations.filter((op) => op.id !== id),
        })),
      setOperations: (ops) => set({ operations: ops }),
    }),
    {
      name: "asmira-operations",
    }
  )
);
