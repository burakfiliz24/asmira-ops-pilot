"use client";

import { useEffect } from "react";
import { AlertTriangle, RefreshCw, Home } from "lucide-react";
import Link from "next/link";

export default function AppError({
  error,
  reset,
}: {
  error: Error & { digest?: string };
  reset: () => void;
}) {
  useEffect(() => {
    console.error("App error:", error);
  }, [error]);

  return (
    <div className="flex min-h-[calc(100vh-120px)] items-center justify-center p-8">
      <div className="w-full max-w-md rounded-2xl border border-red-500/20 bg-gradient-to-br from-red-500/5 to-transparent p-8 text-center">
        <div className="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-red-500/10 shadow-[inset_0_1px_1px_rgba(255,255,255,0.1)]">
          <AlertTriangle className="h-8 w-8 text-red-400" />
        </div>
        <h2 className="mb-2 text-xl font-bold text-white">
          Bir Hata Oluştu
        </h2>
        <p className="mb-6 text-sm leading-relaxed text-white/50">
          Bu sayfada beklenmeyen bir hata meydana geldi. Lütfen tekrar deneyin veya ana sayfaya dönün.
        </p>
        {error.message && (
          <details className="mb-6 text-left">
            <summary className="cursor-pointer text-xs text-white/30 transition-colors hover:text-white/50">
              Hata Detayı
            </summary>
            <pre className="mt-2 max-h-32 overflow-auto rounded-lg bg-black/30 p-3 text-xs text-red-300/70">
              {error.message}
            </pre>
          </details>
        )}
        <div className="flex items-center justify-center gap-3">
          <button
            onClick={reset}
            className="inline-flex items-center gap-2 rounded-xl bg-white/10 px-5 py-2.5 text-sm font-medium text-white transition-all hover:bg-white/20"
          >
            <RefreshCw className="h-4 w-4" />
            Tekrar Dene
          </button>
          <Link
            href="/dashboard"
            className="inline-flex items-center gap-2 rounded-xl bg-blue-500/20 px-5 py-2.5 text-sm font-medium text-blue-300 transition-all hover:bg-blue-500/30"
          >
            <Home className="h-4 w-4" />
            Ana Sayfa
          </Link>
        </div>
      </div>
    </div>
  );
}
