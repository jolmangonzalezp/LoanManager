<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import PageHeader from '../components/ui/PageHeader.vue'
import Ava from '../components/ui/Ava.vue'
import Badge from '../components/ui/Badge.vue'
import GCard from '../components/ui/GCard.vue'
import CardTitle from '../components/ui/CardTitle.vue'
import TableWrap from '../components/ui/TableWrap.vue'
import Amount from '../components/ui/Amount.vue'
import Ref from '../components/ui/Ref.vue'
import Btn from '../components/ui/Btn.vue'
import { useApi, formatCurrency, getStatusLabel, getStatusColor } from '../composables/useApi'

const router = useRouter()
const route = useRoute()
const api = useApi()

const loading = ref(true)
const error = ref(null)

const client = ref({
  id: '',
  initials: '??',
  name: 'Cliente',
  dni: '',
  email: '',
  phone: '',
  address: '',
  status: 'active',
  totalDebt: 0,
  monthlyPayment: 0,
  totalPaid: 0
})

const loans = ref([])

async function loadClient() {
  loading.value = true
  error.value = null
  
  try {
    const customer = await api.get(`/customers/${route.params.id}`)
    
    if (customer) {
      const name = customer.name || {}
      client.value = {
        id: customer.id,
        initials: name.first_name ? name.first_name[0] + (name.last_name?.[0] || '') : 'C?',
        name: name.first_name ? `${name.first_name} ${name.last_name || ''}`.trim() : 'Cliente',
        dni: customer.dni ? `****${customer.dni.number?.slice(-4)}` : '',
        email: customer.email || '',
        phone: customer.phone || '',
        address: customer.address?.street || '',
        status: customer.status || 'active',
        totalDebt: 0,
        monthlyPayment: 0,
        totalPaid: 0
      }
    }
  } catch (e) {
    error.value = e.message
    console.error('Error loading client:', e)
  } finally {
    loading.value = false
  }
}

onMounted(loadClient)
</script>

<template>
  <div class="cliente-detail pu">
    <PageHeader :title="client.name" :show-back="true" @back="router.push('/clientes')">
      <template #action>
        <Btn @click="router.push('/prestamos/new')">+ Préstamo</Btn>
      </template>
    </PageHeader>

    <div v-if="loading" class="loading">Cargando...</div>
    <div v-else-if="error" class="error">{{ error }}</div>

    <template v-else>
      <div class="client-header">
        <Ava :initials="client.initials" :size="52" />
        <div class="client-info">
          <div class="client-name">{{ client.name }}</div>
          <div class="client-meta">{{ client.dni }} · {{ client.address }}</div>
        </div>
        <Badge :type="getStatusColor(client.status)">{{ getStatusLabel(client.status) }}</Badge>
      </div>

      <div class="grid-2">
        <GCard>
          <CardTitle>Información Personal</CardTitle>
          <div class="info-row">
            <label>Email</label>
            <div>{{ client.email || '—' }}</div>
          </div>
          <div class="info-row">
            <label>Teléfono</label>
            <div>{{ client.phone || '—' }}</div>
          </div>
          <div class="info-row">
            <label>Dirección</label>
            <div>{{ client.address || '—' }}</div>
          </div>
        </GCard>
        <GCard>
          <CardTitle>Resumen Financiero</CardTitle>
          <div class="info-row">
            <span>Deuda total activa</span>
            <Amount color="#ef4444">{{ formatCurrency(client.totalDebt) }}</Amount>
          </div>
          <div class="info-row">
            <span>Cuota mensual</span>
            <Amount>{{ formatCurrency(client.monthlyPayment) }}</Amount>
          </div>
          <div class="info-row">
            <span>Total pagado</span>
            <Amount color="#10b981">{{ formatCurrency(client.totalPaid) }}</Amount>
          </div>
        </GCard>
      </div>

      <TableWrap v-if="loans.length > 0" :headers="['Referencia', 'Tipo', 'Monto', 'Cuotas', 'Próxima Cuota', 'Estado']">
        <tr v-for="loan in loans" :key="loan.id" class="trow" @click="router.push('/prestamos/' + loan.id)">
          <td><Ref>{{ loan.id }}</Ref></td>
          <td style="color: #94a3b8">{{ loan.type || 'Personal' }}</td>
          <td><Amount>{{ formatCurrency(loan.capital) }}</Amount></td>
          <td style="color: #94a3b8">{{ loan.cuotas || '—' }}</td>
          <td>{{ loan.nextPaymentDate || '—' }}</td>
          <td><Badge :type="getStatusColor(loan.status)">{{ getStatusLabel(loan.status) }}</Badge></td>
        </tr>
      </TableWrap>

      <div v-else class="empty">
        Este cliente no tiene préstamos
        <Btn @click="router.push('/prestamos/new')">Crear Préstamo</Btn>
      </div>
    </template>
  </div>
</template>

<style scoped>
.cliente-detail {
  animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both;
}

.loading, .error {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
}

.error {
  color: #ef4444;
}

.client-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
  padding: 18px 22px;
  background: rgba(255,255,255,0.03);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(212,175,55,0.28);
  border-radius: 10px;
}

.client-info {
  flex: 1;
}

.client-name {
  font-size: 18px;
  font-weight: 300;
}

.client-meta {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 4px;
}

.grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 22px;
}

.info-row {
  padding: 9px 0;
  border-bottom: 1px solid rgba(255,255,255,0.07);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.info-row label {
  font-size: 10px;
  color: #d4af37;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 700;
}

.info-row span {
  font-size: 12.5px;
  color: #94a3b8;
}

.info-row:last-child {
  border-bottom: none;
}

.empty {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
  display: flex;
  flex-direction: column;
  gap: 12px;
  align-items: center;
}

.trow {
  cursor: pointer;
}
</style>