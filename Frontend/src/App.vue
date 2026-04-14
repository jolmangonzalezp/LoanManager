<script setup>
import { computed, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import Sidebar from '@/Shared/Components/Sidebar.vue'
import { useGlobalModals } from '@/Shared/Composable/useGlobalModals'
import { CustomerFormModal } from '@/Modules/Customer'
import { LoanFormModal } from '@/Modules/Loan'
import { PaymentFormModal } from '@/Modules/Payment'
import { useCustomerApi } from '@/Modules/Customer'
import { useLoanApi } from '@/Modules/Loan'
import { useAlert } from '@/Shared/Composable/useAlert'

const router = useRouter()
const route = useRoute()

const customerApi = useCustomerApi()
const loanApi = useLoanApi()
const alert = useAlert()

const { showNewLoan, showNewCustomer, showNewPayment, openNewLoan, openNewCustomer, openNewPayment, closeAll } = useGlobalModals()
const fabOpen = ref(false)

const customers = ref([])
const loans = ref([])

async function loadCustomers() {
  try {
    customers.value = await customerApi.getAll() || []
  } catch (e) {
    customers.value = []
  }
}

async function loadLoans() {
  try {
    loans.value = await loanApi.getAll() || []
  } catch (e) {
    loans.value = []
  }
}

const showSidebar = computed(() => route.path !== '/login')

const currentRoute = computed(() => {
  const path = route.path
  if (path === '/') return 'dashboard'
  if (path.startsWith('/clientes')) return 'clientes'
  if (path.startsWith('/prestamos')) return 'prestamos'
  if (path.startsWith('/pagos')) return 'pagos'
  if (path.startsWith('/reportes')) return 'reportes'
  return 'dashboard'
})

const navigate = (to) => router.push('/' + to)

function handleNewLoan() {
  loadCustomers()
  loadLoans()
  openNewLoan()
}

function handleNewCustomer() {
  loadCustomers()
  openNewCustomer()
}

function handleNewPayment() {
  loadCustomers()
  loadLoans()
  loadCustomers()
  openNewPayment()
}

async function saveCustomer(data) {
  alert.showLoading('Guardando cliente...')
  try {
    await customerApi.create(data)
    alert.close()
    alert.showSuccess('Cliente creado exitosamente')
    closeAll()
  } catch (e) {
    alert.close()
    alert.showError(e.message || 'Error al crear cliente')
  }
}

async function saveLoan(data) {
  console.log('Sending loan data:', data)
  alert.showLoading('Guardando préstamo...')
  try {
    const result = await loanApi.create(data)
    console.log('Loan created:', result)
    alert.close()
    alert.showSuccess('Préstamo creado exitosamente')
    closeAll()
  } catch (e) {
    console.error('Loan error:', e)
    alert.close()
    alert.showError(e.message || 'Error al crear préstamo')
  }
}

async function savePayment(data) {
  alert.showLoading('Procesando pago...')
  try {
    await fetch('http://localhost:8000/api/payments', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + localStorage.getItem('token') },
      body: JSON.stringify(data)
    })
    alert.close()
    alert.showSuccess('Pago registrado exitosamente')
    closeAll()
  } catch (e) {
    alert.close()
    alert.showError(e.message || 'Error al registrar pago')
  }
}
</script>

<template>
  <div class="app">
    <Sidebar v-if="showSidebar" 
      :current-route="currentRoute" 
      @navigate="navigate"
      @newLoan="handleNewLoan"
      @newCustomer="handleNewCustomer"
      @newPayment="handleNewPayment"
    />
    <main class="main" :class="{ 'no-sidebar': !showSidebar }">
      <router-view />
    </main>

    <div v-if="showSidebar" class="fab" @click="fabOpen = !fabOpen">
      +
      <div v-if="fabOpen" class="fab-menu">
        <div class="fab-item" @click="handleNewLoan">Nuevo Préstamo</div>
        <div class="fab-item" @click="handleNewCustomer">Nuevo Cliente</div>
        <div class="fab-item" @click="handleNewPayment">Nuevo Pago</div>
      </div>
    </div>

    <CustomerFormModal :open="showNewCustomer" @close="closeAll" @save="saveCustomer" />
    <LoanFormModal :open="showNewLoan" :customers="customers" @close="closeAll" @save="saveLoan" />
    <PaymentFormModal :open="showNewPayment" :customers="customers" :loans="loans" @close="closeAll" @save="savePayment" />
  </div>
</template>

<style scoped>
.app { display: flex; min-height: 100vh; }
.main { margin-left: 220px; flex: 1; padding: 32px 30px 80px; min-height: 100vh; }
.main.no-sidebar { margin-left: 0; padding: 0; }

.fab {
  position: fixed;
  bottom: 32px;
  right: 32px;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: linear-gradient(135deg, #d4af37, #b89150);
  border: none;
  color: #06090f;
  font-size: 28px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 18px rgba(212,175,55,0.25);
  z-index: 100;
}

.fab:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 24px rgba(212,175,55,0.35);
}

.fab-menu {
  position: absolute;
  bottom: 100%;
  right: 0;
  margin-bottom: 12px;
  background: rgba(8,12,16,0.95);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 10px;
  padding: 8px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 160px;
}

.fab-item {
  padding: 12px 16px;
  border-radius: 8px;
  cursor: pointer;
  color: #94a3b8;
  font-size: 13px;
  transition: all 0.15s;
}

.fab-item:hover {
  background: rgba(212,175,55,0.1);
  color: #d4af37;
}
</style>