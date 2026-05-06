import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { AuthService, LoginForm, User } from '@/Modules/Auth';
import { useNotifier } from '@/Shared';

export default function useAuth() {
  const router = useRouter();
  const notify =  useNotifier();


  const form = ref<LoginForm>({
    email: '',
    password: ''
  })

  const user = ref<User|null>(null);

  const login = async () => {
      notify.loading("Iniciando Session", "");

    try {
      await AuthService.login(form.value);
      router.push('/');
      notify.success("Bienvenido", "Has iniciado sesion exitosamente");
    } catch (e: any) {
      notify.error("Error", e)
    } finally {
      notify.closeLoading()
    }
  }

  const me = async () => {
    user.value = await AuthService.me();
  }

  const logout = () => {
      notify.loading("Cerrando Session", "");
    try {
        AuthService.logout()
        notify.success("Hasta Pronto", "Has cerrado sesion exitosamente");
    } catch (e: any){
        notify.error("Error", e)
    }finally {
        notify.closeLoading()
    }
  }

  const isAuthenticated = () => AuthService.isAuthenticated()

  return {
    form,
    user,
    login,
    me,
    logout,
    isAuthenticated
  }
}
