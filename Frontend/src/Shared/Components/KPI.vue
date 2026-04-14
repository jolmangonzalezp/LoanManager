<script setup>
import { ref } from 'vue'

const props = defineProps({
  label: String,
  value: String,
  sub: String,
  trend: String,
  trendUp: Boolean,
  goldValue: Boolean,
  onClick: Function
})

const isHovered = ref(false)
</script>

<template>
  <div 
    class="kpi" 
    :class="{ hover: isHovered && onClick }"
    @click="onClick"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
  >
    <div class="label">{{ label }}</div>
    <div class="value" :class="{ gold: goldValue || trendUp === undefined || (trendUp && !trend) }">
      {{ value }}
    </div>
    <div v-if="sub" class="sub">{{ sub }}</div>
    <div v-if="trend" class="trend" :class="{ up: trendUp === true, down: trendUp === false }">
      {{ trend }}
    </div>
  </div>
</template>

<style scoped>
.kpi {
  background: rgba(255,255,255,0.03);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 10px;
  padding: 22px 24px;
  cursor: default;
  transition: all 0.22s;
}

.kpi.hover {
  background: rgba(255,255,255,0.055);
  border-color: rgba(212,175,55,0.28);
  transform: translateY(-2px);
}

.label {
  font-size: 12px;
  color: #94a3b8;
  margin-bottom: 12px;
  font-weight: 400;
}

.value {
  font-family: 'Share Tech Mono', monospace;
  font-size: 36px;
  line-height: 1;
  font-weight: 700;
  color: #fff;
}

.value.gold {
  color: #d4af37;
}

.sub {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 10px;
}

.trend {
  font-size: 12px;
  margin-top: 10px;
  font-weight: 500;
  color: #94a3b8;
}

.trend.up {
  color: #10b981;
}

.trend.down {
  color: #f87171;
}
</style>