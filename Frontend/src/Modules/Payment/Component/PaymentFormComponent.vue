 <script setup lang="ts">
 import { formatCurrency } from '@/Shared/Composable/useApi';
 import {Btn, InputComponent, useModal} from "@Shared";
 import {onMounted} from "vue";
 import {usePayment} from "@Modules/Payment";

interface Props {
  id: string;
  loanId: string;
  loanNumber: string;
  remainDebt: number;
  interestRate: number;
  isEditing: boolean;
}

const props = defineProps<Props>();

const { paymentForm, initForm, create } = usePayment();
const { close } = useModal();

const save = async () => {
  if (props.isEditing) {
    console.log()
  }else {
    await create(paymentForm.value)
  }
  close();
}

onMounted( () => {
  if (!props.isEditing) {
    initForm(
        props.loanId,
        props.remainDebt,
        props.interestRate,
    )
  }
})
</script>

<template>
  <div class="payment-form">
    <div class="payment-form__header">{{ props.isEditing === true ? "Actualizar Pago" : "Pago"}}</div>
    <div class="loan-info">
      <div class="info-row">
        <span>Préstamo:</span>
        <span>{{ props.loanNumber }}</span>
      </div>
      <div class="info-row">
        <span>Saldo:</span>
        <span>{{ formatCurrency(props.remainDebt) }}</span>
      </div>
    </div>
    <form v-if="paymentForm" @submit.prevent="save" class="payment-form__form">
      <InputComponent
          type="text"
          onlyNumbers
          label="Monto:"
          placeholder="Ingrese el monto"
          v-model="paymentForm.amount"
          class="payment-form__input"
      />
      <InputComponent
          type="date"
          label="Fecha de inicio:"
          placeholder=""
          v-model="paymentForm.paymentDate"
          class="payment-form__input"
      />
      <div class="actions">
        <Btn @click="save">
          {{ props.isEditing === true ? "Actualizar" : "Pagar"}}
        </Btn>
      </div>
    </form>
  </div>
</template>

<style scoped>
.payment-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.payment-form__header {
  width: 100%;
  text-align: center;
  font-size: 1.7rem;
  font-weight: 700;
  color: #d4af37;
}

.payment-form__form {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 6px;
}

.payment-form__input {
  width: 300px;
  max-width: 400px;
}

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
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid rgba(255,255,255,0.07);
}
</style>