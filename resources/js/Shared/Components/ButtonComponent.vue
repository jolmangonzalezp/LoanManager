<script setup lang="ts">
interface Props {
  variant?: 'primary' | 'secondary' | 'danger'
  size?: 'sm' | 'md' | 'lg'
  rounded?: boolean
  circle?: boolean
  disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  size: 'sm',
  rounded: false,
  circle: false,
  disabled: false,
})
</script>

<template>
  <button
    class="btn"
    :class="[
      `btn-${props.variant}`,
      `btn-${props.size}`,
      {
        'btn-rounded': props.rounded,
        'btn-circle': props.circle,
      },
    ]"
    :disabled="props.disabled"
  >
    <slot />
  </button>
</template>

<style scoped>
.btn {
  border: none;
  cursor: pointer;
  padding: 8px 14px;
  font-weight: 500;
  background: var(--color-gold);
  border-radius: 20px;
}
.btn:disabled {
  background: none;
  color: transparent;
  cursor: default;
}
.btn-rounded {
  border-radius: 20px;
}
.btn-sm {
  font-size: 12px;
  font-weight: 600;
}
.btn-md {
  font-size: 14px;
  font-weight: 600;
}
.btn-lg {
  font-size: 16px;
  font-weight: 600;
}
.btn-primary {
  background: var(--color-gold);
  color: var(--color-bg-emerald);
}
.btn-primary:hover:not(:disabled) {
  background: var(--color-bg-emerald);
  color: var(--color-gold);
  box-shadow: 0 0 15px var(--color-gold);
}
.btn-secondary {
  background: var(--color-bg-emerald);
  color: var(--color-gold);
}
.btn-danger {
  background: var(--danger);
  color: var(--text-primary);
}
.btn-circle {
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