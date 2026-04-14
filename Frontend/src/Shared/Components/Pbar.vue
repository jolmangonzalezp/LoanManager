<script setup>
const props = defineProps({
  pct: { type: Number, default: 0 },
  color: { type: String, default: '#d4af37' },
  width: { type: [Number, String], default: 88 }
})
</script>

<template>
  <div class="pbar-wrap" :style="{ width: typeof width === 'number' ? width + 'px' : width }">
    <div class="pbar-fill" :style="{ width: pct + '%', background: color }"></div>
  </div>
</template>

<style scoped>
.pbar-wrap {
  height: 3px;
  background: rgba(255,255,255,0.06);
  border-radius: 10px;
  overflow: hidden;
}

.pbar-fill {
  height: 100%;
  border-radius: 10px;
  box-shadow: 0 0 8px currentColor;
  transition: width 0.3s ease;
}
</style>