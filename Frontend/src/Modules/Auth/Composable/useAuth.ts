import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { AuthService } from '../Service/Auth.Service'
import type {LoginForm, User} from '../types/Auth.Type'

export function useAuth() {
  const router = useRouter()

  const loading = ref(false)
  const error = ref<string | null>(null)

  const form = ref<LoginForm>({
    email: '',
    password: ''
  })

  const user = ref<User>({})

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

  const getName = ()=> {
    let i;
    let j = 0;
    let name;
    while (user.value.name.length){
      if (user.value.name[i] !== ""){
        name += user.value.name[i];
      } else {
        j++;
        i++;
      }
      console.log(name);
    }
  }

  const me = async () => {
    user.value = await AuthService.me();
  }

  const logout = () => {
    AuthService.logout()
  }

  const isAuthenticated = () => AuthService.isAuthenticated()

  return {
    loading,
    error,
    form,
    user,
    login,
    me,
    logout,
    isAuthenticated,
    getName
  }
}