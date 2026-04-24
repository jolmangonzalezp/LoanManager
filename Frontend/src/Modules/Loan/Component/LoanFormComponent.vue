<script setup lang="ts">
import {computed, onMounted} from "vue";
import {Btn, InputComponent, useModal} from "@Shared";
import SelectComponent from "@Shared/Components/SelectComponent.vue";
import {useLoan} from "@Modules/Loan";

interface Props {
  isEditing?: boolean;
  id?: string;
  disable?: boolean;
  customerPreselected?: string;
}

const props = defineProps<Props>();

const {customers, loanForm, emptyForm, fillFromCustomer, getCustomers, create, update} = useLoan();
const {close} = useModal();

const options = computed(() =>
    customers.value.map(cn => ({
      label: `${cn.name.firstName} ${cn.name.lastName}`,
      value: cn.id,
    }))
);

const save = async () => {
  if (props.isEditing) {
    await update(props.id, loanForm.value)
  }else {
    await create(loanForm.value);
  }
  close();
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
  <div class="loan-form__header">{{ props.isEditing === true ? "Actualizar Préstamo" : "Crear Préstamo"}}</div>
  <form v-if="loanForm" @submit.prevent="save" class="loan-form__form">
    <SelectComponent
      label="Cliente"
      :options="options"
      placeholder="Seleccione el tipo de documento"
      class="loan-form__input"
      v-model="loanForm.customer"
      :disabled="props.disable"
    />
    <InputComponent
        type="text"
        onlyNumbers
        label="Monto:"
        placeholder="Ingrese el monto"
        v-model="loanForm.capital"
        class="loan-form__input"
    />
    <InputComponent
      type="text"
      label="Tasa de interés:"
      placeholder="Ingrese el primer nombre"
      v-model="loanForm.interestRate"
      class="loan-form__input"
    />
    <InputComponent
        type="date"
        label="Fecha de inicio:"
        placeholder=""
        v-model="loanForm.dateStart"
        class="loan-form__input"
    />
    <div class="actions">
      <Btn>
        {{ props.isEditing === true ? "Actualizar" : "Crear"}}
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