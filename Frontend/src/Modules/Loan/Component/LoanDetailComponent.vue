<script setup lang="ts">
import {Btn, CardTitle, GCard, useModal} from "@Shared";
import {useLoan} from "@Modules/Loan";
import { formatCurrency, formatDate, getStatusLabel, getStatusColor } from '@/Shared/Composable/useApi'
import Badge from "../../../Shared/Components/Badge.vue";
import Pbar from "../../../Shared/Components/Pbar.vue";
import LoanFormComponent from "@Modules/Loan/Component/LoanFormComponent.vue";
import PaymentFormComponent from "@Modules/Payment/Component/PaymentFormComponent.vue";

const {loan, fillForm} = useLoan();
const {open, close} = useModal();

const updateLoan = () => {
  fillForm();
  close();
  open(
      LoanFormComponent,
      {
        size: "lg",
        props: {
          id: loan.value.id,
          isEditing: true,
        }
      }
  );
}

const makePayment = () => {
  close();
  open(
      PaymentFormComponent, {
        size: "lg",
        props: {
          loanId: loan.value.id,
          loanNumber: loan.value.loanNumber,
          remainDebt: loan.value.remainingDebt,
          interestRate: loan.value.interestRate,
        }
      }
  )
}

</script>

<template>
  <div v-if="loan" class="loan-details">
    <section class="loan-details__header">
      <span class="loan-name">{{ loan?.partialName}}</span>
      <Badge :type="getStatusColor(loan?.status || 'active')">
        {{ loan?.loanNumber || getStatusLabel(loan?.status || 'active') }}
      </Badge>
    </section>
    <section class="main-grid">
      <GCard class="grid-card">
        <CardTitle class="grid-title">Monto</CardTitle>
        <div class="grid-value">{{ formatCurrency(loan?.capital) }}</div>
      </GCard>
      <GCard class="grid-card">
        <CardTitle class="grid-title">Saldo</CardTitle>
        <div class="grid-value">{{ formatCurrency(loan?.remainingDebt) }}</div>
      </GCard>
      <GCard class="grid-card">
        <CardTitle class="grid-title">Tasa de interes</CardTitle>
        <div class="grid-value">{{ loan?.interestRate }}%</div>
      </GCard>
      <GCard class="grid-card">
        <CardTitle class="grid-title">Progreso</CardTitle>
        <div class="grid-value">{{ loan?.progress }}%</div>
        <Pbar :pct="loan.progress" style="margin-top: 8px" />
      </GCard>
      <GCard>
        <CardTitle>Pagado</CardTitle>
        <div class="info-row">
          <span>Capital</span>
          <span>{{ formatCurrency(loan) }}</span>
        </div>
        <div class="info-row">
          <span>Interés</span>
          <span>{{ formatCurrency(loan) }}</span>
        </div>
        <div class="info-row total">
          <span>Total Pagado</span>
          <span class="success">{{ formatCurrency(12) }}</span>
        </div>
      </GCard>
      <GCard class="grid-card">
        <CardTitle>Próximo Pago</CardTitle>
        <div class="grid-value">
          {{ formatDate(loan?.nextPaymentDate) }}
        </div>
      </GCard>
    </section>
    <div class="btns">
      <Btn variant="secondary" @click="updateLoan">Actualizar</Btn>
      <Btn @click="makePayment">Hacer Pago</Btn>
    </div>
  </div>
</template>

<style scoped>
.loan-details {
  display: flex;
  flex-direction: column;
  gap: 16px;
  width: 400px;
  margin-top: 1rem;
}

.loan-details__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(255,255,255,0.07);
}

.main-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.grid-card{
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.grid-value{
  color: #d4af37;
  font-size: 1.5rem;
}

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid rgba(255,255,255,0.07);
  font-size: 12px;
}

.info-row label {
  font-size: 10px;
  color: #d4af37;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 700;
}

.info-row span:first-child {
  color: #94a3b8;
  font-size: 12px;
}

.btns{
  width: 100%;
  display: flex;
  justify-content: space-between;
}

.amount {
  font-family: 'Share Tech Mono', monospace;
  font-weight: 700;
}

.amount.danger {
  color: #ef4444;
}

.loans {
  margin-top: 16px;
}

.loan-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.loan-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: rgba(0,0,0,0.2);
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.15s;
}

.loan-item:hover {
  background: rgba(212,175,55,0.08);
}

.loan-info {
  flex: 1;
}

.loan-id {
  font-family: 'Share Tech Mono', monospace;
  font-size: 12px;
}

.loan-type {
  display: block;
  font-size: 11px;
  color: #94a3b8;
}

.loan-amount {
  font-family: 'Share Tech Mono', monospace;
  font-weight: 700;
  color: #fff;
}

.actions {
  display: flex;
  gap: 8px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid rgba(255,255,255,0.07);
}

.loading {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
}
</style>
