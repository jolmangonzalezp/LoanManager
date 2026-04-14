<script setup>
import { ref, onMounted } from 'vue'
import PageHeader from '../components/ui/PageHeader.vue'
import KPI from '../components/ui/KPI.vue'
import Badge from '../components/ui/Badge.vue'
import AlertBanner from '../components/ui/AlertBanner.vue'
import TableWrap from '../components/ui/TableWrap.vue'
import Amount from '../components/ui/Amount.vue'
import Ref from '../components/ui/Ref.vue'
import Btn from '../components/ui/Btn.vue'
import Modal from '../components/ui/Modal.vue'
import { useApi, formatCurrency, getStatusLabel, getStatusColor } from '../composables/useApi'

const api = useApi()

const loading = ref(true)
const activeTab = ref('vencidas')

const projected = ref([])
const collection = ref({ totalCapital: 0, activeLoans: 0 })
const stats = ref({ overdue: 0, overdueAmount: 0, upcoming: 0, upcomingAmount: 0, collected: 0, efficiency: 0 })

const showPaymentModal = ref(false)
const paymentForm = ref({ loan_id: '', amount: 0, payment_date: new Date().toISOString().split('T')[0] })
const processingPayment = ref(false)

const tabs = [
  { id: 'vencidas', label: 'Vencidas' },
  { id: 'proximas', label: 'Por Vencer' },
  { id: 'historial', label: 'Historial' }
]

function getDaysDiff(date) {
  if (!date) return 0
  const d = new Date(date)
  const now = new Date()
  return Math.ceil((d - now) / (1000 * 60 * 60 * 24))
}

async function loadData() {
  loading.value = true
  try {
    const [projectedData, collectionData, profitability] = await Promise.all([
      api.get('/reports/projected-vs-actual').catch(() => []),
      api.get('/reports/collection-availability').catch(() => ({})),
      api.get('/reports/client-profitability').catch(() => [])
    ])

    projected.value = projectedData || []

    const overdue = projected.value.filter(p => p.difference > 0)
    stats.value = {
      overdue: overdue.length,
      overdueAmount: overdue.reduce((sum, p) => sum + (p.difference || 0), 0),
      upcoming: 0,
      upcomingAmount: 0,
      collected: 0,
      efficiency: 85
    }

    if (collectionData[0]) {
      collection.value = {
        totalCapital: collectionData[0].total_capital_available || 0,
        activeLoans: collectionData[0].active_loans || 0
      }
    }
  } catch (e) {
    console.error('Error loading payments:', e)
  } finally {
    loading.value = false
  }
}

async function submitPayment() {
  processingPayment.value = true
  try {
    await api.post('/payments', {
      loan_id: paymentForm.value.loan_id,
      amount: parseInt(paymentForm.value.amount),
      payment_date: paymentForm.value.payment_date
    })
    showPaymentModal.value = false
    paymentForm.value = { loan_id: '', amount: 0, payment_date: new Date().toISOString().split('T')[0] }
    loadData()
  } catch (e) {
    console.error('Error processing payment:', e)
  } finally {
    processingPayment.value = false
  }
}

onMounted(loadData)
</script>

<template>
  <div class="pagos pu">
    <PageHeader title="Pagos">
      <template #action>
        <Btn @click="showPaymentModal = true">$ Registrar Pago</Btn>
      </template>
    </PageHeader>

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else>
      <AlertBanner v-if="stats.overdue > 0" type="danger">
        ▲ {{ stats.overdue }} cuotas vencidas · <strong style="margin: 0 4px">{{ formatCurrency(stats.overdueAmount) }}</strong> pendiente
      </AlertBanner>

      <div class="kpi-grid">
        <KPI label="Cuotas Vencidas" :value="stats.overdue" :sub="formatCurrency(stats.overdueAmount)" :trendUp="false" :goldValue="true" />
        <KPI label="Cartera Activa" :value="collection.activeLoans" sub="préstamos" :goldValue="true" />
        <KPI label="Capital Disponible" :value="formatCurrency(stats.collected)" :goldValue="true" />
        <KPI label="Eficiencia" :value="stats.efficiency + '%'" sub="cobro" :trend="stats.efficiency > 80 ? '▲ buena' : '▼ baja'" :trendUp="stats.efficiency > 80" :goldValue="true" />
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

      <TableWrap v-if="activeTab === 'vencidas' && projected.length > 0" :headers="['Cliente', 'Referencia', 'Proyectado', 'Actual', 'Diferencia', 'Estado']">
        <tr v-for="row in projected.filter(p => p.difference > 0)" :key="row.loan_id">
          <td style="color: #fff; font-weight: 500">{{ row.customer_name || 'Cliente' }}</td>
          <td><Ref>{{ row.loan_id?.slice(0, 8) }}</Ref></td>
          <td><Amount>{{ formatCurrency(row.projected_debt) }}</Amount></td>
          <td><Amount>{{ formatCurrency(row.actual_debt) }}</Amount></td>
          <td><Amount color="#f87171">{{ formatCurrency(row.difference) }}</Amount></td>
          <td><Badge type="overdue">En mora</Badge></td>
        </tr>
      </TableWrap>

      <TableWrap v-if="activeTab === 'proximas'" :headers="['Cliente', 'Referencia', 'Monto', 'Fecha', 'Estado']">
        <tr v-for="row in projected.filter(p => !p.difference || p.difference <= 0).slice(0, 5)" :key="row.loan_id">
          <td style="color: #fff; font-weight: 500">{{ row.customer_name || 'Cliente' }}</td>
          <td><Ref>{{ row.loan_id?.slice(0, 8) }}</Ref></td>
          <td><Amount>{{ formatCurrency(row.projected_debt || row.actual_debt) }}</Amount></td>
          <td>{{ row.as_of_date || '—' }}</td>
          <td><Badge type="pending">Pendiente</Badge></td>
        </tr>
      </TableWrap>

      <div v-if="activeTab === 'historial' || (activeTab === 'vencidas' && projected.length === 0)" class="empty">
        No hay pagos registrados
      </div>
    </template>

    <Modal :open="showPaymentModal" title="Registrar Pago" @close="showPaymentModal = false">
      <div class="payment-form">
        <div class="field">
          <label>Préstamo</label>
          <input type="text" v-model="paymentForm.loan_id" placeholder="ID del préstamo" />
        </div>
        <div class="field">
          <label>Monto ($)</label>
          <input type="number" v-model="paymentForm.amount" placeholder="0" />
        </div>
        <div class="field">
          <label>Fecha</label>
          <input type="date" v-model="paymentForm.payment_date" />
        </div>
        <div class="actions">
          <Btn @click="submitPayment" :disabled="processingPayment">
            {{ processingPayment ? 'Procesando...' : 'Confirmar Pago' }}
          </Btn>
          <Btn variant="ghost" @click="showPaymentModal = false">Cancelar</Btn>
        </div>
      </div>
    </Modal>
  </div>
</template>

<style scoped>
.pagos {
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
  margin-bottom: 24px;
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

.payment-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.field label {
  font-size: 10px;
  font-weight: 700;
  color: #d4af37;
  text-transform: uppercase;
  letter-spacing: 1.5px;
}

.actions {
  display: flex;
  gap: 8px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid rgba(255,255,255,0.07);
}
</style>