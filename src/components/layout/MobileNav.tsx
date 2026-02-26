"use client";

import { useState } from "react";
import Link from "next/link";
import Image from "next/image";
import { usePathname, useRouter } from "next/navigation";
import { Menu, X, ChevronDown, LogOut, User } from "lucide-react";
import { navSections } from "@/lib/constants/nav";
import { cn } from "@/lib/utils/cn";
import { useAuthStore } from "@/store/authStore";

function isActive(pathname: string, href: string) {
  if (href === "/") return pathname === "/";
  return pathname === href || pathname.startsWith(href + "/");
}

export default function MobileNav() {
  const [isOpen, setIsOpen] = useState(false);
  const [openByHref, setOpenByHref] = useState<Record<string, boolean>>({});
  const pathname = usePathname();
  const router = useRouter();
  const { user, logout } = useAuthStore();

  const handleLogout = () => {
    logout();
    router.push("/login");
    setIsOpen(false);
  };

  const handleLinkClick = () => {
    setIsOpen(false);
  };

  return (
    <>
      {/* Mobile Header */}
      <header className="fixed left-0 right-0 top-0 z-50 flex h-14 items-center justify-between border-b border-white/10 bg-[#0b1120]/95 px-4 backdrop-blur-md lg:hidden">
        <div className="flex items-center gap-3">
          <Image
            src="/asmira-marine-logo.png"
            alt="Asmira Marine"
            width={120}
            height={32}
            className="h-8 w-auto object-contain brightness-0 invert"
            priority
          />
        </div>
        <button
          type="button"
          onClick={() => setIsOpen(!isOpen)}
          className="flex h-10 w-10 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-white"
        >
          {isOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
        </button>
      </header>

      {/* Mobile Menu Overlay */}
      {isOpen && (
        <div
          className="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"
          onClick={() => setIsOpen(false)}
        />
      )}

      {/* Mobile Menu Panel */}
      <div
        className={cn(
          "fixed right-0 top-0 z-50 h-full w-72 transform bg-[#0b1120] shadow-2xl transition-transform duration-300 ease-in-out lg:hidden",
          isOpen ? "translate-x-0" : "translate-x-full"
        )}
      >
        {/* Close Button */}
        <div className="flex h-14 items-center justify-between border-b border-white/10 px-4">
          <span className="text-sm font-semibold text-white/70">Menü</span>
          <button
            type="button"
            onClick={() => setIsOpen(false)}
            className="flex h-8 w-8 items-center justify-center rounded-lg text-white/60 hover:bg-white/10 hover:text-white"
          >
            <X className="h-5 w-5" />
          </button>
        </div>

        {/* Navigation */}
        <nav className="flex-1 overflow-y-auto px-3 py-4">
          {navSections.map((section) => (
            <div key={section.title} className="mb-4">
              <div className="px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-white/30">
                {section.title}
              </div>
              <div className="space-y-1">
                {section.items.map((item) => {
                  const active = isActive(pathname, item.href);
                  const Icon = item.icon;

                  if (item.children?.length) {
                    const open = openByHref[item.href] ?? false;
                    return (
                      <div key={item.href}>
                        <button
                          type="button"
                          onClick={() =>
                            setOpenByHref((prev) => ({ ...prev, [item.href]: !open }))
                          }
                          className={cn(
                            "flex w-full items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors",
                            active
                              ? "bg-blue-500/10 text-blue-400"
                              : "text-white/70 hover:bg-white/5 hover:text-white"
                          )}
                        >
                          {Icon && <Icon className="h-5 w-5 shrink-0" />}
                          <span className="flex-1 text-left">{item.title}</span>
                          <ChevronDown
                            className={cn(
                              "h-4 w-4 transition-transform",
                              open && "rotate-180"
                            )}
                          />
                        </button>
                        {open && (
                          <div className="ml-8 mt-1 space-y-1 border-l border-white/10 pl-3">
                            {item.children.map((child) => {
                              const childActive = isActive(pathname, child.href);
                              return (
                                <Link
                                  key={child.href}
                                  href={child.href}
                                  onClick={handleLinkClick}
                                  className={cn(
                                    "block rounded-lg px-3 py-2 text-sm transition-colors",
                                    childActive
                                      ? "bg-blue-500/10 text-blue-400"
                                      : "text-white/60 hover:bg-white/5 hover:text-white"
                                  )}
                                >
                                  {child.title}
                                </Link>
                              );
                            })}
                          </div>
                        )}
                      </div>
                    );
                  }

                  return (
                    <Link
                      key={item.href}
                      href={item.href}
                      onClick={handleLinkClick}
                      className={cn(
                        "flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors",
                        active
                          ? "bg-blue-500/10 text-blue-400"
                          : "text-white/70 hover:bg-white/5 hover:text-white"
                      )}
                    >
                      {Icon && <Icon className="h-5 w-5 shrink-0" />}
                      <span>{item.title}</span>
                    </Link>
                  );
                })}
              </div>
            </div>
          ))}
        </nav>

        {/* User Section */}
        <div className="absolute bottom-0 left-0 right-0 border-t border-white/10 bg-[#0b1120] p-4">
          <div className="flex items-center gap-3">
            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500/20 to-cyan-500/20">
              <User className="h-5 w-5 text-blue-400" />
            </div>
            <div className="min-w-0 flex-1">
              <div className="truncate text-sm font-semibold text-white">
                {user?.name || "Kullanıcı"}
              </div>
              <div className="text-xs text-white/50">
                {user?.role === "admin" ? "Yönetici" : "Kullanıcı"}
              </div>
            </div>
            <button
              type="button"
              onClick={handleLogout}
              className="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 text-white/50 hover:border-red-500/30 hover:bg-red-500/10 hover:text-red-400"
              title="Çıkış Yap"
            >
              <LogOut className="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>
    </>
  );
}
