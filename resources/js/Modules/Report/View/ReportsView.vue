<script setup lang="ts">
import { ref } from 'vue';
import { SummaryReport } from '@/Modules/Report';

const activeComponent = ref(SummaryReport);

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
