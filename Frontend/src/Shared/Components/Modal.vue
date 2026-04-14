<script setup>
import { watch } from 'vue'

const props = defineProps({
  open: { type: Boolean, default: false },
  title: String
})

const emit = defineEmits(['close'])

watch(() => props.open, (val) => {
  document.body.style.overflow = val ? 'hidden' : ''
})
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="open" class="modal-overlay" @click.self="emit('close')">
        <div class="modal-content pu">
          <div class="modal-header">
            <span class="modal-title">{{ title }}</span>
            <button class="modal-close" @click="emit('close')">✕</button>
          </div>
          <slot />
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.75);
  z-index: 500;
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-content {
  background: rgba(8,12,16,0.95);
  backdrop-filter: blur(24px);
  border: 1px solid rgba(212,175,55,0.28);
  border-radius: 14px;
  padding: 28px;
  width: 460px;
  max-width: 94vw;
  max-height: 92vh;
  overflow-y: auto;
  box-shadow: 0 24px 60px rgba(0,0,0,0.7), 0 0 0 1px rgba(212,175,55,0.15);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
  padding-bottom: 14px;
  border-bottom: 1px solid rgba(255,255,255,0.07);
}

.modal-title {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2.5px;
  color: #d4af37;
}

.modal-close {
  width: 28px;
  height: 28px;
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 6px;
  background: transparent;
  cursor: pointer;
  font-size: 13px;
  color: #94a3b8;
}

.modal-close:hover {
  background: rgba(255,255,255,0.05);
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.18s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>