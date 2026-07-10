<template>
  <v-container fluid class="py-4">
    <v-row class="mb-6">
      <v-col cols="12">
        <h1 class="text-h4">Tasks</h1>
      </v-col>
    </v-row>

    <v-row class="mb-4">
      <v-col cols="12" md="8">
        <div class="d-flex gap-2 flex-wrap">
          <v-text-field
            v-model="searchTitle"
            label="Search by title"
            outlined
            dense
            style="flex: 1; min-width: 200px;"
            @input="onSearchChange"
          />
          
          <v-select
            v-model="filterStatus"
            label="Status"
            :items="statusOptions"
            item-title="title"
            item-value="value"
            outlined
            dense
            clearable
            style="flex: 1; min-width: 200px;"
            @update:model-value="onFilterChange"
          />

          <v-select
            v-if="authStore.user?.role === 'admin'"
            v-model="filterAssignedTo"
            label="Assigned to"
            :items="assignees"
            item-title="name"
            item-value="id"
            outlined
            dense
            clearable
            style="flex: 1; min-width: 200px;"
            @update:model-value="onFilterChange"
          />
        </div>
      </v-col>
      <v-col cols="12" md="4" class="text-right">
        <v-btn
          color="primary"
          @click="showCreateDialog = true"
        >
          <v-icon left>mdi-plus</v-icon>
          New Task
        </v-btn>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12">
        <v-progress-linear v-if="taskStore.loading" indeterminate></v-progress-linear>
        
        <v-alert v-if="taskStore.error" type="error" class="mb-4">
          {{ taskStore.error }}
        </v-alert>

        <v-table v-if="taskStore.tasks.length > 0" class="elevation-1">
          <thead>
            <tr>
              <th>Title</th>
              <th>Description</th>
              <th>Status</th>
              <th>Assignee</th>
              <th>Due Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="task in filteredTasks" :key="task.id">
              <td>{{ task.title }}</td>
              <td class="text-truncate" style="max-width: 200px;">{{ task.description }}</td>
              <td>
                <v-chip
                  :color="getStatusColor(task.status)"
                  text-color="white"
                  size="small"
                >
                  {{ formatStatus(task.status) }}
                </v-chip>
              </td>
              <td>{{ task.assignee?.name || 'Unassigned' }}</td>
              <td>{{ formatDate(task.due_date) }}</td>
              <td>
                <v-btn
                  size="small"
                  variant="text"
                  color="primary"
                  @click="editTask(task)"
                  class="mr-2"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  size="small"
                  variant="text"
                  color="error"
                  @click="deleteTask(task.id)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </td>
            </tr>
          </tbody>
        </v-table>

        <v-empty-state
          v-else
          headline="No tasks found"
          text="Create a new task to get started"
        ></v-empty-state>

        <!-- Pagination -->
        <v-pagination
          v-if="taskStore.lastPage > 1"
          v-model="currentPage"
          :length="taskStore.lastPage"
          class="mt-4"
          @update:model-value="onPageChange"
        ></v-pagination>
      </v-col>
    </v-row>

    <!-- Create/Edit Task Dialog -->
    <v-dialog
      v-model="showCreateDialog"
      max-width="500"
    >
      <v-card>
        <v-card-title>{{ editingTask ? 'Edit Task' : 'Create Task' }}</v-card-title>
        
        <v-card-text>
          <v-form @submit.prevent="saveTask">
            <v-text-field
              v-model="formData.title"
              label="Title"
              outlined
              class="mb-4"
              required
            />
            
            <v-textarea
              v-model="formData.description"
              label="Description"
              outlined
              class="mb-4"
              rows="3"
            />

            <v-select
              v-model="formData.status"
              label="Status"
              :items="statusOptions"
              item-title="title"
              item-value="value"
              outlined
              class="mb-4"
              required
            />

            <v-select
              v-model="formData.assigned_to"
              label="Assign to"
              :items="assignees"
              item-title="name"
              item-value="id"
              outlined
              class="mb-4"
              clearable
            />

            <v-text-field
              v-model="formData.due_date"
              label="Due Date"
              type="date"
              outlined
              class="mb-4"
            />

            <v-alert v-if="formError" type="error" class="mb-4">
              {{ formError }}
            </v-alert>

            <div class="d-flex gap-2">
              <v-btn
                type="submit"
                color="primary"
                :loading="formLoading"
              >
                {{ editingTask ? 'Update' : 'Create' }}
              </v-btn>
              <v-btn @click="closeDialog">Cancel</v-btn>
            </div>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useTaskStore, type Task } from '../stores/task'
import { useAuthStore } from '../stores/auth'
import axios from 'axios'

const taskStore = useTaskStore()
const authStore = useAuthStore()

const currentPage = ref(1)
const searchTitle = ref('')
const filterStatus = ref<string | null>(null)
const filterAssignedTo = ref<number | null>(null)
const showCreateDialog = ref(false)
const editingTask = ref<Task | null>(null)
const formLoading = ref(false)
const formError = ref('')
const assignees = ref<any[]>([])

const statusOptions = [
  { title: 'To Do', value: 'todo' },
  { title: 'In Progress', value: 'in_progress' },
  { title: 'Done', value: 'done' },
]

const formData = ref({
  title: '',
  description: '',
  status: 'todo',
  assigned_to: authStore.user?.id || null,
  due_date: '',
})

const filteredTasks = computed(() => {
  return taskStore.tasks.filter(task => {
    if (searchTitle.value && !task.title.toLowerCase().includes(searchTitle.value.toLowerCase())) {
      return false
    }
    return true
  })
})

const getStatusColor = (status: string) => {
  switch (status) {
    case 'todo':
      return 'grey'
    case 'in_progress':
      return 'blue'
    case 'done':
      return 'green'
    default:
      return 'grey'
  }
}

const formatStatus = (status: string) => {
  return status.replace('_', ' ').toUpperCase()
}

const formatDate = (date?: string) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString()
}

const fetchUsers = async () => {
  try {
    const response = await axios.get('/users')
    assignees.value = response.data
  } catch (error) {
    console.error('Failed to fetch users:', error)
  }
}

const onFilterChange = () => {
  currentPage.value = 1
  taskStore.fetchTasks(currentPage.value, filterStatus.value || undefined, filterAssignedTo.value || undefined)
}

const onSearchChange = () => {
  // Search is done client-side on the filtered tasks
}

const onPageChange = (page: number) => {
  taskStore.fetchTasks(page, filterStatus.value || undefined, filterAssignedTo.value || undefined)
}

const editTask = (task: Task) => {
  editingTask.value = task
  formData.value = {
    title: task.title,
    description: task.description || '',
    status: task.status,
    assigned_to: task.assigned_to || null,
    due_date: task.due_date || '',
  }
  showCreateDialog.value = true
}

const saveTask = async () => {
  formLoading.value = true
  formError.value = ''
  
  try {
    if (editingTask.value) {
      await taskStore.updateTask(editingTask.value.id, formData.value)
    } else {
      await taskStore.createTask(formData.value)
    }
    closeDialog()
  } catch (error: any) {
    formError.value = error.response?.data?.message || 'Failed to save task'
  } finally {
    formLoading.value = false
  }
}

const deleteTask = async (id: number) => {
  if (confirm('Are you sure you want to delete this task?')) {
    try {
      await taskStore.deleteTask(id)
    } catch (error) {
      console.error('Failed to delete task:', error)
    }
  }
}

const closeDialog = () => {
  showCreateDialog.value = false
  editingTask.value = null
  formData.value = {
    title: '',
    description: '',
    status: 'todo',
    assigned_to: authStore.user?.id || null,
    due_date: '',
  }
  formError.value = ''
}

watch(
  () => authStore.user?.role,
  (role) => {
    if (role === 'admin') {
      fetchUsers()
    }
  },
  { immediate: true }
)

onMounted(() => {
  taskStore.fetchTasks()
})
</script>
