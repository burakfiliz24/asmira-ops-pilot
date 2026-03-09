import type { NextConfig } from "next";

const isStaticExport = process.env.STATIC_EXPORT === "true";

const nextConfig: NextConfig = {
  ...(isStaticExport ? { output: "export", trailingSlash: true } : {}),
  
  images: {
    unoptimized: true,
  },

  // Native modüller (geliştirme ortamı için)
  serverExternalPackages: [],
  
  // Production optimizations
  poweredByHeader: false,
  
  // Strict mode for better debugging
  reactStrictMode: true,
};

export default nextConfig;
