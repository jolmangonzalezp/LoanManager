<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

onMounted(() => {
  router.replace('/reportes/cartera')
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
