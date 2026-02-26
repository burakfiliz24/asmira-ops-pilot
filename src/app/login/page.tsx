"use client";

import { useState, useEffect } from "react";
import { useRouter } from "next/navigation";
import Image from "next/image";
import { Eye, EyeOff, Lock, User, LogIn, Anchor, Ship, Fuel, Waves } from "lucide-react";
import { useAuthStore } from "@/store/authStore";

export default function LoginPage() {
  const router = useRouter();
  const { login, isAuthenticated } = useAuthStore();

  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const [mounted, setMounted] = useState(false);
  const [cardVisible, setCardVisible] = useState(false);
  const [focusedField, setFocusedField] = useState<string | null>(null);
  const [loginSuccess, setLoginSuccess] = useState(false);

  useEffect(() => {
    setMounted(true);
    const t = setTimeout(() => setCardVisible(true), 150);
    return () => clearTimeout(t);
  }, []);

  useEffect(() => {
    if (mounted && isAuthenticated) {
      router.push("/dashboard");
    }
  }, [isAuthenticated, router, mounted]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setIsLoading(true);

    await new Promise((resolve) => setTimeout(resolve, 600));

    const success = login(username, password);

    if (success) {
      setLoginSuccess(true);
      setIsLoading(false);
      await new Promise((resolve) => setTimeout(resolve, 800));
      router.push("/dashboard");
    } else {
      setError("Kullanıcı adı veya şifre hatalı");
      setIsLoading(false);
    }
  };

  if (!mounted) {
    return null;
  }

  return (
    <div className="h-screen flex items-center justify-center p-4 relative overflow-hidden bg-[#0a0f1a]">
      {/* Background - Maritime & Logistics Theme */}
      <div className="absolute inset-0">
        {/* Ocean gradient base */}
        <div className="absolute inset-0 bg-gradient-to-b from-[#0a1525] via-[#0c1a2e] to-[#071018]" />
        
        {/* Subtle wave pattern at bottom */}
        <div className="absolute bottom-0 left-0 right-0 h-[40%] opacity-[0.06]">
          <svg viewBox="0 0 1440 320" className="absolute bottom-0 w-full" preserveAspectRatio="none">
            <path fill="currentColor" className="text-cyan-400" d="M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,250.7C672,235,768,181,864,181.3C960,181,1056,235,1152,234.7C1248,235,1344,181,1392,154.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" />
          </svg>
          <svg viewBox="0 0 1440 320" className="absolute bottom-0 w-full" preserveAspectRatio="none" style={{ transform: 'translateY(20px)' }}>
            <path fill="currentColor" className="text-blue-500" d="M0,256L48,240C96,224,192,192,288,181.3C384,171,480,181,576,197.3C672,213,768,235,864,224C960,213,1056,171,1152,165.3C1248,160,1344,192,1392,208L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" />
          </svg>
        </div>
        
        {/* Floating maritime icons */}
        <div className="absolute top-[8%] left-[8%] text-cyan-500/[0.08] animate-float-slow">
          <Ship className="w-32 h-32" strokeWidth={1} />
        </div>
        <div className="absolute top-[15%] right-[12%] text-blue-500/[0.09] animate-float-medium">
          <Anchor className="w-24 h-24" strokeWidth={1} />
        </div>
        <div className="absolute bottom-[25%] left-[5%] text-cyan-400/[0.07] animate-float-fast">
          <Fuel className="w-20 h-20" strokeWidth={1} />
        </div>
        <div className="absolute bottom-[15%] right-[8%] text-blue-400/[0.08] animate-float-slow">
          <Waves className="w-28 h-28" strokeWidth={1} />
        </div>
        <div className="absolute top-[45%] left-[15%] text-indigo-500/[0.06] animate-float-medium">
          <Ship className="w-16 h-16" strokeWidth={1} />
        </div>
        <div className="absolute top-[35%] right-[5%] text-cyan-500/[0.07] animate-float-fast">
          <Fuel className="w-14 h-14" strokeWidth={1} />
        </div>
        
        {/* Gradient orbs - ocean colors */}
        <div className="absolute -top-[20%] -right-[10%] w-[600px] h-[600px] rounded-full bg-gradient-to-br from-blue-600/10 to-transparent blur-3xl" />
        <div className="absolute -bottom-[30%] -left-[15%] w-[700px] h-[700px] rounded-full bg-gradient-to-tr from-cyan-600/8 to-transparent blur-3xl" />
        <div className="absolute top-[30%] right-[20%] w-[400px] h-[400px] rounded-full bg-indigo-500/5 blur-3xl" />
      </div>

      {/* Login Card */}
      <div
        className={`relative z-10 w-full max-w-[480px] mx-auto transition-all duration-700 ease-out ${
          loginSuccess
            ? "scale-95 opacity-0"
            : cardVisible
              ? "translate-y-0 opacity-100"
              : "translate-y-6 opacity-0"
        }`}
      >
        {/* Card */}
        <div className="relative bg-[#111827]/80 backdrop-blur-2xl rounded-3xl border border-white/[0.08] shadow-2xl shadow-black/40 overflow-hidden">
          {/* Top accent line */}
          <div className="absolute top-0 left-8 right-8 h-px bg-gradient-to-r from-transparent via-cyan-500/50 to-transparent" />
          
          <div className="p-6 sm:p-8 lg:p-10">
            {/* Logo */}
            <div className="flex justify-center mb-6 lg:mb-8">
              <div className="relative h-16 w-48 sm:h-20 sm:w-56 lg:h-24 lg:w-64">
                <Image
                  src="/asmira-energy-logo.png"
                  alt="Asmira Energy"
                  fill
                  className="object-contain"
                  priority
                />
              </div>
            </div>

            {/* Title */}
            <div className="text-center mb-6 lg:mb-8">
              <h1 className="text-xl sm:text-2xl lg:text-3xl font-light text-white mb-1">
                Operasyon{" "}
                <span className="font-bold bg-gradient-to-r from-cyan-400 to-blue-500 bg-clip-text text-transparent">
                  Yönetim Sistemi
                </span>
              </h1>
              <p className="text-white/40 text-sm">Hesabınıza giriş yapın</p>
            </div>

            {/* Form */}
            <form onSubmit={handleSubmit} className="space-y-4">
              {/* Username */}
              <div className="space-y-2">
                <label className="block text-xs font-medium text-white/40 uppercase tracking-wider">
                  Kullanıcı Adı
                </label>
                <div className={`relative group transition-all duration-300 ${
                  focusedField === "user" ? "scale-[1.02]" : ""
                }`}>
                  <div className={`absolute inset-0 rounded-2xl transition-all duration-300 ${
                    focusedField === "user" 
                      ? "bg-gradient-to-r from-cyan-500/20 to-blue-500/20 blur-xl" 
                      : ""
                  }`} />
                  <div className={`relative flex items-center rounded-2xl border-2 transition-all duration-300 ${
                    focusedField === "user" 
                      ? "border-cyan-500/60 bg-white/[0.06]" 
                      : "border-white/[0.08] bg-white/[0.03] hover:border-white/[0.12]"
                  }`}>
                    <div className="pl-5">
                      <User className={`h-5 w-5 transition-colors duration-300 ${
                        focusedField === "user" ? "text-cyan-400" : "text-white/30"
                      }`} />
                    </div>
                    <input
                      type="text"
                      value={username}
                      onChange={(e) => setUsername(e.target.value)}
                      onFocus={() => setFocusedField("user")}
                      onBlur={() => setFocusedField(null)}
                      placeholder="kullanici@asmira.com"
                      className="w-full h-11 lg:h-14 bg-transparent px-4 text-white text-sm lg:text-[15px] outline-none placeholder:text-white/25"
                      required
                    />
                  </div>
                </div>
              </div>

              {/* Password */}
              <div className="space-y-2">
                <label className="block text-xs font-medium text-white/40 uppercase tracking-wider">
                  Şifre
                </label>
                <div className={`relative group transition-all duration-300 ${
                  focusedField === "pass" ? "scale-[1.02]" : ""
                }`}>
                  <div className={`absolute inset-0 rounded-2xl transition-all duration-300 ${
                    focusedField === "pass" 
                      ? "bg-gradient-to-r from-cyan-500/20 to-blue-500/20 blur-xl" 
                      : ""
                  }`} />
                  <div className={`relative flex items-center rounded-2xl border-2 transition-all duration-300 ${
                    focusedField === "pass" 
                      ? "border-cyan-500/60 bg-white/[0.06]" 
                      : "border-white/[0.08] bg-white/[0.03] hover:border-white/[0.12]"
                  }`}>
                    <div className="pl-5">
                      <Lock className={`h-5 w-5 transition-colors duration-300 ${
                        focusedField === "pass" ? "text-cyan-400" : "text-white/30"
                      }`} />
                    </div>
                    <input
                      type={showPassword ? "text" : "password"}
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                      onFocus={() => setFocusedField("pass")}
                      onBlur={() => setFocusedField(null)}
                      placeholder="••••••••"
                      className="w-full h-11 lg:h-14 bg-transparent px-4 text-white text-sm lg:text-[15px] outline-none placeholder:text-white/25"
                      required
                    />
                    <button
                      type="button"
                      onClick={() => setShowPassword(!showPassword)}
                      className="pr-5 text-white/30 hover:text-white/60 transition-colors"
                    >
                      {showPassword ? <EyeOff className="h-5 w-5" /> : <Eye className="h-5 w-5" />}
                    </button>
                  </div>
                </div>
              </div>

              {/* Error */}
              {error && (
                <div className="flex items-center gap-3 px-5 py-4 rounded-2xl bg-red-500/10 border border-red-500/20">
                  <div className="h-2.5 w-2.5 rounded-full bg-red-500 animate-pulse" />
                  <span className="text-sm text-red-400">{error}</span>
                </div>
              )}

              {/* Submit */}
              <div className="pt-2">
                <button
                  type="submit"
                  disabled={isLoading}
                  className="group relative w-full h-11 lg:h-14 rounded-2xl text-white font-semibold text-sm lg:text-[15px] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed overflow-hidden"
                >
                  {/* Button background */}
                  <div className="absolute inset-0 bg-gradient-to-r from-cyan-500 via-blue-500 to-cyan-500 bg-[length:200%_100%] animate-shimmer" />
                  
                  {/* Hover overlay */}
                  <div className="absolute inset-0 bg-white/0 group-hover:bg-white/10 transition-colors duration-300" />
                  
                  {/* Content */}
                  {isLoading ? (
                    <div className="relative flex items-center justify-center gap-3">
                      <div className="h-5 w-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                      <span>Giriş Yapılıyor...</span>
                    </div>
                  ) : (
                    <div className="relative flex items-center justify-center gap-3">
                      <LogIn className="h-5 w-5" />
                      <span>Giriş Yap</span>
                    </div>
                  )}
                </button>
              </div>
            </form>
          </div>

          {/* Footer */}
          <div className="px-6 lg:px-10 pb-4 lg:pb-8 text-center">
            <div className="h-px w-full bg-gradient-to-r from-transparent via-white/10 to-transparent mb-4 lg:mb-6" />
            <p className="text-xs text-white/25">
              Asmira Denizcilik Operasyon Yönetim Sistemi
            </p>
            <p className="text-[10px] text-white/15 mt-1">
              v1.0.0 • © 2026
            </p>
          </div>
        </div>
      </div>

      {/* Animations */}
      <style jsx>{`
        @keyframes shimmer {
          0% { background-position: 200% 0; }
          100% { background-position: -200% 0; }
        }
        .animate-shimmer {
          animation: shimmer 3s linear infinite;
        }
        @keyframes float-slow {
          0%, 100% { transform: translateY(0) rotate(0deg); }
          50% { transform: translateY(-15px) rotate(3deg); }
        }
        @keyframes float-medium {
          0%, 100% { transform: translateY(0) rotate(0deg); }
          50% { transform: translateY(-10px) rotate(-2deg); }
        }
        @keyframes float-fast {
          0%, 100% { transform: translateY(0); }
          50% { transform: translateY(-8px); }
        }
        :global(.animate-float-slow) { animation: float-slow 8s ease-in-out infinite; }
        :global(.animate-float-medium) { animation: float-medium 6s ease-in-out infinite; }
        :global(.animate-float-fast) { animation: float-fast 4s ease-in-out infinite; }
      `}</style>
    </div>
  );
}
