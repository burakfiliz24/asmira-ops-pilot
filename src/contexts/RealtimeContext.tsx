"use client";

import { createContext, useContext, useCallback, useRef, useEffect, type ReactNode } from "react";
import { createSupabaseBrowserClient } from "@/lib/supabase/client";
import type { RealtimeChannel } from "@supabase/supabase-js";

type BroadcastEvent = {
  type: "operation_insert" | "operation_update" | "operation_delete" | "operation_batch";
  payload: unknown;
  senderId: string;
};

type RealtimeContextType = {
  broadcast: (event: BroadcastEvent["type"], payload: unknown) => void;
  subscribe: (callback: (event: BroadcastEvent) => void) => () => void;
  clientId: string;
};

const RealtimeContext = createContext<RealtimeContextType | null>(null);

function generateClientId() {
  return `client_${Math.random().toString(36).slice(2)}_${Date.now()}`;
}

export function RealtimeProvider({ children }: { children: ReactNode }) {
  const supabaseRef = useRef(createSupabaseBrowserClient());
  const channelRef = useRef<RealtimeChannel | null>(null);
  const subscribersRef = useRef<Set<(event: BroadcastEvent) => void>>(new Set());
  const clientIdRef = useRef(generateClientId());

  useEffect(() => {
    const supabase = supabaseRef.current;
    const clientId = clientIdRef.current;

    channelRef.current = supabase
      .channel("ops-broadcast", {
        config: {
          broadcast: { self: false },
        },
      })
      .on("broadcast", { event: "sync" }, ({ payload }) => {
        const event = payload as BroadcastEvent;
        if (event.senderId !== clientId) {
          subscribersRef.current.forEach((cb) => cb(event));
        }
      })
      .subscribe((status) => {
        if (status === "SUBSCRIBED") {
          console.log("[Realtime] Broadcast channel connected. Client ID:", clientId);
        }
      });

    return () => {
      if (channelRef.current) {
        supabase.removeChannel(channelRef.current);
        channelRef.current = null;
      }
    };
  }, []);

  const broadcast = useCallback((type: BroadcastEvent["type"], payload: unknown) => {
    if (channelRef.current) {
      const event: BroadcastEvent = {
        type,
        payload,
        senderId: clientIdRef.current,
      };
      channelRef.current.send({
        type: "broadcast",
        event: "sync",
        payload: event,
      });
      console.log("[Realtime] Broadcast sent:", type);
    }
  }, []);

  const subscribe = useCallback((callback: (event: BroadcastEvent) => void) => {
    subscribersRef.current.add(callback);
    return () => {
      subscribersRef.current.delete(callback);
    };
  }, []);

  return (
    <RealtimeContext.Provider
      value={{
        broadcast,
        subscribe,
        clientId: clientIdRef.current,
      }}
    >
      {children}
    </RealtimeContext.Provider>
  );
}

export function useRealtime() {
  const context = useContext(RealtimeContext);
  if (!context) {
    throw new Error("useRealtime must be used within a RealtimeProvider");
  }
  return context;
}

export function useRealtimeSync<T extends { id: string }>(
  data: T[],
  setData: React.Dispatch<React.SetStateAction<T[]>>,
  eventPrefix: string
) {
  const { broadcast, subscribe } = useRealtime();

  useEffect(() => {
    const unsubscribe = subscribe((event) => {
      if (!event.type.startsWith(eventPrefix)) return;

      const action = event.type.replace(`${eventPrefix}_`, "");
      const payload = event.payload as T | { id: string } | T[];

      switch (action) {
        case "insert":
          setData((prev) => {
            const newItem = payload as T;
            if (prev.some((item) => item.id === newItem.id)) return prev;
            return [...prev, newItem];
          });
          break;
        case "update":
          setData((prev) =>
            prev.map((item) =>
              item.id === (payload as T).id ? (payload as T) : item
            )
          );
          break;
        case "delete":
          setData((prev) =>
            prev.filter((item) => item.id !== (payload as { id: string }).id)
          );
          break;
        case "batch":
          setData(payload as T[]);
          break;
      }
    });

    return unsubscribe;
  }, [subscribe, setData, eventPrefix]);

  const syncInsert = useCallback(
    (item: T) => {
      broadcast(`${eventPrefix}_insert` as BroadcastEvent["type"], item);
    },
    [broadcast, eventPrefix]
  );

  const syncUpdate = useCallback(
    (item: T) => {
      broadcast(`${eventPrefix}_update` as BroadcastEvent["type"], item);
    },
    [broadcast, eventPrefix]
  );

  const syncDelete = useCallback(
    (id: string) => {
      broadcast(`${eventPrefix}_delete` as BroadcastEvent["type"], { id });
    },
    [broadcast, eventPrefix]
  );

  return { syncInsert, syncUpdate, syncDelete };
}
