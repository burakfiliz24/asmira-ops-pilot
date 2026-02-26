import type { LucideIcon } from "lucide-react";
import { LayoutDashboard, Truck, FileText, BookOpen, Settings, UserCheck, PackageCheck, Navigation } from "lucide-react";

export type NavItem = {
  title: string;
  href: string;
  icon?: LucideIcon;
  children?: NavItem[];
};

export type NavSection = {
  title: string;
  items: NavItem[];
};

export const navSections: NavSection[] = [
  {
    title: "Ana Menü",
    items: [
      { title: "Dashboard", href: "/dashboard", icon: LayoutDashboard },
      { title: "Gemi Takip", href: "/vessel-tracking", icon: Navigation },
      {
        title: "Araç Evrakları",
        href: "/vehicle-documents",
        icon: Truck,
        children: [
          { title: "Asmira Özmal", href: "/vehicle-documents/asmira" },
          { title: "Tedarikçi Araçları", href: "/vehicle-documents/suppliers" },
        ],
      },
      { title: "Şoför Evrakları", href: "/driver-documents", icon: UserCheck },
      { title: "Evrak Paketi", href: "/document-package", icon: PackageCheck },
      { title: "Dilekçeler", href: "/petitions", icon: FileText },
      { title: "Port Wiki", href: "/port-wiki", icon: BookOpen },
      { title: "Ayarlar", href: "/settings", icon: Settings },
    ],
  }
];
