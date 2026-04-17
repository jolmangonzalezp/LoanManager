<script setup>
import { computed } from 'vue'
import Modal from '@/Shared/Components/Modal.vue'
import GCard from '@/Shared/Components/GCard.vue'
import CardTitle from '@/Shared/Components/CardTitle.vue'
import Btn from '@/Shared/Components/Btn.vue'
import { formatCurrency, formatDate } from '@/Shared/Composable/useApi'

const props = defineProps({
  open: Boolean,
  payment: Object
})

const emit = defineEmits(['close', 'payment'])

const loanInfo = computed(() => {
  return props.payment?.loan || {}
})
</script>

<template>
  <Modal :open="open" :title="`Pago #${payment?.id?.slice(0, 8) || ''}`" @close="emit('close')">
    <div class="detail">
      <div class="info-grid">
        <GCard>
          <CardTitle>Cliente</CardTitle>
          <div class="info-value">{{ payment?.customer_name || '-' }}</div>
        </GCard>
        <GCard>
          <CardTitle>Préstamo</CardTitle>
          <div class="info-value"><Ref>#{{ payment?.loan_id?.slice(0, 8) }}</Ref></div>
        </GCard>
      </div>

      <div class="main-grid">
        <div class="kpi">
          <label>Monto Pagado</label>
          <value class="gold">{{ formatCurrency(payment?.amount?.amount || payment?.amount) }}</value>
        </div>
        <div class="kpi">
          <label>Fecha de Pago</label>
          <value>{{ formatDate(payment?.payment_date) }}</value>
        </div>
        <div class="kpi">
          <label>Interés Pagado</label>
          <value>{{ formatCurrency(payment?.interest_paid?.amount || payment?.interest_paid) }}</value>
        </div>
        <div class="kpi">
          <label>Capital Pagado</label>
          <value>{{ formatCurrency(payment?.capital_paid?.amount || payment?.capital_paid) }}</value>
        </div>
      </div>

      <div class="actions">
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

.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.info-value {
  font-size: 14px;
  font-weight: 500;
  margin-top: 8px;
  color: #e2e8f0;
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
  font-size: 16px;
  font-weight: 700;
}

.kpi value.gold {
  color: #d4af37;
}

.actions {
  display: flex;
  gap: 8px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid rgba(255,255,255,0.07);
}
</style>
