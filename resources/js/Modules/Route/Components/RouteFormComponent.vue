<script setup lang="ts">
import { computed, onMounted } from "vue";
import { useRoute } from '@/Modules/Route';
import { Btn, Select, Input, useModal } from '@/Shared';

interface Props {
  isEditing: boolean;
  id: string;
}

const props = defineProps<Props>();

const { routeForm, zones, emptyRoute, fillRoute, create, update, getZones } = useRoute();
const { close } = useModal()

const zoneOptions = computed(() =>
  zones.value.map(z => ({
    label: z.name,
    value: z.id,
  }))
);

const save = async () => {
  if (!routeForm.value) return
  if (props.isEditing) {
    const response = await update(props.id, routeForm.value)
    if (response) close()
  } else {
    const response = await create(routeForm.value)
    if (response) close()
  }
}

onMounted(async () => {
  await getZones()
  if (!props.isEditing) {
    emptyRoute()
  }
})
</script>

<template>
<div class="route-form">
  <div class="route-form__header">{{ props.isEditing ? "Actualizar Ruta" : "Crear Ruta" }}</div>
  <form v-if="routeForm" @submit.prevent="save" class="route-form__form">
    <Input
      type="text"
      label="Nombre de la ruta:"
      placeholder="Ingrese el nombre"
      v-model="routeForm.name"
      class="route-form__input"
    />
    <Select
      label="Zona:"
      :options="zoneOptions"
      placeholder="Seleccione una zona"
      class="route-form__input"
      v-model="routeForm.zoneId"
    />
    <div class="actions">
      <Btn>{{ props.isEditing ? "Actualizar" : "Crear" }}</Btn>
    </div>
  </form>
</div>
</template>

<style scoped>
.route-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
  width: 100%;
  max-width: 400px;
}

.route-form__header {
  width: 100%;
  text-align: center;
  font-size: 2rem;
  font-weight: 700;
  color: #d4af37;
}

.route-form__form {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 6px;
}

.route-form__input {
  width: 300px;
  max-width: 400px;
}

.actions {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 1rem;
}
</style>
