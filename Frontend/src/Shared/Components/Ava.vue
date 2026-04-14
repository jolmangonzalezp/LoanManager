<script setup>
const props = defineProps({
  initials: { type: String, default: '??' },
  size: { type: Number, default: 36 }
})
</script>

<template>
  <div class="ava" :style="{ width: size + 'px', height: size + 'px', fontSize: size * 0.3 + 'px' }">
    {{ initials }}
  </div>
</template>

<style scoped>
.ava {
  border-radius: 8px;
  background: rgba(212,175,55,0.1);
  border: 1px solid rgba(212,175,55,0.28);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  color: #d4af37;
  font-family: 'Share Tech Mono', monospace;
  flex-shrink: 0;
}
</style>