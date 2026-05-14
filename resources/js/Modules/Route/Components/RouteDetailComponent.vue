<script setup lang="ts">
import { computed } from "vue";
import { useAuth } from '@/Modules/Auth';
import { RouteForms, RouteAssignUsers, useRoute } from '@/Modules/Route';
import { Btn, GCard, CardTitle, useModal } from '@/Shared';

const { can } = useAuth()

const { route, users, routeUsers, fillRoute, removeUser } = useRoute();
const { open, close } = useModal()

const updateRoute = () => {
  if (!route.value) return
  fillRoute()
  close()
  open(RouteForms, {
    size: "lg",
    props: {
      id: route.value.id,
      isEditing: true,
    }
  });
}

const openUserAssign = () => {
  if (!route.value) return
  close()
  setTimeout(() => {
    open(RouteAssignUsers, {
      size: "sm",
      props: {
        routeId: route.value!.id,
        assignedUserIds: routeUsers.value.map(u => u.id),
      }
    });
  }, 100)
}

const initials = computed(() => {
  if (!route.value) return "??"
  return route.value.name.slice(0, 2).toUpperCase() || '??'
})
</script>

<template>
  <div v-if="route" class="route-detail">
    <section class="route-detail__header">
      <div class="route-detail__icon">{{ initials }}</div>
      <div class="info">
        <div class="name">{{ route.name }}</div>
        <div class="meta">
          Ruta · {{ route.zoneName || 'Sin zona' }}
        </div>
      </div>
    </section>
    <section class="route-detail__content">
      <GCard class="route-detail__card">
        <CardTitle>Información de la Ruta</CardTitle>
        <div class="info-row">
          <label>Nombre</label>
          <div>{{ route.name }}</div>
        </div>
        <div class="info-row">
          <label>Zona</label>
          <div>{{ route.zoneName || '—' }}</div>
        </div>
        <div class="info-row">
          <label>Usuarios Asignados</label>
          <div>{{ route.userCount }}</div>
        </div>
      </GCard>

      <GCard class="route-detail__card" v-if="routeUsers.length">
        <CardTitle>Usuarios</CardTitle>
        <div class="user-list">
          <div class="user-item" v-for="u in routeUsers" :key="u.id">
            <span class="user-name">{{ u.name }}</span>
            <span v-if="can('routes.update')" class="user-remove" @click="removeUser(route.id, u.id)">
              &times;
            </span>
          </div>
        </div>
      </GCard>
    </section>
    <div class="btns">
      <Btn v-if="can('routes.update')" variant="secondary" @click="updateRoute">Actualizar</Btn>
      <Btn v-if="can('routes.update')" @click="openUserAssign">Asignar Usuarios</Btn>
    </div>
  </div>
</template>

<style scoped>
.route-detail {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.route-detail__header {
  display: flex;
  align-items: center;
  gap: 16px;
}

.route-detail__icon {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: rgba(212, 175, 55, 0.15);
  border: 2px solid #d4af37;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 18px;
  color: #d4af37;
  flex-shrink: 0;
}

.info {
  flex: 1;
}

.name {
  font-size: 18px;
  font-weight: 300;
}

.meta {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 4px;
}

.route-detail__card {
  margin-top: 1rem;
}

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

.btns {
  width: 100%;
  display: flex;
  justify-content: space-between;
}

.user-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  max-height: 10rem;
  overflow-y: auto;
}

.user-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  border-radius: 6px;
  background: rgba(255,255,255,0.03);
  transition: background 0.15s;
}

.user-item:hover {
  background: rgba(212,175,55,0.08);
}

.user-name {
  font-size: 14px;
}

.user-remove {
  color: #ef4444;
  cursor: pointer;
  font-size: 18px;
  font-weight: 700;
  padding: 0 4px;
}

.user-remove:hover {
  color: #ff6b6b;
}
</style>
