<script setup lang="ts">
import {usePayment} from "@Modules/Payment/index.ts";
import {Btn, CardTitle, GCard, useModal} from "@Shared";
import { formatCurrency, formatDate, getStatusLabel, getStatusColor } from '@/Shared/Composable/useApi'
import {useLoan} from "@Modules/Loan";
import LoanDetailComponent from "@Modules/Loan/Component/LoanDetailComponent.vue";
import PaymentFormComponent from "@Modules/Payment/Component/PaymentFormComponent.vue";

const {payment, fillForm} = usePayment();
const {getById} = useLoan();
const {close, open } = useModal();

const goToLoan = async () => {
  await getById(payment.value.loan.id);
  close();
  open(
      LoanDetailComponent, {
        size: "lg"
      }
  )
}

const updatePayment = () => {
  fillForm();
  close();
  open(
      PaymentFormComponent, {
        size: "lg",
        props: {
          id: payment.value.id,
          loanId: payment.value.loan.id,
          loanNumber: payment.value.loan.loanNumber,
          remainDebt: payment.value.loan.remainingDebt,
          interestRate: payment.value.loan.interestRate,
          isEditing: true,
        }
      }
  )
}

</script>

<template>
    <div class="payment-detail" v-if="payment">
      <section class="payment-details__header">
        <span class="loan-number">{{ payment?.loan.loanNumber }}</span>
        <span class="loan-remain">{{ formatCurrency(payment?.loan.remainingDebt) }}</span>
      </section>
      <div class="main-grid">
        <GCard class="grid-card">
          <CardTitle class="grid-title">Fecha pago</CardTitle>
          <div class="grid-value">{{ payment?.paymentDate }}</div>
        </GCard>
      <GCard class="grid-card">
        <CardTitle class="grid-title">Monto Pagado</CardTitle>
        <div class="grid-value">{{ formatCurrency(payment?.amount) }}</div>
      </GCard>
        <GCard class="grid-card">
          <CardTitle class="grid-title">Capital Pagado</CardTitle>
          <div class="grid-value">{{ formatCurrency(payment?.capitalPaid) }}</div>
        </GCard>
        <GCard class="grid-card">
          <CardTitle class="grid-title">Interés Pagado</CardTitle>
          <div class="grid-value">{{ formatCurrency(payment?.interestPaid) }}</div>
        </GCard>
      </div>

      <div class="btns">
        <Btn variant="secondary" @click="updatePayment">Actualizar</Btn>
        <Btn @click="goToLoan">Ir Préstamo</Btn>
      </div>
    </div>
</template>

<style scoped>
.payment-detail {
  display: flex;
  flex-direction: column;
  gap: 16px;
  width: 400px;
  margin-top: 1rem;
}

.payment-details__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(255,255,255,0.07);
  margin-top: 0.5rem;
}

.loan-number, .loan-remain {
  font-size: 1.2rem;
  color: #d4af37;
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

.btns{
  width: 100%;
  display: flex;
  justify-content: space-between;
}
</style>
