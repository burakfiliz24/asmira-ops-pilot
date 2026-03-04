import { create } from "zustand";
import { persist } from "zustand/middleware";
import { operationsApi } from "@/lib/api/client";

export type OperationStatus = "planned" | "approaching" | "active" | "completed" | "cancelled";
export type Unit = "MT" | "L";
export type VesselType = "ship" | "yacht";

export type SupplyOperation = {
  id: string;
  vesselName: string;
  vesselType?: VesselType;
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
  syncFromServer: () => Promise<void>;
};

const initialOperations: SupplyOperation[] = [];

export const useOperationStore = create<OperationStore>()(
  persist(
    (set) => ({
      operations: initialOperations,

      addOperation: (op) => {
        set((state) => ({ operations: [op, ...state.operations] }));
        operationsApi.create(op).catch((e) => console.warn("[Sync] addOperation failed:", e));
      },

      updateOperation: (id, updates) => {
        set((state) => ({
          operations: state.operations.map((op) =>
            op.id === id ? { ...op, ...updates } : op
          ),
        }));
        operationsApi.update(id, updates).catch((e) => console.warn("[Sync] updateOperation failed:", e));
      },

      deleteOperation: (id) => {
        set((state) => ({
          operations: state.operations.filter((op) => op.id !== id),
        }));
        operationsApi.delete(id).catch((e) => console.warn("[Sync] deleteOperation failed:", e));
      },

      setOperations: (ops) => set({ operations: ops }),

      syncFromServer: async () => {
        try {
          const ops = await operationsApi.getAll() as SupplyOperation[];
          if (ops.length > 0) {
            set({ operations: ops });
          }
        } catch (e) {
          console.warn("[Sync] syncFromServer failed, using local data:", e);
        }
      },
    }),
    {
      name: "asmira-operations",
    }
  )
);
