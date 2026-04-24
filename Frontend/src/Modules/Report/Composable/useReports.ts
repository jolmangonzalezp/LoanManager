import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ReportService } from '../Service/Report.Service'

export function useReports() {
  const router = useRouter()

  const loading = ref(false)
  const error = ref<string | null>(null)

  const redirect = () => {
    router.replace('/reportes/cartera')
  }

  onMounted(() => {
    redirect()
  })

  return {
    loading,
    error,
    redirect
  }
}