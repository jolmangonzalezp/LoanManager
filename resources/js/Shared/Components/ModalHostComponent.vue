<script setup lang="ts">
import { useModal, Modal } from '@/Shared';


const { isOpen, currentComponent, currentProps, size, close } = useModal()
function onClose() {
  close()
}
</script>

<template>
  <Teleport to="body">
    <Transition name="fade">
      <Modal
        v-if="isOpen"
        :size="size"
        @close="onClose"
      >
          <component :is="currentComponent" v-bind="currentProps"/>
      </Modal>
    </Transition>
  </Teleport>
</template>

<style scoped></style>
