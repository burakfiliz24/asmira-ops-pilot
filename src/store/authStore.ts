import { create } from "zustand";
import { persist } from "zustand/middleware";
import { authApi, usersApi } from "@/lib/api/client";

export interface User {
  id: string;
  username: string;
  password: string;
  name: string;
  role: "admin" | "user";
}

interface AuthState {
  user: Omit<User, "password"> | null;
  isAuthenticated: boolean;
  users: User[];
  login: (username: string, password: string) => Promise<boolean>;
  logout: () => void;
  addUser: (user: Omit<User, "id">) => void;
  updateUser: (id: string, updates: Partial<Omit<User, "id">>) => void;
  deleteUser: (id: string) => boolean;
  syncFromServer: () => Promise<void>;
}

const DEFAULT_USERS: User[] = [
  {
    id: "user_1",
    username: "asmira",
    password: "123",
    name: "Asmira Operasyon",
    role: "admin",
  },
];

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      user: null,
      isAuthenticated: false,
      users: DEFAULT_USERS,
      
      login: async (username: string, password: string) => {
        try {
          // Sunucu üzerinden doğrula (PHP API)
          const user = await authApi.login(username, password);
          set({
            user: { id: user.id, username: user.username, name: user.name, role: user.role as "admin" | "user" },
            isAuthenticated: true,
          });
          return true;
        } catch {
          // Sunucu erişilemezse lokal kontrol (offline fallback)
          const { users } = get();
          const foundUser = users.find(
            (u) => u.username.toLowerCase() === username.toLowerCase() && u.password === password
          );
          if (foundUser) {
            const { password: _, ...userWithoutPassword } = foundUser;
            set({ user: userWithoutPassword, isAuthenticated: true });
            return true;
          }
          return false;
        }
      },
      
      logout: () => {
        set({
          user: null,
          isAuthenticated: false,
        });
      },

      addUser: (userData) => {
        const newUser: User = {
          ...userData,
          id: `user_${Date.now()}`,
        };
        set((state) => ({
          users: [...state.users, newUser],
        }));
        usersApi.create(userData as { username: string; password: string; name: string; role: string })
          .catch((e) => console.warn("[Sync] addUser failed:", e));
      },

      updateUser: (id, updates) => {
        set((state) => ({
          users: state.users.map((u) =>
            u.id === id ? { ...u, ...updates } : u
          ),
          user: state.user?.id === id
            ? { ...state.user, ...updates, id }
            : state.user,
        }));
        usersApi.update(id, updates as Record<string, unknown>)
          .catch((e) => console.warn("[Sync] updateUser failed:", e));
      },

      deleteUser: (id) => {
        const { users, user } = get();
        const admins = users.filter((u) => u.role === "admin");
        const userToDelete = users.find((u) => u.id === id);
        
        if (userToDelete?.role === "admin" && admins.length <= 1) {
          return false;
        }
        
        if (user?.id === id) {
          return false;
        }
        
        set((state) => ({
          users: state.users.filter((u) => u.id !== id),
        }));
        usersApi.delete(id).catch((e) => console.warn("[Sync] deleteUser failed:", e));
        return true;
      },

      syncFromServer: async () => {
        try {
          const serverUsers = await usersApi.getAll() as User[];
          if (serverUsers.length > 0) {
            set({ users: serverUsers });
          }
        } catch (e) {
          console.warn("[Sync] syncUsers failed, using local data:", e);
        }
      },
    }),
    {
      name: "asmira-auth",
    }
  )
);
