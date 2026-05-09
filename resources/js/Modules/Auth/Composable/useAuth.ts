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
            notify.closeLoading();
            notify.success("Bienvenido", "Has iniciado sesion exitosamente");
            await router.push('/');
        } catch (e: any) {
            notify.closeLoading();
            notify.toastError(e.message);
        }
    }

    const me = async (): Promise<void> => {
        user.value = await AuthService.me();
    }

    const logout = async () => {
        notify.loading("Cerrando Session", "");
        try {
            await AuthService.logout()
            notify.closeLoading();
            notify.success("Hasta Pronto", "Has cerrado sesion exitosamente");
            await router.push('/login');
        } catch (e: any){
            notify.closeLoading();
            notify.error("Error", e.message);
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
