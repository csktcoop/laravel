<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{
  status: "todo" | "in_progress" | "done";
}>();

const tagType = computed(() => {
  switch (props.status) {
    case "done":
      return "success";
    case "in_progress":
      return "primary";
    default:
      return "info";
  }
});
</script>

<template>
  <el-tag :type="tagType">{{ status.replace("_", " ").toUpperCase() }}</el-tag>
</template>
