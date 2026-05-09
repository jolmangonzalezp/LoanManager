<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useUser } from '@/Modules/User'
import { Btn } from '@/Shared'

interface Props {
  userId: string
  currentRoles: string[]
}
const props = defineProps<Props>()

const { allRoles, loadRoles, assignRoles } = useUser()
const selectedRoles = ref<string[]>([])

const toggleRole = (slug: string) => {
  const idx = selectedRoles.value.indexOf(slug)
  if (idx >= 0) {
    selectedRoles.value.splice(idx, 1)
  } else {
    selectedRoles.value.push(slug)
  }
}

const save = async () => {
  await assignRoles(props.userId, selectedRoles.value)
}

onMounted(async () => {
  await loadRoles()
  selectedRoles.value = [...props.currentRoles]
})
</script>

<template>
  <div class="roles-form">
    <div class="roles-form__header">Asignar Roles</div>
    <div v-if="allRoles.length === 0" class="loading">Cargando roles...</div>
    <div v-else class="roles-list">
      <div
        v-for="role in allRoles"
        :key="role.id"
        class="role-item"
        :class="{ selected: selectedRoles.includes(role.slug) }"
        @click="toggleRole(role.slug)"
      >
        <div class="role-check">
          <div v-if="selectedRoles.includes(role.slug)" class="checkmark">✓</div>
        </div>
        <div class="role-info">
          <div class="role-name">{{ role.name }}</div>
          <div class="role-slug">{{ role.slug }}</div>
          <div v-if="role.description" class="role-desc">{{ role.description }}</div>
        </div>
      </div>
    </div>
    <div class="actions">
      <Btn @click="save">Guardar</Btn>
    </div>
  </div>
</template>

<style scoped>
.roles-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
  width: 100%;
  max-width: 400px;
}
.roles-form__header {
  width: 100%;
  text-align: center;
  font-size: 2rem;
  font-weight: 700;
  color: #d4af37;
}
.loading {
  text-align: center;
  color: #94a3b8;
  padding: 20px;
}
.roles-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.role-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border-radius: 6px;
  cursor: pointer;
  border: 1px solid rgba(255,255,255,0.07);
  transition: all 0.15s;
}
.role-item:hover {
  background: rgba(212, 175, 55, 0.08);
}
.role-item.selected {
  background: rgba(212, 175, 55, 0.12);
  border-color: rgba(212, 175, 55, 0.28);
}
.role-check {
  width: 24px;
  height: 24px;
  border-radius: 4px;
  border: 2px solid #94a3b8;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.role-item.selected .role-check {
  border-color: #d4af37;
  background: #d4af37;
}
.checkmark {
  color: #0a1f1a;
  font-weight: 700;
  font-size: 14px;
}
.role-info { flex: 1; }
.role-name { font-size: 14px; font-weight: 600; }
.role-slug { font-size: 11px; color: #94a3b8; }
.role-desc { font-size: 11px; color: #d4af37; margin-top: 2px; }
.actions {
  width: 100%;
  display: flex;
  justify-content: center;
  margin-top: 1rem;
}
</style>
