<script setup>
import { useReportsView } from '@/Modules/Report'
import { onMounted } from 'vue'

const { redirect } = useReportsView()

onMounted(() => {
  redirect()
})
</script>

<template>
  <div class="reports-redirect">
    <p>Redirigiendo...</p>
  </div>
</template>

<style scoped>
.reports-redirect {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: #94a3b8;
}
</style>
