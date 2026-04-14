<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '../components/ui/PageHeader.vue'
import KPI from '../components/ui/KPI.vue'
import TableWrap from '../components/ui/TableWrap.vue'
import Ref from '../components/ui/Ref.vue'
import Btn from '../components/ui/Btn.vue'
import { useApi, formatCurrency } from '../composables/useApi'

const router = useRouter()
const api = useApi()

const loading = ref(true)
const clients = ref([])
const stats = ref({ total: 0, active: 0, inactive: 0 })

function maskName(name) {
  if (!name) return ''
  const parts = name.split(' ')
  return parts.map(p => p.slice(0, 4) + '*'.repeat(Math.max(4, p.length - 4))).join(' ')
}

function maskDni(dni) {
  if (!dni) return ''
  return '****' + dni.slice(-4)
}

async function loadClients() {
  loading.value = true
  try {
    const [customers, summary] = await Promise.all([
      api.get('/customers').catch(() => []),
      api.get('/customers/summary').catch(() => ({}))
    ])

    clients.value = (customers || []).map(c => ({
      id: c.id,
      name: c.name?.first_name ? `${c.name.first_name} ${c.name.last_name}` : 'Cliente',
      dni: c.dni ? maskDni(c.dni.number) : '*****',
      email: c.email || '',
      phone: c.phone || '',
      address: c.address?.street || '',
      loans: 0,
      status: 'active'
    }))

    stats.value = {
      total: summary.total_customers || clients.value.length,
      active: summary.customers_with_loans || 0,
      inactive: summary.customers_without_loans || 0
    }
  } catch (e) {
    console.error('Error loading clients:', e)
  } finally {
    loading.value = false
  }
}

onMounted(loadClients)
</script>

<template>
  <div class="clientes pu">
    <PageHeader title="Clientes" />

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else>
      <div class="kpi-grid">
        <KPI label="Total Clientes" :value="stats.total" sub="Registrados" :goldValue="true" />
        <KPI label="Con préstamos activos" :value="stats.active" sub="Activos" :goldValue="true" />
        <KPI label="Sin préstamos" :value="stats.inactive" sub="Sin actividad" :goldValue="true" />
      </div>

      <TableWrap v-if="clients.length > 0" :headers="['Nombre Completo', 'Cedula', 'Email', 'Telefono', 'Direccion']">
        <tr 
          v-for="client in clients" 
          :key="client.id"
          class="trow"
          @click="router.push('/clientes/' + client.id)"
        >
          <td style="color: #fff; text-align: center">{{ maskName(client.name) }}</td>
          <td><Ref>{{ client.dni }}</Ref></td>
          <td style="color: #94a3b8">{{ client.email }}</td>
          <td style="color: #94a3b8">{{ client.phone }}</td>
          <td style="color: #94a3b8">{{ client.address }}</td>
        </tr>
      </TableWrap>

      <div v-else class="empty">
        No hay clientes registrados
        <Btn @click="router.push('/clientes/new')">Crear Cliente</Btn>
      </div>
    </template>

    <Btn class="fab" @click="router.push('/clientes/new')">+</Btn>
  </div>
</template>

<style scoped>
.clientes {
  animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
}

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
  margin-bottom: 26px;
}

.trow {
  cursor: pointer;
}

.trow:hover > td {
  background: rgba(212,175,55,0.04) !important;
}

.empty {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
}

.fab {
  position: fixed;
  bottom: 32px;
  right: 32px;
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: linear-gradient(135deg, #d4af37, #b89150);
  border: none;
  cursor: pointer;
  font-size: 24px;
  color: #06090f;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 18px rgba(212,175,55,0.15);
  font-weight: 700;
  line-height: 1;
}

.fab:hover {
  transform: scale(1.08);
  box-shadow: 0 8px 28px rgba(212,175,55,0.25), 0 0 0 4px rgba(212,175,55,0.15);
}
</style>