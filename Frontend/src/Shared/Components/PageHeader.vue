<script setup>
defineProps({
  title: String,
  showBack: Boolean
})

const emit = defineEmits(['back'])
</script>

<template>
  <div class="page-header">
    <div class="left">
      <button v-if="showBack" class="back-btn" @click="emit('back')">←</button>
      <h1 class="title">{{ title }}</h1>
    </div>
    <div class="right">
      <slot name="action" />
    </div>
  </div>
</template>

<style scoped>
.page-header {
  margin-bottom: 28px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.back-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  color: #94a3b8;
  font-size: 18px;
  padding: 0 4px 0 0;
  line-height: 1;
}

.title {
  font-size: 22px;
  font-weight: 300;
  color: #fff;
}

.right {
  display: flex;
  gap: 8px;
}
</style>