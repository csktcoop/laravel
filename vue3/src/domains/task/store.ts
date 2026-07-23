import api, {
  initApiClient,
  buildRequestConfig,
  createAbortController,
  isCancellationError,
} from "@/shared/api";
import { defineStore } from "pinia";
import { ref } from "vue";
import type { PaginatedResponse, Task } from "./model";

export const useTaskStore = defineStore("task", () => {
  const tasks = ref<Task[]>([]);
  const currentPage = ref(1);
  const lastPage = ref(1);
  const total = ref(0);
  const loading = ref(false);
  const error = ref<string | null>(null);
  let activeTasksRequest: AbortController | null = null;

  initApiClient()
  const fetchTasks = async (
    page: number = 1,
    status?: string,
    assignedTo?: number,
  ) => {
    loading.value = true;
    error.value = null;
    activeTasksRequest?.abort();
    activeTasksRequest = createAbortController();
    try {
      const params: Record<string, string | number | boolean | undefined> = {
        page,
      };
      if (status) params.status = status;
      if (assignedTo) params.assigned_to = assignedTo;

      const response = await api.get<PaginatedResponse<Task>>("/tasks", {
        params,
        ...buildRequestConfig(activeTasksRequest),
      });
      tasks.value = response.data.data;
      currentPage.value = response.data.current_page;
      lastPage.value = response.data.last_page;
      total.value = response.data.total;
    } catch (err) {
      if (isCancellationError(err)) {
        return;
      }
      error.value = "Failed to fetch tasks";
      console.error(err);
    } finally {
      activeTasksRequest = null;
      loading.value = false;
    }
  };

  const createTask = async (taskData: Partial<Task>) => {
    try {
      const response = await api.post<Task>(
        "/tasks",
        taskData,
        buildRequestConfig(),
      );
      tasks.value.unshift(response.data);
      return response.data;
    } catch (err) {
      error.value = "Failed to create task";
      console.error(err);
      throw err;
    }
  };

  const updateTask = async (id: number, taskData: Partial<Task>) => {
    try {
      const response = await api.put<Task>(
        `/tasks/${id}`,
        taskData,
        buildRequestConfig(),
      );
      const index = tasks.value.findIndex((t) => t.id === id);
      if (index !== -1) {
        tasks.value[index] = response.data;
      }
      return response.data;
    } catch (err) {
      error.value = "Failed to update task";
      console.error(err);
      throw err;
    }
  };

  const deleteTask = async (id: number) => {
    try {
      await api.delete(`/tasks/${id}`, buildRequestConfig());
      tasks.value = tasks.value.filter((t) => t.id !== id);
    } catch (err) {
      error.value = "Failed to delete task";
      console.error(err);
      throw err;
    }
  };

  return {
    tasks,
    currentPage,
    lastPage,
    total,
    loading,
    error,
    fetchTasks,
    createTask,
    updateTask,
    deleteTask,
  };
});
