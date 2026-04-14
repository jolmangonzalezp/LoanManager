<script setup>
const props = defineProps({
  goldBorder: Boolean
})
</script>

<template>
  <div class="gcard" :class="{ goldBorder }">
    <slot />
  </div>
</template>

<style scoped>
.gcard {
  background: rgba(255,255,255,0.03);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 10px;
  padding: 22px;
}

.gcard.goldBorder {
  border-color: rgba(212,175,55,0.28);
}
</style>