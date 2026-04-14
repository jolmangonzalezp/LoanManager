<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '../components/ui/PageHeader.vue'
import KPI from '../components/ui/KPI.vue'
import TableWrap from '../components/ui/TableWrap.vue'
import Badge from '../components/ui/Badge.vue'
import Pbar from '../components/ui/Pbar.vue'
import Amount from '../components/ui/Amount.vue'
import Ref from '../components/ui/Ref.vue'
import Btn from '../components/ui/Btn.vue'
import { useApi, formatCurrency, getStatusLabel, getStatusColor } from '../composables/useApi'

const router = useRouter()
const api = useApi()

const loading = ref(true)
const filter = ref('')
const loans = ref([])
const stats = ref({ total: 0, active: 0, overdue: 0, pending: 0 })

const filteredLoans = computed(() => {
  if (!filter.value) return loans.value
  return loans.value.filter(l => l.status === filter.value)
})

function calculateProgress(paid, total) {
  if (!total) return 0
  return Math.round((paid / total) * 100)
}

async function loadLoans() {
  loading.value = true
  try {
    const [loansData, report] = await Promise.all([
      api.get('/loans').catch(() => []),
      api.get('/loans/report').catch(() => ({}))
    ])

    loans.value = (loansData || []).map(l => ({
      id: l.id?.slice(0, 8) || '#' + l.id?.slice(0, 8),
      loanId: l.id,
      client: l.customer_name || l.customerId || 'Cliente',
      type: l.type || 'Personal',
      amount: l.capital?.amount || l.original_capital?.amount || l.capital || 0,
      remaining: l.remaining_debt?.amount || l.remaining_debt || 0,
      paid: l.paid_capital?.amount || 0,
      cuotas: l.term || '—',
      pct: calculateProgress(l.paid_capital?.amount || 0, l.original_capital?.amount || l.capital || 1),
      tasa: l.interest_rate?.annual ? `${l.interest_rate.annual}% E.A.` : '—',
      next: l.next_payment_date || '—',
      status: l.status || 'active'
    }))

    stats.value = {
      total: report.total_loans || loans.value.length,
      active: report.active_loans || loans.value.filter(l => l.status === 'active').length,
      overdue: report.defaulted_loans || loans.value.filter(l => l.status === 'defaulted').length,
      pending: loans.value.filter(l => l.status === 'pending').length
    }
  } catch (e) {
    console.error('Error loading loans:', e)
  } finally {
    loading.value = false
  }
}

onMounted(loadLoans)
</script>

<template>
  <div class="prestamos pu">
    <PageHeader title="Cartera de Préstamos">
      <template #action>
        <Btn @click="router.push('/prestamos/new')">+ Nueva Operación</Btn>
      </template>
    </PageHeader>

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else>
      <div class="kpi-grid">
        <KPI label="Cartera Total" :value="formatCurrency(loans.reduce((sum, l) => sum + l.amount, 0))" :goldValue="true" />
        <KPI label="Activos" :value="stats.active" :goldValue="true" />
        <KPI label="En Mora" :value="stats.overdue" :goldValue="true" />
        <KPI label="Pendientes" :value="stats.pending" :goldValue="true" />
      </div>

      <div class="filters">
        <select v-model="filter" style="width: auto">
          <option value="">Todos</option>
          <option value="active">Activo</option>
          <option value="overdue">En mora</option>
          <option value="pending">Pendiente</option>
          <option value="closed">Cerrado</option>
        </select>
      </div>

      <TableWrap v-if="filteredLoans.length > 0" :headers="['Referencia', 'Cliente', 'Monto', 'Saldo', 'Prog.', 'Tasa', 'Próxima Cuota', 'Estado', '']">
        <tr 
          v-for="loan in filteredLoans" 
          :key="loan.id"
          class="trow"
          @click="router.push('/prestamos/' + loan.loanId)"
        >
          <td><Ref>{{ loan.id }}</Ref></td>
          <td style="color: #fff">{{ loan.client }}</td>
          <td>
            <Amount :color="loan.status === 'overdue' ? '#f87171' : '#10b981'">
              {{ formatCurrency(loan.amount) }}
            </Amount>
          </td>
          <td>{{ formatCurrency(loan.remaining) }}</td>
          <td><Pbar :pct="loan.pct" :color="getStatusColor(loan.status)" /></td>
          <td style="color: #94a3b8">{{ loan.tasa }}</td>
          <td :style="{ color: loan.status === 'overdue' ? '#f87171' : '#fff' }">{{ loan.next }}</td>
          <td><Badge :type="getStatusColor(loan.status)">{{ getStatusLabel(loan.status) }}</Badge></td>
          <td @click.stop>
            <Btn size="sm" variant="ghost">$</Btn>
          </td>
        </tr>
      </TableWrap>

      <div v-else class="empty">No hay préstamos</div>
    </template>
  </div>
</template>

<style scoped>
.prestamos {
  animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both;
}

.loading, .empty {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
}

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
  margin-bottom: 22px;
}

.filters {
  display: flex;
  gap: 10px;
  margin-bottom: 16px;
}

.trow {
  cursor: pointer;
}

.trow:hover > td {
  background: rgba(212,175,55,0.04) !important;
}
</style>