<script setup lang="ts">
interface InputProps {
  type?: string
  label?: string
  placeholder?: string
  disabled?: boolean
  modelValue?: string | number
  onlyNumbers?: boolean
}
const props = withDefaults(defineProps<InputProps>(), {
  type: 'text',
  placeholder: 'Ingrese el texto',
  disabled: false,
  onlyNumbers: false,
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | number): void
}>()

const handleInput = (e: Event) => {
  const target = e.target as HTMLInputElement
  let value = target.value

  if (props.onlyNumbers) {
    value = value.replace(/\D/g, '')
    target.value = value
  }

  emit('update:modelValue', value)
}
</script>

<template>
  <div class="input">
    <label class="input_label"> {{ props.label }}</label>
    <input
      class="input_input"
      :value="props.modelValue"
      :type="props.type"
      :placeholder="props.placeholder"
      :disabled="props.disabled"
      @input="handleInput"
    />
  </div>
</template>

<style scoped>
.input {
  height: 60px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}
.input_label {
  width: 100%;
  height: 30px;
  color: var(--color-bg-emerald);
  font-weight: 600;
  font-size: 1.25rem;
}
.input_input {
  width: 100%;
  height: 30px;
  padding: 5px 10px;
  font-size: 1.25rem;
  border-radius: 0 20px 20px 20px;
  border: 2px solid var(--color-bg-emerald);
}
</style>