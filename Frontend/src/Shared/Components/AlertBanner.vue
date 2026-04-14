<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: { type: String, default: 'danger' },
  clickable: { type: Boolean, default: false }
})

const configs = {
  danger: { bg: 'rgba(239,68,68,0.08)', color: '#f87171', border: 'rgba(239,68,68,0.2)' },
  warning: { bg: 'rgba(245,158,11,0.08)', color: '#fbbf24', border: 'rgba(245,158,11,0.2)' },
  success: { bg: 'rgba(16,185,129,0.08)', color: '#10b981', border: 'rgba(16,185,129,0.2)' }
}

const config = computed(() => configs[props.type] || configs.danger)
</script>

<template>
  <div 
    class="alert-banner" 
    :class="{ clickable }"
    :style="{ background: config.bg, color: config.color, borderColor: config.border }"
  >
    <slot />
  </div>
</template>

<style scoped>
.alert-banner {
  padding: 11px 16px;
  border-radius: 7px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13px;
  border: 1px solid;
  backdrop-filter: blur(8px);
}

.alert-banner.clickable {
  cursor: pointer;
}

.alert-banner.clickable:hover {
  filter: brightness(1.1);
}
</style>