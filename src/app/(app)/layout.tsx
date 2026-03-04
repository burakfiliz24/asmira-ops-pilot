import type { ReactNode } from "react";
import AppShell from "@/components/layout/AppShell";
import { AuthGuard } from "@/components/auth/AuthGuard";
import { RealtimeProvider } from "@/contexts/RealtimeContext";
import { ServerSyncProvider } from "@/components/layout/ServerSyncProvider";

export default function AppLayout({ children }: { children: ReactNode }) {
  return (
    <AuthGuard>
      <RealtimeProvider>
        <ServerSyncProvider>
          <AppShell>{children}</AppShell>
        </ServerSyncProvider>
      </RealtimeProvider>
    </AuthGuard>
  );
}
