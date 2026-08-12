<template>
  <div class="tasks-page">
    <div class="page-header">
      <h1>Tasks</h1>
      <el-button role="button" type="primary" @click="showCreateDialog = true"
        >New Task</el-button
      >
    </div>

    <el-card class="toolbar-card">
      <div class="toolbar">
        <el-input
          v-model="searchTitle"
          placeholder="Search by title"
          clearable
          @input="onSearchChange"
        />
        <el-select
          v-model="filterStatus"
          placeholder="Status"
          clearable
          @change="onFilterChange"
        >
          <el-option
            v-for="item in statusOptions"
            :key="item.value"
            :label="item.title"
            :value="item.value"
          />
        </el-select>
        <el-select
          v-if="authStore.user?.role === 'admin'"
          v-model="filterAssignedTo"
          placeholder="Assigned to"
          clearable
          @change="onFilterChange"
        >
          <el-option
            v-for="item in assignees"
            :key="item.id"
            :label="item.name"
            :value="item.id"
          />
        </el-select>
      </div>
    </el-card>

    <el-card class="content-card">
      <el-progress v-if="taskStore.loading" :indeterminate="true" />
      <el-alert
        v-if="taskStore.error"
        :title="taskStore.error"
        type="error"
        show-icon
        class="mb-4"
      />

      <el-empty
        v-if="taskStore.tasks.length === 0"
        description="No tasks found"
      />

      <el-table v-else :data="filteredTasks" stripe>
        <el-table-column prop="title" label="Title" />
        <el-table-column prop="description" label="Description" />
        <el-table-column label="Status">
          <template #default="{ row }">
            <TaskStatusTag :status="row.status" />
          </template>
        </el-table-column>
        <el-table-column label="Assignee">
          <template #default="{ row }">
            {{ row.assignee?.name || 'Unassigned' }}
          </template>
        </el-table-column>
        <el-table-column label="Due Date">
          <template #default="{ row }">
            {{ formatDate(row.due_date) }}
          </template>
        </el-table-column>
        <el-table-column label="Actions" width="120">
          <template #default="{ row }">
            <el-button role="button" link type="primary" @click="editTask(row)"
              >Edit</el-button
            >
            <el-button role="button" link type="danger" @click="deleteTask(row.id)"
              >Delete</el-button
            >
          </template>
        </el-table-column>
      </el-table>

      <el-pagination
        v-if="taskStore.lastPage > 1"
        class="pagination"
        v-model:current-page="currentPage"
        :page-size="20"
        :total="taskStore.total"
        layout="prev, pager, next"
        @current-change="onPageChange"
      />
    </el-card>

    <el-dialog
      v-model="showCreateDialog"
      :title="editingTask ? 'Edit Task' : 'Create Task'"
      width="560px"
    >
      <el-form :model="formData" label-width="100px" @submit.prevent="saveTask">
        <el-form-item label="Title">
          <el-input v-model="formData.title" required />
        </el-form-item>
        <el-form-item label="Description">
          <el-input v-model="formData.description" type="textarea" :rows="3" />
        </el-form-item>
        <el-form-item label="Status">
          <el-select v-model="formData.status" placeholder="Select status">
            <el-option
              v-for="item in statusOptions"
              :key="item.value"
              :label="item.title"
              :value="item.value"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="Assign to">
          <el-select
            :key="showCreateDialog"
            v-model="formData.assigned_to"
            placeholder="Select assignee"
            clearable
            :disabled="!isAdmin"
            :teleported="false"
          >
            <el-option
              v-for="item in assignees"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="Due date">
          <el-date-picker
            v-model="formData.due_date"
            type="date"
            format="YYYY-MM-DD"
            value-format="YYYY-MM-DD"
          />
        </el-form-item>
        <el-alert
          v-if="formError"
          :title="formError"
          type="error"
          show-icon
          class="mb-4"
        />
      </el-form>

      <template #footer>
        <span class="dialog-footer">
          <el-button role="button" @click="closeDialog">Cancel</el-button>
          <el-button role="button" type="primary" :loading="formLoading" @click="saveTask">
            {{ editingTask ? 'Update' : 'Create' }}
          </el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from "vue";
import TaskStatusTag from "@/components/TaskStatusTag.vue";
import { useTaskStore, useUserStore } from "@/domains";
import type { Task } from "@/domains/task/model";
import type { User } from "@/domains/user/model";
import api, {
  buildRequestConfig,
  createAbortController,
  getApiErrorMessage,
  isCancellationError,
} from "@/shared/api";

const taskStore = useTaskStore();
const authStore = useUserStore();

const currentPage = ref(1);
const searchTitle = ref("");
const filterStatus = ref<string | null>(null);
const filterAssignedTo = ref<number | null>(null);
const showCreateDialog = ref(false);
const editingTask = ref<Task | null>(null);
const formLoading = ref(false);
const formError = ref("");

const assignees = ref<User[]>([]);
let usersRequest: AbortController | null = null;

const statusOptions = [
  { title: "To Do", value: "todo" },
  { title: "In Progress", value: "in_progress" },
  { title: "Done", value: "done" },
];

type TaskForm = {
  title: string;
  description: string | null;
  status: Task["status"];
  assigned_to: number | null;
  due_date: string | null;
};

const emptyForm = (): TaskForm => ({
  title: "",
  description: null,
  status: "todo",
  assigned_to: (authStore.user?.id ?? null),
  due_date: "",
});

const formData = ref(emptyForm());

const filteredTasks = computed(() => {
  return taskStore.tasks.filter((task) => {
    if (
      searchTitle.value &&
      !task.title.toLowerCase().includes(searchTitle.value.toLowerCase())
    ) {
      return false;
    }
    return true;
  });
});

const formatDate = (date: string | null) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString();
};

const normalizeDateValue = (value?: string | null): string => {
  if (!value) return "";
  const datePart = value.split(" ")[0] ?? "";
  return datePart === "0000-00-00" ? "" : datePart;
};

const isAdmin = computed(() => authStore.user?.role === 'admin');

const fetchUsers = async () => {
  if (!authStore.user) {
    assignees.value = [];
    return;
  }

  if (authStore.user.role !== "admin") {
    assignees.value = [authStore.user];
    formData.value.assigned_to = authStore.user.id;
    return;
  }

  usersRequest?.abort();
  usersRequest = createAbortController();

  try {
    const response = await api.get("/users", buildRequestConfig(usersRequest));
    assignees.value = response.data;
  } catch (error) {
    if (isCancellationError(error)) {
      return;
    }
    console.error("Failed to fetch users:", error);
    assignees.value = [authStore.user];
  } finally {
    usersRequest = null;
  }
};

const onFilterChange = () => {
  currentPage.value = 1;
  taskStore.fetchTasks(
    currentPage.value,
    filterStatus.value || undefined,
    filterAssignedTo.value || undefined,
    searchTitle.value || undefined,
  );
};

const onPageChange = (page: number) => {
  taskStore.fetchTasks(
    page,
    filterStatus.value || undefined,
    filterAssignedTo.value || undefined,
    searchTitle.value || undefined,
  );
};

const editTask = (task: Task) => {
  editingTask.value = task;
  formData.value = {
    title: task.title,
    description: task.description || null,
    status: task.status,
    assigned_to: task.assigned_to ?? (!isAdmin.value ? (authStore.user?.id ?? null) : null),
    due_date: normalizeDateValue(task.due_date),
  };

  showCreateDialog.value = true;
};

const saveTask = async () => {
  formLoading.value = true;
  formError.value = "";

  try {
    const payload = {
      ...formData.value,
      due_date: formData.value.due_date || null,
    };

    if (editingTask.value) {
      await taskStore.updateTask(editingTask.value.id, payload);
    } else {
      await taskStore.createTask(payload);
      await taskStore.fetchTasks(
        currentPage.value,
        filterStatus.value || undefined,
        filterAssignedTo.value || undefined,
      );
    }
    closeDialog();
  } catch (error: unknown) {
    formError.value = getApiErrorMessage(error, "Failed to save task");
  } finally {
    formLoading.value = false;
  }
};

const deleteTask = async (id: number) => {
  if (confirm("Are you sure you want to delete this task?")) {
    try {
      await taskStore.deleteTask(id);
      await taskStore.fetchTasks(
        currentPage.value,
        filterStatus.value || undefined,
        filterAssignedTo.value || undefined,
      );

      if (currentPage.value > taskStore.lastPage) {
        currentPage.value = taskStore.lastPage;
        await taskStore.fetchTasks(
          currentPage.value,
          filterStatus.value || undefined,
          filterAssignedTo.value || undefined,
        );
      }
    } catch (error) {
      console.error("Failed to delete task:", error);
    }
  }
};

const closeDialog = () => {
  showCreateDialog.value = false;
  editingTask.value = null;
  formData.value = emptyForm();
  // formData.value?.resetFields();
  formError.value = "";
};

watch(
  () => authStore.user?.role,
  (_role) => {
    fetchUsers();
  },
  { immediate: true },
);

onMounted(() => {
  taskStore.fetchTasks();
});
</script>

<style scoped>
/* biome-ignore-all lint/correctness/noUnknownPseudoClass: Vue's :deep() selector is required for scoped styles. */
.tasks-page {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.toolbar {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.toolbar :deep(.el-input),
.toolbar :deep(.el-select) {
  min-width: 220px;
}

.content-card {
  min-height: 360px;
}

.pagination {
  margin-top: 16px;
  justify-content: flex-end;
}

.dialog-footer {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}

.mb-4 {
  margin-bottom: 16px;
}
</style>
