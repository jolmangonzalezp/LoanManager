<script setup>
import { ref, computed, watch } from 'vue'
import Modal from '@/Shared/Components/Modal.vue'
import Btn from '@/Shared/Components/Btn.vue'
import { formatCurrency } from '@/Shared/Composable/useApi'

function getLocalDate() {
  const now = new Date()
  const colombianDate = new Date(now.getTime() - (5 * 60 * 60 * 1000))
  return colombianDate.toISOString().split('T')[0]
}

const props = defineProps({
  open: Boolean,
  loan: Object,
  customers: Array,
  loans: Array
})

const emit = defineEmits(['close', 'save'])

const loading = ref(false)
const error = ref('')

const form = ref({
  customer_id: '',
  loan_id: '',
  amount: 0,
  payment_date: getLocalDate()
})

const selectedLoan = computed(() => {
  if (!form.value.loan_id) return null
  return props.loans?.find(l => l.id === form.value.loan_id) || props.loan
})

const customerLoans = computed(() => {
  if (!form.value.customer_id) return []
  return props.loans?.filter(l => l.customer_id === form.value.customer_id) || []
})

watch(() => props.loan, (l) => {
  if (l) {
    const capitalInPesos = (l.capital?.amount || l.remaining_debt?.amount || 0) / 100
    form.value = {
      customer_id: l.customer_id || '',
      loan_id: l.id || '',
      amount: Math.round(capitalInPesos / (l.term || 24)),
      payment_date: getLocalDate()
    }
  }
}, { immediate: true })

watch(() => form.value.customer_id, () => {
  form.value.loan_id = ''
  form.value.amount = 0
})

watch(() => form.value.loan_id, () => {
  if (form.value.loan_id && customerLoans.value.length) {
    const loan = customerLoans.value.find(l => l.id === form.value.loan_id)
    if (loan) {
      const capitalAmount = (loan.capital?.amount || loan.remaining_debt?.amount || 0) / 100
      const term = loan.term || 24
      form.value.amount = Math.round(capitalAmount / term)
    }
  }
})

function getCustomerName(id) {
  const c = props.customers?.find(c => c.id === id)
  if (!c) return ''
  return `${c.name?.first_name || ''} ${c.name?.last_name || ''}`.trim()
}

async function save() {
  console.log('Payment save - amount:', form.value.amount, 'type:', typeof form.value.amount)
  if (!form.value.customer_id) {
    error.value = 'Seleccione un cliente'
    return
  }
  if (!form.value.loan_id) {
    error.value = 'Seleccione un préstamo'
    return
  }
  if (!form.value.amount || form.value.amount <= 0) {
    error.value = 'Monto inválido'
    return
  }

  loading.value = true
  error.value = ''
  
  try {
    emit('save', {
      loan_id: form.value.loan_id,
      amount: parseInt(form.value.amount),
      payment_date: form.value.payment_date
    })
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <Modal :open="open" title="Registrar Pago" @close="emit('close')">
    <div class="form">
      <div class="field">
        <label>Cliente</label>
        <select v-model="form.customer_id">
          <option value="">Seleccionar...</option>
          <option v-for="c in customers" :key="c.id" :value="c.id">
            {{ c.name?.first_name }} {{ c.name?.last_name }}
          </option>
        </select>
      </div>

      <div class="field">
        <label>Préstamo</label>
        <select v-model="form.loan_id" :disabled="!form.customer_id">
          <option value="">Seleccionar...</option>
          <option v-for="l in customerLoans" :key="l.id" :value="l.id">
            #{{ l.id?.slice(0, 8) }} - {{ formatCurrency(l.capital?.amount) }}
          </option>
        </select>
      </div>

      <div v-if="error" class="error">{{ error }}</div>

      <div class="field">
        <label>Monto ($)</label>
        <input type="number" v-model="form.amount" min="1" />
      </div>

      <div class="field">
        <label>Fecha de Pago</label>
        <input type="date" v-model="form.payment_date" />
      </div>

      <div class="actions">
        <Btn @click="save" :disabled="loading">
          {{ loading ? 'Procesando...' : 'Confirmar Pago' }}
        </Btn>
        <Btn variant="ghost" @click="emit('close')">Cancelar</Btn>
      </div>
    </div>
  </Modal>
</template>

<style scoped>
.payment-info {
  text-align: center;
  padding: 8px;
}

.info-label {
  font-size: 10px;
  color: #d4af37;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 700;
}

.info-value {
  font-size: 14px;
  font-weight: 300;
  margin-top: 4px;
}

.info-value.danger {
  color: #ef4444;
}

.error {
  background: rgba(239,68,68,0.1);
  border: 1px solid rgba(239,68,68,0.3);
  color: #ef4444;
  padding: 12px;
  border-radius: 6px;
  margin-bottom: 16px;
  font-size: 13px;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 16px;
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