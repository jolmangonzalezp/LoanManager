<script setup lang="ts">
import { onMounted } from 'vue'
import { useUser } from '@/Modules/User'
import { Btn, Input } from '@/Shared'

interface Props {
  isEditing: boolean
  id: string
}

const props = defineProps<Props>()
const { userForm, user, emptyUser, fillUser, create, update } = useUser()

const save = async () => {
  if (props.isEditing) {
    await update(props.id, {
      username: userForm.value!.username,
      name: userForm.value!.name,
      email: userForm.value!.email,
      phone: userForm.value!.phone,
    })
  } else {
    await create(userForm.value!)
  }
}

const fullName = (): string => {
  if (!userForm.value) return ''
  const parts = [
    userForm.value.name.firstName,
    userForm.value.name.middleName,
    userForm.value.name.lastName,
    userForm.value.name.secondLastName,
  ].filter(Boolean)
  return parts.join(' ') || ''
}

onMounted(() => {
  if (props.isEditing) {
    fillUser()
  } else {
    emptyUser()
  }
})
</script>

<template>
  <div class="user-form">
    <div class="user-form__header">{{ props.isEditing ? 'Actualizar Usuario' : 'Crear Usuario' }}</div>
    <form v-if="userForm" @submit.prevent="save" class="user-form__form">
      <Input
        type="text"
        label="Usuario:"
        placeholder="Ingrese el nombre de usuario"
        v-model="userForm.username"
        class="user-form__input"
      />
      <Input
        type="password"
        label="Contraseña:"
        placeholder="Ingrese la contraseña"
        v-model="userForm.password"
        class="user-form__input"
        :required="!props.isEditing"
      />
      <Input
        type="text"
        label="Nombre:"
        placeholder="Ingrese el primer nombre"
        v-model="userForm.name.firstName"
        class="user-form__input"
      />
      <Input
        type="text"
        label="Segundo nombre (Opcional):"
        placeholder="Ingrese el segundo nombre"
        v-model="userForm.name.middleName"
        class="user-form__input"
      />
      <Input
        type="text"
        label="Apellido:"
        placeholder="Ingrese el apellido"
        v-model="userForm.name.lastName"
        class="user-form__input"
      />
      <Input
        type="text"
        label="Segundo apellido:"
        placeholder="Ingrese el segundo apellido"
        v-model="userForm.name.secondLastName"
        class="user-form__input"
      />
      <Input
        type="email"
        label="Email:"
        placeholder="Ingrese el email"
        v-model="userForm.email"
        class="user-form__input"
      />
      <Input
        type="text"
        label="Teléfono:"
        placeholder="Ingrese el teléfono"
        v-model="userForm.phone"
        class="user-form__input"
      />
      <div class="actions">
        <Btn>{{ props.isEditing ? 'Actualizar' : 'Crear' }}</Btn>
      </div>
    </form>
  </div>
</template>

<style scoped>
.user-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
  width: 100%;
  max-width: 400px;
}
.user-form__header {
  width: 100%;
  text-align: center;
  font-size: 2rem;
  font-weight: 700;
  color: #d4af37;
}
.user-form__form {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 6px;
}
.user-form__input {
  width: 300px;
  max-width: 400px;
}
.actions {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 1rem;
}
</style>
