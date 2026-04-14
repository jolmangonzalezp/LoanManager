<script setup>
const props = defineProps({
  action: Boolean
})
</script>

<template>
  <div class="card-title">
    <slot />
    <div v-if="action" class="action">
      <slot name="action" />
    </div>
  </div>
</template>

<style scoped>
.card-title {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 14px;
  margin-bottom: 18px;
  border-bottom: 1px solid rgba(255,255,255,0.07);
}

.card-title :deep(.action) {
  display: flex;
  gap: 8px;
}
</style>