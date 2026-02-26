import type { ReactNode } from "react";
import Sidebar from "@/components/layout/Sidebar";
import MobileNav from "@/components/layout/MobileNav";
import { ToastContainer } from "@/components/ui/ToastContainer";

export default function AppShell({ children }: { children: ReactNode }) {
  return (
    <div className="min-h-screen bg-gradient-to-br from-[#0b1120] via-[#0f172a] to-[#0b1120] text-[var(--foreground)]">
      {/* Mobile Navigation */}
      <MobileNav />
      
      <div className="mx-auto flex min-h-screen w-full">
        <Sidebar />
        <div className="flex min-w-0 flex-1 flex-col">
          {/* Add top padding on mobile for fixed header */}
          <main className="min-w-0 flex-1 px-0 pb-4 pt-16 sm:px-4 lg:px-6 lg:pt-6">
            <div className="w-full">{children}</div>
          </main>
        </div>
      </div>
      <ToastContainer />
    </div>
  );
}
