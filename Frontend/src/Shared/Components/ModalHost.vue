<script setup lang="ts">
import { useModal } from '@/Shared/Composable/useModal';
import ModalComponent from '@/Shared/Components/ModalComponent.vue';

const { isOpen, currentComponent, currentProps, size, close } = useModal()
function onClose() {
  close()
}
</script>

<template>
  <Teleport to="body">
    <Transition name="fade">
      <ModalComponent
        v-if="isOpen"
        :size="size"
        @close="onClose"
      >
          <component :is="currentComponent" v-bind="currentProps"/>
      </ModalComponent>
    </Transition>
  </Teleport>
</template>

<style scoped></style>