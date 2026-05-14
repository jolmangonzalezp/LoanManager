<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useRoute } from '@/Modules/Route';
import { UserService } from '@/Modules/User';
import { Btn, useModal } from '@/Shared';

interface Props {
  routeId: string;
  assignedUserIds?: string[];
}

const props = defineProps<Props>();

const { assignUsers } = useRoute();
const { close } = useModal()

const allUsers = ref<{ id: string; name: string }[]>([])
const selectedIds = ref<Set<string>>(new Set(props.assignedUserIds ?? []))

const save = async () => {
  await assignUsers(props.routeId, Array.from(selectedIds.value))
  close()
}

onMounted(async () => {
  try {
    const users = await UserService.getAll()
    allUsers.value = users.map(u => ({
      id: u.id,
      name: u.name
        ? [u.name.firstName, u.name.middleName, u.name.lastName, u.name.secondLastName].filter(Boolean).join(' ')
        : u.username,
    }))
  } catch {
    allUsers.value = []
  }
})
</script>

<template>
<div class="user-assign">
  <div class="user-assign__header">Asignar Usuarios a Ruta</div>
  <div class="user-assign__list">
    <label
      v-for="u in allUsers"
      :key="u.id"
      class="user-assign__item"
    >
      <input
        type="checkbox"
        :checked="selectedIds.has(u.id)"
        @change="(e: Event) => {
          const cb = e.target as HTMLInputElement
          if (cb.checked) selectedIds.add(u.id)
          else selectedIds.delete(u.id)
        }"
      />
      <span>{{ u.name }}</span>
    </label>
    <div v-if="!allUsers.length" class="user-assign__empty">
      No hay usuarios disponibles
    </div>
  </div>
  <div class="user-assign__actions">
    <Btn variant="secondary" @click="close">Cancelar</Btn>
    <Btn @click="save">Guardar</Btn>
  </div>
</div>
</template>

<style scoped>
.user-assign {
  display: flex;
  flex-direction: column;
  gap: 14px;
  width: 100%;
  max-width: 350px;
}

.user-assign__header {
  width: 100%;
  text-align: center;
  font-size: 1.5rem;
  font-weight: 700;
  color: #d4af37;
}

.user-assign__list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  max-height: 300px;
  overflow-y: auto;
  padding: 8px 0;
}

.user-assign__item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.15s;
  color: #e0e0e0;
  font-size: 14px;
}

.user-assign__item:hover {
  background: rgba(212, 175, 55, 0.08);
}

.user-assign__item input[type="checkbox"] {
  accent-color: #d4af37;
  width: 16px;
  height: 16px;
}

.user-assign__empty {
  text-align: center;
  color: #94a3b8;
  padding: 20px;
  font-size: 13px;
}

.user-assign__actions {
  display: flex;
  justify-content: center;
  gap: 12px;
  margin-top: 8px;
}
</style>
