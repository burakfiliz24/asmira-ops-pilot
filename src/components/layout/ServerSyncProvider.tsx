"use client";

import { useServerSync } from "@/hooks/useServerSync";

export function ServerSyncProvider({ children }: { children: React.ReactNode }) {
  useServerSync();
  return <>{children}</>;
}
