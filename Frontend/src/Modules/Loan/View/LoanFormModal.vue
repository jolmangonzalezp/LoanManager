<script setup>
import { ref, watch } from 'vue'
import Modal from '@/Shared/Components/Modal.vue'
import Btn from '@/Shared/Components/Btn.vue'

function getLocalDate() {
  const now = new Date()
  const colombianDate = new Date(now.getTime() - (5 * 60 * 60 * 1000))
  return colombianDate.toISOString().split('T')[0]
}

const props = defineProps({
  open: Boolean,
  loan: Object,
  customers: Array
})

const emit = defineEmits(['close', 'save'])

const loading = ref(false)
const error = ref('')

const form = ref({
  customer_id: '',
  capital: 0,
  interest_rate: 0,
  start_date: getLocalDate()
})

watch(() => props.loan, (l) => {
  if (l) {
    form.value = {
      customer_id: l.customer_id,
      capital: l.capital?.amount || 0,
      interest_rate: l.interest_rate?.monthly || l.interest_rate?.annual || 0,
      start_date: l.start_date || getLocalDate()
    }
  } else {
    reset()
  }
}, { immediate: true })

function reset() {
  form.value = {
    customer_id: '',
    capital: 0,
    interest_rate: 0,
    start_date: getLocalDate()
  }
}

async function save() {
  if (!form.value.customer_id) {
    error.value = 'Seleccione un cliente'
    return
  }
  if (!form.value.capital || form.value.capital <= 0) {
    error.value = 'Monto inválido'
    return
  }

  loading.value = true
  error.value = ''
  
  try {
    const data = {
      customer_id: form.value.customer_id,
      capital: parseInt(form.value.capital),
      interest_rate: parseFloat(form.value.interest_rate),
      start_date: getLocalDate()
    }
    console.log('Loan form data:', data)
    console.log('interest_rate type:', typeof data.interest_rate, 'value:', data.interest_rate)
    emit('save', data)
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <Modal :open="open" :title="loan ? 'Editar Préstamo' : 'Nuevo Préstamo'" @close="emit('close')">
    <div class="form">
      <div v-if="error" class="error">{{ error }}</div>

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
        <label>Monto ($)</label>
        <input type="number" v-model.number="form.capital" min="1" placeholder="0" />
      </div>

      <div class="field">
        <label>Tasa Mensual (%)</label>
        <input type="number" v-model.number="form.interest_rate" min="0" step="0.01" placeholder="0" />
      </div>

      <div class="field">
        <label>Fecha Desembolso</label>
        <input type="date" v-model="form.start_date" />
      </div>

      <div class="actions">
        <Btn @click="save" :disabled="loading">
          {{ loading ? (loan ? 'Guardando...' : 'Creando...') : (loan ? 'Guardar' : 'Crear Préstamo') }}
        </Btn>
        <Btn variant="ghost" @click="emit('close')">Cancelar</Btn>
      </div>
    </div>
  </Modal>
</template>

<style scoped>
.error {
  background: rgba(239,68,68,0.1);
  border: 1px solid rgba(239,68,68,0.3);
  color: #ef4444;
  padding: 12px;
  border-radius: 6px;
  font-size: 13px;
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