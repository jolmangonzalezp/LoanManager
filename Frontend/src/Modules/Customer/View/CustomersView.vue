<script setup>
import { onMounted, computed } from 'vue'
import { PageHeader, KPI, TableWrap, Ref } from '@/Shared'
import { useCustomerApi, CustomerFormModal, CustomerDetailModal } from '@/Modules/Customer'
import { useLoanApi } from '@/Modules/Loan'
import { useDataLoader, useModalState } from '@/Shared'

const customerApi = useCustomerApi()
const loanApi = useLoanApi()

const { loading, data: customers, load: loadCustomers } = useDataLoader(() => customerApi.getAll())
const { data: summaryData, load: loadSummary } = useDataLoader(() => customerApi.getSummary())
const { data: loansData, load: loadLoans } = useDataLoader(() => loanApi.getAll())

const summary = computed(() => {
  return {
    total: summaryData.value?.total_customers || 0,
    active: summaryData.value?.customers_with_loans || 0,
    inactive: summaryData.value?.customers_without_loans || 0
  }
})

const { showForm, showDetail, editing, selected, openForm, closeForm, openDetail, closeDetail } = useModalState()
const customerLoans = computed(() => {
  if (!selected.value) return []
  return loansData.value?.filter(l => l.customer_id === selected.value.id) || []
})

async function openCustomer(c) {
  await loadLoans()
  openDetail(c)
}

async function saveCustomer(data) {
  if (editing.value?.id) {
    await customerApi.update(editing.value.id, data)
  } else {
    await customerApi.create(data)
  }
  closeForm()
  loadCustomers()
}

function maskDni(dni) {
  return dni ? '****' + dni.number?.slice(-4) : ''
}

onMounted(() => {
  loadCustomers()
  loadSummary()
  loadLoans()
})
</script>

<template>
  <div class="page pu">
    <PageHeader title="Clientes" />

    <div v-if="loading" class="loading">Cargando...</div>

    <template v-else>
      <div class="kpi-grid">
        <KPI label="Total Clientes" :value="summary.total" sub="Registrados" :goldValue="true" />
        <KPI label="Con préstamos activos" :value="summary.active" sub="Activos" :goldValue="true" />
        <KPI label="Sin préstamos" :value="summary.inactive" sub="Sin actividad" :goldValue="true" />
      </div>

      <TableWrap :headers="['Primer Nombre', 'Segundo Nombre', 'Documento', 'Email', 'Teléfono']">
        <tr v-for="c in customers" :key="c.id" class="trow" @click="openCustomer(c)">
          <td>{{ c.name?.first_name || '' }}</td>
          <td>{{ c.name?.last_name || '' }}</td>
          <td><Ref>{{ maskDni(c.dni) }}</Ref></td>
          <td>{{ c.email }}</td>
          <td>{{ c.phone }}</td>
        </tr>
      </TableWrap>
    </template>

    <CustomerFormModal 
      :open="showForm" 
      :customer="editing"
      @close="closeForm" 
      @save="saveCustomer"
    />

    <CustomerDetailModal 
      :open="showDetail" 
      :customer="selected"
      :loans="customerLoans"
      @close="closeDetail"
      @edit="openForm(selected)"
    />
  </div>
</template>

<style scoped>
.page { animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both; }
.loading { text-align: center; padding: 40px; color: #94a3b8; }
.kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 26px; }
.trow { cursor: pointer; }
.trow:hover > td { background: rgba(212,175,55,0.04) !important; }
</style>