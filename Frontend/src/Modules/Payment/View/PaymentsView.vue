<script setup>
import { computed, onMounted, ref } from 'vue'
import { PageHeader, KPI, AlertBanner, TableWrap, Ref, Amount, Btn } from '@/Shared'
import { usePaymentApi, PaymentFormModal } from '@/Modules/Payment'
import { useLoanApi } from '@/Modules/Loan'
import { useCustomerApi } from '@/Modules/Customer'
import { useDataLoader, useModalState, formatCurrency, formatDate } from '@/Shared'

const paymentApi = usePaymentApi()
const loanApi = useLoanApi()
const customerApi = useCustomerApi()

const { loading: load1, data: projected, load: loadProjected } = useDataLoader(() => paymentApi.getProjectedVsActual())
const { data: collection } = useDataLoader(() => paymentApi.getCollectionAvailability())
const { loading: load2, data: payments, load: loadPayments } = useDataLoader(() => paymentApi.getAll())
const { data: customers } = useDataLoader(() => customerApi.getAll())
const { data: loans } = useDataLoader(() => loanApi.getAll())

const loading = computed(() => load1.value || load2.value)

const overdue = computed(() => projected.value?.filter(p => p.difference > 0) || [])
const totalOverdue = computed(() => overdue.value.reduce((s, p) => s + p.difference, 0))

const { showPayment, selected, openDetail, closeDetail } = useModalState()

function openPay(loan) {
  selected.value = loan
  showPayment.value = true
}

async function savePayment(data) {
  await paymentApi.create(data)
  closeDetail()
  loadPayments()
  loadProjected()
}

function getCustomerName(loan) {
  if (loan?.customer?.name) {
    return `${loan.customer.name.first_name} ${loan.customer.name.last_name}`
  }
  return '-'
}

onMounted(() => {
  loadProjected()
  loadPayments()
})
</script>

<template>
  <div class="page pu">
    <PageHeader title="Pagos" />

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else>
      <AlertBanner v-if="overdue.length" type="danger">
        <strong>{{ overdue.length }}</strong> cuotas vencidas · <strong>{{ formatCurrency(totalOverdue) }}</strong>
      </AlertBanner>

      <div class="kpi-grid">
        <KPI label="Vencidas" :value="overdue.length" :sub="formatCurrency(totalOverdue)" :goldValue="true" />
        <KPI label="Cartera" :value="collection?.total_loans || 0" sub="préstamos" :goldValue="true" />
        <KPI label="Total Pagos" :value="payments.length" sub="registrados" :goldValue="true" />
      </div>

      <template v-if="overdue.length">
        <h3 class="section-title">Cuotas Vencidas</h3>
        <TableWrap :headers="['Cliente', 'Referencia', 'Proyectado', 'Real', 'Diferencia', '']">
          <tr v-for="p in overdue" :key="p.loan_id">
            <td>{{ p.customer_name }}</td>
            <td><Ref>#{{ p.loan_id?.slice(0, 8) }}</Ref></td>
            <td><Amount>{{ formatCurrency(p.projected_debt) }}</Amount></td>
            <td><Amount>{{ formatCurrency(p.actual_debt) }}</Amount></td>
            <td><Amount color="#ef4444">{{ formatCurrency(p.difference) }}</Amount></td>
            <td><Btn size="sm" @click.stop="openPay({ id: p.loan_id, customer_name: p.customer_name, capital: { amount: p.projected_debt }, remaining_debt: { amount: p.difference }, term: 24 })">cobrar</Btn></td>
          </tr>
        </TableWrap>
      </template>

      <template v-if="payments.length">
        <h3 class="section-title" style="margin-top: 24px">Historial de Pagos</h3>
        <TableWrap :headers="['Fecha', 'Cliente', 'Préstamo', 'Monto', 'Estado']">
          <tr v-for="p in payments" :key="p.id">
            <td>{{ formatDate(p.payment_date) }}</td>
            <td>{{ getCustomerName(p.loan) }}</td>
            <td><Ref>#{{ p.loan_id?.slice(0, 8) }}</Ref></td>
            <td><Amount>{{ formatCurrency(p.amount) }}</Amount></td>
            <td>{{ p.status }}</td>
          </tr>
        </TableWrap>
      </template>
    </template>

    <PaymentFormModal :open="showPayment" :loan="selected" :customers="customers" :loans="loans" @close="closeDetail" @save="savePayment" />
  </div>
</template>

<style scoped>
.page { animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both; }
.loading { text-align: center; padding: 40px; color: #94a3b8; }
.kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 22px; }
.section-title { font-size: 12px; color: #d4af37; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 12px; }
</style>