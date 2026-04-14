<script setup>
import { onMounted, computed } from 'vue'
import { PageHeader, KPI, AlertBanner, GCard, CardTitle, Btn, Ref, Amount } from '@/Shared'
import { usePaymentApi } from '@/Modules/Payment'
import { useLoanApi } from '@/Modules/Loan'
import { useCustomerApi } from '@/Modules/Customer'
import { useDataLoader, formatCurrency, getStatusLabel, getStatusColor } from '@/Shared'

const paymentApi = usePaymentApi()
const loanApi = useLoanApi()
const customerApi = useCustomerApi()

const { loading: load1, data: projected, load: loadProjected } = useDataLoader(() => paymentApi.getProjectedVsActual())
const { loading: load2, data: collectionData, load: loadCollection } = useDataLoader(() => paymentApi.getCollectionAvailability())
const { loading: load3, data: loanReport, load: loadReport } = useDataLoader(() => loanApi.getReport())
const { loading: load4, data: loans, load: loadLoans } = useDataLoader(() => loanApi.getAll())
const { loading: load5, data: customers, load: loadCustomers } = useDataLoader(() => customerApi.getAll())

const loading = computed(() => load1.value || load2.value || load3.value || load4.value || load5.value)

const overdue = computed(() => projected.value?.filter(p => p.difference > 0) || [])

const stats = computed(() => ({
  capital: collectionData.value?.[0]?.total_capital_available || 0,
  activeLoans: loanReport.value?.active_loans || 0,
  mora: overdue.value.length,
  collection: collectionData.value?.[0]?.total_capital_available || 0
}))

function getCustomerName(id) {
  const c = customers.value.find(c => c.id === id)
  if (!c) return 'Cliente'
  return `${c.name?.first_name || ''} ${c.name?.last_name || ''}`.trim() || 'Cliente'
}

onMounted(() => {
  loadProjected()
  loadCollection()
  loadReport()
  loadLoans()
  loadCustomers()
})
</script>

<template>
  <div class="dashboard pu">
    <PageHeader title="Resumen de Activos">
      <template #action>
        <Btn @click="$router.push('/prestamos')">Nueva Operación</Btn>
      </template>
    </PageHeader>

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else>
      <AlertBanner v-if="overdue.length" type="danger">
        <strong>{{ overdue.length }}</strong> cuotas vencidas
      </AlertBanner>

      <div class="kpi-grid">
        <KPI label="Capital Disponible" :value="formatCurrency(stats.capital)" sub="Para recaudo" :goldValue="true" />
        <KPI label="Préstamos Activos" :value="stats.activeLoans" sub="En cartera" :goldValue="true" />
        <KPI label="En Mora" :value="stats.mora" sub="Préstamos" :goldValue="true" />
      </div>

      <div class="dashboard-grid">
        <GCard v-if="loans.length">
          <CardTitle>Préstamos Recientes</CardTitle>
          <div class="loan-list">
            <div v-for="l in loans.slice(0, 5)" :key="l.id" class="loan-item">
              <div class="loan-info">
                <Ref>#{{ l.id?.slice(0, 8) }}</Ref>
                <span class="loan-customer">{{ getCustomerName(l.customer_id) }}</span>
              </div>
              <div class="loan-amount">
                {{ formatCurrency(l.capital?.amount || l.remaining_debt?.amount || 0) }}
              </div>
              <span class="loan-status" :class="getStatusColor(l.status)">{{ getStatusLabel(l.status) }}</span>
            </div>
          </div>
        </GCard>

        <GCard v-if="overdue.length">
          <CardTitle>Pagos Vencidos</CardTitle>
          <div class="overdue-list">
            <div v-for="p in overdue.slice(0, 4)" :key="p.loan_id" class="overdue-item">
              <div class="overdue-info">
                <span>{{ p.customer_name }}</span>
                <Ref>#{{ p.loan_id?.slice(0, 8) }}</Ref>
              </div>
              <span class="overdue-amount">{{ formatCurrency(p.difference) }}</span>
            </div>
          </div>
        </GCard>
      </div>
    </template>
  </div>
</template>

<style scoped>
.dashboard { animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both; }
.loading { text-align: center; padding: 40px; color: #94a3b8; }
.kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 26px; }
.dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.loan-list, .overdue-list { display: flex; flex-direction: column; gap: 10px; margin-top: 12px; }
.loan-item, .overdue-item { display: flex; align-items: center; gap: 12px; padding: 10px; background: rgba(0,0,0,0.2); border-radius: 6px; }
.loan-info { flex: 1; display: flex; flex-direction: column; gap: 2px; }
.loan-customer, .overdue-info span:first-child { font-size: 12px; color: #94a3b8; }
.loan-amount, .overdue-amount { font-family: 'Share Tech Mono', monospace; font-weight: 700; color: #fff; }
.loan-status { font-size: 10px; padding: 4px 8px; border-radius: 4px; text-transform: uppercase; font-weight: 700; }
.loan-status.active { background: rgba(16,185,129,0.2); color: #10b981; }
.loan-status.paid { background: rgba(212,175,55,0.2); color: #d4af37; }
.loan-status.defaulted { background: rgba(239,68,68,0.2); color: #ef4444; }
.overdue-info { flex: 1; display: flex; flex-direction: column; gap: 2px; }
</style>