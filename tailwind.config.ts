import type { Config } from "tailwindcss";

const config: Config = {
  content: ["./src/**/*.{js,ts,jsx,tsx,mdx}"],
  theme: {
    extend: {
      colors: {
        brand: {
          navy: "#0B1F3B",
          navySoft: "#112A4F",
          white: "#FFFFFF",
        },
      },
    },
  },
  plugins: [],
};

export default config;
