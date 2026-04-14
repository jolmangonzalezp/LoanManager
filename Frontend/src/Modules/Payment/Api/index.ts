import { useApi } from '@/Shared/Composable/useApi'
import { useCrud } from '@/Shared/Composable/useCrud'

export function usePaymentApi() {
  const api = useApi()

  async function getAll() {
    return api.get('/payments')
  }

  async function getProjectedVsActual() {
    return api.get('/reports/projected-vs-actual')
  }

  async function getCollectionAvailability() {
    return api.get('/reports/collection-availability')
  }

  async function getClientProfitability() {
    return api.get('/reports/client-profitability')
  }

  async function create(data) {
    return api.post('/payments', data)
  }

  async function remove(id) {
    return api.delete(`/payments/${id}`)
  }

  const crud = useCrud({ create, remove })

  return {
    loading: api.loading,
    error: api.error,
    getAll,
    getProjectedVsActual,
    getCollectionAvailability,
    getClientProfitability,
    create: (data) => crud.create(data, 'Pago registrado'),
    delete: (id) => crud.remove(id, 'Pago eliminado')
  }
}