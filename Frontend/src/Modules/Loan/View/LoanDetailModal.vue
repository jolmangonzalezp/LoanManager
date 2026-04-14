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
  if (!props.loan?.term) return 0
  const paid = props.loan?.paid_capital?.amount || 0
  const total = props.loan?.capital?.amount || props.loan?.original_capital?.amount || 1
  return Math.round((paid / total) * 100)
})

const paidAmount = computed(() => 
  (props.loan?.paid_capital?.amount || 0) + (props.loan?.paid_interest?.amount || 0)
)
</script>

<template>
  <Modal :open="open" :title="`#${loan?.id?.slice(0, 8)}`" @close="emit('close')">
    <div class="detail">
      <GCard>
        <div class="loan-header">
          <div>
            <div class="loan-title">Préstamo {{ loan?.type || 'Personal' }}</div>
            <div class="loan-meta">
              Cliente: {{ loan?.customer_name }} · Inicio: {{ formatDate(loan?.start_date) }}
            </div>
          </div>
          <Badge :type="getStatusColor(loan?.status || 'active')">
            {{ getStatusLabel(loan?.status || 'active') }}
          </Badge>
        </div>
      </GCard>

      <div class="kpi-grid">
        <div class="kpi">
          <label>Monto Original</label>
          <value>{{ formatCurrency(loan?.capital?.amount || loan?.original_capital?.amount) }}</value>
        </div>
        <div class="kpi">
          <label>Saldo Pendiente</label>
          <value class="danger">{{ formatCurrency(loan?.remaining_debt?.amount) }}</value>
        </div>
        <div class="kpi">
          <label>Cuota Mensual</label>
          <value class="gold">${{ Math.round((loan?.capital?.amount || 0) / (loan?.term || 1)).toLocaleString('es-CO') }}</value>
        </div>
        <div class="kpi">
          <label>Tasa</label>
          <value>{{ loan?.interest_rate?.annual }}% E.A.</value>
        </div>
        <div class="kpi">
          <label>Plazo</label>
          <value>{{ loan?.term }} meses</value>
        </div>
        <div class="kpi">
          <label>Progreso</label>
          <value>{{ progress }}% <Pbar :pct="progress" style="margin-top: 8px" /></value>
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
            <div class="date">{{ formatDate(loan?.next_payment_date) }}</div>
            <div class="amount">${{ Math.round((loan?.capital?.amount || 0) / (loan?.term || 1)).toLocaleString('es-CO') }}</div>
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

.loan-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
}

.loan-title {
  font-size: 18px;
  font-weight: 300;
}

.loan-meta {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 4px;
}

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.kpi {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 8px;
  padding: 14px;
  text-align: center;
}

.kpi label {
  display: block;
  font-size: 9px;
  color: #d4af37;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 700;
  margin-bottom: 6px;
}

.kpi value {
  font-family: 'Share Tech Mono', monospace;
  font-size: 16px;
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
}

.next-payment .date {
  font-size: 12px;
  color: #94a3b8;
}

.next-payment .amount {
  font-family: 'Share Tech Mono', monospace;
  font-size: 20px;
  font-weight: 700;
  color: #d4af37;
  margin-top: 4px;
}

.actions {
  display: flex;
  gap: 8px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid rgba(255,255,255,0.07);
}
</style>