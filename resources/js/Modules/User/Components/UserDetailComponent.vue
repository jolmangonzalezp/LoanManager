<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useAuth } from '@/Modules/Auth'
import { useUser } from '@/Modules/User'
import { Btn, GCard, CardTitle, useModal, useMask } from '@/Shared'
import UserRolesFormComponent from './UserRolesFormComponent.vue'
import UserFormComponent from './UserFormComponent.vue'

const { can } = useAuth()
const { user, userPermissions, userRoles, getPermissions, getRoles } = useUser()
const { open, close } = useModal()
const { maskStart, maskEmail } = useMask()

interface Props {
  id: string
}
const props = defineProps<Props>()

const initials = computed(() => {
  if (!user.value) return '??'
  const name = user.value.name || user.value.username
  return name.substring(0, 2).toUpperCase() || '??'
})

const updateUser = () => {
  if (!user.value) return
  close()
  open(UserFormComponent, {
    size: 'lg',
    props: { id: user.value.id, isEditing: true },
  })
}

const assignRoles = () => {
  if (!user.value) return
  close()
  open(UserRolesFormComponent, {
    size: 'lg',
    props: { userId: user.value.id, currentRoles: userRoles.value },
  })
}

onMounted(async () => {
  await getPermissions(props.id)
  await getRoles(props.id)
})
</script>

<template>
  <div v-if="user" class="user-details">
    <section class="user-details__header">
      <div class="user-details__avatar">{{ initials }}</div>
      <div class="info">
        <div class="name">{{ user.name || user.username }}</div>
        <div class="meta">@{{ user.username }} · {{ user.enabled ? 'Activo' : 'Inactivo' }}</div>
      </div>
    </section>
    <section class="user-details__content">
      <GCard class="user-details__card">
        <CardTitle>Información</CardTitle>
        <div class="info-row">
          <label>Email</label>
          <div>{{ maskEmail(user.email || '—') }}</div>
        </div>
        <div class="info-row">
          <label>Teléfono</label>
          <div>{{ maskStart(user.phone || '—') }}</div>
        </div>
        <div class="info-row">
          <label>Creado</label>
          <div>{{ user.createdAt || '—' }}</div>
        </div>
      </GCard>
      <GCard class="user-details__card">
        <CardTitle>Permisos</CardTitle>
        <div v-if="userPermissions.length === 0" class="no-permissions">Sin permisos asignados</div>
        <div v-else class="permission-list">
          <span v-for="p in userPermissions" :key="p" class="permission-tag">{{ p }}</span>
        </div>
      </GCard>
    </section>
    <div class="btns">
      <Btn v-if="can('users.update')" variant="secondary" @click="updateUser">Actualizar</Btn>
      <Btn v-if="can('roles.update')" @click="assignRoles">Asignar Roles</Btn>
    </div>
  </div>
</template>

<style scoped>
.user-details {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.user-details__header {
  display: flex;
  align-items: center;
  gap: 16px;
}
.user-details__avatar {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: #d4af37;
  color: #0a1f1a;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 18px;
}
.info { flex: 1; }
.name { font-size: 18px; font-weight: 300; }
.meta { font-size: 12px; color: #94a3b8; margin-top: 4px; }
.user-details__card { margin-top: 1rem; }
.info-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid rgba(255,255,255,0.07);
}
.info-row label {
  font-size: 10px;
  color: #d4af37;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 700;
}
.permission-list {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  padding: 8px 0;
}
.permission-tag {
  background: rgba(212, 175, 55, 0.12);
  border: 1px solid rgba(212, 175, 55, 0.28);
  border-radius: 4px;
  padding: 2px 8px;
  font-size: 11px;
  color: #d4af37;
}
.no-permissions {
  padding: 8px 0;
  color: #94a3b8;
  font-size: 13px;
}
.btns {
  width: 100%;
  display: flex;
  justify-content: space-between;
}
</style>
