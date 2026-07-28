const AUTH_TOKEN_KEY = "auth_token";

export const authStorage = {
  get(): string | null {
    return localStorage.getItem(AUTH_TOKEN_KEY);
  },
  set(token: string): void {
    localStorage.setItem(AUTH_TOKEN_KEY, token);
  },
  remove(): void {
    localStorage.removeItem(AUTH_TOKEN_KEY);
  },
};
