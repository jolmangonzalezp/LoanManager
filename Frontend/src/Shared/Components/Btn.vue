<script setup>
const props = defineProps({
  variant: { type: String, default: 'primary' },
  size: { type: String, default: 'md' },
  disabled: { type: Boolean, default: false },
  circle: { type: Boolean, default: false },
})

const emit = defineEmits(['click'])

const variants = {
  primary: {
    background: 'linear-gradient(135deg, #d4af37, #b89150)',
    color: '#06090f',
    border: 'none'
  },
  ghost: {
    background: 'transparent',
    color: '#94a3b8',
    border: `1px solid rgba(255,255,255,0.07)`
  },
  danger: {
    background: '#ef4444',
    color: '#fff',
    border: 'none'
  }
}

const sizes = {
  sm: { padding: '6px 12px' },
  md: { padding: '10px 20px' }
}
</script>

<template>
  <button
    class="btn"
    :class="[variant, size, {'circle' : circle}]"
    :disabled="disabled"
    @click="emit('click', $event)"
  >
    <slot />
  </button>
</template>

<style scoped>
.btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  font-weight: 700;
  border-radius: 6px;
  transition: all 0.22s;
  white-space: nowrap;
  line-height: 1;
  letter-spacing: 1px;
  text-transform: uppercase;
  font-size: 11px;
}

.btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.btn.primary {
  background: linear-gradient(135deg, #d4af37, #b89150);
  color: #06090f;
  border: none;
}

.btn.ghost {
  background: transparent;
  color: #94a3b8;
  border: 1px solid rgba(255,255,255,0.07);
}

.btn.ghost:hover {
  background: rgba(212,175,55,0.08);
  color: #e8c84a;
  border-color: rgba(212,175,55,0.28);
}

.btn.danger {
  background: #ef4444;
  color: #fff;
  border: none;
}

.btn.sm {
  padding: 6px 12px;
}

.btn.md {
  padding: 10px 20px;
}

.circle{
  width: 56px;
  height: 56px;
  border-radius: 50%;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
}
</style>