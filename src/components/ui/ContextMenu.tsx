"use client";

import { useState, useEffect, useRef } from "react";
import { createPortal } from "react-dom";
import { Trash2, Edit3 } from "lucide-react";

type ContextMenuItem = {
  label: string;
  icon: React.ReactNode;
  onClick: () => void;
  danger?: boolean;
};

type ContextMenuProps = {
  items: ContextMenuItem[];
  children: React.ReactNode;
};

export function ContextMenu({ items, children }: ContextMenuProps) {
  const [isOpen, setIsOpen] = useState(false);
  const [position, setPosition] = useState({ x: 0, y: 0 });
  const [mounted, setMounted] = useState(false);
  const menuRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    setMounted(true);
  }, []);

  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (menuRef.current && !menuRef.current.contains(event.target as Node)) {
        setIsOpen(false);
      }
    }

    function handleEscape(event: KeyboardEvent) {
      if (event.key === "Escape") {
        setIsOpen(false);
      }
    }

    function handleScroll() {
      setIsOpen(false);
    }

    if (isOpen) {
      document.addEventListener("mousedown", handleClickOutside);
      document.addEventListener("keydown", handleEscape);
      document.addEventListener("scroll", handleScroll, true);
    }

    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
      document.removeEventListener("keydown", handleEscape);
      document.removeEventListener("scroll", handleScroll, true);
    };
  }, [isOpen]);

  function handleContextMenu(event: React.MouseEvent) {
    event.preventDefault();
    event.stopPropagation();

    let x = event.clientX;
    let y = event.clientY;

    const menuWidth = 160;
    const menuHeight = items.length * 44 + 8;
    
    if (x + menuWidth > window.innerWidth) {
      x = window.innerWidth - menuWidth - 10;
    }
    if (y + menuHeight > window.innerHeight) {
      y = window.innerHeight - menuHeight - 10;
    }

    setPosition({ x, y });
    setIsOpen(true);
  }

  const menu = isOpen && mounted ? createPortal(
    <div
      ref={menuRef}
      className="fixed z-[9999] min-w-[160px] overflow-hidden rounded-lg border border-white/20 bg-[#0d1526] py-1 shadow-[0_10px_40px_rgba(0,0,0,0.6)]"
      style={{ left: position.x, top: position.y }}
    >
      {items.map((item, index) => (
        <button
          key={index}
          type="button"
          onClick={(e) => {
            e.preventDefault();
            e.stopPropagation();
            setIsOpen(false);
            item.onClick();
          }}
          className={`flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm transition ${
            item.danger
              ? "text-red-400 hover:bg-red-500/20"
              : "text-white/90 hover:bg-white/10"
          }`}
        >
          {item.icon}
          {item.label}
        </button>
      ))}
    </div>,
    document.body
  ) : null;

  return (
    <div onContextMenu={handleContextMenu} className="h-full">
      {children}
      {menu}
    </div>
  );
}

export function DeleteMenuItem({ onDelete }: { onDelete: () => void }) {
  return {
    label: "Sil",
    icon: <Trash2 className="h-4 w-4" />,
    onClick: onDelete,
    danger: true,
  };
}

export function EditMenuItem({ onEdit }: { onEdit: () => void }) {
  return {
    label: "Düzenle",
    icon: <Edit3 className="h-4 w-4" />,
    onClick: onEdit,
    danger: false,
  };
}
