<script setup lang="ts">
import { defineEmits, defineProps } from 'vue'
import {faTimes} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

interface Props {
  size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl' | 'full'
}
const props = defineProps<Props>()
const emit = defineEmits(['close'])
const handleClose = () => {
  emit('close')
}
</script>

<template>
  <div class="modal" @click.self="handleClose">
    <div
      class="modal__inner"
      :class="`modal__${props.size || 'md'}`"
    >
      <div class="modal__close" @click="handleClose">
        <FontAwesomeIcon :icon="faTimes" />
      </div>
      <slot></slot>
    </div>
  </div>
</template>

<style scoped>
.modal {
  position: fixed;
  inset: 0;
  z-index: 500;
  backdrop-filter: blur(8px);
  background: rgba(0,0,0,0.75);
  display: flex;
  justify-content: center;
  align-items: center;
}
.modal__inner {
  background: rgba(8,12,16,0.95);
  backdrop-filter: blur(24px);
  border: 1px solid rgba(212,175,55,0.28);
  border-radius: 14px;
  padding: 28px;
  max-width: 94vw;
  max-height: 92vh;
  overflow-y: auto;
  box-shadow: 0 24px 60px rgba(0,0,0,0.7), 0 0 0 1px rgba(212,175,55,0.15);
  transition: all 250ms ease-in-out;
}

.modal__xs {
  max-width: 280px;
}
.modal__sm {
  max-width: 380px;
}
.modal__md {
  max-width: 520px;
}
.modal__lg {
  max-width: 720px;
}
.modal__xl {
  max-width: 960px;
}
.modal__full {
  width: 95vw;
  height: 95vh;
  border-radius: 6px;
}
.modal__close {
  position: absolute;
  height: 52px;
  width: 52px;
  display: flex;
  justify-content: center;
  align-items: center;
  top: 0;
  right: 0;
  font-size: 25px;
  color: var(--color-bg-emerald);
  cursor: pointer;
}
.modal__title {
  margin-top: 1rem;
}
.fade-enter-active,
.fade-leave-active {
  transition: all 250ms ease-in-out;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}
.fade-leave-active {
  transition-delay: 250ms;
}

@media screen and (max-width: 768px) {
  .modal__inner {
    width: 80%;
    max-height: 90vh;
    padding: 1rem;
  }
}
@media screen and (max-width: 480px) {
  .modal__inner {
    width: 95%;
    max-height: 90vh;
    padding: 1rem;
  }
}
</style>