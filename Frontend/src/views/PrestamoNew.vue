<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '../components/ui/PageHeader.vue'
import GCard from '../components/ui/GCard.vue'
import CardTitle from '../components/ui/CardTitle.vue'
import Btn from '../components/ui/Btn.vue'
import { useApi, formatCurrency } from '../composables/useApi'

const router = useRouter()
const api = useApi()

const loading = ref(false)
const saving = ref(false)
const error = ref(null)

const clients = ref([])
const form = ref({
  customer_id: '',
  capital: 10000000,
  interest_rate: 18,
  term: 24,
  start_date: new Date().toISOString().split('T')[0]
})

const calc = (m, t, p) => {
  const tm = Math.pow(1 + t / 100, 1 / 12) - 1
  const c = tm === 0 ? m / p : (m * tm) / (1 - Math.pow(1 + tm, -p))
  return { c, tot: c * p, int: c * p - m }
}

const r = ref(calc(form.value.capital, form.value.interest_rate, form.value.term))

function updateCalc() {
  r.value = calc(form.value.capital, form.value.interest_rate, form.value.term)
}

async function loadClients() {
  loading.value = true
  try {
    const data = await api.get('/customers').catch(() => [])
    clients.value = (data || []).map(c => ({
      id: c.id,
      name: c.name?.first_name ? `${c.name.first_name} ${c.name.last_name}` : 'Cliente'
    }))
  } catch (e) {
    console.error('Error loading clients:', e)
  } finally {
    loading.value = false
  }
}

async function createLoan() {
  if (!form.value.customer_id) {
    error.value = 'Seleccione un cliente'
    return
  }

  saving.value = true
  error.value = null

  try {
    const payload = {
      customer_id: form.value.customer_id,
      capital: parseInt(form.value.capital),
      interest_rate: parseFloat(form.value.interest_rate),
      term: parseInt(form.value.term),
      start_date: form.value.start_date
    }

    await api.post('/loans', payload)
    router.push('/prestamos')
  } catch (e) {
    error.value = e.message
  } finally {
    saving.value = false
  }
}

onMounted(loadClients)
</script>

<template>
  <div class="prestamo-new pu">
    <PageHeader title="Nueva Operación" :show-back="true" @back="router.push('/prestamos')" />

    <div class="grid">
      <GCard>
        <CardTitle>Parámetros del Préstamo</CardTitle>
        <div class="form-grid">
          <div class="field full">
            <label>Cliente</label>
            <select v-model="form.customer_id" :disabled="loading">
              <option value="">Seleccionar...</option>
              <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
          <div class="field">
            <label>Monto ($)</label>
            <input type="number" v-model="form.capital" @input="updateCalc" />
          </div>
          <div class="field">
            <label>Tasa Anual (%)</label>
            <input type="number" v-model="form.interest_rate" @input="updateCalc" />
          </div>
          <div class="field">
            <label>Plazo (meses)</label>
            <input type="number" v-model="form.term" @input="updateCalc" />
          </div>
          <div class="field">
            <label>Fecha Desembolso</label>
            <input type="date" v-model="form.start_date" />
          </div>
        </div>
      </GCard>

      <div class="side">
        <GCard gold-border>
          <div class="calc-title">Cuota Mensual Estimada</div>
          <div class="calc-value">{{ formatCurrency(Math.round(r.c)) }}</div>
          <div class="calc-subtitle">Sistema francés · cuota fija</div>
          <div class="calc-details">
            <div class="calc-row">
              <span>Monto</span>
              <span>{{ formatCurrency(form.capital) }}</span>
            </div>
            <div class="calc-row">
              <span>Intereses</span>
              <span>{{ formatCurrency(Math.round(r.int)) }}</span>
            </div>
            <div class="calc-row">
              <span>Total</span>
              <span>{{ formatCurrency(Math.round(r.tot)) }}</span>
            </div>
          </div>
        </GCard>
        <GCard>
          <CardTitle>Verificación</CardTitle>
          <div class="check-list">
            <div :class="['check-item', form.customer_id ? 'ok' : 'fail']">
              {{ form.customer_id ? '✓' : '✗' }} Cliente seleccionado
            </div>
            <div :class="['check-item', form.capital > 0 ? 'ok' : 'fail']">
              {{ form.capital > 0 ? '✓' : '✗' }} Monto válido
            </div>
            <div :class="['check-item', form.term > 0 ? 'ok' : 'fail']">
              {{ form.term > 0 ? '✓' : '✗' }} Plazo definido
            </div>
          </div>
          <div v-if="error" class="error">{{ error }}</div>
          <div class="actions">
            <Btn @click="createLoan" :disabled="saving || !form.customer_id">
              {{ saving ? 'Creando...' : 'Crear Préstamo' }}
            </Btn>
            <Btn variant="ghost" @click="router.push('/prestamos')">Cancelar</Btn>
          </div>
        </GCard>
      </div>
    </div>
  </div>
</template>

<style scoped>
.prestamo-new {
  animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both;
}

.grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.field.full {
  grid-column: 1 / -1;
}

.field label {
  font-size: 10px;
  font-weight: 700;
  color: #d4af37;
  text-transform: uppercase;
  letter-spacing: 1.5px;
}

.side {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.calc-title {
  font-size: 10px;
  color: #d4af37;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin-bottom: 10px;
  font-weight: 700;
}

.calc-value {
  font-family: 'Share Tech Mono', monospace;
  font-size: 38px;
  font-weight: 700;
  color: #d4af37;
  text-shadow: 0 0 20px rgba(212,175,55,0.15);
}

.calc-subtitle {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 6px;
}

.calc-details {
  margin-top: 18px;
  text-align: left;
}

.calc-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  padding: 9px 0;
  border-bottom: 1px solid rgba(255,255,255,0.07);
}

.calc-row span:first-child {
  color: #94a3b8;
}

.calc-row span:last-child {
  font-family: 'Share Tech Mono', monospace;
  font-weight: 700;
}

.check-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  font-size: 13px;
  border-bottom: 1px solid rgba(255,255,255,0.07);
  margin-bottom: 16px;
}

.check-item {
  display: flex;
  gap: 10px;
}

.check-item.ok {
  color: #10b981;
}

.check-item.fail {
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

.actions {
  display: flex;
  gap: 8px;
}
</style>