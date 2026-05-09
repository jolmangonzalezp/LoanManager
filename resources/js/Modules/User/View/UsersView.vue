<script setup lang="ts">
import { onMounted } from 'vue'
import { useUser } from '@/Modules/User'
import UserDetailComponent from '@/Modules/User/Components/UserDetailComponent.vue'
import UserFormComponent from '@/Modules/User/Components/UserFormComponent.vue'
import { DataTable, KPI, PageHeader, QuickActions, useModal } from '@/Shared'

const {
  users,
  columns,
  summary,
  getAll,
  getById,
  user,
} = useUser()

const { open } = useModal()

const handleRowClick = async (id: string) => {
  await getById(id)
  open(UserDetailComponent, {
    size: 'lg',
    props: { id },
  })
}

const handleNew = () => {
  open(UserFormComponent, { size: 'lg', props: { isEditing: false, id: '' } })
}

onMounted(async () => {
  await getAll()
})
</script>

<template>
  <QuickActions />
  <div class="page pu">
    <PageHeader title="Usuarios" />

    <div class="kpi-grid">
      <KPI label="Total Usuarios" :value="String(summary.total)" sub="Registrados" :goldValue="true" class="kpi-grid-item" />
      <KPI label="Activos" :value="String(summary.enabled)" sub="Habilitados" :goldValue="true" class="kpi-grid-item" />
      <KPI label="Inactivos" :value="String(summary.disabled)" sub="Deshabilitados" :goldValue="true" class="kpi-grid-item" />
    </div>

    <DataTable :columns="columns" :rows="users" @row-click="handleRowClick">
      <template #cell-enabled="{ value }">
        <span :class="value ? 'badge-active' : 'badge-inactive'">
          {{ value ? 'Activo' : 'Inactivo' }}
        </span>
      </template>
    </DataTable>
  </div>
</template>

<style scoped>
.page { animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both; }
.kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 26px; }
.badge-active { color: #22c55e; font-weight: 600; }
.badge-inactive { color: #ef4444; font-weight: 600; }
@media screen and (max-width: 430px) {
  .kpi-grid { grid-template-columns: 1fr; grid-template-rows: repeat(2, 1fr); }
}
</style>
