<script setup lang="ts">
import { ref } from "vue";
import { Btn, Input, useModal } from "@/Shared";
import { LoanTypeApi, useLoan } from "@/Modules/Loan";

const { getLoanTypes } = useLoan();
const { close } = useModal();
const name = ref("");
const saving = ref(false);

const save = async () => {
  if (!name.value.trim()) return;
  saving.value = true;
  try {
    await LoanTypeApi.create(name.value.trim());
    await getLoanTypes();
    close();
  } catch {
    saving.value = false;
  }
};
</script>

<template>
  <div class="add-loan-type">
    <div class="add-loan-type__header">Nuevo Tipo de Préstamo</div>
    <form @submit.prevent="save" class="add-loan-type__form">
      <Input
        type="text"
        label="Nombre"
        placeholder="Ingrese el nombre del tipo"
        v-model="name"
      />
      <div class="actions">
        <Btn v-if="!saving">Guardar</Btn>
      </div>
    </form>
  </div>
</template>

<style scoped>
.add-loan-type {
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.add-loan-type__header {
  width: 100%;
  text-align: center;
  font-size: 1.7rem;
  font-weight: 700;
  color: #d4af37;
}
.add-loan-type__form {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}
.actions {
  width: 100%;
  display: flex;
  justify-content: center;
  margin-top: 1rem;
}
</style>
