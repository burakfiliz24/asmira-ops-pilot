import { cn } from "@/lib/utils/cn";
import type { ButtonHTMLAttributes } from "react";

type Variant = "primary" | "secondary" | "ghost";
type Size = "sm" | "md";

type Props = ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: Variant;
  size?: Size;
};

export function buttonClasses({
  variant = "primary",
  size = "md",
  className,
}: {
  variant?: Variant;
  size?: Size;
  className?: string;
}) {
  const base =
    "inline-flex items-center justify-center gap-2 rounded-md font-medium transition-colors disabled:opacity-50 disabled:pointer-events-none";

  const variants: Record<Variant, string> = {
    primary:
      "bg-brand-navy text-white hover:bg-brand-navySoft border border-white/10",
    secondary:
      "bg-white text-[var(--foreground)] border border-[var(--border)] hover:bg-black/[.02]",
    ghost:
      "bg-transparent text-[var(--foreground)] hover:bg-black/[.04] border border-transparent",
  };

  const sizes: Record<Size, string> = {
    sm: "h-9 px-3 text-sm",
    md: "h-10 px-4 text-sm",
  };

  return cn(base, variants[variant], sizes[size], className);
}

export default function Button({
  className,
  variant = "primary",
  size = "md",
  ...props
}: Props) {
  return (
    <button
      className={buttonClasses({ variant, size, className })}
      {...props}
    />
  );
}
