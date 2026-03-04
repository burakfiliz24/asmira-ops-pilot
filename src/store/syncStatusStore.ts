import { create } from "zustand";

interface SyncStatusState {
  isSyncing: boolean;
  isOnline: boolean;
  lastSyncTime: Date | null;
  setSyncing: (val: boolean) => void;
  setOnline: (val: boolean) => void;
  setLastSyncTime: (time: Date) => void;
}

export const useSyncStatusStore = create<SyncStatusState>()((set) => ({
  isSyncing: false,
  isOnline: true,
  lastSyncTime: null,
  setSyncing: (val) => set({ isSyncing: val }),
  setOnline: (val) => set({ isOnline: val }),
  setLastSyncTime: (time) => set({ lastSyncTime: time }),
}));
