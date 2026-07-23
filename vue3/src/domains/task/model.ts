export interface Task {
  id: number;
  title: string;
  description?: string;
  status: "todo" | "in_progress" | "done";
  assigned_to: number | null;
  due_date: string | null;
  created_at: string;
  updated_at: string;
  assignee: {
    id: number;
    name: string;
    email: string;
  };
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  total: number;
}
