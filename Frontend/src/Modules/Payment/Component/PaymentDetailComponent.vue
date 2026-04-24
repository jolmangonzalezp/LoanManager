<script setup lang="ts">
import {usePayment} from "@Modules/Payment/index.ts";
import {Btn, CardTitle, GCard} from "@Shared";
import Badge from "../../../Shared/Components/Badge.vue";
import { formatCurrency, formatDate, getStatusLabel, getStatusColor } from '@/Shared/Composable/useApi'

const {payment} = usePayment();

</script>

<template>
    <div class="payment-detail" v-if="payment">
      <section class="payment-details__header">
        <span class="loan-number">{{ payment?.loan.loanNumber }}</span>
        <span class="loan-remain">{{ formatCurrency(payment?.loan.remainingDebt) }}</span>
      </section>
      <div class="info-grid">
      <GCard>
        <CardTitle>Cliente</CardTitle>
        <div class="info-value">{{ '-' }}</div>
      </GCard>
      <GCard>
        <CardTitle>Préstamo</CardTitle>
          <div class="info-value">{{ payment?.loan.loanNumber }}</div>
        </GCard>
      </div>
<!--
      <div class="main-grid">
        <div class="kpi">
          <label>Monto Pagado</label>
          <div class="gold">{{ formatCurrency(payment?.amount?.amount || payment?.amount) }}</div>
        </div>
        <div class="kpi">
          <label>Fecha de Pago</label>
          <div>{{ formatDate(payment?.payment_date) }}</div>
        </div>
        <div class="kpi">
          <label>Interés Pagado</label>
          <div>{{ formatCurrency(payment?.interest_paid?.amount || payment?.interest_paid) }}</div>
        </div>
        <div class="kpi">
          <label>Capital Pagado</label>
          <div>{{ formatCurrency(payment?.capital_paid?.amount || payment?.capital_paid) }}</div>
        </div>
      </div>
-->
      <div class="actions">
        <Btn variant="ghost" @click="emit('close')">Cerrar</Btn>
      </div>
    </div>
</template>

<style scoped>
.payment-detail {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.payment-details__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(255,255,255,0.07);
  margin-top: 0.5rem;
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
