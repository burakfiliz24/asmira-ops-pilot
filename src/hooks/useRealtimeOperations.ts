"use client";

import { useEffect, useCallback, useRef } from "react";
import { createSupabaseBrowserClient } from "@/lib/supabase/client";
import type { RealtimeChannel } from "@supabase/supabase-js";

export type OperationStatus = "planned" | "approaching" | "active" | "completed" | "cancelled";
export type Unit = "MT" | "L";

export type SupplyOperation = {
  id: string;
  vesselName: string;
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

type RealtimePayload = {
  eventType: "INSERT" | "UPDATE" | "DELETE";
  new: SupplyOperation | null;
  old: { id: string } | null;
};

type UseRealtimeOperationsOptions = {
  onInsert?: (operation: SupplyOperation) => void;
  onUpdate?: (operation: SupplyOperation) => void;
  onDelete?: (id: string) => void;
  onAnyChange?: () => void;
  enabled?: boolean;
};

export function useRealtimeOperations(options: UseRealtimeOperationsOptions = {}) {
  const { onInsert, onUpdate, onDelete, onAnyChange, enabled = true } = options;
  const channelRef = useRef<RealtimeChannel | null>(null);
  const supabaseRef = useRef(createSupabaseBrowserClient());

  const handleRealtimeChange = useCallback(
    (payload: RealtimePayload) => {
      console.log("[Realtime] Değişiklik algılandı:", payload.eventType);

      switch (payload.eventType) {
        case "INSERT":
          if (payload.new) {
            onInsert?.(payload.new);
          }
          break;
        case "UPDATE":
          if (payload.new) {
            onUpdate?.(payload.new);
          }
          break;
        case "DELETE":
          if (payload.old?.id) {
            onDelete?.(payload.old.id);
          }
          break;
      }

      onAnyChange?.();
    },
    [onInsert, onUpdate, onDelete, onAnyChange]
  );

  useEffect(() => {
    if (!enabled) return;

    const supabase = supabaseRef.current;

    channelRef.current = supabase
      .channel("supply-operations-realtime")
      .on(
        "postgres_changes",
        {
          event: "*",
          schema: "public",
          table: "supply_operations",
        },
        (payload) => {
          handleRealtimeChange(payload as unknown as RealtimePayload);
        }
      )
      .subscribe((status) => {
        console.log("[Realtime] Subscription status:", status);
      });

    return () => {
      if (channelRef.current) {
        supabase.removeChannel(channelRef.current);
        channelRef.current = null;
      }
    };
  }, [enabled, handleRealtimeChange]);

  const broadcast = useCallback(
    (event: "insert" | "update" | "delete", data: SupplyOperation | { id: string }) => {
      if (channelRef.current) {
        channelRef.current.send({
          type: "broadcast",
          event,
          payload: data,
        });
      }
    },
    []
  );

  return { broadcast };
}

export function useBroadcastOperations(
  setOperations: React.Dispatch<React.SetStateAction<SupplyOperation[]>>
) {
  const handleInsert = useCallback(
    (operation: SupplyOperation) => {
      setOperations((prev) => {
        if (prev.some((op) => op.id === operation.id)) {
          return prev;
        }
        return [...prev, operation];
      });
    },
    [setOperations]
  );

  const handleUpdate = useCallback(
    (operation: SupplyOperation) => {
      setOperations((prev) =>
        prev.map((op) => (op.id === operation.id ? operation : op))
      );
    },
    [setOperations]
  );

  const handleDelete = useCallback(
    (id: string) => {
      setOperations((prev) => prev.filter((op) => op.id !== id));
    },
    [setOperations]
  );

  return useRealtimeOperations({
    onInsert: handleInsert,
    onUpdate: handleUpdate,
    onDelete: handleDelete,
  });
}
