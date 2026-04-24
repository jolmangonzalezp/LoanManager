<script setup lang="ts">
import {useCustomer} from "@Modules/Customer";
import {computed, onMounted} from "vue";
import {Btn, InputComponent} from "@Shared";
import SelectComponent from "@Shared/Components/SelectComponent.vue";

interface Props {
  isEditing: boolean;
  id: string;
}

const props = defineProps<Props>();

const {customerForm, emptyCustomer, create, update} = useCustomer();

const docType = [
  {abrev:"CC", name:"Cédula de ciudadanía"},
  {abrev: "CE", name: "Cédula de extranjería"},
  {abrev: "NIT", name: "NIT"},
  {abrev: "PASSPORT", name: "Pasaporte"}
]

const options = computed(() =>
    docType.map(cn => ({
      label: `${cn.name}`,
      value: cn.abrev,
    }))
);

const save = async () => {
  if (props.isEditing) {
    await update(props.id, customerForm.value)
  }else {
    await create(customerForm.value);
  }
}

onMounted(() => {
  if (!props.isEditing) {
    emptyCustomer();
  }
})

</script>

<template>
<div class="customer-form">
  <div class="customer-form__header">{{ props.isEditing === true ? "Actualizar Cliente" : "Crear Cliente"}}</div>
  <form v-if="customerForm" @submit.prevent="save" class="customer-form__form">
    <SelectComponent
      label="Tipo de documento:"
      :options="options"
      placeholder="Seleccione el tipo de documento"
      class="customer-form__input"
      v-model="customerForm.dni.type"
    />
    <InputComponent
        type="text"
        onlyNumbers
        label="Numero de documento:"
        placeholder="Ingrese el numero de documento"
        v-model="customerForm.dni.number"
        class="customer-form__input"
    />
    <InputComponent
      type="text"
      label="Nombre:"
      placeholder="Ingrese el primer nombre"
      v-model="customerForm.name.firstName"
      class="customer-form__input"
    />
    <InputComponent
        type="text"
        label="Segundo nombre (Opcional):"
        placeholder="Ingrese el segundo nombre"
        v-model="customerForm.name.middleName"
        class="customer-form__input"
    />
    <InputComponent
        type="text"
        label="Apellido:"
        placeholder="Ingrese el apellido:"
        v-model="customerForm.name.lastName"
        class="customer-form__input"
    />
    <InputComponent
        type="text"
        label="Segundo apellido:"
        placeholder="Ingrese el segundo apellido"
        v-model="customerForm.name.secondLastName"
        class="customer-form__input"
    />
    <InputComponent
        type="text"
        label="Email:"
        placeholder="Ingrese el email"
        v-model="customerForm.email"
        class="customer-form__input"
    />

    <InputComponent
        type="text"
        onlyNumbers
        label="Teléfono:"
        placeholder="Ingrese el numero de telefono"
        v-model="customerForm.phone"
        class="customer-form__input"
    />

    <InputComponent
        type="text"
        label="Dirección:"
        placeholder="Ingrese la direccion"
        v-model="customerForm.address"
        class="customer-form__input"
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
.customer-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.customer-form__header {
  width: 100%;
  text-align: center;
  font-size: 2rem;
  font-weight: 700;
  color: #d4af37;
}

.customer-form__form{
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 6px;
}

.customer-form__input{
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