 <script setup>
import { ref, watch } from 'vue'
import Btn from '@/Shared/Components/Btn.vue'
import { formatCurrency } from '@/Shared/Composable/useApi'

function getLocalDate() {
  const now = new Date()
  const colombianDate = new Date(now.getTime() - (5 * 60 * 60 * 1000))
  return colombianDate.toISOString().split('T')[0]
}

const props = defineProps({
  open: Boolean,
  loan: Object
})

const emit = defineEmits(['close', 'save'])

const loading = ref(false)
const error = ref('')

const form = ref({
  amount: 0,
  payment_date: getLocalDate()
})

watch(() => props.loan, (l) => {
  if (l) {
    const capitalAmount = l.capital?.amount || l.remaining_debt?.amount || 0
    form.value = {
      amount: Math.round(capitalAmount * 0.1),
      payment_date: getLocalDate()
    }
  } else {
    reset()
  }
}, { immediate: true })

function reset() {
  form.value = {
    amount: 0,
    payment_date: getLocalDate()
  }
}

async function save() {
  if (!form.value.amount || form.value.amount <= 0) {
    error.value = 'Monto inválido'
    return
  }

  loading.value = true
  error.value = ''

  try {
    emit('save', {
      loan_id: props.loan.id,
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
  <div class="form">
    <div v-if="error" class="error">{{ error }}</div>

    <div class="loan-info">
      <div class="info-row">
        <span>Cliente</span>
        <span>{{ loan?.customer_name }}</span>
      </div>
      <div class="info-row">
        <span>Préstamo</span>
        <span>{{ loan?.loan_number || loan?.id?.slice(0, 8) }}</span>
      </div>
      <div class="info-row">
        <span>Saldo</span>
        <span>{{ formatCurrency(loan?.remaining_debt?.amount) }}</span>
      </div>
    </div>

    <div class="field">
      <label>Monto ($)</label>
      <input type="number" v-model.number="form.amount" min="1" placeholder="0" />
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
</template>

<style scoped>
.loan-info {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 8px;
  padding: 12px;
  margin-bottom: 16px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 4px 0;
  font-size: 12px;
}

.info-row span:first-child {
  color: #94a3b8;
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