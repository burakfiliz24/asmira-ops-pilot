import { create } from "zustand";
import { persist } from "zustand/middleware";

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
  login: (username: string, password: string) => boolean;
  logout: () => void;
  addUser: (user: Omit<User, "id">) => void;
  updateUser: (id: string, updates: Partial<Omit<User, "id">>) => void;
  deleteUser: (id: string) => boolean;
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
      
      login: (username: string, password: string) => {
        const { users } = get();
        const foundUser = users.find(
          (u) => u.username.toLowerCase() === username.toLowerCase() && u.password === password
        );
        
        if (foundUser) {
          // eslint-disable-next-line @typescript-eslint/no-unused-vars
          const { password: _unusedPassword, ...userWithoutPassword } = foundUser;
          set({
            user: userWithoutPassword,
            isAuthenticated: true,
          });
          return true;
        }
        return false;
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
      },

      updateUser: (id, updates) => {
        set((state) => ({
          users: state.users.map((u) =>
            u.id === id ? { ...u, ...updates } : u
          ),
          // If updating current user, update the session too
          user: state.user?.id === id
            ? { ...state.user, ...updates, id }
            : state.user,
        }));
      },

      deleteUser: (id) => {
        const { users, user } = get();
        // Can't delete the last admin
        const admins = users.filter((u) => u.role === "admin");
        const userToDelete = users.find((u) => u.id === id);
        
        if (userToDelete?.role === "admin" && admins.length <= 1) {
          return false;
        }
        
        // Can't delete yourself
        if (user?.id === id) {
          return false;
        }
        
        set((state) => ({
          users: state.users.filter((u) => u.id !== id),
        }));
        return true;
      },
    }),
    {
      name: "asmira-auth",
    }
  )
);
