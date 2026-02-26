import type { ReactNode } from "react";
import AppShell from "@/components/layout/AppShell";
import { AuthGuard } from "@/components/auth/AuthGuard";
import { RealtimeProvider } from "@/contexts/RealtimeContext";

export default function AppLayout({ children }: { children: ReactNode }) {
  return (
    <AuthGuard>
      <RealtimeProvider>
        <AppShell>{children}</AppShell>
      </RealtimeProvider>
    </AuthGuard>
  );
}
