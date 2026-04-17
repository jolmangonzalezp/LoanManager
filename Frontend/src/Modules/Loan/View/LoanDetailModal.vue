<script setup>
import { computed } from 'vue'
import Modal from '@/Shared/Components/Modal.vue'
import GCard from '@/Shared/Components/GCard.vue'
import CardTitle from '@/Shared/Components/CardTitle.vue'
import Badge from '@/Shared/Components/Badge.vue'
import Pbar from '@/Shared/Components/Pbar.vue'
import Btn from '@/Shared/Components/Btn.vue'
import { formatCurrency, formatDate, getStatusLabel, getStatusColor } from '@/Shared/Composable/useApi'


const props = defineProps({
  open: Boolean,
  loan: Object
})

const emit = defineEmits(['close', 'edit', 'payment'])

const progress = computed(() => {
  const capital = props.loan?.capital?.amount || 0
  const paid = props.loan?.paid_capital?.amount || 0
  const total = capital || 1
  return Math.round((paid / total) * 100)
})

const paidAmount = computed(() => 
  (props.loan?.paid_capital?.amount || 0) + (props.loan?.paid_interest?.amount || 0)
)
</script>

<template>
  <Modal :open="open" :title="`Préstamo ${loan?.loan_number || ''}`" @close="emit('close')">
    <div class="detail">
      <div class="header-bar">
        <span class="customer-name">{{ loan?.customer_name }}</span>
        <Badge :type="getStatusColor(loan?.status || 'active')">
          {{ loan?.loan_number || getStatusLabel(loan?.status || 'active') }}
        </Badge>
      </div>

      <div class="main-grid">
        <div class="kpi">
          <label>Monto</label>
          <value>{{ formatCurrency(loan?.capital?.amount) }}</value>
        </div>
        <div class="kpi">
          <label>Saldo Pendiente</label>
          <value class="danger">{{ formatCurrency(loan?.remaining_debt?.amount) }}</value>
        </div>
        <div class="kpi">
          <label>Tasa Mensual</label>
          <value class="gold">{{ loan?.interest_rate?.monthly || 0 }}%</value>
        </div>
        <div class="kpi">
          <label>Progreso</label>
          <value>{{ progress }}%</value>
          <Pbar :pct="progress" style="margin-top: 8px" />
        </div>
      </div>

      <div class="info-grid">
        <GCard>
          <CardTitle>Pagado</CardTitle>
          <div class="info-row">
            <span>Capital</span>
            <span>{{ formatCurrency(loan?.paid_capital?.amount) }}</span>
          </div>
          <div class="info-row">
            <span>Interés</span>
            <span>{{ formatCurrency(loan?.paid_interest?.amount) }}</span>
          </div>
          <div class="info-row total">
            <span>Total Pagado</span>
            <span class="success">{{ formatCurrency(paidAmount) }}</span>
          </div>
        </GCard>
        <GCard>
          <CardTitle>Próximo Pago</CardTitle>
          <div class="next-payment">
            {{ formatDate(loan?.next_payment_date) }}
          </div>
        </GCard>
      </div>

      <div class="actions">
        <Btn @click="emit('payment', loan)">$ Registrar Pago</Btn>
        <Btn variant="secondary" @click="emit('edit', loan)">Editar</Btn>
        <Btn variant="ghost" @click="emit('close')">Cerrar</Btn>
      </div>
    </div>
  </Modal>
</template>

<style scoped>
.detail {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.header-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(255,255,255,0.07);
}

.customer-name {
  font-size: 14px;
  color: #94a3b8;
}

.main-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.kpi {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 8px;
  padding: 16px;
  text-align: center;
}

.kpi label {
  display: block;
  font-size: 9px;
  color: #d4af37;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 700;
  margin-bottom: 8px;
}

.kpi value {
  font-family: 'Share Tech Mono', monospace;
  font-size: 18px;
  font-weight: 700;
}

.kpi value.danger {
  color: #ef4444;
}

.kpi value.gold {
  color: #d4af37;
}

.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid rgba(255,255,255,0.07);
  font-size: 12px;
}

.info-row span:first-child {
  color: #94a3b8;
}

.info-row.total {
  border-bottom: none;
  font-weight: 700;
}

.info-row .success {
  color: #10b981;
}

.next-payment {
  text-align: center;
  font-size: 16px;
  font-weight: 600;
  padding: 12px 0;
}

.actions {
  display: flex;
  gap: 8px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid rgba(255,255,255,0.07);
}
</style>