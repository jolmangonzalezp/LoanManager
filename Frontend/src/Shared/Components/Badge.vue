<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: { type: String, default: 'active' }
})

const configs = {
  active: { bg: 'rgba(212,175,55,0.15)', color: '#e8c84a', border: 'rgba(212,175,55,0.28)' },
  pending: { bg: 'rgba(245,158,11,0.1)', color: '#fbbf24', border: 'rgba(245,158,11,0.25)' },
  overdue: { bg: 'rgba(239,68,68,0.08)', color: '#f87171', border: 'rgba(239,68,68,0.25)' },
  closed: { bg: 'rgba(0,0,0,0.2)', color: '#94a3b8', border: 'rgba(255,255,255,0.07)' },
  success: { bg: 'rgba(16,185,129,0.1)', color: '#10b981', border: 'rgba(16,185,129,0.2)' },
  info: { bg: 'rgba(59,130,246,0.1)', color: '#60a5fa', border: 'rgba(59,130,246,0.25)' }
}

const config = computed(() => configs[props.type] || configs.active)
</script>

<template>
  <span class="badge" :style="{
    background: config.bg,
    color: config.color,
    borderColor: config.border
  }">
    <slot />
  </span>
</template>

<style scoped>
.badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1.2px;
  text-transform: uppercase;
  border: 1px solid;
  backdrop-filter: blur(8px);
}
</style>