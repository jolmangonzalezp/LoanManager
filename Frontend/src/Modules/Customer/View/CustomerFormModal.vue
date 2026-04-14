<script setup>
import { ref, watch } from 'vue'
import Modal from '@/Shared/Components/Modal.vue'
import Btn from '@/Shared/Components/Btn.vue'

const props = defineProps({
  open: Boolean,
  customer: Object
})

const emit = defineEmits(['close', 'save'])

const loading = ref(false)
const error = ref('')

const form = ref({
  name: { first_name: '', last_name: '', second_last_name: '', third_last_name: '' },
  dni: { type: 'CC', number: '' },
  email: '',
  phone: '',
  address: { street: '', city: '', country: 'CO' }
})

watch(() => props.customer, (c) => {
  if (c) {
    form.value = {
      name: { 
        first_name: c.name.first_name, 
        last_name: c.name.last_name || '',
        second_last_name: c.name.second_last_name || '',
        third_last_name: c.name.third_last_name || ''
      },
      dni: { type: c.dni.type, number: c.dni.number },
      email: c.email || '',
      phone: c.phone || '',
      address: c.address ? { 
        street: c.address.street, 
        city: c.address.city || '',
        country: c.address.country || 'CO'
      } : { street: '', city: '', country: 'CO' }
    }
  } else {
    reset()
  }
}, { immediate: true })

function reset() {
  form.value = {
    name: { first_name: '', last_name: '', second_last_name: '', third_last_name: '' },
    dni: { type: 'CC', number: '' },
    email: '',
    phone: '',
    address: { street: '', city: '', country: 'CO' }
  }
}

async function save() {
  if (!form.value.name.first_name) {
    error.value = 'Primer nombre es requerido'
    return
  }
  if (!form.value.name.last_name) {
    error.value = 'Primer apellido es requerido'
    return
  }
  if (!form.value.dni.number) {
    error.value = 'Documento es requerido'
    return
  }

  loading.value = true
  error.value = ''
  
  try {
    emit('save', { ...form.value })
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <Modal :open="open" :title="customer ? 'Editar Cliente' : 'Nuevo Cliente'" @close="emit('close')">
    <div class="form">
      <div v-if="error" class="error">{{ error }}</div>
      
      <div class="field">
        <label>Tipo Documento</label>
        <select v-model="form.dni.type">
          <option value="CC">Cédula de Ciudadanía</option>
          <option value="CE">Cédula de Extranjería</option>
          <option value="NIT">NIT</option>
        </select>
      </div>
      
      <div class="field">
        <label>Número de Documento</label>
        <input type="text" v-model="form.dni.number" placeholder="79.000.000" />
      </div>
      
      <div class="field">
        <label>Primer Nombre</label>
        <input type="text" v-model="form.name.first_name" placeholder="Juan" />
      </div>
      
      <div class="field">
        <label>Segundo Nombre</label>
        <input type="text" v-model="form.name.last_name" placeholder="Carlos" />
      </div>
      
      <div class="field">
        <label>Primer Apellido</label>
        <input type="text" v-model="form.name.second_last_name" placeholder="Pérez" />
      </div>
      
      <div class="field">
        <label>Segundo Apellido</label>
        <input type="text" v-model="form.name.third_last_name" placeholder="García" />
      </div>
      
      <div class="field">
        <label>Teléfono</label>
        <input type="tel" v-model="form.phone" placeholder="300 000 0000" />
      </div>
      
      <div class="field">
        <label>Email</label>
        <input type="email" v-model="form.email" placeholder="correo@ejemplo.com" />
      </div>
      
      <div class="field">
        <label>Ciudad</label>
        <input type="text" v-model="form.address.city" placeholder="Bogotá" />
      </div>
      
      <div class="field">
        <label>Dirección</label>
        <input type="text" v-model="form.address.street" placeholder="Calle 10 # 20-30" />
      </div>
      
      <div class="actions">
        <Btn @click="save" :disabled="loading">
          {{ loading ? 'Guardando...' : 'Guardar' }}
        </Btn>
        <Btn variant="ghost" @click="emit('close')">Cancelar</Btn>
      </div>
    </div>
  </Modal>
</template>

<style scoped>
.form {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.error {
  background: rgba(239,68,68,0.1);
  border: 1px solid rgba(239,68,68,0.3);
  color: #ef4444;
  padding: 12px;
  border-radius: 6px;
  font-size: 13px;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
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
  gap: 8px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid rgba(255,255,255,0.07);
}
</style>