<script setup>
import { ref, onMounted, computed } from 'vue'
import { PageHeader, KPI, TableWrap, Ref } from '@/Shared'
import { useCustomerApi, CustomerFormModal, CustomerDetailModal } from '@/Modules/Customer'
import { useLoanApi } from '@/Modules/Loan'
import { useDataLoader, useModalState } from '@/Shared'

const customerApi = useCustomerApi()
const loanApi = useLoanApi()

const { loading, data: customers, load: loadCustomers } = useDataLoader(() => customerApi.getAll())
const { data: loansData, load: loadLoans } = useDataLoader(() => loanApi.getAll())

const detailLoading = ref(false)
const detailCustomer = ref(null)
const detailLoans = ref([])

const summary = computed(() => {
  const customerIdsWithLoans = new Set(loansData.value?.map(l => l.customer_id) || [])
  const active = customers.value?.filter(c => customerIdsWithLoans.has(c.id)).length || 0
  return {
    total: customers.value?.length || 0,
    active: active,
    inactive: (customers.value?.length || 0) - active
  }
})

const { showForm, showDetail, editing, selected, openForm, closeForm, openDetail, closeDetail } = useModalState()

function editCustomer() {
  closeDetail()
  openForm(detailCustomer.value)
}

async function openCustomer(c) {
  detailLoading.value = true
  try {
    const [unmaskedCustomer, loans] = await Promise.all([
      customerApi.getById(c.id),
      loanApi.getAll()
    ])
    detailCustomer.value = unmaskedCustomer
    detailLoans.value = loans.filter(l => l.customer_id === c.id)
    openDetail(unmaskedCustomer)
  } catch (e) {
    console.error('Error loading customer detail:', e)
  } finally {
    detailLoading.value = false
  }
}

function openNewLoan() {
  closeDetail()
  window.location.href = '/prestamos?new=true'
}

async function saveCustomer(data) {
  if (editing.value?.id) {
    await customerApi.update(editing.value.id, data)
  } else {
    await customerApi.create(data)
  }
  closeForm()
  await loadCustomers()
}

function maskDni(dni) {
  return dni ? '****' + dni.number?.slice(-4) : ''
}

onMounted(async () => {
  await Promise.all([loadCustomers(), loadLoans()])
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

      <TableWrap :headers="['Primer Nombre', 'Primer Apellido', 'Documento', 'Email', 'Teléfono']">
        <tr v-for="c in customers" :key="c.id" class="trow" @click="openCustomer(c)">
          <td>{{ c.first_name || '' }}</td>
          <td>{{ c.last_name || '' }}</td>
          <td><Ref>{{ maskDni(c.dni) }}</Ref></td>
          <td>{{ c.email || '' }}</td>
          <td>{{ c.phone || '' }}</td>
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
      :customer="detailCustomer"
      :loans="detailLoans"
      :loading="detailLoading"
      @close="closeDetail"
      @edit="editCustomer"
      @new-loan="openNewLoan"
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