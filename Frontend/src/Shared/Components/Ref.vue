<script setup>
defineProps({
  children: String
})
</script>

<template>
  <span class="ref">
    <slot />
  </span>
</template>

<style scoped>
.ref {
  font-family: 'Share Tech Mono', monospace;
  font-size: 12px;
  color: #94a3b8;
  letter-spacing: 0.5px;
}
</style>