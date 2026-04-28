<script setup lang="ts">
import { ref } from 'vue'
import SummaryReport from './reports/SummaryReport.vue'

const activeReport = ref('summary')

const reports = [
  { id: 'summary', label: 'Resumen' },
]
const activeComponent = ref(SummaryReport)

const selectReport = (report: any) => {
  activeReport.value = report.id
}
</script>

<template>
  <div class="reports-container">
    <component :is="activeComponent" />
  </div>
</template>

<style scoped>
.reports-container {
  padding: 20px;
  height: calc(100vh - 100px);
}
</style>