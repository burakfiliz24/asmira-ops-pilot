"use client";

import { useEffect, useRef, useCallback } from "react";
import { useOperationStore } from "@/store/operationStore";
import { useAuthStore } from "@/store/authStore";
import { useDocumentStore } from "@/store/documentStore";
import { usePetitionStore } from "@/store/petitionStore";
import { useSyncStatusStore } from "@/store/syncStatusStore";

const POLL_INTERVAL = 30_000; // 30 saniye

export function useServerSync() {
  const syncedRef = useRef(false);
  const intervalRef = useRef<ReturnType<typeof setInterval> | null>(null);

  const syncOperations = useOperationStore((s) => s.syncFromServer);
  const syncUsers = useAuthStore((s) => s.syncFromServer);
  const syncDocuments = useDocumentStore((s) => s.syncFromServer);
  const syncPetitions = usePetitionStore((s) => s.syncFromServer);

  const setSyncing = useSyncStatusStore((s) => s.setSyncing);
  const setLastSyncTime = useSyncStatusStore((s) => s.setLastSyncTime);
  const setOnline = useSyncStatusStore((s) => s.setOnline);

  const syncAll = useCallback(async () => {
    setSyncing(true);
    try {
      await Promise.allSettled([
        syncOperations(),
        syncUsers(),
        syncDocuments(),
        syncPetitions(),
      ]);
      setLastSyncTime(new Date());
      setOnline(true);
      console.log("[ServerSync] Senkronizasyon tamamlandı.");
    } catch {
      setOnline(false);
      console.warn("[ServerSync] Senkronizasyon başarısız.");
    } finally {
      setSyncing(false);
    }
  }, [syncOperations, syncUsers, syncDocuments, syncPetitions, setSyncing, setLastSyncTime, setOnline]);

  useEffect(() => {
    // İlk açılışta sync
    if (!syncedRef.current) {
      syncedRef.current = true;
      console.log("[ServerSync] İlk senkronizasyon başlatılıyor...");
      syncAll();
    }

    // Periyodik polling (30sn)
    intervalRef.current = setInterval(() => {
      syncAll();
    }, POLL_INTERVAL);

    // Sayfa tekrar odaklanınca anında sync (tab switch)
    const handleVisibilityChange = () => {
      if (document.visibilityState === "visible") {
        console.log("[ServerSync] Sayfa odaklandı, senkronizasyon...");
        syncAll();
      }
    };

    // Çevrimiçi/çevrimdışı durumu
    const handleOnline = () => {
      setOnline(true);
      syncAll();
    };
    const handleOffline = () => setOnline(false);

    document.addEventListener("visibilitychange", handleVisibilityChange);
    window.addEventListener("online", handleOnline);
    window.addEventListener("offline", handleOffline);
    setOnline(navigator.onLine);

    return () => {
      if (intervalRef.current) clearInterval(intervalRef.current);
      document.removeEventListener("visibilitychange", handleVisibilityChange);
      window.removeEventListener("online", handleOnline);
      window.removeEventListener("offline", handleOffline);
    };
  }, [syncAll, setOnline]);
}
