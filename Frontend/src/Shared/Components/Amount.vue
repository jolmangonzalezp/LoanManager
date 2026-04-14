<script setup>
const props = defineProps({
  color: String
})
</script>

<template>
  <span class="amount" :style="{ color }">
    <slot />
  </span>
</template>

<style scoped>
.amount {
  font-family: 'Share Tech Mono', monospace;
  font-size: 14px;
  font-weight: 700;
}
</style>