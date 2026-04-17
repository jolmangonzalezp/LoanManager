<script setup>
import { onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { PageHeader, KPI, TableWrap, Ref, Amount, Btn } from '@/Shared'
import { useLoanApi, LoanFormModal, LoanDetailModal } from '@/Modules/Loan'
import { usePaymentApi, PaymentFormModal } from '@/Modules/Payment'
import { useCustomerApi } from '@/Modules/Customer'
import { useDataLoader, useModalState, formatCurrency, getStatusLabel } from '@/Shared'

const route = useRoute()
const loanApi = useLoanApi()
const paymentApi = usePaymentApi()
const customerApi = useCustomerApi()

const { loading, data: loans, load: loadLoans } = useDataLoader(() => loanApi.getAll())
const { load: loadReport } = useDataLoader(() => loanApi.getReport())
const { data: customers, load: loadCustomers } = useDataLoader(() => customerApi.getAll())
const { data: reportData, load: loadReportData } = useDataLoader(() => loanApi.getReport())

const report = computed(() => reportData.value || {})

const preselectedCustomer = computed(() => {
  const cid = route.query.customer_id
  return customers.value?.find(c => c.id === cid)
})

const { showForm, showDetail, showPayment, editing, selected, openForm, closeForm, openDetail, closeDetail } = useModalState()

function editLoan() {
  const loanToEdit = selected.value
  closeDetail()
  setTimeout(() => openForm(loanToEdit), 100)
}

function viewPayment(loan) {
  selected.value = loan
  showPayment.value = true
}

async function saveLoan(data) {
  if (editing.value?.id) {
    await loanApi.update(editing.value.id, data)
  } else {
    await loanApi.create(data)
  }
  closeForm()
  loadLoans()
}

async function savePayment(data) {
  await paymentApi.create(data)
  showPayment.value = false
  loadLoans()
}

function openNewLoan() {
  if (preselectedCustomer.value) {
    openForm({ customer_id: preselectedCustomer.value.id })
  } else {
    openForm()
  }
}

onMounted(() => {
  loadLoans()
  loadReportData()
  loadCustomers().then(() => {
    if (route.query.new === 'true') {
      openNewLoan()
    }
  })
})
</script>

<template>
  <div class="page pu">
    <PageHeader title="Cartera de Préstamos" />

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else>
      <div class="kpi-grid">
        <KPI label="Cartera Total" :value="formatCurrency(loans.reduce((s,l) => s + (l.capital?.amount || 0), 0))" :goldValue="true" />
        <KPI label="Activos" :value="report.active_loans || 0" :goldValue="true" />
        <KPI label="En Mora" :value="report.defaulted_loans || 0" :goldValue="true" />
      </div>

      <TableWrap v-if="loans.length" :headers="['No. Préstamo', 'Cliente', 'Monto', 'Estado', '']">
        <tr v-for="l in loans" :key="l.id" class="trow" @click="openDetail(l)">
          <td><Ref>{{ l.loan_number || l.id?.slice(0, 8) }}</Ref></td>
          <td>{{ l.customer_name }}</td>
          <td><Amount>{{ formatCurrency(l.capital?.amount) }}</Amount></td>
          <td>{{ getStatusLabel(l.status) }}</td>
          <td @click.stop><Btn size="sm" variant="ghost" @click="viewPayment(l)">$</Btn></td>
        </tr>
      </TableWrap>
    </template>

    <LoanFormModal :open="showForm" :customers="customers" :loan="editing" @close="closeForm" @save="saveLoan" />
    <LoanDetailModal :open="showDetail" :loan="selected" @close="closeDetail" @edit="editLoan" @payment="viewPayment" />
    <PaymentFormModal :open="showPayment" :loan="selected" @close="showPayment = false" @save="savePayment" />
  </div>
</template>

<style scoped>
.page { animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both; }
.loading { text-align: center; padding: 40px; color: #94a3b8; }
.kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 22px; }
.trow { cursor: pointer; }
.trow:hover > td { background: rgba(212,175,55,0.04) !important; }
</style>