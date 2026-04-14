<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import GCard from '../components/ui/GCard.vue'
import Btn from '../components/ui/Btn.vue'
import { useApi } from '../composables/useApi'

const router = useRouter()
const api = useApi()

const loading = ref(false)
const error = ref(null)

const form = ref({
  email: '',
  password: ''
})

async function login() {
  loading.value = true
  error.value = null
  
  try {
    const data = await api.post('/auth/login', {
      email: form.value.email,
      password: form.value.password
    })
    
    api.setToken(data.token)
    router.push('/')
  } catch (e) {
    error.value = e.message || 'Credenciales inválidas'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="login-page">
    <div class="login-container">
      <div class="logo">
        <span class="logo-icon">◆</span>
        <span class="logo-text">LoanManager</span>
      </div>
      
      <GCard>
        <h1 class="title">Iniciar Sesión</h1>
        
        <div v-if="error" class="error">{{ error }}</div>
        
        <div class="field">
          <label>Email</label>
          <input type="email" v-model="form.email" placeholder="correo@ejemplo.com" />
        </div>
        
        <div class="field">
          <label>Contraseña</label>
          <input type="password" v-model="form.password" placeholder="••••••••" />
        </div>
        
        <Btn @click="login" :disabled="loading" style="width: 100%; justify-content: center">
          {{ loading ? 'Ingresando...' : 'Ingresar' }}
        </Btn>
        
        <div class="hint">
          Demo: test@example.com / password
        </div>
      </GCard>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: radial-gradient(circle at top right, #0a1f1a 0%, #06090f 100%);
}

.login-container {
  width: 100%;
  max-width: 360px;
  padding: 20px;
}

.logo {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  margin-bottom: 32px;
}

.logo-icon {
  font-size: 28px;
  color: #d4af37;
}

.logo-text {
  font-size: 18px;
  font-weight: 600;
  color: #fff;
  text-transform: uppercase;
  letter-spacing: 2px;
}

.title {
  font-size: 16px;
  font-weight: 600;
  color: #fff;
  text-align: center;
  margin-bottom: 24px;
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

.error {
  background: rgba(239,68,68,0.1);
  border: 1px solid rgba(239,68,68,0.3);
  color: #ef4444;
  padding: 12px;
  border-radius: 6px;
  margin-bottom: 16px;
  font-size: 13px;
  text-align: center;
}

.hint {
  text-align: center;
  margin-top: 16px;
  font-size: 11px;
  color: #3d4f5a;
}
</style>