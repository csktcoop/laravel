export interface User {
  id: number;
  name: string;
  email: string;
  role: "admin" | "user";
}

export class UserProfile {
  constructor(
    public id: number,
    public name: string,
    public email: string,
    public role: string,
  ) {}

  get displayName(): string {
    return `${this.name} (${this.email})`;
  }
}
