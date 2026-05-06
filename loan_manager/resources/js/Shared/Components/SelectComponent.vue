<script setup lang="ts">
interface Option {
  label: string;
  value: string | number;
}
interface props {
  label: string;
  options: Option[];
  placeholder: string;
  modelValue: string | number;
  disabled: boolean;
}
const props = defineProps<props>();
const emit = defineEmits(['update:modelValue']);

const handleChange = (event: Event) => {
  const target = event.target as HTMLSelectElement;

  emit('update:modelValue', target.value);
};
</script>

<template>
<div class="select">
  <label :for="props.label" class="select-label">
    {{ props.label }}
  </label>
  <select
      :name="props.label"
      :id="props.label"
      class="select-options"
      :value="props.modelValue"
      @change="handleChange"
      :disabled="disabled"
  >
    <option value="" disabled>
      {{ props.placeholder }}
    </option>
    <option v-for="opt in options" :key="opt.value" :value="opt.value">
      {{ opt.label }}
    </option>
  </select>
</div>
</template>

<style scoped>
.select {
  height: 60px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.select-label {
  width: 100%;
  height: 30px;
  color: var(--color-bg-emerald);
  font-weight: 600;
  font-size: 1.25rem;
}

.select-options {
  width: 100%;
  height: 30px;
  padding: 5px 10px;
  font-size: 1.25rem;
  border-radius: 0 20px 20px 20px;
  border: 2px solid var(--color-bg-emerald);
}

</style>