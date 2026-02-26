export default function Topbar() {
  return (
    <header className="sticky top-0 z-10 flex h-16 items-center border-b border-[var(--border)] bg-white/90 px-6 backdrop-blur">
      <div className="min-w-0 flex-1">
        <div className="truncate text-sm font-medium">Operasyon Kontrol Paneli</div>
        <div className="truncate text-xs opacity-70">Asmira Petrol</div>
      </div>
    </header>
  );
}
