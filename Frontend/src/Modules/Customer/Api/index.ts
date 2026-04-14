import { useApi } from '@/Shared/Composable/useApi'
import { useCrud } from '@/Shared/Composable/useCrud'

export function useCustomerApi() {
  const api = useApi()

  async function getAll() {
    return api.get('/customers')
  }

  async function getById(id) {
    return api.get(`/customers/${id}`)
  }

  async function getSummary() {
    return api.get('/customers/summary')
  }

  async function create(data) {
    return api.post('/customers', data)
  }

  async function update(id, data) {
    return api.put(`/customers/${id}`, data)
  }

  async function remove(id) {
    return api.delete(`/customers/${id}`)
  }

  const crud = useCrud({ create, update, remove })

  return {
    loading: api.loading,
    error: api.error,
    getAll,
    getById,
    getSummary,
    create: (data) => crud.create(data, 'Cliente creado'),
    update: (id, data) => crud.update(id, data, 'Cliente actualizado'),
    remove: (id) => crud.remove(id, 'Cliente eliminado')
  }
}