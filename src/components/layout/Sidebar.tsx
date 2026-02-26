"use client";

import Image from "next/image";
import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { useEffect, useMemo, useState } from "react";
import { navSections } from "@/lib/constants/nav";
import { cn } from "@/lib/utils/cn";
import { ChevronDown, LogOut, User } from "lucide-react";
import { useAuthStore } from "@/store/authStore";

function isActive(pathname: string, href: string) {
  if (href === "/") return pathname === "/";
  return pathname === href || pathname.startsWith(href + "/");
}

export default function Sidebar() {
  const pathname = usePathname();
  const router = useRouter();
  const { user, logout } = useAuthStore();
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  const handleLogout = () => {
    logout();
    router.push("/login");
  };

  const initialOpen = useMemo(() => {
    const record: Record<string, boolean> = {};
    for (const section of navSections) {
      for (const item of section.items) {
        if (item.children?.length) {
          record[item.href] = isActive(pathname, item.href);
        }
      }
    }
    return record;
  }, [pathname]);

  const [openByHref, setOpenByHref] = useState<Record<string, boolean>>(initialOpen);

  useEffect(() => {
    setOpenByHref((prev) => {
      const next = { ...prev };
      for (const section of navSections) {
        for (const item of section.items) {
          if (item.children?.length && isActive(pathname, item.href)) {
            next[item.href] = true;
          }
        }
      }
      return next;
    });
  }, [pathname]);

  // Flatten items for stagger index
  const allItems = useMemo(() => {
    const items: { href: string; index: number }[] = [];
    let idx = 0;
    for (const section of navSections) {
      for (const item of section.items) {
        items.push({ href: item.href, index: idx++ });
      }
    }
    return items;
  }, []);

  function getStaggerIndex(href: string) {
    return allItems.find((i) => i.href === href)?.index ?? 0;
  }

  return (
    <aside className="sticky top-0 hidden h-screen w-72 shrink-0 flex-col bg-[#0b1120]/80 text-white shadow-[4px_0_24px_-2px_rgba(0,0,0,0.5)] backdrop-blur-md lg:flex">
      {/* Top: Logo */}
      <div className="relative flex w-full flex-col items-center justify-center px-4 pb-2 pt-8">
        <Image
          src="/asmira-energy-logo.png"
          alt="Asmira Energy"
          width={500}
          height={130}
          className="w-full max-w-[240px] object-contain"
          priority
        />

        {/* Ocean Scene */}
        <div className="relative mt-4 h-24 w-full overflow-hidden">
          {/* Sky gradient - blends with sidebar bg */}
          <div className="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[#0b1120]/40" />

          {/* Stars */}
          <div className="absolute left-[15%] top-[12%] h-[2px] w-[2px] rounded-full bg-white/30" />
          <div className="absolute left-[45%] top-[8%] h-[1.5px] w-[1.5px] rounded-full bg-white/20" />
          <div className="absolute left-[75%] top-[15%] h-[1px] w-[1px] rounded-full bg-white/25" />
          <div className="absolute left-[60%] top-[5%] h-[1.5px] w-[1.5px] rounded-full bg-white/15" />
          <div className="absolute left-[30%] top-[18%] h-[1px] w-[1px] rounded-full bg-white/20" />

          {/* Ship 1 - Large cargo ship */}
          <div className="sidebar-ship-1 ship-bob absolute bottom-[6px]">
            <svg width="72" height="42" viewBox="0 0 48 28" fill="none">
              {/* Hull */}
              <path d="M2,22 L6,26 H42 L46,22 Z" fill="rgba(96,165,250,0.15)" stroke="rgba(96,165,250,0.5)" strokeWidth="0.8" />
              {/* Deck */}
              <rect x="8" y="17" width="32" height="5" rx="0.5" fill="rgba(96,165,250,0.1)" stroke="rgba(96,165,250,0.35)" strokeWidth="0.7" />
              {/* Bridge */}
              <rect x="14" y="10" width="12" height="7" rx="0.5" fill="rgba(96,165,250,0.08)" stroke="rgba(96,165,250,0.3)" strokeWidth="0.6" />
              {/* Windows */}
              <rect x="16" y="12" width="2.5" height="1.5" rx="0.3" fill="rgba(251,191,36,0.4)" />
              <rect x="20" y="12" width="2.5" height="1.5" rx="0.3" fill="rgba(251,191,36,0.3)" />
              {/* Funnel */}
              <rect x="28" y="12" width="3" height="5" rx="0.3" fill="rgba(96,165,250,0.12)" stroke="rgba(96,165,250,0.3)" strokeWidth="0.5" />
              <line x1="28" y1="13.5" x2="31" y2="13.5" stroke="rgba(239,68,68,0.4)" strokeWidth="1" />
              {/* Mast */}
              <line x1="20" y1="5" x2="20" y2="10" stroke="rgba(148,163,184,0.3)" strokeWidth="0.5" />
              <circle cx="20" cy="5" r="1" fill="rgba(239,68,68,0.5)" />
              {/* Bow */}
              <line x1="42" y1="18" x2="46" y2="22" stroke="rgba(96,165,250,0.3)" strokeWidth="0.5" />
            </svg>
            {/* Ship glow on water */}
            <div className="absolute -bottom-[4px] left-[10%] right-[10%] h-[4px] rounded-full bg-blue-400/10 blur-[3px]" />
          </div>

          {/* Ship 2 - Smaller vessel, sails opposite direction */}
          <div className="sidebar-ship-2 ship-bob-2 absolute bottom-[8px]">
            <svg width="52" height="32" viewBox="0 0 32 20" fill="none" style={{ transform: "scaleX(-1)" }}>
              {/* Hull */}
              <path d="M2,16 L4,18 H28 L30,16 Z" fill="rgba(6,182,212,0.12)" stroke="rgba(6,182,212,0.4)" strokeWidth="0.7" />
              {/* Deck */}
              <rect x="6" y="12" width="20" height="4" rx="0.5" fill="rgba(6,182,212,0.08)" stroke="rgba(6,182,212,0.3)" strokeWidth="0.6" />
              {/* Cabin */}
              <rect x="10" y="7" width="8" height="5" rx="0.5" fill="rgba(6,182,212,0.06)" stroke="rgba(6,182,212,0.25)" strokeWidth="0.5" />
              {/* Window */}
              <rect x="12" y="8.5" width="2" height="1.5" rx="0.3" fill="rgba(251,191,36,0.3)" />
              {/* Mast */}
              <line x1="14" y1="3" x2="14" y2="7" stroke="rgba(148,163,184,0.25)" strokeWidth="0.4" />
              <circle cx="14" cy="3" r="0.7" fill="rgba(52,211,153,0.4)" />
            </svg>
          </div>

          {/* Wave layers - filled, colorful */}
          <svg className="sidebar-wave-1 absolute bottom-0 w-[200%]" viewBox="0 0 1200 30" preserveAspectRatio="none" style={{ height: "18px" }}>
            <path d="M0,12 C100,4 200,20 300,12 C400,4 500,20 600,12 C700,4 800,20 900,12 C1000,4 1100,20 1200,12 L1200,30 L0,30Z" fill="rgba(6,182,212,0.12)" />
            <path d="M0,12 C100,4 200,20 300,12 C400,4 500,20 600,12 C700,4 800,20 900,12 C1000,4 1100,20 1200,12" stroke="rgba(6,182,212,0.3)" strokeWidth="1" fill="none" />
          </svg>
          <svg className="sidebar-wave-2 absolute bottom-0 w-[200%]" viewBox="0 0 1200 30" preserveAspectRatio="none" style={{ height: "14px" }}>
            <path d="M0,12 C150,20 250,4 400,12 C550,20 650,4 800,12 C950,20 1050,4 1200,12 L1200,30 L0,30Z" fill="rgba(59,130,246,0.1)" />
            <path d="M0,12 C150,20 250,4 400,12 C550,20 650,4 800,12 C950,20 1050,4 1200,12" stroke="rgba(59,130,246,0.25)" strokeWidth="0.8" fill="none" />
          </svg>
          <svg className="sidebar-wave-3 absolute bottom-0 w-[200%]" viewBox="0 0 1200 30" preserveAspectRatio="none" style={{ height: "8px" }}>
            <path d="M0,12 C200,18 300,6 500,12 C700,18 800,6 1000,12 C1100,15 1150,9 1200,12 L1200,30 L0,30Z" fill="rgba(6,182,212,0.08)" />
          </svg>
        </div>
      </div>

      {/* Separator */}
      <div className="mx-3 h-px bg-gradient-to-r from-transparent via-white/[0.06] to-transparent" />

      {/* Navigation */}
      <nav className="flex flex-1 flex-col overflow-y-auto px-3 pb-20 pt-2">
        {navSections.map((section) => (
          <div key={section.title} className="mt-2">
            <div className="px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-white/30">
              {section.title}
            </div>
            <div className="space-y-0.5">
              {section.items.map((item) => {
                const active = isActive(pathname, item.href);
                const Icon = item.icon;
                const staggerIdx = getStaggerIndex(item.href);

                if (item.children?.length) {
                  const open = openByHref[item.href] ?? false;
                  return (
                    <div
                      key={item.href}
                      style={mounted ? { animationDelay: `${staggerIdx * 50}ms` } : undefined}
                      className={mounted ? "sidebar-item-enter" : ""}
                    >
                      <div className="group relative">
                        <button
                          type="button"
                          onClick={() =>
                            setOpenByHref((prev) => ({ ...prev, [item.href]: !open }))
                          }
                          className={cn(
                            "relative flex w-full items-center gap-2.5 rounded-lg px-3 py-2.5 text-[13px] font-semibold uppercase tracking-[0.12em] transition-all duration-300",
                            active
                              ? "text-blue-400"
                              : "text-white/60 hover:text-white"
                          )}
                        >
                          {active && (
                            <div className="absolute left-0 top-1/2 -translate-y-1/2">
                              <div className="h-6 w-[3px] rounded-full bg-blue-400 shadow-[0_0_8px_rgba(96,165,250,0.6),0_0_20px_rgba(96,165,250,0.3)]" />
                            </div>
                          )}
                          <div className={cn(
                            "pointer-events-none absolute inset-0 rounded-lg transition-all duration-300",
                            active
                              ? "bg-gradient-to-r from-blue-500/[0.08] via-blue-500/[0.04] to-transparent"
                              : "bg-transparent group-hover:bg-white/[0.04] group-hover:shadow-[inset_0_0_20px_rgba(255,255,255,0.02)]"
                          )} />
                          {Icon ? (
                            <Icon className={cn(
                              "relative h-[18px] w-[18px] shrink-0 transition-all duration-300",
                              active
                                ? "text-blue-400 drop-shadow-[0_0_6px_rgba(96,165,250,0.5)]"
                                : "text-white/40 group-hover:text-white/80 group-hover:drop-shadow-[0_0_4px_rgba(255,255,255,0.1)]"
                            )} />
                          ) : null}
                          <span className="relative min-w-0 flex-1 truncate text-left">{item.title}</span>
                          <ChevronDown className={cn(
                            "relative h-3.5 w-3.5 shrink-0 transition-transform duration-300",
                            !open && "-rotate-90",
                            active ? "text-blue-400/60" : "text-white/30"
                          )} />
                        </button>
                      </div>

                      {open && (
                        <div className="mt-0.5 space-y-0.5 overflow-hidden pl-5">
                          <div className="ml-[9px] border-l border-white/[0.06]">
                            {item.children.map((child, ci) => {
                              const childActive = isActive(pathname, child.href);
                              return (
                                <Link
                                  key={child.href}
                                  href={child.href}
                                  className={cn(
                                    "group/child relative flex items-center rounded-r-lg py-2 pl-4 pr-3 text-[12px] font-medium uppercase tracking-[0.1em] transition-all duration-300",
                                    childActive
                                      ? "text-blue-400"
                                      : "text-white/45 hover:text-white/80"
                                  )}
                                  style={{ animationDelay: `${ci * 40}ms` }}
                                >
                                  {childActive && (
                                    <div className="absolute left-0 top-1/2 -translate-x-px -translate-y-1/2">
                                      <div className="h-4 w-[2px] rounded-full bg-blue-400 shadow-[0_0_6px_rgba(96,165,250,0.5)]" />
                                    </div>
                                  )}
                                  <div className={cn(
                                    "pointer-events-none absolute inset-0 rounded-r-lg transition-all duration-300",
                                    childActive
                                      ? "bg-gradient-to-r from-blue-500/[0.06] to-transparent"
                                      : "group-hover/child:bg-white/[0.03]"
                                  )} />
                                  <span className="relative truncate">{child.title}</span>
                                </Link>
                              );
                            })}
                          </div>
                        </div>
                      )}
                    </div>
                  );
                }

                return (
                  <div
                    key={item.href}
                    style={mounted ? { animationDelay: `${staggerIdx * 50}ms` } : undefined}
                    className={cn("group relative", mounted && "sidebar-item-enter")}
                  >
                    <Link
                      href={item.href}
                      className={cn(
                        "relative flex items-center gap-2.5 rounded-lg px-3 py-2.5 text-[13px] font-semibold uppercase tracking-[0.12em] transition-all duration-300",
                        active
                          ? "text-blue-400"
                          : "text-white/60 hover:text-white"
                      )}
                    >
                      {active && (
                        <div className="absolute left-0 top-1/2 -translate-y-1/2">
                          <div className="h-6 w-[3px] rounded-full bg-blue-400 shadow-[0_0_8px_rgba(96,165,250,0.6),0_0_20px_rgba(96,165,250,0.3)]" />
                        </div>
                      )}
                      <div className={cn(
                        "pointer-events-none absolute inset-0 rounded-lg transition-all duration-300",
                        active
                          ? "bg-gradient-to-r from-blue-500/[0.08] via-blue-500/[0.04] to-transparent"
                          : "bg-transparent group-hover:bg-white/[0.04] group-hover:shadow-[inset_0_0_20px_rgba(255,255,255,0.02)]"
                      )} />
                      {Icon ? (
                        <Icon className={cn(
                          "relative h-[18px] w-[18px] shrink-0 transition-all duration-300",
                          active
                            ? "text-blue-400 drop-shadow-[0_0_6px_rgba(96,165,250,0.5)]"
                            : "text-white/40 group-hover:text-white/80 group-hover:drop-shadow-[0_0_4px_rgba(255,255,255,0.1)]"
                        )} />
                      ) : null}
                      <span className="relative truncate">{item.title}</span>
                    </Link>
                  </div>
                );
              })}
            </div>
          </div>
        ))}
      </nav>

      {/* User Section */}
      <div className="absolute bottom-0 left-0 right-0 border-t border-white/[0.06] bg-[#0b1120]/95 px-3 py-3 backdrop-blur-md">
        <div className="flex items-center gap-3">
          <div className="group relative">
            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/20 to-cyan-500/20 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
              <div className="absolute inset-0 rounded-xl bg-blue-400/0 transition-all duration-300 group-hover:bg-blue-400/10" />
              <User className="relative h-5 w-5 text-blue-400" />
            </div>
            <div className="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-[#0b1120] bg-emerald-400 shadow-[0_0_6px_rgba(52,211,153,0.5)]" />
          </div>
          <div className="min-w-0 flex-1">
            <div className="truncate text-[13px] font-semibold text-white">{user?.name || "Kullanıcı"}</div>
            <div className="flex items-center gap-1.5">
              <div className="h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-[0_0_4px_rgba(52,211,153,0.5)]" />
              <span className="text-[10px] font-medium text-emerald-400/80">Çevrimiçi</span>
              <span className="text-[10px] text-white/30">•</span>
              <span className="text-[10px] text-white/40">{user?.role === "admin" ? "Yönetici" : "Kullanıcı"}</span>
            </div>
          </div>
          <button
            type="button"
            onClick={handleLogout}
            className="group/logout flex h-8 w-8 items-center justify-center rounded-lg border border-white/[0.06] bg-white/[0.02] text-white/30 transition-all duration-300 hover:border-red-500/30 hover:bg-red-500/10 hover:text-red-400"
            title="Çıkış Yap"
          >
            <LogOut className="h-3.5 w-3.5 transition-transform duration-300 group-hover/logout:translate-x-0.5" />
          </button>
        </div>
      </div>
    </aside>
  );
}
