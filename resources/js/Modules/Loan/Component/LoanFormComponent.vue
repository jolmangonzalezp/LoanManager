<script setup lang="ts">
import {computed, onMounted, ref} from "vue";
import {Btn, Input, Select, useModal} from "@/Shared";
import { useLoan } from '@/Modules/Loan';


interface Props {
  isEditing?: boolean;
  id?: string;
  disable?: boolean;
  customerPreselected?: string;
}

const props = defineProps<Props>();

const {customers, loanForm, emptyForm, fillFromCustomer, getCustomers, create, update} = useLoan();
const {close} = useModal();
const loading = ref(false);
const options = computed(() =>
    customers.value.map(cn => ({
      label: `${cn.name.firstName} ${cn.name.lastName}`,
      value: cn.id,
    }))
);

const capitalFormatted = computed({
    get() {
        return loanForm.value?.capital
            ? loanForm.value.capital.toLocaleString('es-CO')
            : ''
    },

    set(value: string) {
        if (!loanForm.value) return

        loanForm.value.capital = Number(
            value.replace(/\./g, '').replace(',', '.')
        ) || 0
    }
})

const save = async () => {
  if (!loanForm.value) return
  if (props.isEditing) {
      if (!props.id) return
    const response = await update(props.id, loanForm.value)
      if (response) {
          close();
      }
  }else {
    const response = await create(loanForm.value);
      if (response) {
          close();
      }
  }
}

onMounted(() => {
  if (!props.isEditing) {
    emptyForm();
  }
  if (props.customerPreselected) {
    fillFromCustomer(props.customerPreselected);
  }
  getCustomers();
})

</script>

<template>
<div class="loan-form">
  <div class="loan-form__header">{{ props?.isEditing === true ? "Actualizar Préstamo" : "Crear Préstamo"}}</div>
  <form v-if="loanForm" @submit.prevent="save" class="loan-form__form">
    <Select
      label="Cliente"
      :options="options"
      placeholder="Seleccione el cliente"
      class="loan-form__input"
      v-model="loanForm.customer"
      :disabled="props.disable"
    />
    <Input
        type="text"
        onlyNumbers
        label="Monto:"
        placeholder="Ingrese el monto"
        v-model="capitalFormatted"
        class="loan-form__input"
    />
    <Input
      type="text"
      label="Tasa de interés:"
      placeholder="Ingrese el interes"
      v-model="loanForm.interestRate"
      class="loan-form__input"
    />
    <Input
        type="date"
        label="Fecha de inicio:"
        placeholder=""
        v-model="loanForm.dateStart"
        class="loan-form__input"
    />
    <div class="actions">
      <Btn v-if="!loading">
        {{ props?.isEditing === true ? "Actualizar" : "Crear"}}
      </Btn>
    </div>
  </form>
</div>
</template>

<style scoped>
.loan-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.loan-form__header {
  width: 100%;
  text-align: center;
  font-size: 1.7rem;
  font-weight: 700;
  color: #d4af37;
}

.loan-form__form{
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 6px;
}

.loan-form__input{
  width: 300px;
  max-width: 400px;
}

.actions{
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 1rem;
}
</style>
