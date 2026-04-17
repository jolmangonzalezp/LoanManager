<script setup>
import { computed, onMounted } from 'vue'
import { PageHeader, KPI, TableWrap, Ref, Amount, Btn } from '@/Shared'
import { usePaymentApi, PaymentFormModal, PaymentDetailModal } from '@/Modules/Payment'
import { useLoanApi } from '@/Modules/Loan'
import { useDataLoader, useModalState, formatCurrency, formatDate } from '@/Shared'

const paymentApi = usePaymentApi()
const loanApi = useLoanApi()

const { loading, data: payments, load: loadPayments } = useDataLoader(() => paymentApi.getAll())
const { data: monthlyReport, load: loadMonthlyReport } = useDataLoader(() => paymentApi.getMonthlyReport())
const { data: loans } = useDataLoader(() => loanApi.getAll())

const { showPayment, showDetail, selected, openDetail, closeDetail } = useModalState()

function openPay(paymentData) {
  const loan = loans.value?.find(l => l.id === paymentData.loan_id) || {}
  selected.value = { ...loan, ...paymentData }
  showDetail.value = true
}

async function savePayment(data) {
  await paymentApi.create(data)
  closeDetail()
  loadPayments()
  loadMonthlyReport()
}

function getCustomerName(payment) {
  if (payment.customer_name) {
    return payment.customer_name
  }
  return '-'
}

onMounted(() => {
  loadPayments()
  loadMonthlyReport()
})
</script>

<template>
  <div class="page pu">
    <PageHeader :title="`Pagos - ${monthlyReport?.month} ${monthlyReport?.year}`" />

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else>
      <div class="kpi-grid">
        <KPI label="Capital Retornado" :value="formatCurrency(monthlyReport?.capital_returned)" :goldValue="true" />
        <KPI label="Intereses Recaudados" :value="formatCurrency(monthlyReport?.interest_collected)" :goldValue="true" />
        <KPI label="Pagos Realizados" :value="monthlyReport?.payments_count || 0" sub="este mes" :goldValue="true" />
      </div>

      <TableWrap v-if="payments.length" :headers="['Fecha', 'Cliente', 'Préstamo', 'Monto', 'Estado']">
        <tr v-for="p in payments" :key="p.id" @click="openPay(p)" class="trow">
          <td>{{ formatDate(p.payment_date) }}</td>
          <td>{{ getCustomerName(p) }}</td>
          <td><Ref>#{{ p.loan_id?.slice(0, 8) }}</Ref></td>
          <td><Amount>{{ formatCurrency(p.amount?.amount || p.amount) }}</Amount></td>
          <td>{{ p.status }}</td>
        </tr>
      </TableWrap>

      <div v-else class="empty">No hay pagos registrados</div>
    </template>

    <PaymentFormModal :open="showPayment" :loan="selected" @close="showPayment = false" @save="savePayment" />
    <PaymentDetailModal :open="showDetail" :payment="selected" @close="closeDetail" />
  </div>
</template>

<style scoped>
.page { animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both; }
.loading { text-align: center; padding: 40px; color: #94a3b8; }
.empty { text-align: center; padding: 40px; color: #64748b; }
.kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 22px; }
.trow { cursor: pointer; }
.trow:hover > td { background: rgba(212,175,55,0.04) !important; }
</style>