import { createClient } from "@supabase/supabase-js";

export function createSupabaseBrowserClient() {
  const url = process.env.NEXT_PUBLIC_SUPABASE_URL || "http://localhost:54321";
  const anonKey = process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY || "anon";

  if (!process.env.NEXT_PUBLIC_SUPABASE_URL || !process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY) {
    console.warn(
      "Supabase env is missing (NEXT_PUBLIC_SUPABASE_URL / NEXT_PUBLIC_SUPABASE_ANON_KEY). Running in disabled mode."
    );
  }

  return createClient(url, anonKey);
}
