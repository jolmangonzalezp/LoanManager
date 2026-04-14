<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import PageHeader from '../components/ui/PageHeader.vue'
import GCard from '../components/ui/GCard.vue'
import Btn from '../components/ui/Btn.vue'
import { useApi } from '../composables/useApi'

const router = useRouter()
const api = useApi()

const loading = ref(false)
const error = ref(null)

const form = ref({
  dni_type: 'CC',
  dni: '',
  first_name: '',
  last_name: '',
  second_last_name: '',
  email: '',
  phone: '',
  city: '',
  address: ''
})

async function saveClient() {
  loading.value = true
  error.value = null
  
  try {
    const payload = {
      name: {
        first_name: form.value.first_name,
        last_name: form.value.last_name,
        second_last_name: form.value.second_last_name || undefined
      },
      dni: {
        type: form.value.dni_type,
        number: form.value.dni
      },
      email: form.value.email || undefined,
      phone: form.value.phone || undefined,
      address: form.value.address ? {
        street: form.value.address,
        city: form.value.city,
        country: 'CO'
      } : undefined
    }

    await api.post('/customers', payload)
    router.push('/clientes')
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="cliente-new pu">
    <PageHeader title="Nuevo Cliente" :show-back="true" @back="router.push('/clientes')" />

    <GCard>
      <div v-if="error" class="error">{{ error }}</div>

      <div class="form-grid">
        <div class="field">
          <label>Tipo Documento</label>
          <select v-model="form.dni_type">
            <option value="CC">Cédula de Ciudadanía</option>
            <option value="CE">Cédula de Extranjería</option>
            <option value="NIT">NIT</option>
          </select>
        </div>
        <div class="field">
          <label>Número de Documento</label>
          <input type="text" v-model="form.dni" placeholder="79.000.000" />
        </div>
        <div class="field">
          <label>Nombre(s)</label>
          <input type="text" v-model="form.first_name" placeholder="Juan" />
        </div>
        <div class="field">
          <label>Primer Apellido</label>
          <input type="text" v-model="form.last_name" placeholder="Pérez" />
        </div>
        <div class="field">
          <label>Segundo Apellido</label>
          <input type="text" v-model="form.second_last_name" placeholder="García" />
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
          <input type="text" v-model="form.city" placeholder="Bogotá" />
        </div>
        <div class="field">
          <label>Dirección</label>
          <input type="text" v-model="form.address" placeholder="Calle 10 # 20-30" />
        </div>
      </div>

      <div class="actions">
        <Btn @click="saveClient" :disabled="loading">
          {{ loading ? 'Guardando...' : 'Guardar Cliente' }}
        </Btn>
        <Btn variant="ghost" @click="router.push('/clientes')">Cancelar</Btn>
      </div>
    </GCard>
  </div>
</template>

<style scoped>
.cliente-new {
  animation: fadeUp 0.22s cubic-bezier(0.22, 1, 0.36, 1) both;
}

.error {
  background: rgba(239,68,68,0.1);
  border: 1px solid rgba(239,68,68,0.3);
  color: #ef4444;
  padding: 12px;
  border-radius: 6px;
  margin-bottom: 16px;
  font-size: 13px;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
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
  margin-top: 22px;
  padding-top: 18px;
  border-top: 1px solid rgba(255,255,255,0.07);
}
</style>