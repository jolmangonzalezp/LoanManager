<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import PageHeader from '../components/ui/PageHeader.vue'
import Badge from '../components/ui/Badge.vue'
import GCard from '../components/ui/GCard.vue'
import CardTitle from '../components/ui/CardTitle.vue'
import TableWrap from '../components/ui/TableWrap.vue'
import Pbar from '../components/ui/Pbar.vue'
import Amount from '../components/ui/Amount.vue'
import Btn from '../components/ui/Btn.vue'
import { useApi, formatCurrency, formatDate, getStatusLabel, getStatusColor } from '../composables/useApi'

const router = useRouter()
const route = useRoute()
const api = useApi()

const loading = ref(true)
const error = ref(null)
const activeTab = ref('plan')

const loan = ref({
  id: '',
  loanId: '',
  client: '',
  type: 'Personal',
  capital: 0,
  remaining: 0,
  paidCapital: 0,
  paidInterest: 0,
  monthlyPayment: 0,
  rate: 0,
  term: 0,
  progress: 0,
  status: 'active',
  startDate: '',
  nextPaymentDate: ''
})

const schedule = ref([])
const payments = ref([])

const tabs = [
  { id: 'plan', label: 'Plan' },
  { id: 'hist', label: 'Historial' },
  { id: 'docs', label: 'Documentos' }
]

function calculateProgress() {
  if (!loan.value.term) return 0
  return Math.round((loan.value.progress / loan.value.term) * 100)
}

async function loadLoan() {
  loading.value = true
  error.value = null
  
  try {
    const data = await api.get(`/loans/${route.params.id}`)
    
    if (data) {
      loan.value = {
        id: '#' + data.id?.slice(0, 8),
        loanId: data.id,
        client: data.customer_name || 'Cliente',
        type: 'Personal',
        capital: data.capital?.amount || data.original_capital?.amount || 0,
        remaining: data.remaining_debt?.amount || 0,
        paidCapital: data.paid_capital?.amount || 0,
        paidInterest: data.paid_interest?.amount || 0,
        monthlyPayment: data.term ? Math.round((data.capital?.amount || 0) / data.term) : 0,
        rate: data.interest_rate?.annual || 0,
        term: data.term || 0,
        progress: Math.round((data.paid_capital?.amount || 0) / (data.original_capital?.amount || 1) * (data.term || 1)),
        status: data.status || 'active',
        startDate: data.start_date,
        nextPaymentDate: data.next_payment_date
      }
    }
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}

onMounted(loadLoan)
</script>

<template>
  <div class="prestamo-detail pu">
    <PageHeader :title="loan.id" :show-back="true" @back="router.push('/prestamos')">
      <template #action>
        <div style="display: flex; gap: 8px">
          <Btn variant="ghost" size="sm">Editar</Btn>
          <Btn>$ Registrar Pago</Btn>
        </div>
      </template>
    </PageHeader>

    <div v-if="loading" class="loading">Cargando...</div>
    <div v-else-if="error" class="error">{{ error }}</div>

    <template v-else>
      <GCard style="margin-bottom: 20px; display: flex; align-items: flex-start; gap: 16px">
        <div style="flex: 1">
          <div style="font-size: 18px; font-weight: 300">Préstamo {{ loan.type }} · {{ loan.client }}</div>
          <div style="font-size: 12px; color: #94a3b8; margin-top: 4px">
            {{ loan.id }} · {{ formatDate(loan.startDate) }}
          </div>
        </div>
        <Badge :type="getStatusColor(loan.status)">{{ getStatusLabel(loan.status) }}</Badge>
      </GCard>

      <div class="kpi-grid">
        <GCard style="padding: 16px 20px; text-align: center">
          <div style="font-size: 10px; color: #d4af37; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px; font-weight: 700">Monto Original</div>
          <div style="font-family: 'Share Tech Mono', monospace; font-size: 18px; font-weight: 700">{{ formatCurrency(loan.capital) }}</div>
        </GCard>
        <GCard style="padding: 16px 20px; text-align: center">
          <div style="font-size: 10px; color: #d4af37; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px; font-weight: 700">Saldo Pendiente</div>
          <div style="font-family: 'Share Tech Mono', monospace; font-size: 18px; font-weight: 700; color: #f87171">{{ formatCurrency(loan.remaining) }}</div>
        </GCard>
        <GCard style="padding: 16px 20px; text-align: center">
          <div style="font-size: 10px; color: #d4af37; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px; font-weight: 700">Cuota Mensual</div>
          <div style="font-family: 'Share Tech Mono', monospace; font-size: 18px; font-weight: 700; color: #d4af37">{{ formatCurrency(loan.monthlyPayment) }}</div>
        </GCard>
        <GCard style="padding: 16px 20px; text-align: center">
          <div style="font-size: 10px; color: #d4af37; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px; font-weight: 700">Tasa</div>
          <div style="font-family: 'Share Tech Mono', monospace; font-size: 18px; font-weight: 700">{{ loan.rate }}% E.A.</div>
        </GCard>
        <GCard style="padding: 16px 20px; text-align: center">
          <div style="font-size: 10px; color: #d4af37; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px; font-weight: 700">Plazo Total</div>
          <div style="font-family: 'Share Tech Mono', monospace; font-size: 18px; font-weight: 700">{{ loan.term }} meses</div>
        </GCard>
        <GCard style="padding: 16px 20px; text-align: center">
          <div style="font-size: 10px; color: #d4af37; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px; font-weight: 700">Progreso</div>
          <div style="font-family: 'Share Tech Mono', monospace; font-size: 18px; font-weight: 700">{{ loan.progress }}/{{ loan.term }}</div>
          <Pbar :pct="calculateProgress()" color="#d4af37" style="margin-top: 10px" />
        </GCard>
      </div>

      <div class="tabs">
        <div 
          v-for="tab in tabs" 
          :key="tab.id"
          class="tab"
          :class="{ active: activeTab === tab.id }"
          @click="activeTab = tab.id"
        >
          {{ tab.label }}
        </div>
      </div>

      <div v-if="activeTab === 'plan' || activeTab === 'hist'" class="empty-state">
        <div v-if="schedule.length === 0 && payments.length === 0">
          No hay datos de {{ activeTab === 'plan' ? 'cronograma' : 'pagos' }} disponibles
        </div>
      </div>

      <GCard v-if="activeTab === 'docs'">
        <CardTitle>Documentos</CardTitle>
        <div class="empty-state">No hay documentos adjuntos</div>
        <Btn variant="ghost" size="sm">+ Adjuntar</Btn>
      </GCard>
    </template>
  </div>
</template>

<style scoped>
.prestamo-detail {
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

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-bottom: 22px;
}

.tabs {
  display: flex;
  border-bottom: 1px solid rgba(255,255,255,0.07);
  margin-bottom: 22px;
}

.tab {
  padding: 10px 18px;
  font-size: 12px;
  cursor: pointer;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  font-weight: 600;
  color: #94a3b8;
  border-bottom: 2px solid transparent;
  margin-bottom: -1px;
  transition: all 0.15s;
}

.tab.active {
  color: #d4af37;
  border-bottom-color: #d4af37;
}

.empty-state {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
  font-size: 13px;
}
</style>