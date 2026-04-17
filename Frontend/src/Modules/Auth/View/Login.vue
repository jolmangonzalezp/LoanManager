<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import GCard from '@/Shared/Components/GCard.vue'
import Btn from '@/Shared/Components/Btn.vue'
import { useApi } from '@/Shared/Composable/useApi'

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
  
  console.log('Intentando login...')
  console.log('Email:', form.value.email)
  console.log('Password:', form.value.password)
  
  try {
    const data = await api.post('/auth/login', {
      email: form.value.email,
      password: form.value.password
    })
    
    console.log('Response:', data)
    
    api.setToken(data.token)
    router.push('/')
  } catch (e) {
    console.error('Login error:', e)
    error.value = e.message || 'Credenciales inválidas'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="login-page">
    <div class="login-container">
      <GCard>
        <div class="logo">
          <img src="/logo.webp" alt="Logo" class="logo-img" />
        </div>
        
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
          Demo: admin@loanmanager.com / admin123
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
}

.login-container {
  width: 100%;
  max-width: 320px;
}

.logo {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.logo-img {
  width: 64px;
  height: 64px;
  background: #fff;
  border-radius: 50%;
  padding: 10px;
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