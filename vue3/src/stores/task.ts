import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export interface Task {
  id: number
  title: string
  description?: string
  status: 'todo' | 'in_progress' | 'done'
  assigned_to?: number
  due_date?: string
  created_at: string
  updated_at: string
  assignee?: {
    id: number
    name: string
    email: string
  }
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  total: number
}

export const useTaskStore = defineStore('task', () => {
  const tasks = ref<Task[]>([])
  const currentPage = ref(1)
  const lastPage = ref(1)
  const total = ref(0)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchTasks = async (
    page: number = 1,
    status?: string,
    assignedTo?: number
  ) => {
    loading.value = true
    error.value = null
    try {
      const params: Record<string, any> = { page }
      if (status) params.status = status
      if (assignedTo) params.assigned_to = assignedTo

      const response = await axios.get<PaginatedResponse<Task>>('/tasks', { params })
      tasks.value = response.data.data
      currentPage.value = response.data.current_page
      lastPage.value = response.data.last_page
      total.value = response.data.total
    } catch (err) {
      error.value = 'Failed to fetch tasks'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  const createTask = async (taskData: Partial<Task>) => {
    try {
      const response = await axios.post<Task>('/tasks', taskData)
      tasks.value.unshift(response.data)
      return response.data
    } catch (err) {
      error.value = 'Failed to create task'
      console.error(err)
      throw err
    }
  }

  const updateTask = async (id: number, taskData: Partial<Task>) => {
    try {
      const response = await axios.put<Task>(`/tasks/${id}`, taskData)
      const index = tasks.value.findIndex(t => t.id === id)
      if (index !== -1) {
        tasks.value[index] = response.data
      }
      return response.data
    } catch (err) {
      error.value = 'Failed to update task'
      console.error(err)
      throw err
    }
  }

  const deleteTask = async (id: number) => {
    try {
      await axios.delete(`/tasks/${id}`)
      tasks.value = tasks.value.filter(t => t.id !== id)
    } catch (err) {
      error.value = 'Failed to delete task'
      console.error(err)
      throw err
    }
  }

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
  }
})
