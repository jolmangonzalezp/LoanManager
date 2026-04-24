import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { AuthService } from '../Service/Auth.Service'
import type { LoginForm } from '../types/Auth.Type'

export function useAuth() {
  const router = useRouter()

  const loading = ref(false)
  const error = ref<string | null>(null)

  const form = ref<LoginForm>({
    email: '',
    password: ''
  })

  const login = async () => {
    loading.value = true
    error.value = null

    try {
      await AuthService.login(form.value)
      router.push('/')
    } catch (e: any) {
      error.value = e.message || 'Credenciales inválidas'
    } finally {
      loading.value = false
    }
  }

  const logout = () => {
    AuthService.logout()
  }

  const isAuthenticated = () => AuthService.isAuthenticated()

  return {
    loading,
    error,
    form,
    login,
    logout,
    isAuthenticated
  }
}